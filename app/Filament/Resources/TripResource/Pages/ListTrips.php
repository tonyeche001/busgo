<?php

namespace App\Filament\Resources\TripResource\Pages;

use App\Filament\Resources\TripResource;
use App\Models\Bus;
use App\Models\BusRoute;
use App\Models\Trip;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTrips extends ListRecords
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('bulk')
                ->label('Create trips in bulk')
                ->icon('heroicon-o-calendar-days')
                ->form([
                    Forms\Components\Select::make('bus_route_id')
                        ->label('Route')
                        ->options(fn () => BusRoute::where('is_active', true)->get()
                            ->mapWithKeys(fn ($r) => [$r->id => "{$r->origin} to {$r->destination}"]))
                        ->required(),
                    Forms\Components\Select::make('bus_id')
                        ->label('Bus')
                        ->options(fn () => Bus::where('is_active', true)->pluck('plate_number', 'id'))
                        ->required(),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start date')
                        ->minDate(now()->toDateString())
                        ->required(),
                    Forms\Components\TextInput::make('days')
                        ->label('Number of days')
                        ->numeric()->minValue(1)->maxValue(90)->default(30)
                        ->required(),
                    Forms\Components\TimePicker::make('departure_time')
                        ->label('Departure time')
                        ->seconds(false)
                        ->required(),
                    Forms\Components\TextInput::make('duration_hours')
                        ->label('Trip length in hours (optional)')
                        ->numeric(),
                    Forms\Components\TextInput::make('fare')
                        ->numeric()->prefix('₦')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $start = Carbon::parse($data['start_date']);
                    $time = Carbon::parse($data['departure_time']);
                    $created = 0;
                    $skipped = 0;

                    for ($i = 0; $i < (int) $data['days']; $i++) {
                        $departure = $start->copy()->addDays($i)->setTime($time->hour, $time->minute);

                        $exists = Trip::where('bus_id', $data['bus_id'])
                            ->where('departure_at', $departure)
                            ->exists();

                        if ($exists) {
                            $skipped++;
                            continue;
                        }

                        Trip::create([
                            'bus_route_id' => $data['bus_route_id'],
                            'bus_id' => $data['bus_id'],
                            'departure_at' => $departure,
                            'arrival_at' => $data['duration_hours']
                                ? $departure->copy()->addHours((int) $data['duration_hours'])
                                : null,
                            'fare' => $data['fare'],
                            'status' => 'scheduled',
                        ]);

                        $created++;
                    }

                    Notification::make()
                        ->title("{$created} trips created" . ($skipped ? ", {$skipped} skipped (already exist)" : ''))
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}