<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static ?string $title = 'Nuevo Usuario';
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';    
    protected static string $resource = UserResource::class;
}
