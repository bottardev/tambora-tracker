<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Trip, Hiker, Route};
use Illuminate\Support\Str;


class TripController extends Controller
{
    public function store(Request $r)
    {
        $data = $r->validate([
            'hiker_id' => 'required|uuid|exists:hikers,id',
            'route_id' => 'required|uuid|exists:routes,id',
            'start_time' => 'required|date'
        ]);
        $data['status'] = 'ongoing';
        $trip = Trip::create($data);
        return $trip;
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
