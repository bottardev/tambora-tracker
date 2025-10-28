<?php

namespace App\Filament\Widgets;

use App\Models\RouteDailyQuota;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class UpcomingCapacityTable extends BaseWidget
{
    protected static ?string $heading = 'Upcoming Capacity (14 days)';

    protected function getTableQuery(): Builder
    {
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(14);

        return RouteDailyQuota::query()
            ->with('route')
            ->whereBetween('date', [$today, $limitDate])
            ->orderBy('date')
            ->orderBy('route_id');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('date')
                ->date()
                ->label('Date')
                ->sortable(),
            TextColumn::make('route.name')
                ->label('Route')
                ->sortable(),
            TextColumn::make('capacity')
                ->label('Capacity')
                ->sortable(),
            TextColumn::make('booked')
                ->label('Booked')
                ->sortable()
                ->badge()
                ->color(fn ($record) => $record->available <= 0 ? 'danger' : 'info'),
            TextColumn::make('available')
                ->label('Available')
                ->badge()
                ->color(fn ($record) => $record->available <= 0 ? 'danger' : ($record->available < 5 ? 'warning' : 'success')), 
            TextColumn::make('status')
                ->badge()
                ->label('Status')
                ->color(fn ($state) => $state === 'closed' ? 'danger' : 'success'),
        ];
    }
}
