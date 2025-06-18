<?php

namespace App\Filament\Resources\RoomRentalResource\Pages;

use App\Filament\Resources\RoomRentalResource;
use App\Models\RoomRental; // Necesario para obtener información del modelo
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\FilteredIncomeWidget;

class ListRoomRentals extends ListRecords
{
    protected static string $resource = RoomRentalResource::class;

    // Se llama después de que el componente se monta y las propiedades se hidratan.
    public function booted(): void
    {
        $this->updateSumAndDispatchEvent();
    }

    // Estos métodos se llaman cuando las propiedades correspondientes de la tabla se actualizan
    public function updatedTableFilters(): void
    {
        $this->updateSumAndDispatchEvent();
    }

    public function updatedTableSearchQuery(): void
    {
        $this->updateSumAndDispatchEvent();
    }

    public function updatedTableSortColumn(): void
    {
        $this->updateSumAndDispatchEvent();
    }

    public function updatedTableSortDirection(): void
    {
        $this->updateSumAndDispatchEvent();
    }

    public function updatedTableRecordsPerPage(): void
    {
        $this->updateSumAndDispatchEvent();
    }

    protected function updateSumAndDispatchEvent(): void
    {
        try {
            $baseQuery = $this->getFilteredTableQuery();

            if (!$baseQuery) {
                // Si no se puede obtener la consulta (por ejemplo, durante la inicialización temprana), enviar 0.
                $this->dispatch('updateFilteredRevenue', newSum: 0.0);
                return;
            }

            // Clonar la consulta para no afectar la paginación de la tabla principal
            $sumQuery = clone $baseQuery;

            // Obtener el modelo y nombres de tabla/columnas para la relación
            /** @var RoomRental $roomRentalInstance */
            $roomRentalInstance = static::getResource()::getModel();
            $roomRentalTable = (new $roomRentalInstance)->getTable();

            /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $rentRelation */
            $rentRelation = (new $roomRentalInstance)->rent(); // Asume que RoomRental tiene un método rent()
            $rentModelTable = $rentRelation->getRelated()->getTable(); // Tabla 'rents'
            $foreignKeyOnRoomRentalTable = $rentRelation->getForeignKeyName(); // e.g., 'rent_id' en 'room_rentals'
            $ownerKeyOnRentTable = $rentRelation->getOwnerKeyName(); // e.g., 'id' en 'rents'
            $costColumnOnRentTable = 'cost'; // Columna de costo en la tabla 'rents'

            $totalSum = $sumQuery
                ->join($rentModelTable, "{$roomRentalTable}.{$foreignKeyOnRoomRentalTable}", '=', "{$rentModelTable}.{$ownerKeyOnRentTable}")
                ->sum("{$rentModelTable}.{$costColumnOnRentTable}");

            $this->dispatch('updateFilteredRevenue', newSum: (float)($totalSum ?? 0.0));
        } catch (\Throwable $e) {
            // En caso de cualquier error durante el cálculo, enviar 0 y registrar el error.
            \Illuminate\Support\Facades\Log::error('Error calculating filtered revenue: ' . $e->getMessage());
            $this->dispatch('updateFilteredRevenue', newSum: 0.0);
        }
    }

    protected function getFooterWidgets(): array
    {
        return [
            
            FilteredIncomeWidget::class,
        ];
    }
    
}
