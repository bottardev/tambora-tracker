<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Trip, Event, Checkpoint};


class EventController extends Controller
{
    public function store(Request $r, Trip $trip)
    {
        $data = $r->validate([
            'type' => 'required|in:CHECKIN_POS,OFF_TRAIL,SOS,RETURNED,CUSTOM',
            'checkpoint_id' => 'nullable|uuid|exists:checkpoints,id',
            'ts' => 'required|date',
            'note' => 'nullable|string'
        ]);
        $data['trip_id'] = $trip->id;
        $ev = Event::create($data);


        if ($data['type'] === 'SOS') {
            // kirim notifikasi (opsional)
            // TelegramNotifier::send("SOS Trip {$trip->id} at {$data['ts']}");
        }
        return $ev;
    }
}
