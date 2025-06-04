<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Room;
use App\Models\RoomRental;
use App\Models\Rent;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class RoomOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static string $view = 'filament.pages.room-overview';

    protected static ?string $navigationGroup = 'Dashboard';
    protected static ?int $navigationSort = -1;
    public static ?string $slug = '/';

    public $rooms;
    public $rents;
    public $selectedRoomId = null;

    public function mount()
    {
        $this->loadRooms();
        $this->rents = Rent::all();
    }

    public function loadRooms()
    {
        $this->rooms = Room::with('currentRental.rent')->get();
    }

    public function desocupar($roomId)
    {
        $room = Room::findOrFail($roomId);
        $room->update(['status' => 'disponible']);

        Notification::make()
            ->title("Habitación {$room->name} marcada como disponible")
            ->success()
            ->send();

        $this->loadRooms();
    }

    public function extender($rentalId, $minutos = 30)
    {
        $rental = RoomRental::findOrFail($rentalId);
        $rental->update(['end_time' => $rental->end_time->addMinutes($minutos)]);

        Notification::make()
            ->title("Tiempo extendido por {$minutos} minutos")
            ->success()
            ->send();

        $this->loadRooms();
    }

    public function seleccionarHabitacion($roomId)
    {
        $this->selectedRoomId = $roomId;
    }

    public function iniciarRenta($rentId)
    {
        $room = Room::with('currentRental')->findOrFail($this->selectedRoomId);

        // Verificar si ya tiene una renta activa (por seguridad, además del estado)
        if ($room->currentRental && $room->currentRental->end_time > now()) {
            Notification::make()
                ->title("La habitación {$room->name} ya tiene una renta activa.")
                ->danger()
                ->send();
            $this->selectedRoomId = null;
            return;
        }

        // Verificar estado disponible
        if ($room->status !== 'disponible') {
            Notification::make()
                ->title("La habitación {$room->name} no está disponible.")
                ->danger()
                ->send();
            $this->selectedRoomId = null;
            return;
        }

        $rent = Rent::findOrFail($rentId);
        $inicio = now();
        $fin = now()->addMinutes($rent->duration);

        RoomRental::create([
            'room_id' => $room->id,
            'rent_id' => $rent->id,
            'start_time' => $inicio,
            'end_time' => $fin,
        ]);

        $room->update(['status' => 'ocupada']);

        Notification::make()
            ->title("Renta iniciada para {$room->name}")
            ->success()
            ->send();

        $this->selectedRoomId = null;
        $this->loadRooms();
    }

    public function isRoomRentable($room): bool
        {
            if ($room->currentRental && $room->currentRental->end_time > now()) {
                return false;
            }

            return $room->status === 'disponible';
        }
}