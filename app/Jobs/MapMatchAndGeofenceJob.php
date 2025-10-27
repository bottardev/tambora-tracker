<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\{Trip, Checkpoint, Event};


class MapMatchAndGeofenceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public string $tripId) {}


    public function handle(): void
    {
        $trip = Trip::with('route', 'route.checkpoints')->find($this->tripId);
        if (!$trip) return;


        // Tandai on_trail jika jarak ke path < threshold (misal 40m)
        if ($trip->route && $trip->route->path) {
            $threshold = 0.04; // ~40 meter (perkiraan), tergantung unit & SRID
            // Contoh pseudo (MySQL belum punya ST_Distance ke LINESTRING dengan SRID 4326 yang mudah),
            // bisa konversi ke planar projection atau gunakan perhitungan haversine custom per segmen.
            // Di MVP, lewati atau lakukan di device.
        }


        // Geofence POS: buat event CHECKIN_POS jika titik berada dalam radius dari checkpoint
        $checkpoints = $trip->route?->checkpoints ?? collect();
        foreach ($checkpoints as $cp) {
            // ambil lokasi terbaru X menit terakhir
        $lat = $this->getLat($cp->location);
        $lng = $this->getLng($cp->location);
        if ($lat === null || $lng === null) {
            continue;
        }
        $wkt = sprintf('POINT(%F %F)', $lng, $lat);
        $pts = DB::select(
            "SELECT id, recorded_at, ST_Distance_Sphere(location, ST_GeomFromText(?, 4326)) as dist
FROM locations WHERE trip_id=? AND recorded_at>= (NOW() - INTERVAL 2 HOUR)
ORDER BY recorded_at DESC LIMIT 50",
                [$wkt, $trip->id]
            );
            if (!$pts) continue;
            foreach ($pts as $pt) {
                if ($pt->dist <= ($cp->radius_m ?? 100)) {
                    Event::firstOrCreate([
                        'trip_id' => $trip->id,
                        'type' => 'CHECKIN_POS',
                        'checkpoint_id' => $cp->id,
                        'ts' => $pt->recorded_at,
                    ]);
                    break;
                }
            }
        }
    }


    private function getLat($point)
    {
        $r = DB::selectOne("SELECT ST_Y(?) as lat", [$point]);
        return $r->lat ?? null;
    }
    private function getLng($point)
    {
        $r = DB::selectOne("SELECT ST_X(?) as lng", [$point]);
        return $r->lng ?? null;
    }
}
