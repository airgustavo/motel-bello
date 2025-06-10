<?php

namespace App\Filament\Resources\RentResource\Pages;

use App\Filament\Resources\RentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRents extends ListRecords
{
    protected static string $resource = RentResource::class;
    protected static ?string $title = 'Costo de habitaciones';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Costo')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->modalHeading('Crear nuevo costo de habitación')
                ->modalButton('Crear Costo')
                ->successNotificationTitle('Costo de habitación creado exitosamente'),
        ];
    }
}
