<x-filament::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($rooms as $room)
            <div wire:key="room-{{ $room->id }}" class="p-4 rounded-xl shadow-md text-white cursor-pointer"
                wire:click="seleccionarHabitacion({{ $room->id }})" @class([
                    'bg-green-600' => $room->status === 'disponible',
                    'bg-red-600' => $room->status === 'ocupada',
                    'bg-gray-500' => $room->status === 'fuera_de_servicio',
                ])>
                <h2 class="text-xl font-bold">{{ $room->name }}</h2>
                <p class="text-sm capitalize">Estado: {{ $room->status }}</p>

                @if ($room->status === 'ocupada' && $room->currentRental)
                    <p class="text-sm mt-1">
                        Renta: {{ $room->currentRental->rent->name }}<br>
                        Fin: {{ $room->currentRental->end_time->format('H:i') }}
                    </p>
                    @php
                        $minutosRestantes = now()->diffInMinutes($room->currentRental->end_time, false);
                    @endphp
                    <p class="text-xs mt-1 {{ $minutosRestantes <= 10 ? 'text-red-300 font-bold' : '' }}">
                        Quedan: {{ $minutosRestantes }} min
                    </p>

                    <div class="mt-2 space-x-2">
                        <x-filament::button wire:click.stop="$emit('confirmExtender', {{ $room->currentRental->id }})"
                            color="danger" size="sm">
                            ⏱ Extender +30min
                        </x-filament::button>

                        <x-filament::button wire:click.stop="desocupar({{ $room->id }})" color="success"
                            size="sm">
                            ✅ Desocupar
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Modal -->
    @if ($selectedRoomId)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-bold mb-4">Seleccionar tipo de renta</h3>
                <ul class="space-y-2">
                    @foreach ($rents as $rent)
                        <li>
                            <x-filament::button wire:click="iniciarRenta({{ $rent->id }})" color="primary"
                                size="sm" class="w-full justify-between">
                                <span>{{ $rent->name }} - ${{ $rent->cost }}</span>
                                <span class="text-xs">{{ $rent->duration }} min</span>
                            </x-filament::button>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 text-right">
                    <x-filament::button wire:click="$set('selectedRoomId', null)" color="danger" size="sm">
                        Cancelar
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            Livewire.on('confirmExtender', function(rentalId) {
                if (confirm('¿Extender el tiempo de esta habitación por 30 minutos más?')) {
                    Livewire.dispatch('extender', {
                        rentalId: rentalId
                    });
                }
            });
        </script>
    @endpush
</x-filament::page>
