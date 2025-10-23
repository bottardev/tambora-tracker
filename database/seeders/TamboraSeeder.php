<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class TamboraSeeder extends Seeder
{
    public function run(): void
    {
        $routeId = (string) Str::uuid();

        $checkpoints = [
            ['POS 1', -8.247100, 118.000200, 100, 1],
            ['POS 2', -8.242500, 118.007800, 100, 2],
            ['POS 3', -8.236900, 118.013400, 120, 3],
            ['POS 4', -8.231400, 118.019900, 120, 4],
        ];

        $lineStringWkt = 'LINESTRING(' . collect($checkpoints)
            ->map(fn ($cp) => sprintf('%f %f', $cp[2], $cp[1])) // lng lat
            ->implode(', ') . ')';

        DB::statement(
            'INSERT INTO routes (id, name, description, path, total_distance_km, created_at, updated_at)
VALUES (?, ?, ?, ST_GeomFromText(?, 4326), ?, NOW(), NOW())',
            [$routeId, 'Tambora Jalur Pancasila', 'Jalur umum via Desa Pancasila', $lineStringWkt, 18.5]
        );

        foreach ($checkpoints as [$name, $lat, $lng, $radius, $order]) {
            $id = (string) Str::uuid();
            $pointWkt = sprintf('POINT(%f %f)', $lng, $lat);

            DB::statement(
                "INSERT INTO checkpoints (id, route_id, name, order_no, location, radius_m, created_at, updated_at)
VALUES (?, ?, ?, ?, ST_GeomFromText(?, 4326), ?, NOW(), NOW())",
                [$id, $routeId, $name, $order, $pointWkt, $radius]
            );
        }
    }
}
