<?php

namespace App\Filament\Resources\BookingSettingResource\Pages;

use App\Filament\Resources\BookingSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookingSetting extends EditRecord
{
    protected static string $resource = BookingSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
