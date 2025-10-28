<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Trip;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;

class BookingStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        Booking::expireOverdue();

        $now = Carbon::now();
        $currentStart = $now->copy()->startOfMonth();
        $currentEnd = $now->copy()->endOfMonth();

        $previousStart = $currentStart->copy()->subMonth();
        $previousEnd = $currentStart->copy()->subSecond();

        $currentBookings = Booking::whereBetween('trip_date', [$currentStart, $currentEnd])->count();
        $previousBookings = Booking::whereBetween('trip_date', [$previousStart, $previousEnd])->count();

        $change = null;
        if ($previousBookings > 0) {
            $change = (($currentBookings - $previousBookings) / $previousBookings) * 100;
        }

        $pendingPayment = Booking::where('status', 'pending-payment')->count();
        $awaitingValidation = Booking::where('status', 'awaiting-validation')->count();
        $activeTrips = Trip::where('status', 'ongoing')->count();

        return [
            Card::make('Bookings this month', number_format($currentBookings))
                ->description($change === null ? 'No previous month data' : number_format($change, 1) . '% vs last month')
                ->descriptionIcon($change !== null && $change < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->chart([$previousBookings, $currentBookings])
                ->color($change !== null && $change < 0 ? 'danger' : 'success'),
            Card::make('Awaiting Payment', number_format($pendingPayment))
                ->description('Pending payment bookings')
                ->icon('heroicon-m-credit-card')
                ->color('warning'),
            Card::make('Awaiting Validation', number_format($awaitingValidation))
                ->description('Need admin review')
                ->icon('heroicon-m-clock')
                ->color('info'),
            Card::make('Active Trips', number_format($activeTrips))
                ->description('Trips in progress')
                ->icon('heroicon-m-map')
                ->color('success'),
        ];
    }
}
