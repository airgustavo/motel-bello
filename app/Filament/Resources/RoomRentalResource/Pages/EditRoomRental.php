<?php

namespace App\Filament\Resources\RoomRentalResource\Pages;

use App\Filament\Resources\RoomRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomRental extends EditRecord
{
    protected static string $resource = RoomRentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
