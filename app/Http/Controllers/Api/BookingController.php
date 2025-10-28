<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Holiday;
use App\Models\RouteDailyQuota;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $hiker = $user->loadMissing('hiker')->hiker;

        if (! $hiker) {
            return response()->json(['message' => 'User is not linked to a hiker profile'], 422);
        }

        $bookings = Booking::query()
            ->with(['route', 'participants'])
            ->where('hiker_id', $hiker->id)
            ->orderByDesc('trip_date')
            ->paginate(20);

        return $bookings;
    }

    public function availability(Request $request)
    {
        $validated = $request->validate([
            'route_id' => ['required', 'uuid', Rule::exists('routes', 'id')],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $minDays = (int) config('booking.min_days_before_trip', 3);
        $maxDays = (int) config('booking.max_days_before_trip', 30);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->startOfDay()->addDays($minDays);

        $upperLimit = now()->startOfDay()->addDays($maxDays);

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->startOfDay()
            : $from->copy()->addDays(30);

        if ($to->gt($upperLimit)) {
            $to = $upperLimit->copy();
        }

        if ($to->lessThan($from)) {
            [$from, $to] = [$to, $from];
        }

        $routeId = $validated['route_id'];

        $quotas = RouteDailyQuota::query()
            ->where('route_id', $routeId)
            ->whereBetween('date', [$from, $to])
            ->get()
            ->keyBy(fn ($quota) => Carbon::parse($quota->date)->format('Y-m-d'));

        $holidays = Holiday::query()
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($routeId) {
                $query->whereNull('route_id')->orWhere('route_id', $routeId);
            })
            ->get()
            ->keyBy(fn ($holiday) => Carbon::parse($holiday->date)->format('Y-m-d'));

        $bookings = Booking::query()
            ->where('route_id', $routeId)
            ->whereBetween('trip_date', [$from, $to])
            ->whereIn('status', ['pending-payment', 'awaiting-validation', 'confirmed'])
            ->get()
            ->groupBy(fn ($booking) => Carbon::parse($booking->trip_date)->format('Y-m-d'));

        $results = [];
        $disabledDates = [];

        for ($cursor = $from->copy(); $cursor->lte($to); $cursor->addDay()) {
            $key = $cursor->format('Y-m-d');

            $quota = $quotas->get($key);
            $holiday = $holidays->get($key);
            $dayBookings = $bookings->get($key, collect());

            $capacity = $quota?->capacity ?? (int) config('booking.default_daily_capacity', 120);
            $bookedViaQuota = $quota?->booked ?? 0;
            $status = $quota?->status ?? 'open';

            if ($holiday && $holiday->is_closed) {
                $status = 'closed';
            }

            $reserved = $dayBookings->sum('participants_count');
            if ($bookedViaQuota && $bookedViaQuota > 0) {
                $reserved = max($reserved, $bookedViaQuota);
            }

            $available = $status === 'open' ? max($capacity - $reserved, 0) : 0;

            $payload = [
                'date' => $key,
                'capacity' => $capacity,
                'reserved' => $reserved,
                'available' => $available,
                'status' => $status,
                'is_holiday' => (bool) $holiday,
                'holiday_reason' => $holiday->reason ?? null,
                'label' => sprintf('%d/%d', $capacity, $reserved),
            ];

            if ($status !== 'open') {
                $disabledDates[] = $key;
            }

            $results[] = $payload;
        }

        return [
            'data' => $results,
            'disabled_dates' => array_values(array_unique($disabledDates)),
        ];
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $hiker = $user->loadMissing('hiker')->hiker;

        if (! $hiker) {
            return response()->json(['message' => 'User is not linked to a hiker profile'], 422);
        }

        $validated = $request->validate([
            'route_id' => ['required', 'uuid', Rule::exists('routes', 'id')],
            'trip_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'contact_phone' => ['required', 'string', 'max:32'],
            'notes' => ['nullable', 'string'],
            'proof_of_payment_path' => ['nullable', 'string'],
            'proof_of_payment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.name' => ['required', 'string', 'max:255'],
            'participants.*.gender' => ['nullable', 'string', 'max:16'],
            'participants.*.nationality' => ['required', 'string', 'max:64'],
            'participants.*.origin_country' => ['required', 'string', 'max:64'],
            'participants.*.age_group' => ['required', 'integer', 'between:17,70'],
            'participants.*.occupation' => ['required', 'string', 'max:64'],
            'participants.*.id_type' => ['required', Rule::in(['KTP', 'SIM', 'NPWP'])],
            'participants.*.id_number' => ['required', 'string', 'max:128'],
            'participants.*.health_certificate_path' => ['nullable', 'string'],
            'participants.*.health_certificate' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:5120',
            ],
            'participants.*.is_leader' => ['nullable', 'boolean'],
        ]);

        $tripDate = Carbon::parse($validated['trip_date'])->startOfDay();
        $minDays = (int) config('booking.min_days_before_trip', 3);
        $maxDays = (int) config('booking.max_days_before_trip', 30);

        if ($tripDate->lt(now()->startOfDay()->addDays($minDays))) {
            return response()->json([
                'message' => "Booking must be made at least {$minDays} days before the trip date",
            ], 422);
        }

        if ($tripDate->gt(now()->startOfDay()->addDays($maxDays))) {
            return response()->json([
                'message' => "Booking cannot be made more than {$maxDays} days in advance",
            ], 422);
        }

        $routeId = $validated['route_id'];

        $holiday = Holiday::query()
            ->whereDate('date', $tripDate)
            ->where(function ($query) use ($routeId) {
                $query->whereNull('route_id')->orWhere('route_id', $routeId);
            })
            ->where('is_closed', true)
            ->exists();

        if ($holiday) {
            return response()->json([
                'message' => 'Selected date is not available for booking',
            ], 422);
        }

        if ($validated['payment_method'] === 'transfer' && ! $request->hasFile('proof_of_payment') && empty($validated['proof_of_payment_path'])) {
            return response()->json([
                'message' => 'Proof of payment is required for bank transfer.',
            ], 422);
        }

        $participants = $validated['participants'];
        $participantCount = count($participants);

        $leaders = collect($participants)->filter(fn ($participant) => (bool) ($participant['is_leader'] ?? false));
        if ($leaders->count() !== 1) {
            return response()->json([
                'message' => 'Exactly one participant must be assigned as the leader.',
            ], 422);
        }

        $quota = RouteDailyQuota::query()
            ->where('route_id', $routeId)
            ->whereDate('date', $tripDate)
            ->first();

        $capacity = $quota?->capacity ?? (int) config('booking.default_daily_capacity', 120);
        $status = $quota?->status ?? 'open';

        if ($status !== 'open') {
            return response()->json([
                'message' => 'Bookings are closed for the selected date.',
            ], 422);
        }

        $reservedSeats = Booking::query()
            ->where('route_id', $routeId)
            ->whereDate('trip_date', $tripDate)
            ->whereIn('status', ['pending-payment', 'awaiting-validation', 'confirmed'])
            ->sum('participants_count');

        $remaining = max($capacity - $reservedSeats, 0);

        if ($participantCount > $remaining) {
            return response()->json([
                'message' => 'Quota exceeded for the selected date.',
                'remaining_quota' => $remaining,
            ], 422);
        }

        $booking = DB::transaction(function () use ($validated, $participants, $participantCount, $hiker, $user, $tripDate, $request) {
            $proofPath = $validated['proof_of_payment_path'] ?? null;

            if ($request->hasFile('proof_of_payment')) {
                $proofPath = $request->file('proof_of_payment')->store('payment-proofs', 'public');
            }
            $booking = Booking::create([
                'trip_date' => $tripDate,
                'route_id' => $validated['route_id'],
                'hiker_id' => $hiker->id,
                'status' => 'pending-payment',
                'payment_method' => $validated['payment_method'],
                'payment_due_at' => $this->paymentDueDate($validated['payment_method'], $tripDate),
                'amount' => $validated['amount'] ?? 0,
                'currency' => strtoupper($validated['currency'] ?? 'IDR'),
                'contact_phone' => $validated['contact_phone'],
                'proof_of_payment_path' => $proofPath,
                'notes' => $validated['notes'] ?? null,
                'participants_count' => $participantCount,
                'created_by' => $user->id,
                'created_via' => 'app',
            ]);

            $hasLeader = false;

            foreach ($participants as $index => $participant) {
                $isLeader = (bool) ($participant['is_leader'] ?? false);

                if (! $hasLeader && ($isLeader || $index === 0)) {
                    $isLeader = true;
                    $hasLeader = true;
                }

                $healthCertificatePath = $participant['health_certificate_path'] ?? null;

                if ($request->hasFile("participants.$index.health_certificate")) {
                    $file = $request->file("participants.$index.health_certificate");
                    $healthCertificatePath = $file->store('health-certificates', 'public');
                }

                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'name' => $participant['name'],
                    'gender' => $participant['gender'] ?? null,
                    'nationality' => $participant['nationality'] ?? null,
                    'origin_country' => $participant['origin_country'] ?? null,
                    'age_group' => $participant['age_group'] ?? null,
                    'occupation' => $participant['occupation'] ?? null,
                    'id_type' => $participant['id_type'] ?? null,
                    'id_number' => $participant['id_number'] ?? null,
                    'health_certificate_path' => $healthCertificatePath,
                    'is_leader' => $isLeader,
                    'meta' => [],
                ]);
            }

            $booking->refreshParticipantsCount();
            $booking->syncQuota();

            return $booking->load(['route', 'participants']);
        });

        return response()->json($booking, 201);
    }

    protected function paymentDueDate(string $method, Carbon $tripDate): ?Carbon
    {
        if ($method === 'cash') {
            // cash payments are due at the ranger office on the day before the trip
            return $tripDate->copy()->subDay()->setTime(17, 0, 0);
        }

        // transfer payments due within 3 days from booking creation
        return now()->addDays(3);
    }
}
