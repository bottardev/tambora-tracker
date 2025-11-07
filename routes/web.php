<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicBookingController;

// Test route to check if Laravel is working
Route::get('/test', function () {
    return 'Laravel is working! Routes count: ' . \App\Models\Route::count();
});

// Test route to create a sample route
Route::post('/api/test/create-route', function () {
    $route = \App\Models\Route::create([
        'name' => 'Mount Tambora Summit Trail',
        'description' => 'A challenging hike to the summit of Mount Tambora with breathtaking views of the crater lake.',
        'total_distance_km' => 15.5
    ]);
    return response()->json(['success' => true, 'route' => $route]);
});

// Homepage with booking functionality
Route::get('/', [PublicBookingController::class, 'homepage'])->name('booking.homepage');

// Public Booking API endpoints (no authentication required)
Route::prefix('api/public')->group(function () {
    Route::post('/bookings/check-availability', [PublicBookingController::class, 'checkAvailability'])->name('api.booking.availability');
    Route::post('/bookings', [PublicBookingController::class, 'store'])->name('api.booking.store');
    Route::post('/bookings/status', [PublicBookingController::class, 'getStatus'])->name('api.booking.status');
    Route::post('/bookings/{bookingCode}/payment', [PublicBookingController::class, 'uploadPayment'])->name('api.booking.payment');
});

// Booking status checking page
Route::get('/check-booking', [PublicBookingController::class, 'checkStatus'])->name('booking.check-status');

// Payment page for specific booking
Route::get('/booking/{bookingCode}/payment', [PublicBookingController::class, 'showPayment'])->name('booking.payment');
