<?php

use App\Livewire\ShowHomePage;
use App\Livewire\ShowProfile;
use App\Livewire\ShowAbout;
use App\Livewire\ShowStudio;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\MidtransController;
use App\Http\Livewire\Client\StudioAvailability;


/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/
Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/
/* Route::get('/', function () {
    return view('welcome');
}); */

/* Route::middleware('guest')->group(function () {
    Route::get('/client/register', [RegisteredUserController::class, 'create'])->name('client.register');
    Route::post('/client/register', [RegisteredUserController::class, 'store']);
}); */

// Route::get('/booking/{id}/receipt', function ($id) {
//     $booking = \App\Models\Booking::findOrFail($id);

//     return view('booking.receipt', [
//         'booking' => $booking
//     ]);
// })->name('booking.receipt');

Route::post('/payment/notification', function () {
    $notif = new \Midtrans\Notification();
    
    $transaction = $notif->transaction_status;
    $orderId = $notif->order_id;
    $fraud = $notif->fraud_status;

    // Cari booking berdasarkan payment_order_id
    $booking = \App\Models\Booking::where('payment_order_id', $orderId)->first();

    if (!$booking) {
        return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
    }

    if ($transaction == 'capture') {
        if ($fraud == 'challenge') {
            $booking->payment_status = 'challenge';
        } else if ($fraud == 'accept') {
            $booking->payment_status = 'paid';
        }
    } else if ($transaction == 'settlement') {
        $booking->payment_status = 'paid';
    } else if ($transaction == 'pending') {
        $booking->payment_status = 'pending';
    } else if ($transaction == 'cancel' ||
        $transaction == 'deny' ||
        $transaction == 'expire') {
        $booking->payment_status = 'failed';
    }

    $booking->payment_metadata = json_encode($notif);
    $booking->save();

    return response()->json(['status' => 'success']);
})->name('payment.notification');

//Route::get('/cek-ketersediaan', StudioAvailability::class)->name('cek-ketersediaan');



Route::get('/', ShowHomePage::class)->name('home');
ROute::get('/studio', ShowStudio::class)->name('studio');
Route::get('/about', ShowAbout::class)->name('about');
