<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripStopController;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'admin'])->group(function(){
    Route::apiResource('stations', StationController::class);
    Route::apiResource('buses', BusController::class);
    Route::apiResource('trips', TripController::class);
    Route::apiResource('trip-stops', TripStopController::class);
});

Route::middleware(['auth:sanctum'])->group(function(){
    Route::apiResource('bookings', BookingController::class);
    Route::get('/available-seats', [TripController::class, 'getAvailableSeats']);
});

// Optional: You can add specific routes for additional actions
Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel']);  // Example of adding a custom route for cancellation