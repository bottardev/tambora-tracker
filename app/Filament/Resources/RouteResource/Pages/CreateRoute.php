<?php

namespace App\Filament\Resources\RouteResource\Pages;

use App\Filament\Resources\RouteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoute extends CreateRecord
{
    protected static string $resource = RouteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $path = $data['path_wkt'] ?? null;

        $data['path'] = filled($path) ? $path : null;

        unset($data['path_wkt']);

        return $data;
    }
}
