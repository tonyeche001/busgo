<?php

namespace App\Filament\Resources\BusRouteResource\Pages;

use App\Filament\Resources\BusRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusRoute extends EditRecord
{
    protected static string $resource = BusRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
