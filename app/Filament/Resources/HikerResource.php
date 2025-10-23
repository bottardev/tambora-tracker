<?php

namespace App\Filament\Resources;

use App\Models\Hiker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\HikerResource\Pages;


class HikerResource extends Resource
{
    protected static ?string $model = Hiker::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-user';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email()->required(),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\TextInput::make('emergency_contact'),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('phone'),
            Tables\Columns\TextColumn::make('trips_count')->counts('trips')->label('Trips'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHikers::route('/'),
            'create' => Pages\CreateHiker::route('/create'),
            'edit' => Pages\EditHiker::route('/{record}/edit'),
        ];
    }
}
