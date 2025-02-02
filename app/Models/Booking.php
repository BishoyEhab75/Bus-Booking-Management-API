<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'trip_id', 'seat_number', 'from_station', 'to_station'];

    public function trip() {
        return $this->belongsTo(Trip::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function fromStation() {
        return $this->belongsTo(Station::class, 'from_station');
    }

    public function toStation() {
        return $this->belongsTo(Station::class, 'to_station');
    }
}
