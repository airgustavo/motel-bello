<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Editar Usuario';
    protected static ?string $navigationIcon = 'heroicon-o-user-edit'; 


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar Usuario')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->modalHeading('Eliminar Usuario')
                ->modalButton('Eliminar')
                ->successNotificationTitle('Usuario eliminado exitosamente'),
        ];
    }
}
