<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\RoomsRentalsTodayWidget; // Importa tu widget
use App\Filament\Widgets\IncomeTodayWidget;       // Importa tu otro widget

class RoomOverview extends Page
{
    protected static ?string $title = 'Asignar Habitaciones';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.room-overview';

    public function getHeaderWidgetsColumns(): int | array
{
    return 3;
}

     protected function getFooterWidgets(): array
     {
         return [
             RoomsRentalsTodayWidget::class,
             IncomeTodayWidget::class,
         ];
     }
     
}