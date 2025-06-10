<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = ['name', 'status'];

    public function rentals(): HasMany
    {
        return $this->hasMany(RoomRental::class);
    }

    // Última ocupación activa (relación uno a uno)
    public function currentRental(): HasOne
    {
        return $this->hasOne(RoomRental::class)->where('end_time', '>', now());
    }
}
