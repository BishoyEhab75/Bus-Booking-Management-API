<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Station;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with('bus', 'startStation', 'endStation')->get();

        $formattedTrips = $trips->map(function ($trip) {
            // Get total seats from the bus
            $totalSeats = $trip->bus->capacity;

            // Get booked seat numbers
            $bookedSeats = Booking::where('trip_id', $trip->id)->pluck('seat_number')->toArray();

            // Get available seats
            $availableSeats = array_values(array_diff(range(1, $totalSeats), $bookedSeats));

            return [
                'trip_id' => $trip->id,
                'start_station' => $trip->startStation->name,
                'end_station' => $trip->endStation->name,
                'total_available_seats' => count($availableSeats)
            ];
        });

        return response()->json([
            'message' => 'Trips retrieved successfully',
            'data' => $formattedTrips
        ]);
    }

    public function show($id)
    {
        $trip = Trip::with('bus', 'startStation', 'endStation')->findOrFail($id);
        $totalSeats = $trip->bus->capacity;
        $bookedSeats = Booking::where('trip_id', $trip->id)->pluck('seat_number')->toArray();
        $availableSeats = array_values(array_diff(range(1, $totalSeats), $bookedSeats));
        $trip->available_seats = $availableSeats;

        return response()->json([
            'message' => 'Trip retrieved successfully',
            'data' => [
                'trip_id' => $trip->id,
                'start_station' => $trip->startStation->name,
                'end_station' => $trip->endStation->name,
                'total_available_seats' => count($availableSeats)
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'start_station' => 'required|exists:stations,id',
            'end_station' => 'required|exists:stations,id',
        ]);
        return Trip::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'start_station' => 'required|exists:stations,id',
            'end_station' => 'required|exists:stations,id',
        ]);
        $trip = Trip::findOrFail($id);
        $trip->update($request->all());
        return $trip;
    }

    public function destroy($id)
    {
        Trip::findOrFail($id)->delete();
        return response()->json(['message' => 'Trip deleted successfully']);
    }

    public function getAvailableSeats(Request $request)
    {
        $request->validate([
            'from_station' => 'required|exists:stations,id',
            'to_station' => 'required|exists:stations,id',
        ]);

        $trips = Trip::whereHas('tripStops', function ($query) use ($request) {
            $query->where('station_id', $request->from_station);
        })->whereHas('tripStops', function ($query) use ($request) {
            $query->where('station_id', $request->to_station);
        })->with(['tripStops' => function ($query) {
            $query->orderBy('stop_order');
        }, 'bus'])->get();

        if ($trips->isEmpty()) {
            return response()->json(['message' => 'No trip available for the selected route'], 404);
        }

        $availableTrips = [];

        foreach ($trips as $trip) {
            $tripStops = $trip->tripStops;
            $stopOrderMap = $tripStops->pluck('stop_order', 'station_id')->toArray();


            // Check if stations exist in this trip
            if (!isset($stopOrderMap[$request->from_station]) || !isset($stopOrderMap[$request->to_station])) {
                Log::warning("Stations not found in trip {$trip->id}");
                continue;
            }

            $fromOrder = $stopOrderMap[$request->from_station];
            $toOrder = $stopOrderMap[$request->to_station];

            // Ensure from_station comes before to_station
            if ($fromOrder >= $toOrder) {
                Log::warning("Invalid order for trip {$trip->id}: from {$request->from_station} (order: $fromOrder) to {$request->to_station} (order: $toOrder)");
                continue;
            }

            // Get total seats for the trip's bus
            $totalSeats = $trip->bus->capacity ?? 12;
            $allSeats = range(1, $totalSeats);

            // Get booked seats for this segment
            $bookedSeats = Booking::where('trip_id', $trip->id)
                ->where(function ($query) use ($fromOrder, $toOrder, $stopOrderMap) {
                    $query->where(function ($q) use ($fromOrder, $toOrder, $stopOrderMap) {
                        $q->whereIn('from_station', array_keys($stopOrderMap, $fromOrder))
                            ->whereIn('to_station', array_keys($stopOrderMap, $toOrder));
                    })
                        ->orWhere(function ($q) use ($fromOrder, $toOrder, $stopOrderMap) {
                            $q->whereIn('from_station', array_keys($stopOrderMap, $fromOrder, true))
                                ->where('to_station', '>', $toOrder);
                        })
                        ->orWhere(function ($q) use ($fromOrder, $toOrder, $stopOrderMap) {
                            $q->where('from_station', '<', $fromOrder)
                                ->whereIn('to_station', array_keys($stopOrderMap, $toOrder, true));
                        });
                })
                ->pluck('seat_number')
                ->toArray();

            // Get available seats by excluding booked ones
            $availableSeats = array_values(array_diff($allSeats, $bookedSeats));

            $availableTrips[] = [
                'trip_id' => $trip->id,
                'from_station' => Station::find($request->from_station)->name,
                'to_station' => Station::find($request->to_station)->name,
                'total_seats' => count($allSeats) - count($bookedSeats),
                'available_seats' => $availableSeats,
            ];
        }

        if (empty($availableTrips)) {
            return response()->json(['message' => 'No available seats for the selected route'], 404);
        }

        return response()->json(['available_trips' => $availableTrips]);
    }
}