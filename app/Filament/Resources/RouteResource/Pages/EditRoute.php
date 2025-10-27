<?php

namespace App\Filament\Resources\RouteResource\Pages;

use App\Filament\Resources\RouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoute extends EditRecord
{
    protected static string $resource = RouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $path = $data['path_wkt'] ?? null;

        $data['path'] = filled($path) ? $path : null;

        unset($data['path_wkt']);

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['path_wkt'] = $data['path'] ?? null;

        unset($data['path']);

        return $data;
    }
}
