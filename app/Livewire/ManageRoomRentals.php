<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room;
use App\Models\Rent;
use App\Models\RoomRental;
use Filament\Notifications\Notification;

class ManageRoomRentals extends Component
{
    public $rooms = [];
    public $rents = [];

    public ?int $selectedRoomId = null;
    public ?int $selectedRentId = null;

    public bool $showConfirmDesocuparModal = false;
    public ?int $roomIdToDesocupar = null;
    public ?string $roomNameToDesocupar = null;
    public array $lowTimeWarningSentForRoomRentalIds = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->rooms = Room::with(['currentRental.rent'])->get();
        $this->rents = Rent::all();
    }

    public function seleccionarHabitacion(int $roomId): void
    {
        $room = Room::with('currentRental')->find($roomId);
    
        if (!$room) {
            // Manejar caso de habitación no encontrada si es necesario
            return;
        }
    
        if ($room->status !== 'disponible') {
            return;
        }
        $this->selectedRoomId = $roomId;
        $this->selectedRentId = null;
    }

    public function iniciarRenta(): void
    {
        if (!$this->selectedRoomId || !$this->selectedRentId) {
            return;
        }
    
        $room = Room::find($this->selectedRoomId);
        $rent = Rent::find($this->selectedRentId);
    
        if (!$room || !$rent) {
            return;
        }
    
        if ($room->status !== 'disponible') {
            return;
        }
        RoomRental::create([
            'room_id' => $room->id,
            'rent_id' => $rent->id,
            'start_time' => now(),
            'end_time' => now()->addMinutes($rent->duration),
        ]);

        $room->update(['status' => 'ocupada']);    
    
        $this->reset(['selectedRoomId', 'selectedRentId']);
        $this->loadData();
    }

    // Método original que ejecuta la lógica de desocupación
    public function desocupar(int $roomId): void
    {
        $room = Room::with('currentRental')->find($roomId);
    
        if ($room) {
            if ($room->currentRental) {
                $room->currentRental->update(['end_time' => now()]);
            }
            $room->update(['status' => 'disponible']);
            $this->loadData();
        }
    }

    // Abre el modal de confirmación
    public function requestDesocupar(int $roomId): void
    {
        $room = Room::find($roomId);
        if ($room) {
            $this->roomIdToDesocupar = $roomId;
            $this->roomNameToDesocupar = $room->name;
            $this->showConfirmDesocuparModal = true;
        }
    }

    // Cierra el modal de confirmación
    public function cancelDesocupar(): void
    {
        $this->roomIdToDesocupar = null;
        $this->roomNameToDesocupar = null;
        $this->showConfirmDesocuparModal = false;
    }

    // Acción confirmada desde el modal
    public function confirmActionDesocupar(): void
    {
        if ($this->roomIdToDesocupar) {
            $this->desocupar($this->roomIdToDesocupar);
        }
        $this->cancelDesocupar(); // Cierra el modal después de la acción
    }

    public function render()
    {
        // Parte 1: Actualización automática de estado para rentas expiradas
        // Obtenemos las habitaciones marcadas como 'ocupada' y cargamos su 'currentRental'
        // La relación 'currentRental' en tu modelo Room ya debería filtrar por end_time > now()
        $roomsToUpdateStatus = Room::where('status', 'ocupada')->with('currentRental')->get();
        foreach ($roomsToUpdateStatus as $roomToUpdate) { // Usar un nombre de variable diferente para evitar colisiones
            if (is_null($roomToUpdate->currentRental)) {
                // Si está 'ocupada' pero no tiene una renta activa (porque currentRental es null),
                // significa que la renta ha expirado. La ponemos disponible.
                $roomToUpdate->update(['status' => 'disponible']);
            }
        }

        // Parte 2: Cargar datos frescos para la vista (esto incluye los estados actualizados)
        $this->loadData(); // Esto cargará $this->rooms con los datos más recientes

        // Parte 3: Verificar y enviar notificaciones de tiempo bajo
        $activeRentalIdsThisCycle = [];
        // Iterar sobre $this->rooms que fue cargado por loadData()
        foreach ($this->rooms as $roomForNotification) { // Usar un nombre de variable diferente
            if ($roomForNotification->status === 'ocupada' && $roomForNotification->currentRental) {
                $activeRentalIdsThisCycle[] = $roomForNotification->currentRental->id; // Guardar ID de renta activa
                $endTime = $roomForNotification->currentRental->end_time; // Asumimos que es una instancia Carbon
                $minutosRestantes = now()->diffInMinutes($endTime, false);
                $minutosRestantes = floor($minutosRestantes); // Redondear hacia abajo
                // Enviar notificación si quedan 10 minutos o menos
                if ($minutosRestantes <= 10 && $minutosRestantes >= 0) { // Tiempo bajo y aún no ha terminado completamente
                    if (!in_array($roomForNotification->currentRental->id, $this->lowTimeWarningSentForRoomRentalIds)) {
                        Notification::make()
                            ->title('Tiempo por terminar')
                            ->warning()
                            ->body("A la habitación {$roomForNotification->name} le quedan aproximadamente {$minutosRestantes} minuto(s).")
                            ->persistent() // El usuario debe descartarla manualmente
                            ->send();
                        $this->lowTimeWarningSentForRoomRentalIds[] = $roomForNotification->currentRental->id;
                    }
                }
            }
        }
        // Limpiar la lista de advertencias para rentas que ya no están activas
        $this->lowTimeWarningSentForRoomRentalIds = array_intersect($this->lowTimeWarningSentForRoomRentalIds, $activeRentalIdsThisCycle);

        return view('livewire.manage-room-rentals'); // Asegurarse de que esto se ejecute al final
    }
}
