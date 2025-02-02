<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index() {
        return Bus::all();
    }

    public function show($id) {
        return Bus::findOrFail($id);
    }

    public function store(Request $request) {
        $request->validate([
            'plate_number' => 'required|string|unique:buses,plate_number',
        ]);
        return Bus::create($request->all());
    }

    public function update(Request $request, $id) {
        $request->validate([
            'plate_number' => 'required|string|unique:buses,plate_number,' . $id,
            'capacity' => 'required|integer|min:1'
        ]);
        $bus = Bus::findOrFail($id);
        $bus->update($request->all());
        return $bus;
    }

    public function destroy($id) {
        Bus::findOrFail($id)->delete();
        return response()->json(['message' => 'Bus deleted successfully']);
    }
}
