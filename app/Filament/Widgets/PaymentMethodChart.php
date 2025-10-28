<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;

class PaymentMethodChart extends ChartWidget
{
    protected static ?string $heading = 'Payments by Method';

    protected function getData(): array
    {
        Booking::expireOverdue();

        $data = Booking::selectRaw('COALESCE(payment_method, "Unknown") as method, COUNT(*) as total')
            ->whereIn('status', ['pending-payment', 'awaiting-validation', 'confirmed'])
            ->groupBy('method')
            ->pluck('total', 'method');

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data->values(),
                    'backgroundColor' => [
                        '#0ea5e9',
                        '#8b5cf6',
                        '#f97316',
                        '#22c55e',
                    ],
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
