<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBookings extends BaseWidget
{
    protected static ?string $heading = 'Recent Bookings';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::with(['trip.busRoute', 'trip.bus'])->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Booking Code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('passenger_name')
                    ->label('Passenger')
                    ->description(fn ($record) => $record->passenger_phone),

                Tables\Columns\TextColumn::make('trip')
                    ->label('Route & Departure')
                    ->formatStateUsing(fn ($record) => "{$record->trip?->busRoute?->origin} → {$record->trip?->busRoute?->destination}")
                    ->description(fn ($record) => $record->trip?->departure_at ? $record->trip->departure_at->format('M j, Y · g:i A') : ''),

                Tables\Columns\TextColumn::make('seat_number')
                    ->label('Seat')
                    ->badge()
                    ->formatStateUsing(fn ($state) => "Seat {$state}"),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Fare')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Booked')
                    ->since(),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5);
    }
}

