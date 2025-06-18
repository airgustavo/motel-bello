<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Enums\IconPosition;

class FilteredIncomeWidget extends BaseWidget
{
    public float $totalFilteredRevenue = 0.0;

    protected static ?string $pollingInterval = null; // Desactivamos el polling si no es necesario, ya que se actualiza por evento.

    protected $listeners = ['updateFilteredRevenue' => 'updateRevenue'];
    

    public function updateRevenue(float $newSum): void
    {
        $this->totalFilteredRevenue = $newSum;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Ingresos (Filtrado)', '$' . number_format($this->totalFilteredRevenue, 2))
                ->descriptionIcon('heroicon-m-currency-dollar', IconPosition::Before)
                ->description('Suma de ingresos según los filtros aplicados.')                
                ->color('primary'), // Puedes elegir 'success', 'warning', 'danger', 'info', etc.
        ];
    }
}
