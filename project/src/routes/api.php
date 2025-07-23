<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\StudioController;
use Carbon\Carbon;

Route::middleware('apikey')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    Route::post('/bookings/decrypt', [BookingController::class, 'decryptResponse']);
});

Route::middleware('apikey')->group(function () {
    Route::get('/studios', [StudioController::class, 'index']);
    Route::post('/studios', [StudioController::class, 'store']);
    Route::get('/studios/{id}', [StudioController::class, 'show']);
    Route::put('/studios/{id}', [StudioController::class, 'update']);
    Route::delete('/studios/{id}', [StudioController::class, 'destroy']);
    Route::post('/studios/decrypt', [StudioController::class, 'decryptResponse']); // decrypt endpoint
});


Route::get('/public-studios', [StudioController::class, 'index']);


Route::get('/studio/{id}/availability', function ($id) {
    $date = request('date');

    $studio = \App\Models\Studio::findOrFail($id);
    $bookings = \App\Models\Booking::with('user')
        ->where('studio_id', $id)
        ->whereDate('tanggal_booking', $date)
        ->where('status', '!=', 'canceled')
        ->get();

    $openingTime = Carbon::createFromFormat('H:i:s', $studio->jam_buka ?? '09:00:00');
    $closingTime = Carbon::createFromFormat('H:i:s', $studio->jam_tutup ?? '21:00:00');
    $currentSlot = $openingTime->copy();
    $availability = [];

    while ($currentSlot < $closingTime) {
        $slotEnd = $currentSlot->copy()->addHour();
        $isBooked = false;
        $bookingInfo = null;

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->jam_mulai);
            $end = Carbon::parse($booking->jam_selesai);

            if ($currentSlot < $end && $slotEnd > $start) {
                $isBooked = true;
                $bookingInfo = [
                    'booking_id' => $booking->id,
                    'user' => $booking->user->name ?? 'Tidak diketahui',
                    'status' => $booking->status,
                    'jam_mulai' => $start->format('H:i'),
                    'jam_selesai' => $end->format('H:i'),
                ];
                break;
            }
        }

        $availability[] = [
            'start' => $currentSlot->format('H:i'),
            'end' => $slotEnd->format('H:i'),
            'status' => $isBooked ? 'booked' : 'available',
            'booking' => $bookingInfo,
        ];

        $currentSlot->addHour();
    }

    return response()->json([
        'availability_timeline' => $availability,
        'booked_slots' => array_filter($availability, fn($slot) => $slot['status'] === 'booked'),
        'available_slots' => array_filter($availability, fn($slot) => $slot['status'] === 'available'),
    ]);
});
