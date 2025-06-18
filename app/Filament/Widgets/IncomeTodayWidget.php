<?php

namespace App\Filament\Widgets;

use App\Models\RoomRental; // Asegúrate de que este es tu modelo para las rentas de habitación
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeTodayWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $rentalsToday = RoomRental::with('rent') // Carga la relación 'rent'
            ->whereDate('created_at', Carbon::today())
            ->get();

        // Usar el operador nullsafe (?->) para evitar errores si $rental->rent es null
        $totalRevenue = $rentalsToday->sum(fn($rental) => $rental->rent?->cost ?? 0);

        return [
            Stat::make('Ingresos del Día', '$' . number_format((float)$totalRevenue, 2))
                ->description('Suma total de ingresos por rentas iniciadas hoy')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
        ];
    }
}
