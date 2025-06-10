<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomRental extends Model
{
    protected $fillable = ['room_id', 'rent_id', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class);
    }

    // Determina si esta renta aún está activa
    public function isActive(): bool
    {
        return now()->between($this->start_time, $this->end_time);
    }
}
