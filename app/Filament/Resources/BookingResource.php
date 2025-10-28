<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Booking Information')
                    ->schema([
                        TextInput::make('code')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($livewire) => filled($livewire->record?->code ?? null)),
                        Select::make('route_id')
                            ->relationship('route', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('hiker_id')
                            ->relationship('hiker', 'name')
                            ->searchable()
                            ->required(),
                        DatePicker::make('trip_date')
                            ->required()
                            ->minDate(now()->addDays(config('booking.min_days_before_trip', 30))),
                        Select::make('status')
                            ->options([
                                'pending-payment' => 'Pending Payment',
                                'awaiting-validation' => 'Awaiting Validation',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'expired' => 'Expired',
                            ])
                            ->required(),
                        Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'transfer' => 'Transfer',
                            ])
                            ->required(),
                        DateTimePicker::make('payment_due_at'),
                        DateTimePicker::make('paid_at'),
                        TextInput::make('amount')
                            ->numeric()
                            ->step('0.01')
                            ->default(0),
                        TextInput::make('currency')
                            ->maxLength(3)
                            ->default('IDR')
                            ->required(),
                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),
                Section::make('Participants')
                    ->schema([
                        Repeater::make('participants')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')->required()->maxLength(255),
                                Select::make('gender')
                                    ->label('Gender')
                                    ->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                        'other' => 'Other',
                                    ])
                                    ->nullable(),
                                TextInput::make('nationality')->maxLength(64),
                                TextInput::make('origin_country')->label('Origin Country')->maxLength(64),
                                TextInput::make('age_group')->label('Age Group')->maxLength(32),
                                TextInput::make('occupation')->maxLength(64),
                                TextInput::make('id_type')->label('ID Type')->maxLength(64),
                                TextInput::make('id_number')->label('ID Number')->maxLength(128),
                                FileUpload::make('health_certificate_path')
                                    ->label('Health Certificate')
                                    ->disk('public')
                                    ->directory('health-certificates')
                                    ->image()
                                    ->imagePreviewHeight('150')
                                    ->downloadable()
                                    ->openable()
                                    ->maxSize(5120)
                                    ->nullable(),
                                Forms\Components\Toggle::make('is_leader')->label('Leader')->inline(false),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->collapsed(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('trip_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('route.name')
                    ->label('Route')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hiker.name')
                    ->label('Hiker')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Participants')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending-payment',
                        'info' => 'awaiting-validation',
                        'success' => 'confirmed',
                        'danger' => ['cancelled', 'expired'],
                    ])
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency ?? 'IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_due_at')
                    ->dateTime()
                    ->label('Payment Due')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending-payment' => 'Pending Payment',
                        'awaiting-validation' => 'Awaiting Validation',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ]),
                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Booking')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('code')->label('Code')->copyable(),
                                TextEntry::make('status')->badge(),
                                TextEntry::make('payment_method')->label('Payment Method'),
                                TextEntry::make('trip_date')->date()->label('Trip Date'),
                                TextEntry::make('payment_due_at')->dateTime()->label('Payment Due'),
                                TextEntry::make('paid_at')->dateTime()->label('Paid At'),
                                TextEntry::make('amount')->money(fn ($record) => $record->currency ?? 'IDR'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('route.name')->label('Route'),
                                TextEntry::make('hiker.name')->label('Hiker'),
                            ]),
                        TextEntry::make('notes')->columnSpanFull(),
                    ]),
                InfolistSection::make('Participants')
                    ->schema([
                        TextEntry::make('participants.name')
                            ->badge()
                            ->listWithLineBreaks(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
