<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Models\Holiday;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')->required(),
                Select::make('route_id')
                    ->relationship('route', 'name')
                    ->searchable()
                    ->label('Specific Route')
                    ->nullable(),
                TextInput::make('reason')->maxLength(255),
                Toggle::make('is_closed')->default(true)->label('Closed for bookings'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('route.name')
                    ->label('Route')
                    ->sortable()
                    ->placeholder('All Routes'),
                Tables\Columns\TextColumn::make('reason')
                    ->wrap(),
                Tables\Columns\IconColumn::make('is_closed')
                    ->boolean()
                    ->label('Closed'),
            ])
            ->filters([
                TernaryFilter::make('is_closed')
                    ->label('Closed')
                    ->trueLabel('Only closed dates')
                    ->falseLabel('Only open dates'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHolidays::route('/'),
        ];
    }
}
