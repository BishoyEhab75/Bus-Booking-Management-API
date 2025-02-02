<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        return Booking::with(['user', 'trip', 'fromStation', 'toStation'])->get();
    }

    public function show($id)
    {
        return Booking::with(['user', 'trip', 'fromStation', 'toStation'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'seat_number' => 'required|integer|min:1|max:12',
            'from_station' => 'required|exists:stations,id',
            'to_station' => 'required|exists:stations,id',
        ]);


        $trip = Trip::findOrFail($request->trip_id);
        $bookedSeats = Booking::where('trip_id', $request->trip_id)
            ->where('from_station', $request->from_station)
            ->where('to_station', $request->to_station)
            ->pluck('seat_number')
            ->toArray();

        if (in_array($request->seat_number, $bookedSeats)) {
            return response()->json(['message' => 'Seat already booked for this trip.'], 400);
        }
        
        $user = Auth::user();

        $booking = Booking::create([
            'user_id' => $user->id,
            'trip_id' => $request->trip_id,
            'seat_number' => $request->seat_number,
            'from_station' => $request->from_station,
            'to_station' => $request->to_station,
        ]);

        return $booking;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'seat_number' => 'required|integer',
            'from_station' => 'required|exists:stations,id',
            'to_station' => 'required|exists:stations,id',
        ]);
        $booking = Booking::findOrFail($id);
        $booking->update($request->all());
        return $booking;
    }

    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();
        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
