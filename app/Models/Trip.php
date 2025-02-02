<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'start_station', 'end_station'];

    public function bus() {
        return $this->belongsTo(Bus::class);
    }

    public function startStation() {
        return $this->belongsTo(Station::class, 'start_station');
    }

    public function endStation() {
        return $this->belongsTo(Station::class, 'end_station');
    }

    public function tripStops() {
        return $this->hasMany(TripStop::class);
    }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }
}
