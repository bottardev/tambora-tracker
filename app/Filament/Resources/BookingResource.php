<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Holiday;
use Illuminate\Support\Carbon;
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
use Filament\Infolists\Components\RepeatableEntry;
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
                            ->reactive()
                            ->required(),
                        Select::make('hiker_id')
                            ->relationship('hiker', 'name')
                            ->searchable()
                            ->required(),
                        DatePicker::make('trip_date')
                            ->required()
                            ->minDate(now()->addDays(config('booking.min_days_before_trip', 3)))
                            ->maxDate(now()->addDays(config('booking.max_days_before_trip', 30)))
                            ->disabledDates(function (callable $get) {
                                $routeId = $get('route_id');

                                $query = Holiday::query()->where('is_closed', true);

                                $query->where(function ($q) use ($routeId) {
                                    $q->whereNull('route_id');

                                    if ($routeId) {
                                        $q->orWhere('route_id', $routeId);
                                    }
                                });

                                return $query
                                    ->pluck('date')
                                    ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
                                    ->all();
                            })
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if (! $value) {
                                        return;
                                    }

                                    $routeId = $get('route_id');
                                    $date = Carbon::parse($value)->startOfDay();

                                    $holidayExists = Holiday::query()
                                        ->whereDate('date', $date)
                                        ->where('is_closed', true)
                                        ->where(function ($q) use ($routeId) {
                                            if ($routeId) {
                                                $q->whereNull('route_id')->orWhere('route_id', $routeId);
                                            } else {
                                                $q->whereNull('route_id');
                                            }
                                        })
                                        ->exists();

                                    if ($holidayExists) {
                                        $fail('Tanggal ini ditandai sebagai hari libur.');
                                    }
                                };
                            }),
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
                        DateTimePicker::make('payment_due_at')
                            ->required(),
                        DateTimePicker::make('paid_at'),
                        TextInput::make('amount')
                            ->numeric()
                            ->step('0.01')
                            ->default(0)
                            ->required(),
                        FileUpload::make('proof_of_payment_path')
                            ->label('Proof of Payment')
                            ->disk('public')
                            ->directory('payment-proofs')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(5120)
                            ->required(fn (callable $get) => $get('payment_method') === 'transfer')
                            ->helperText('Upload proof for bank transfers.')
                            ->openable()
                            ->downloadable(),
                        TextInput::make('currency')
                            ->maxLength(3)
                            ->default('IDR')
                            ->required(),
                        TextInput::make('contact_phone')
                            ->label('Contact Phone')
                            ->tel()
                            ->maxLength(32)
                            ->required(),
                        TextInput::make('duration_days')
                            ->label('Duration (days)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(3)
                            ->required(),
                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),
                Section::make('Participants')
                    ->schema([
                        Repeater::make('participants')
                            ->relationship()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $leaders = collect($state)->filter(fn ($item) => (bool) ($item['is_leader'] ?? false));
                                if ($leaders->count() === 0 && count($state)) {
                                    $set('participants.0.is_leader', true);
                                }
                            })
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
                                TextInput::make('nationality')->required()->maxLength(64),
                                TextInput::make('origin_country')->label('Origin Country')->required()->maxLength(64),
                                TextInput::make('age_group')
                                    ->label('Age')
                                    ->numeric()
                                    ->minValue(17)
                                    ->maxValue(70)
                                    ->required(),
                                TextInput::make('occupation')->required()->maxLength(64),
                                Select::make('id_type')
                                    ->label('ID Type')
                                    ->options([
                                        'KTP' => 'KTP',
                                        'SIM' => 'SIM',
                                        'NPWP' => 'NPWP',
                                    ])
                                    ->required(),
                                TextInput::make('id_number')->label('ID Number')->required()->maxLength(128),
                                FileUpload::make('health_certificate_path')
                                    ->label('Health Certificate')
                                    ->disk('public')
                                    ->directory('health-certificates')
                                    ->acceptedFileTypes([
                                        'image/jpeg',
                                        'image/png',
                                        'application/pdf',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    ])
                                    ->imagePreviewHeight('150')
                                    ->previewable()
                                    ->downloadable()
                                    ->openable()
                                    ->maxSize(5120)
                                    ->nullable(),
                                Forms\Components\Toggle::make('is_leader')
                                    ->label('Leader')
                                    ->inline(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set, $component) {
                                        if ($state) {
                                            $currentPath = $component->getStatePath();
                                            $participants = $get('participants');
                                            foreach ($participants as $index => $participant) {
                                                $path = "participants.$index.is_leader";
                                                if ($path !== $currentPath && ($participant['is_leader'] ?? false)) {
                                                    $set($path, false);
                                                }
                                            }
                                        }
                                    }),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if (! is_array($value) || ! count($value)) {
                                        return;
                                    }

                                    $leaderCount = collect($value)->where('is_leader', true)->count();
                                    if ($leaderCount !== 1) {
                                        $fail('Harus ada tepat satu peserta sebagai leader.');
                                    }
                                };
                            })
                            ->collapsed(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        Booking::expireOverdue();

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
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending-payment' => 'Pending Payment',
                        'awaiting-validation' => 'Awaiting Validation',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                        default => ucfirst($state),
                    })
                    ->colors([
                        'warning' => 'pending-payment',
                        'info' => 'awaiting-validation',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'gray' => 'expired',
                    ])
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency ?? 'IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('Phone')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Duration')
                    ->suffix(' hari')
                    ->sortable(),
                Tables\Columns\IconColumn::make('proof_of_payment_path')
                    ->label('Proof')
                    ->boolean()
                    ->tooltip(fn ($record) => $record->proof_of_payment_path ? 'Download proof' : 'No proof uploaded')
                    ->url(fn ($record) => $record->proof_of_payment_url, shouldOpenInNewTab: true),
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
                                TextEntry::make('duration_days')->label('Duration (days)'),
                                TextEntry::make('contact_phone')->label('Contact Phone'),
                                TextEntry::make('proof_of_payment_url')
                                    ->label('Proof of Payment')
                                    ->url(fn ($state) => $state)
                                    ->openUrlInNewTab()
                                    ->placeholder('-')
                                    ->formatStateUsing(fn ($state) => $state ? 'Download' : '-'),
                            ]),
                        TextEntry::make('notes')->columnSpanFull(),
                    ]),
                InfolistSection::make('Participants')
                    ->schema([
                        TextEntry::make('participants_count')
                            ->label('Total Participants')
                            ->weight('bold'),
                        RepeatableEntry::make('participants')
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                    'xl' => 3,
                                ])->schema([
                                    TextEntry::make('name')
                                        ->label('Name'),
                                    TextEntry::make('is_leader')
                                        ->label('Role')
                                        ->formatStateUsing(fn ($state) => $state ? 'Leader' : 'Member')
                                        ->badge()
                                        ->color(fn ($state) => $state ? 'success' : 'gray'),
                                    TextEntry::make('nationality')->label('Nationality'),
                                    TextEntry::make('origin_country')->label('Origin Country'),
                                    TextEntry::make('age_group')->label('Age'),
                                    TextEntry::make('occupation')->label('Occupation'),
                                    TextEntry::make('id_type')->label('ID Type'),
                                    TextEntry::make('id_number')->label('ID Number'),
                                    TextEntry::make('health_certificate_url')
                                        ->label('Health Certificate')
                                        ->url(fn ($state) => $state)
                                        ->placeholder('-')
                                        ->openUrlInNewTab()
                                        ->formatStateUsing(fn ($state) => $state ? 'Download' : '-')
                                        ->columnSpanFull(),
                                ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
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
