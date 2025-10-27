<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['participants_count'] = isset($data['participants']) ? count($data['participants']) : 0;
        $data['created_by'] = auth()->id();
        $data['created_via'] = 'dashboard';
        $data['currency'] = strtoupper($data['currency'] ?? 'IDR');

        return $data;
    }
}
