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

        $minDays = (int) config('booking.min_days_before_trip', 30);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->startOfDay()->addDays($minDays);

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->startOfDay()
            : $from->copy()->addDays(60);

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

        for ($cursor = $from->copy(); $cursor->lte($to); $cursor->addDay()) {
            $key = $cursor->format('Y-m-d');

            $quota = $quotas->get($key);
            $holiday = $holidays->get($key);
            $dayBookings = $bookings->get($key, collect());

            $capacity = $quota?->capacity ?? (int) config('booking.default_daily_capacity', 120);
            $status = $quota?->status ?? 'open';

            if ($holiday && $holiday->is_closed) {
                $status = 'closed';
            }

            $reserved = $dayBookings->sum('participants_count');
            $available = $status === 'open' ? max($capacity - $reserved, 0) : 0;

            $results[] = [
                'date' => $key,
                'capacity' => $capacity,
                'reserved' => $reserved,
                'available' => $available,
                'status' => $status,
                'is_holiday' => (bool) $holiday,
                'holiday_reason' => $holiday->reason ?? null,
            ];
        }

        return [
            'data' => $results,
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
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.name' => ['required', 'string', 'max:255'],
            'participants.*.gender' => ['nullable', 'string', 'max:16'],
            'participants.*.nationality' => ['nullable', 'string', 'max:64'],
            'participants.*.origin_country' => ['nullable', 'string', 'max:64'],
            'participants.*.age_group' => ['nullable', 'string', 'max:32'],
            'participants.*.occupation' => ['nullable', 'string', 'max:64'],
            'participants.*.id_type' => ['nullable', 'string', 'max:64'],
            'participants.*.id_number' => ['nullable', 'string', 'max:128'],
            'participants.*.health_certificate_path' => ['nullable', 'string'],
            'participants.*.is_leader' => ['nullable', 'boolean'],
        ]);

        $tripDate = Carbon::parse($validated['trip_date'])->startOfDay();
        $minDays = (int) config('booking.min_days_before_trip', 30);

        if ($tripDate->lt(now()->startOfDay()->addDays($minDays))) {
            return response()->json([
                'message' => "Booking must be made at least {$minDays} days before the trip date",
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

        $participants = $validated['participants'];
        $participantCount = count($participants);

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

        $booking = DB::transaction(function () use ($validated, $participants, $participantCount, $hiker, $user, $tripDate) {
            $booking = Booking::create([
                'trip_date' => $tripDate,
                'route_id' => $validated['route_id'],
                'hiker_id' => $hiker->id,
                'status' => 'pending-payment',
                'payment_method' => $validated['payment_method'],
                'payment_due_at' => $this->paymentDueDate($validated['payment_method'], $tripDate),
                'amount' => $validated['amount'] ?? 0,
                'currency' => strtoupper($validated['currency'] ?? 'IDR'),
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
                    'health_certificate_path' => $participant['health_certificate_path'] ?? null,
                    'is_leader' => $isLeader,
                    'meta' => [],
                ]);
            }

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
