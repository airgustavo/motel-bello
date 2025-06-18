<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\RoomRental; // Asegúrate de que este es tu modelo para las rentas de habitación
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoomsRentalsTodayWidget extends BaseWidget
{ 
    protected function getStats(): array
    {
        $count = RoomRental::whereDate('created_at', Carbon::today())->count();

        return [
            Stat::make('Habitaciones Rentadas Hoy', $count)
                ->description('Total de habitaciones cuya renta inició hoy')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),
        ];
    }
}
