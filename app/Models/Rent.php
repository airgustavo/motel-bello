<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rent extends Model
{
    protected $fillable = ['name', 'cost', 'duration'];

    public function rentals(): HasMany
    {
        return $this->hasMany(RoomRental::class);
    }
}



    

   
        
