<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index() {
        return Station::all();
    }

    public function show($id) {
        return Station::findOrFail($id);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|unique:stations,name'
        ]);
        return Station::create($request->all());
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|unique:stations,name,' . $id
        ]);
        $station = Station::findOrFail($id);
        $station->update($request->all());
        return $station;
    }

    public function destroy($id) {
        Station::findOrFail($id)->delete();
        return response()->json(['message' => 'Station deleted successfully']);
    }
}
