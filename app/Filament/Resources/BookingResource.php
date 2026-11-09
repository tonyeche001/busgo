<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('booking_code')
                ->default(fn () => 'BK' . strtoupper(\Illuminate\Support\Str::random(8)))
                ->disabled()
                ->dehydrated()
                ->required(),
                    
                Forms\Components\Select::make('trip_id')
                ->label('Trip')
                ->relationship('trip', 'id', modifyQueryUsing:fn($query)=>$query->with('busRoute'))
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->busRoute->origin} to {$record->busRoute->destination} -" . $record->departure_at->format('M j, g:i A'))
                ->preload()
                ->live()
                ->afterStateUpdated(fn ($state, \Filament\Forms\Set $set) => $set('amount', \App\Models\Trip::find($state)?->fare))
                ->required(),
                
                Forms\Components\Select::make('user_id')
                ->label('passenger account(optional)')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(),
                    
                Forms\Components\TextInput::make('passenger_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('passenger_phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('seat_number')
                    ->required()
                    ->minValue(1)
                    ->numeric()
                    ->unique(
                        ignoreRecord:true,
                        modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule, \Filament\Forms\Get $get) => $rule->where('trip_id', $get('trip_id'))

                    )
                    ->validationMessages([
                        'unique' => 'This seat is already booked on this trip.'
                    ]),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->prefix('₦')
                    ->disabled()
                    ->dehydrated()
                    ->numeric(),
                Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'cancelled' => 'Cancelled'
                ])
                    ->required()
                    ->default('pending'),
                Forms\Components\DateTimePicker::make('paid_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Booking Code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('trip')
                    ->label('Trip')
                    ->formatStateUsing(fn ($record) => "{$record->trip?->busRoute?->origin} to {$record->trip?->busRoute?->destination}")
                    ->description(fn ($record) => $record->trip?->departure_at ? $record->trip->departure_at->format('M j, Y · g:i A') : '')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passenger_name')
                    ->label('Passenger')
                    ->searchable(),
                Tables\Columns\TextColumn::make('passenger_phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seat_number')
                    ->label('Seat')
                    ->badge()
                    ->formatStateUsing(fn ($state) => "Seat {$state}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state))
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('trip_date')
                    ->form([
                        Forms\Components\DatePicker::make('departure_date')
                            ->label('Trip Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['departure_date'] ?? null,
                            fn (Builder $query, $date): Builder => $query->whereHas('trip', fn ($q) => $q->whereDate('departure_at', $date)),
                        );
                    }),
            ])
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
