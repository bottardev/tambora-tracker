<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class OutstandingPaymentsTable extends BaseWidget
{
    protected static ?string $heading = 'Outstanding Transfer Payments';

    protected function getTableQuery(): Builder
    {
        return Booking::query()
            ->with(['hiker'])
            ->where('payment_method', 'transfer')
            ->whereIn('status', ['pending-payment', 'awaiting-validation'])
            ->orderBy('payment_due_at');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('code')
                ->label('Booking')
                ->searchable()
                ->copyable(),
            TextColumn::make('hiker.name')
                ->label('Hiker'),
            TextColumn::make('payment_due_at')
                ->label('Due Date')
                ->dateTime()
                ->since(),
            TextColumn::make('amount')
                ->money(fn ($record) => $record->currency ?? 'IDR'),
            BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending-payment',
                    'info' => 'awaiting-validation',
                ])
                ->formatStateUsing(fn ($state) => match ($state) {
                    'pending-payment' => 'Pending Payment',
                    'awaiting-validation' => 'Awaiting Validation',
                    default => ucfirst($state),
                }),
        ];
    }
}
