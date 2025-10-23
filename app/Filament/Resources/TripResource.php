<?php

namespace App\Filament\Resources;

use App\Models\Trip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\TripResource\Pages;


class TripResource extends Resource
{
    protected static ?string $model = Trip::class;
    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationIcon = 'heroicon-o-map';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('hiker_id')->relationship('hiker', 'name')->required(),
            Forms\Components\Select::make('route_id')->relationship('route', 'name')->required(),
            Forms\Components\DateTimePicker::make('start_time')->required(),
            Forms\Components\DateTimePicker::make('end_time'),
            Forms\Components\Select::make('status')->options([
                'draft' => 'Draft',
                'ongoing' => 'Ongoing',
                'paused' => 'Paused',
                'finished' => 'Finished',
                'canceled' => 'Canceled'
            ])->required(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('hiker.name')->label('Hiker')->searchable(),
            Tables\Columns\TextColumn::make('route.name')->label('Route'),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'secondary' => 'draft',
                'success' => 'ongoing',
                'warning' => 'paused',
                'primary' => 'finished',
                'danger' => 'canceled'
            ]),
            Tables\Columns\TextColumn::make('start_time')->dateTime(),
            Tables\Columns\TextColumn::make('last_location')->label('Last Lat,Lng')->getStateUsing(function (Trip $record) {
                $loc = $record->locations()->latest('ts')->first();
                if (!$loc) return '-';
                $p = data_get($loc, 'point'); // tampilkan raw WKT jika pakai spatial package
                return $p ? 'point' : '-';
            }),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
