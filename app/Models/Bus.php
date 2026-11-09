<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $fillable = [
        'plate_number',
        'name',
        'bus_type',
        'seat_capacity',
        'is_active',
    ];
    public function trip()
    {
        return $this->hasMany(Trip::class);
    }
}
