<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
     protected static ?string $title = 'Usuarios';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Usuario')
                ->icon('heroicon-o-plus')
                ->color('info')
                ->modalHeading('Crear nuevo usuario')
                ->modalButton('Crear Usuario')
                ->successNotificationTitle('Usuario creado exitosamente'),
        ];
    }
}
