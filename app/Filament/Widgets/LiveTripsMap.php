<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class LiveTripsMap extends Widget
{
    protected static string $view = 'filament.widgets.live-trips-map';

    /**
     * Refresh the widget automatically every 30 seconds.
     */
    protected static ?string $pollingInterval = '30s';

    protected function getViewData(): array
    {
        [$timestampColumn, $pointColumn] = $this->getLocationColumnNames();

        $trips = Trip::query()
            ->where('status', 'ongoing')
            ->with([
                'hiker:id,name',
                'route:id,name',
                'locations' => function ($query) use ($timestampColumn, $pointColumn) {
                    $query
                        ->orderByDesc($timestampColumn)
                        ->limit(1)
                        ->selectRaw(sprintf(
                            'id, trip_id, %1$s as recorded_at, ST_X(%2$s) as lng, ST_Y(%2$s) as lat',
                            $timestampColumn,
                            $pointColumn
                        ));
                },
            ])
            ->get()
            ->map(function (Trip $trip) {
                $location = $trip->locations->first();

                if (! $location || $location->lat === null || $location->lng === null) {
                    return null;
                }

                $lastSeen = $location->recorded_at
                    ? Carbon::parse($location->recorded_at)
                    : null;

                return [
                    'id' => $trip->id,
                    'hiker' => $trip->hiker?->name,
                    'route' => $trip->route?->name,
                    'status' => $trip->status,
                    'last_seen_at' => $lastSeen?->toIso8601ZuluString(),
                    'lat' => (float) $location->lat,
                    'lng' => (float) $location->lng,
                ];
            })
            ->filter()
            ->values();

        return [
            'trips' => $trips,
            'mapboxToken' => config('services.mapbox.token'),
        ];
    }

    public function getPollingInterval(): ?string
    {
        return static::$pollingInterval;
    }

    /**
     * Determine the column names for timestamp and point fields on the locations table.
     *
     * @return array{0: string, 1: string}
     */
    protected function getLocationColumnNames(): array
    {
        static $columns = null;

        if ($columns !== null) {
            return $columns;
        }

        if (! Schema::hasTable('locations')) {
            return $columns = ['recorded_at', 'location'];
        }

        $schemaColumns = Schema::getColumnListing('locations');

        $timestampColumn = in_array('recorded_at', $schemaColumns, true)
            ? 'recorded_at'
            : (in_array('ts', $schemaColumns, true) ? 'ts' : 'created_at');

        $pointColumn = in_array('location', $schemaColumns, true)
            ? 'location'
            : (in_array('point', $schemaColumns, true) ? 'point' : 'location');

        return $columns = [$timestampColumn, $pointColumn];
    }
}
