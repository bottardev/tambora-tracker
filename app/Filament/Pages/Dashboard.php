<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BookingStatsOverview;
use App\Filament\Widgets\LiveTripsMap;
use App\Filament\Widgets\OutstandingPaymentsTable;
use App\Filament\Widgets\PaymentMethodChart;
use App\Filament\Widgets\UpcomingCapacityTable;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            BookingStatsOverview::class,
            PaymentMethodChart::class,
            UpcomingCapacityTable::class,
            OutstandingPaymentsTable::class,
            LiveTripsMap::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'lg' => 2,
            'xl' => 3,
        ];
    }
}
