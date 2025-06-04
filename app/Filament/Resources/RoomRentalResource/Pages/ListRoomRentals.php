<?php

namespace App\Filament\Resources\RoomRentalResource\Pages;

use App\Filament\Resources\RoomRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomRentals extends ListRecords
{
    protected static string $resource = RoomRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
