<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\TripResource\Pages;
use App\Filament\Resources\TripResource\RelationManagers;
use App\Models\Trip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->disabled()
                    ->dehydrated(false)
                    ->hint('Generated when trip is saved')
                    ->visible(fn($livewire) => filled($livewire->record?->code ?? null))
                    ->default(fn($livewire) => $livewire->record?->code),
                Select::make('booking_id')
                    ->label('Booking')
                    ->relationship('booking', 'code')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('hiker_id')
                    ->label('Hiker')
                    ->relationship('hiker', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('route_id')
                    ->label('Route')
                    ->relationship('route', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time'),
                Select::make('status')
                    ->options([
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('ongoing')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['booking', 'hiker', 'route', 'lastLocation']))
            ->recordUrl(fn(Trip $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('booking.code')
                    ->label('Booking')
                    ->badge()
                    ->color('info')
                    ->url(fn(Trip $record) => $record->booking ? BookingResource::getUrl('view', ['record' => $record->booking]) : null, shouldOpenInNewTab: true)
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hiker.name')
                    ->label('Hiker')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('route.name')
                    ->label('Route')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('lastLocation.recorded_at')
                    ->label('Last Seen')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_location_coords')
                    ->label('Last Location')
                    ->getStateUsing(fn(Trip $record) => $record->lastLocation && $record->lastLocation->lat !== null && $record->lastLocation->lng !== null
                        ? sprintf('%.5f, %.5f', $record->lastLocation->lat, $record->lastLocation->lng)
                        : '-')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->url(fn(Trip $record) => $record->lastLocation && $record->lastLocation->lat !== null && $record->lastLocation->lng !== null
                        ? sprintf('https://www.google.com/maps?q=%F,%F', $record->lastLocation->lat, $record->lastLocation->lng)
                        : null, shouldOpenInNewTab: true)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $activeTrips = static::getModel()::query()
            ->where('status', 'ongoing')
            ->count();

        return $activeTrips > 0 ? (string) $activeTrips : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Trip')
                ->schema([
                    Grid::make()
                        ->schema([
                            TextEntry::make('code')->label('Code')->copyable(),
                            TextEntry::make('id')->label('Trip ID')->copyable(),
                            TextEntry::make('booking.code')
                                ->label('Booking Code')
                                ->placeholder('-')
                                ->url(fn(Trip $record) => $record->booking ? BookingResource::getUrl('view', ['record' => $record->booking]) : null, shouldOpenInNewTab: true),
                            TextEntry::make('status')->badge(),
                            TextEntry::make('start_time')->dateTime()->label('Start time'),
                            TextEntry::make('end_time')->dateTime()->label('End time'),
                        ])->columns(2),
                    Grid::make()
                        ->schema([
                            TextEntry::make('hiker.name')->label('Hiker'),
                            TextEntry::make('route.name')->label('Route'),
                        ])->columns(2),
                ]),
            Section::make('Latest Location')
                ->schema([
                    Grid::make()
                        ->schema([
                            TextEntry::make('lastLocation.recorded_at')
                                ->label('Recorded at')
                                ->dateTime()
                                ->placeholder('-'),
                            TextEntry::make('lastLocation.lat')
                                ->label('Latitude')
                                ->formatStateUsing(fn(?float $state) => $state !== null ? number_format($state, 5) : '-')
                                ->placeholder('-'),
                            TextEntry::make('lastLocation.lng')
                                ->label('Longitude')
                                ->formatStateUsing(fn(?float $state) => $state !== null ? number_format($state, 5) : '-')
                                ->placeholder('-'),
                            TextEntry::make('last_location_map')
                                ->label('Open in Maps')
                                ->state(fn(Trip $record) => $record->lastLocation && $record->lastLocation->lat !== null && $record->lastLocation->lng !== null
                                    ? 'Lihat di Google Maps'
                                    : '-')
                                ->url(
                                    fn(Trip $record) => $record->lastLocation && $record->lastLocation->lat !== null && $record->lastLocation->lng !== null
                                        ? sprintf('https://www.google.com/maps?q=%F,%F', $record->lastLocation->lat, $record->lastLocation->lng)
                                        : null,
                                    shouldOpenInNewTab: true
                                )
                                ->placeholder('-'),
                        ])
                        ->columns(2),
                ])
                ->collapsible(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'view' => Pages\ViewTrip::route('/{record}'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
