<?php

namespace App\Http\Controllers;

use App\Models\TripStop;
use Illuminate\Http\Request;

class TripStopController extends Controller
{
    public function index()
    {
        return TripStop::with(['trip', 'station'])->get();
    }

    public function show($id)
    {
        return TripStop::with(['trip', 'station'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'station_id' => 'required|exists:stations,id',
            'stop_order' => 'required|integer|min:1'
        ]);

        $exists = TripStop::where('trip_id', $request->trip_id)
            ->where('station_id', $request->station_id)
            ->exists();
        if ($exists) {
            return response()->json([
                'message' => 'This station is already assigned to the trip.'
            ], 422);
        }

        return TripStop::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'station_id' => 'required|exists:stations,id',
            'stop_order' => 'required|integer|min:1'
        ]);
        $tripStop = TripStop::findOrFail($id);
        $tripStop->update($request->all());
        return $tripStop;
    }

    public function destroy($id)
    {
        TripStop::findOrFail($id)->delete();
        return response()->json(['message' => 'Trip stop deleted successfully']);
    }
}
