<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LiveTripsMap;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            LiveTripsMap::class,
        ];
    }
}
