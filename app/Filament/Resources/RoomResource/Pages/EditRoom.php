<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use App\Models\Room;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;
    protected static ?string $title = 'Editar Habitación';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar Habitación')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->successNotificationTitle('Habitación eliminada correctamente')
                ->disabled(fn (Room $record): bool => $record->status !== 'disponible')
                ->tooltip(function (Room $record): ?string {
                    if ($record->status !== 'disponible') {
                        return 'Para eliminar, la habitación debe estar "disponible". Estado actual: ' . ucfirst(str_replace('_', ' ', $record->status)) . '.';
                    }
                    return null;
                }),
        ];
    }
}
