<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\BookingSetting;
use App\Models\Hiker;
use App\Models\Route;
use App\Models\RouteDailyQuota;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicBookingController extends Controller
{
    public function homepage()
    {
        $routes = Route::all();
        $pricePerParticipant = BookingSetting::get('price_per_participant', 20000);
        $currencySymbol = BookingSetting::get('currency_symbol', 'Rp');
        
        return view('booking.minimal-homepage', compact('routes', 'pricePerParticipant', 'currencySymbol'));
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'route_id' => ['required', 'uuid', Rule::exists('routes', 'id')],
            'trip_date' => ['required', 'date', 'after:' . now()->addDays(2)->format('Y-m-d')],
        ]);

        $routeId = $validated['route_id'];
        $tripDate = Carbon::parse($validated['trip_date']);

        // Check if date is within booking window
        $minDays = (int) config('booking.min_days_before_trip', 3);
        $maxDays = (int) config('booking.max_days_before_trip', 30);
        
        if ($tripDate->lt(now()->addDays($minDays)) || $tripDate->gt(now()->addDays($maxDays))) {
            return response()->json([
                'available' => false,
                'message' => "Booking is only available {$minDays} to {$maxDays} days in advance."
            ]);
        }

        // Get quota for the date
        $quota = RouteDailyQuota::where('route_id', $routeId)
            ->where('date', $tripDate->format('Y-m-d'))
            ->first();

        if (!$quota) {
            // Create default quota if not exists
            $quota = RouteDailyQuota::create([
                'route_id' => $routeId,
                'date' => $tripDate->format('Y-m-d'),
                'capacity' => config('booking.default_daily_capacity', 120),
                'booked' => 0,
                'status' => 'open'
            ]);
        }

        $available = $quota->status === 'open' && $quota->available > 0;

        return response()->json([
            'available' => $available,
            'capacity' => $quota->capacity,
            'booked' => $quota->booked,
            'remaining' => $quota->available,
            'status' => $quota->status
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'route_id' => ['required', 'uuid', Rule::exists('routes', 'id')],
            'trip_date' => ['required', 'date', 'after:' . now()->addDays(2)->format('Y-m-d')],
            'participants_count' => ['required', 'integer', 'min:1', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'participants' => ['required', 'array'],
            'participants.*.name' => ['required', 'string', 'max:255'],
            'participants.*.phone' => ['nullable', 'string', 'max:20'],
            'participants.*.emergency_contact' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            // Check availability again
            $availabilityCheck = $this->checkAvailability($request);
            $availability = $availabilityCheck->getData(true);
            
            if (!$availability['available']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, this date is no longer available.'
                ], 422);
            }

            if ($availability['remaining'] < $validated['participants_count']) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$availability['remaining']} spots remaining for this date."
                ], 422);
            }

            // Create or find hiker
            $hiker = Hiker::where('email', $validated['email'])->first();
            
            if (!$hiker) {
                $hiker = Hiker::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            }

            // Get route for pricing
            $route = Route::findOrFail($validated['route_id']);
            $pricePerParticipant = $this->getPricePerParticipant($route);
            $totalAmount = $pricePerParticipant * $validated['participants_count'];

            // Create booking
            $booking = Booking::create([
                'route_id' => $validated['route_id'],
                'hiker_id' => $hiker->id,
                'trip_date' => $validated['trip_date'],
                'participants_count' => $validated['participants_count'],
                'contact_phone' => $validated['phone'],
                'notes' => $validated['notes'],
                'amount' => $totalAmount,
                'currency' => 'IDR',
                'status' => 'pending-payment',
                'payment_due_at' => now()->addHours(BookingSetting::get('payment_due_hours', 24)),
                'created_via' => 'web',
                'duration_days' => 1, // Default 1 day trip
            ]);

            // Add participants
            foreach ($validated['participants'] as $participantData) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'name' => $participantData['name'],
                    'phone' => $participantData['phone'] ?? null,
                    'emergency_contact' => $participantData['emergency_contact'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking_code' => $booking->code,
                'payment_due_at' => $booking->payment_due_at->format('Y-m-d H:i:s'),
                'amount' => $booking->amount,
                'currency' => $booking->currency
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking. Please try again.'
            ], 500);
        }
    }

    public function checkStatus()
    {
        return view('booking.minimal-check-status');
    }

    public function getStatus(Request $request)
    {
        $validated = $request->validate([
            'booking_code' => ['required', 'string']
        ]);

        $booking = Booking::with(['route', 'hiker', 'participants'])
            ->where('code', $validated['booking_code'])
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        // Check if booking is expired
        if ($booking->status === 'pending-payment' && $booking->payment_due_at < now()) {
            $booking->update(['status' => 'expired']);
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'code' => $booking->code,
                'route_name' => $booking->route->name,
                'trip_date' => $booking->trip_date->format('Y-m-d'),
                'participants_count' => $booking->participants_count,
                'amount' => $booking->amount,
                'currency' => $booking->currency,
                'status' => $booking->status,
                'payment_due_at' => $booking->payment_due_at?->format('Y-m-d H:i:s'),
                'paid_at' => $booking->paid_at?->format('Y-m-d H:i:s'),
                'hiker' => [
                    'name' => $booking->hiker->name,
                    'email' => $booking->hiker->email,
                    'phone' => $booking->hiker->phone,
                ],
                'participants' => $booking->participants->map(function($p) {
                    return [
                        'name' => $p->name,
                        'phone' => $p->phone,
                        'emergency_contact' => $p->emergency_contact,
                    ];
                }),
                'notes' => $booking->notes,
            ]
        ]);
    }

    public function showPayment($bookingCode)
    {
        $booking = Booking::where('code', $bookingCode)
            ->with(['route', 'hiker', 'participants'])
            ->first();

        if (!$booking) {
            return redirect()->route('booking.check-status')
                ->with('error', 'Booking not found.');
        }

        $paymentMethods = $this->getPaymentMethods();
        
        return view('booking.minimal-payment', compact('booking', 'paymentMethods'));
    }

    public function uploadPayment(Request $request, $bookingCode)
    {
        $validated = $request->validate([
            'proof_of_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB max
            'payment_method' => ['required', 'string', 'in:bank_transfer,e_wallet,cash'],
        ]);

        $booking = Booking::where('code', $bookingCode)->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.'
            ], 404);
        }

        if ($booking->status !== 'pending-payment') {
            return response()->json([
                'success' => false,
                'message' => 'This booking is not available for payment.'
            ], 422);
        }

        // Store proof of payment
        $file = $request->file('proof_of_payment');
        $path = $file->store('payment-proofs', 'public');

        $booking->update([
            'proof_of_payment_path' => $path,
            'payment_method' => $validated['payment_method'],
            'status' => 'awaiting-validation',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded successfully! Your booking is now awaiting validation.'
        ]);
    }

    /**
     * Get price per participant for a route
     * Checks for route-specific pricing first, then falls back to default
     */
    private function getPricePerParticipant(Route $route): int
    {
        // TODO: Add route-specific pricing support in future
        // For now, use default price from database settings
        return (int) BookingSetting::get('price_per_participant', 20000);
    }

    /**
     * Format currency amount
     */
    private function formatCurrency(int $amount): string
    {
        $symbol = BookingSetting::get('currency_symbol', 'Rp');
        return $symbol . ' ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get available payment methods from database settings
     */
    private function getPaymentMethods(): array
    {
        $paymentMethods = [];

        // Bank Transfer
        if (BookingSetting::get('payment_bank_transfer_enabled', true)) {
            $paymentMethods['bank_transfer'] = [
                'name' => 'Bank Transfer',
                'description' => 'Transfer to our bank account (Most common method)',
                'enabled' => true,
                'icon' => 'fas fa-university',
                'details' => [
                    'bank_name' => BookingSetting::get('bank_name', 'Bank Mandiri'),
                    'account_number' => BookingSetting::get('bank_account_number', '1234-567890-123'),
                    'account_name' => BookingSetting::get('bank_account_name', 'PT Tambora Adventure'),
                    'instructions' => 'Please include your booking code in the transfer description.',
                ]
            ];
        }

        // E-Wallet
        if (BookingSetting::get('payment_e_wallet_enabled', true)) {
            $paymentMethods['e_wallet'] = [
                'name' => 'E-Wallet',
                'description' => 'GoPay, OVO, DANA, ShopeePay',
                'enabled' => true,
                'icon' => 'fas fa-mobile-alt',
                'details' => [
                    'accounts' => BookingSetting::get('ewallet_accounts', [
                        'GoPay' => '0812-3456-7890',
                        'OVO' => '0812-3456-7890',
                        'DANA' => '0812-3456-7890',
                        'ShopeePay' => '0812-3456-7890'
                    ]),
                    'instructions' => 'Please include your booking code in the transfer note.',
                ]
            ];
        }

        // Cash Payment
        if (BookingSetting::get('payment_cash_enabled', true)) {
            $paymentMethods['cash'] = [
                'name' => 'Cash Payment',
                'description' => 'Pay at our office or during pickup',
                'enabled' => true,
                'icon' => 'fas fa-money-bill',
                'details' => [
                    'office_address' => BookingSetting::get('office_address', 'Jl. Raya Tambora No. 123, Dompu, West Nusa Tenggara'),
                    'office_hours' => BookingSetting::get('office_hours', 'Monday - Sunday, 08:00 - 17:00'),
                    'contact' => BookingSetting::get('contact_phone', '+62 812-3456-7890'),
                    'instructions' => 'Please contact us via WhatsApp to arrange cash payment.',
                ]
            ];
        }

        return $paymentMethods;
    }
}