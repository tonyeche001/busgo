<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'distance_km',
        'base_fare',
        'is_active',
    ];
    public function trip()
    {
        return $this->hasMany(Trip::class);
    }
    
}
