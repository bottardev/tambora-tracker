<?php

namespace App\Filament\Resources\HikerResource\Pages;

use App\Filament\Resources\HikerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHikers extends ListRecords
{
    protected static string $resource = HikerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
