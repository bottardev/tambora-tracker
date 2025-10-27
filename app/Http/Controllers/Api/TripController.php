<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Trip, Hiker, Route, Booking};
use Illuminate\Support\Str;


class TripController extends Controller
{
    public function store(Request $r)
    {
        $user = $r->user();
        $hiker = $user->loadMissing('hiker')->hiker;

        if (! $hiker) {
            return response()->json(['message' => 'User is not linked to a hiker profile'], 422);
        }

        $data = $r->validate([
            'booking_code' => ['required', 'string'],
            'start_time' => ['nullable', 'date'],
        ]);

        $booking = Booking::where('code', $data['booking_code'])->first();

        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->hiker_id !== $hiker->id) {
            return response()->json(['message' => 'This booking does not belong to the authenticated hiker.'], 403);
        }

        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Booking must be confirmed before starting a trip.'], 422);
        }

        if (! now()->isSameDay($booking->trip_date)) {
            return response()->json(['message' => 'Trip can only be started on the booked date.'], 422);
        }

        if ($booking->trip) {
            return response()->json(['message' => 'Trip has already been started for this booking.'], 422);
        }

        $startTime = isset($data['start_time']) ? Carbon::parse($data['start_time']) : now();

        $trip = DB::transaction(function () use ($booking, $hiker, $startTime) {
            return Trip::create([
                'booking_id' => $booking->id,
                'code' => Trip::generateUniqueCode(),
                'hiker_id' => $hiker->id,
                'route_id' => $booking->route_id,
                'start_time' => $startTime,
                'status' => 'ongoing',
            ]);
        });

        return $trip->load(['route', 'booking']);
    }
    public function show(Trip $trip)
    {
        return $trip->load('hiker', 'route');
    }
    public function update(Request $r, Trip $trip)
    {
        $trip->update($r->validate(['status' => 'in:ongoing,completed,cancelled', 'end_time' => 'nullable|date']));
        return $trip;
    }
}
