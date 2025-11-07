<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingSettingResource\Pages;
use App\Models\BookingSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingSettingResource extends Resource
{
    protected static ?string $model = BookingSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Booking Settings';

    protected static ?string $modelLabel = 'Booking Setting';

    protected static ?string $pluralModelLabel = 'Booking Settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting Details')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Setting Key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Unique identifier for this setting (e.g., price_per_participant)'),
                            
                        Forms\Components\TextInput::make('label')
                            ->label('Display Label')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Human-readable name for this setting'),
                            
                        Forms\Components\Select::make('type')
                            ->label('Value Type')
                            ->options([
                                'string' => 'Text',
                                'integer' => 'Number (Integer)',
                                'float' => 'Number (Decimal)',
                                'boolean' => 'True/False',
                                'json' => 'JSON Data',
                            ])
                            ->required()
                            ->default('string')
                            ->reactive()
                            ->helperText('Choose the type of value this setting stores'),
                            
                        Forms\Components\Select::make('group')
                            ->label('Setting Group')
                            ->options([
                                'general' => 'General',
                                'pricing' => 'Pricing',
                                'payment' => 'Payment',
                                'contact' => 'Contact Information',
                            ])
                            ->required()
                            ->default('general')
                            ->helperText('Category for organizing settings'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Setting Value')
                    ->schema([
                        Forms\Components\TextInput::make('value')
                            ->label('Value')
                            ->required()
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['string', 'integer', 'float']))
                            ->helperText('Enter the setting value'),
                            
                        Forms\Components\Toggle::make('value')
                            ->label('Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'boolean')
                            ->helperText('Enable or disable this setting'),
                            
                        Forms\Components\Textarea::make('value')
                            ->label('JSON Value')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json')
                            ->rows(4)
                            ->helperText('Enter valid JSON data (e.g., {"key": "value"})'),
                    ]),
                    
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->helperText('Optional description of what this setting controls'),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Whether this setting is active and will be used'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Setting Name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->copyable()
                    ->size('sm')
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('value')
                    ->label('Current Value')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getState();
                    })
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->type === 'boolean') {
                            return $state ? 'Yes' : 'No';
                        }
                        if ($record->type === 'json') {
                            return 'JSON Data';
                        }
                        return $state;
                    }),
                    
                Tables\Columns\BadgeColumn::make('group')
                    ->label('Group')
                    ->colors([
                        'primary' => 'general',
                        'success' => 'pricing',
                        'warning' => 'payment',
                        'info' => 'contact',
                    ]),
                    
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'secondary' => 'string',
                        'primary' => 'integer',
                        'success' => 'boolean',
                        'warning' => 'json',
                    ]),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'pricing' => 'Pricing',
                        'payment' => 'Payment',
                        'contact' => 'Contact Information',
                    ]),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'string' => 'Text',
                        'integer' => 'Number (Integer)',
                        'boolean' => 'True/False',
                        'json' => 'JSON Data',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        // Clear cache after editing settings
                        BookingSetting::clearCache();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Clear cache after deleting settings
                            BookingSetting::clearCache();
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clearCache')
                    ->label('Clear Settings Cache')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function () {
                        BookingSetting::clearCache();
                    })
                    ->requiresConfirmation()
                    ->modalDescription('This will clear the cached booking settings and force them to reload from the database.')
                    ->successNotificationTitle('Settings cache cleared successfully!'),
            ])
            ->defaultSort('group')
            ->defaultGroup('group');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingSettings::route('/'),
            'create' => Pages\CreateBookingSetting::route('/create'),
            'edit' => Pages\EditBookingSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}
