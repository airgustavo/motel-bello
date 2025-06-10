<div wire:poll.60s>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($rooms as $room)
            <div wire:key="room-{{ $room->id }}"
                wire:click="seleccionarHabitacion({{ $room->id }})"
                    class="block max-w-sm p-6 border rounded-lg shadow-xl" 
                    style="
                    @if($room->status === 'disponible') background-color: #50b25d; border-color: #000000; dark:border-color: #ffffff; hover:baground-color: #bfdbfe;
                        box-shadow:
                            1px 2px 2px hsl(220deg 60% 50% / 0.333),
                            2px 4px 4px hsl(220deg 60% 50% / 0.333),
                            3px 6px 6px hsl(220deg 60% 50% / 0.333);
                        @elseif($room->status === 'ocupada') background-color: #ff3c3c; border-color: #000000; hover:bg-blue-100;
                        box-shadow:
                            1px 2px 2px hsl(220deg 60% 50% / 0.333),
                            2px 4px 4px hsl(220deg 60% 50% / 0.333),
                            3px 6px 6px hsl(220deg 60% 50% / 0.333);
                        @elseif($room->status === 'fuera_de_servicio') background-color: #faff89; border-color: #000000; hover:bg-blue-100;
                        box-shadow:
                            1px 2px 2px hsl(220deg 60% 50% / 0.333),
                            2px 4px 4px hsl(220deg 60% 50% / 0.333),
                            3px 6px 6px hsl(220deg 60% 50% / 0.333); 
                    @endif">
                    <div class="col-span-2 flex justify-between items-center">
                        <h2 class="text-3xl font-bold">{{ $room->name }}</h2>
                        <p class="text-m capitalize">{{ $room->status }}</p>
                    </div>

                    @if ($room->status === 'ocupada' && $room->currentRental)
                        <div class="col-span-2 flex justify-between items-center">
                            <p class="text-sm mt-1">
                                Renta: {{ $room->currentRental->rent->name }}                               
                            </p>
                            <p class="text-sm mt-1">
                                Finaliza: {{ $room->currentRental->end_time->format('H:i') }} hrs.
                            </p>
                        </div>
                        <div class="col-span-2 flex justify-between items-center mt-3">
                            <p class="text-xs mt-1 font-semibold">
                                @php
                                    $minutosRestantes = now()->diffInMinutes($room->currentRental->end_time, false);
                                @endphp
                                Tiempo Restante: <span class="{{ $minutosRestantes <= 10 ? 'text-red-600' : '' }}">
                                    {{ floor($minutosRestantes / 60) }} h {{ $minutosRestantes % 60 }} min
                                </span>
                            </p>
                        
                            <x-filament::button
                                icon="heroicon-m-arrow-right-start-on-rectangle"
                                wire:click.stop="requestDesocupar({{ $room->id }})"
                                tooltip="Clic para desocupar la habitación"
                                color="warning"
                                size="sm">
                                Desocupar
                            </x-filament::button>
                        </div>
                    @endif
            </div>
        @endforeach
    </div>
    
    {{-- Modal con select --}}
    @if ($selectedRoomId)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-xl font-bold mb-4">Seleccionar tipo de renta</h3>

                <div class="mb-4">
                    <select wire:model.live="selectedRentId"
                        class="w-full border-gray-300 dark:bg-gray-800 rounded-md shadow-sm focus:ring focus:ring-primary-500">
                        <option value="">-- Selecciona una opción --</option>
                        @foreach ($rents as $rent)
                            <option value="{{ $rent->id }}">
                                {{ $rent->name }} - ${{ $rent->cost }} ({{ $rent->duration }} min)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-between">
                    <x-filament::button 
                        wire:click="iniciarRenta" 
                        color="primary" 
                        size="sm"
                        :disabled="!$selectedRentId">
                        Confirmar Renta
                    </x-filament::button>

                    <x-filament::button 
                        wire:click="$set('selectedRoomId', null)" 
                        color="danger" 
                        size="sm">
                        Cancelar
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Confirmación para Desocupar --}}
    @if ($showConfirmDesocuparModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Desocupar Habitación</h3>
                <p class="mb-6 text-gray-700 dark:text-white justify-center">
                    ¿Estás seguro de que deseas desocupar la habitación <strong>{{ $roomNameToDesocupar ?? 'seleccionada' }}</strong>, una vez desocupada no podra retomar el tiempo?
                </p>
                <div class="flex justify-between" style="margin-top: 2rem">
                    <x-filament::button 
                        wire:click="cancelDesocupar" 
                        color="danger">
                        Cancelar
                    </x-filament::button>
                    <x-filament::button 
                        wire:click="confirmActionDesocupar" 
                        color="primary">
                        Sí, Desocupar
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</div>
