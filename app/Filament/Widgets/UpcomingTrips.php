<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingTrips extends BaseWidget
{
    protected static ?string $heading = 'Upcoming Departures';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Trip::with(['busRoute', 'bus'])
                    ->withCount(['bookings' => fn ($q) => $q->where('status', '!=', 'cancelled')])
                    ->where('departure_at', '>=', now())
                    ->orderBy('departure_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('busRoute.origin')
                    ->label('Route')
                    ->formatStateUsing(fn ($record) => "{$record->busRoute?->origin} → {$record->busRoute?->destination}")
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('departure_at')
                    ->label('Departure Time')
                    ->dateTime('M j, Y · g:i A')
                    ->description(fn ($record) => $record->departure_at->diffForHumans()),

                Tables\Columns\TextColumn::make('bus.plate_number')
                    ->label('Bus Vehicle')
                    ->description(fn ($record) => $record->bus?->bus_type),

                Tables\Columns\TextColumn::make('seats_left')
                    ->label('Seats Left / Capacity')
                    ->state(function (Trip $record): string {
                        $booked = $record->bookings_count ?? $record->bookings()->where('status', '!=', 'cancelled')->count();
                        $capacity = $record->bus?->seat_capacity ?? 0;
                        $left = max(0, $capacity - $booked);
                        return "{$left} / {$capacity} seats left";
                    })
                    ->badge()
                    ->color(function (Trip $record): string {
                        $booked = $record->bookings_count ?? $record->bookings()->where('status', '!=', 'cancelled')->count();
                        $capacity = $record->bus?->seat_capacity ?? 0;
                        $left = max(0, $capacity - $booked);
                        if ($left === 0) {
                            return 'danger';
                        }
                        if ($left <= 3) {
                            return 'warning';
                        }
                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('fare')
                    ->label('Fare')
                    ->formatStateUsing(fn ($state) => '₦' . number_format($state))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5);
    }
}

