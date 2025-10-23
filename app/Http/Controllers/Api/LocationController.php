<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Trip, Location, Checkpoint, Route as RouteModel};


class LocationController extends Controller
{
    public function storeBatch(Request $r, Trip $trip)
    {
        $payload = $r->validate([
            'items' => 'required|array|max:500',
            'items.*.ts' => 'required|date',
            'items.*.lat' => 'required|numeric',
            'items.*.lng' => 'required|numeric',
            'items.*.accuracy_m' => 'nullable|numeric',
            'items.*.battery_pct' => 'nullable|integer'
        ]);


        DB::beginTransaction();
        foreach ($payload['items'] as $it) {
            // Insert POINT dengan SRID 4326
            DB::statement(
                "INSERT INTO locations (trip_id, ts, point, accuracy_m, battery_pct, snapped, on_trail, created_at, updated_at)
VALUES (?, ?, ST_SRID(Point(?, ?), 4326), ?, ?, 0, 0, NOW(), NOW())",
                [$trip->id, $it['ts'], $it['lng'], $it['lat'], $it['accuracy_m'] ?? null, $it['battery_pct'] ?? null]
            );
        }
        DB::commit();


        // (Opsional) dispatch job untuk map-matching & geofence
        // MapMatchAndGeofenceJob::dispatch($trip->id);


        return response()->json(['status' => true]);
    }


    public function last(Trip $trip)
    {
        $loc = $trip->locations()->latest('ts')->first();
        if (!$loc) return ['data' => null];
        // Ambil WKT => lat,lng simple (butuh spatial package untuk decode lebih rapi)
        $pt = DB::selectOne("SELECT ST_X(point) as lng, ST_Y(point) as lat FROM locations WHERE id=?", [$loc->id]);
        return [
            'ts' => $loc->ts,
            'lat' => $pt->lat ?? null,
            'lng' => $pt->lng ?? null,
            'accuracy_m' => $loc->accuracy_m,
            'battery_pct' => $loc->battery_pct,
            'snapped' => $loc->snapped,
            'on_trail' => $loc->on_trail,
        ];
    }
}
