<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'trip_id',
        'user_id',
        'passenger_name',
        'passenger_phone',
        'seat_number',
        'amount',
        'paid_at',
        'status',
        'payment_reference'
    ];
    protected $cast = [
        'paid_at' =>'datetime',
    ];
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
