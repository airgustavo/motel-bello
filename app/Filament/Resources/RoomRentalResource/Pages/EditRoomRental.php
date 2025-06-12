<?php

namespace App\Filament\Resources\RoomRentalResource\Pages;

use App\Filament\Resources\RoomRentalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomRental extends EditRecord
{
    protected static string $resource = RoomRentalResource::class;
    protected static ?string $title = 'Editar Renta de Habitación';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar Renta')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->tooltip('Eliminar esta renta de habitación'),
        ];
    }
}
