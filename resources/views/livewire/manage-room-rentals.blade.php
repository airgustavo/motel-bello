<div wire:poll.60s>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($rooms as $room)
            @php
                $timestampFinal = $room->status === 'ocupada' && $room->currentRental
                    ? $room->currentRental->end_time->timestamp
                    : null;
            @endphp

            <div
                wire:key="room-{{ $room->id }}"
                wire:click="seleccionarHabitacion({{ $room->id }})"
                class="relative block p-6 h-60 border rounded-lg shadow-xl cursor-pointer transform hover:scale-110 hover:opacity-90 transition duration-200 ease-in-out overflow-hidden"
                style="
                    @if($room->status === 'disponible') background-color: #50b25d;
                    @elseif($room->status === 'ocupada') background-color: #ff3c3c;
                    @elseif($room->status === 'fuera_de_servicio') background-color: #faff89;
                    @endif
                    border-color: #000000;
                    box-shadow:
                        1px 2px 2px hsl(220deg 60% 50% / 0.333),
                        2px 4px 4px hsl(220deg 60% 50% / 0.333),
                        3px 6px 6px hsl(220deg 60% 50% / 0.333);
                "
            >
                {{-- Imagen de fondo decorativa --}}
                <div class="absolute inset-x-0 top-0 flex justify-center opacity-10 pointer-events-none select-none mt-1">
                    @if ($room->status === 'disponible')
                        <img src="{{ asset('images/status/disponible.png') }}" class="max-w-[10px]" alt="Disponible" style="max-width: 80px; opacity: 0.2;">
                    @elseif ($room->status === 'ocupada')
                        <img src="{{ asset('images/status/ocupada.png') }}" class="max-w-[10px]" alt="Ocupada" style="max-width: 80px; opacity: 0.2;">
                    @elseif ($room->status === 'fuera_de_servicio')
                        <img src="{{ asset('images/status/fuera_servicio.png') }}" class="max-w-[10px]" alt="Fuera de Servicio" style="max-width: 80px; opacity: 0.2;">
                    @endif
                </div>

                {{-- Contenido --}}
                <div class="relative z-10">
                    <div class="col-span-2 flex justify-between items-center">
                        <h2 class="text-3xl font-bold" style="color: black">{{ $room->name }}</h2>
                        <p class="text-m capitalize" style="color: black">{{ $room->status }}</p>
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
                            {{-- Cuenta regresiva con Alpine --}}
                            <div 
                                x-data="cuentaRegresiva({{ $timestampFinal }})" 
                                x-init="iniciar()"
                                class="text-xs mt-1 font-semibold"
                            >
                                Tiempo Restante:
                                <span :class="minutosTotales <= 10 ? 'text-red-600' : ''">
                                    <span x-text="horas"></span> h 
                                    <span x-text="minutos"></span> min 
                                    <span x-text="segundos"></span> s
                                </span>
                            </div>

                            <x-filament::button
                                icon="heroicon-m-arrow-right-start-on-rectangle"
                                wire:click.stop="requestDesocupar({{ $room->id }})"
                                tooltip="Clic para desocupar la habitación"
                                color="warning"
                                size="sm"
                            >
                                Desocupar
                            </x-filament::button>
                        </div>
                    @elseif ($room->status === 'disponible')
                        <div class="col-span-2 flex justify-center items-center" style="margin-top: 2.6rem;">
                            <p class="text-m" style="color: black">Esta habitación se puede ocupar</p>
                        </div>                        
                    @elseif ($room->status === 'fuera_de_servicio')
                        <div class="col-span-2 flex justify-center items-center" style="margin-top: 2.6rem;">
                            <p class="text-m" style="color: black">Esta habitación esta en mantenimiento</p>
                        </div>
                    @endif
                </div>
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
                    ¿Estás seguro de que deseas desocupar la habitación <strong>{{ $roomNameToDesocupar ?? 'seleccionada' }}</strong>, una vez desocupada no podrá retomar el tiempo?
                </p>
                <div class="flex justify-between mt-8">
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

{{-- Script Alpine.js para cuenta regresiva --}}
<script>
    function cuentaRegresiva(timestampFinal) {
        return {
            tiempoRestante: timestampFinal - Math.floor(Date.now() / 1000),
            horas: 0,
            minutos: 0,
            segundos: 0,
            minutosTotales: 0,

            iniciar() {
                this.actualizar();
                setInterval(() => this.actualizar(), 1000);
            },

            actualizar() {
                this.tiempoRestante = timestampFinal - Math.floor(Date.now() / 1000);

                if (this.tiempoRestante <= 0) {
                    this.horas = this.minutos = this.segundos = 0;
                    this.minutosTotales = 0;
                    return;
                }

                this.minutosTotales = Math.floor(this.tiempoRestante / 60);
                this.horas = Math.floor(this.minutosTotales / 60);
                this.minutos = this.minutosTotales % 60;
                this.segundos = this.tiempoRestante % 60;
            }
        };
    }
</script>
