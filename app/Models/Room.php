<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


// app/Models/Room.php
class Room extends Model
{
    protected $fillable = ['name', 'status'];

    // Relación con ocupaciones
    public function rentals(): HasMany
    {
        return $this->hasMany(RoomRental::class);
    }

    // Última ocupación activa (opcional)
    public function currentRental()
    {
        return $this->hasOne(RoomRental::class)->where('end_time', '>', now());
    }
}

