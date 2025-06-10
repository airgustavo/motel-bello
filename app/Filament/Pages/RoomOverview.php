<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RoomOverview extends Page
{
    protected static ?string $title = 'Asignar Habitaciones';
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.room-overview';
}