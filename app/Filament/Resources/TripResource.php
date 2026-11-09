<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripResource\Pages;
use App\Filament\Resources\TripResource\RelationManagers;
use App\Models\Trip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('bus_route_id')
                ->label('Route')
                ->relationship('busRoute', 'origin')
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->origin} to {$record->destination}")
                ->searchable(['origin', 'destination'])
                ->preload()
                ->required(),
                    
                Forms\Components\Select::make('bus_id')
                ->label('Bus')
                ->relationship('bus', 'plate_number')
                ->searchable()
                ->preload()
                ->required(),
                
                Forms\Components\DateTimePicker::make('departure_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('arrival_at'),
                Forms\Components\TextInput::make('fare')
                    ->required()
                    ->prefix('₦')
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('scheduled'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('busRoute.origin')
                ->label('Route')
                ->formatStateUsing(fn ($record) => "{$record->busRoute?->origin} to {$record->busRoute?->destination}")
                ->searchable()
                ->sortable()
                ->weight('bold'),
                Tables\Columns\TextColumn::make('bus.plate_number')
                ->label('Bus Plate')
                ->description(fn ($record) => $record->bus?->bus_type)
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('departure_at')
                    ->label('Departure')
                    ->dateTime('M j, Y · g:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_at')
                    ->label('Arrival')
                    ->dateTime('M j, Y · g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fare')
                    ->label('Fare')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state))
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('seats_booked')
                    ->label('Seats Booked / Capacity')
                    ->state(function (Trip $record): string {
                        $booked = $record->bookings()->where('status', '!=', 'cancelled')->count();
                        $capacity = $record->bus?->seat_capacity ?? 0;
                        return "{$booked} / {$capacity} seats";
                    })
                    ->badge()
                    ->color(function (Trip $record): string {
                        $booked = $record->bookings()->where('status', '!=', 'cancelled')->count();
                        $capacity = $record->bus?->seat_capacity ?? 0;
                        if ($capacity > 0 && $booked >= $capacity) {
                            return 'danger';
                        }
                        if ($booked > 0) {
                            return 'warning';
                        }
                        return 'success';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
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
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
