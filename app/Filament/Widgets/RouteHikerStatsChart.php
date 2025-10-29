<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Route;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RouteHikerStatsChart extends ChartWidget
{
    protected static ?string $heading = 'Trips & Hikers per Route';

    protected function getData(): array
    {
        Booking::expireOverdue();

        $selected = $this->filter ?? Carbon::now()->format('Y-m');
        $period = Carbon::createFromFormat('Y-m', $selected);
        $start = $period->copy()->startOfMonth();
        $end = $period->copy()->endOfMonth();

        $stats = Booking::selectRaw('route_id, COUNT(*) AS trip_count, SUM(participants_count) AS hikers')
            ->whereIn('status', ['confirmed', 'finished', 'ongoing'])
            ->whereBetween('trip_date', [$start, $end])
            ->groupBy('route_id')
            ->get()
            ->keyBy('route_id');

        $routes = Route::query()
            ->whereIn('id', $stats->keys())
            ->pluck('name', 'id');

        $labels = [];
        $tripCounts = [];
        $hikerCounts = [];

        foreach ($stats as $routeId => $row) {
            $labels[] = $routes[$routeId] ?? 'Unknown';
            $tripCounts[] = (int) $row->trip_count;
            $hikerCounts[] = (int) ($row->hikers ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Trips',
                    'data' => $tripCounts,
                    'backgroundColor' => '#0ea5e9',
                ],
                [
                    'label' => 'Hikers',
                    'data' => $hikerCounts,
                    'backgroundColor' => '#22c55e',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): array
    {
        $months = [];
        $current = Carbon::now()->startOfMonth();

        for ($i = 0; $i < 6; $i++) {
            $label = $current->copy()->subMonths($i);
            $months[$label->format('Y-m')] = $label->translatedFormat('F Y');
        }

        return $months;
    }

    public function getHeading(): string
    {
        $filter = $this->filter ?? Carbon::now()->format('Y-m');
        $label = Carbon::createFromFormat('Y-m', $filter)->translatedFormat('F Y');
        return "Trips & Hikers per Route ({$label})";
    }
}
