<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'bus_id',
        'bus_route_id',
        'arrival_at',
        'departure_at',
        'fare',
        'status',
    ];
    protected $casts = [
        'departure_at' => 'datetime',
        'arrival_at' => 'datetime',
    ];
    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class);
    }
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
