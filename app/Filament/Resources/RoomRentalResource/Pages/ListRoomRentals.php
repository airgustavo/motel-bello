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
            // Actions\CreateAction::make()
            //     ->label('Nueva Renta')
            //     ->icon('heroicon-o-plus')
            //     ->color('primary')
            //     ->tooltip('Registrar una nueva renta de habitación'),
        ];
    }
}
