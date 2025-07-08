<?php

namespace App\Http\Livewire\Client;

use Livewire\Component;
use Midtrans\Config;
use Midtrans\Snap;

class BookingPayment extends Component
{
    public $booking;
    public $snapToken;
    public $isPaymentReady = false;

    public function mount($booking)
    {
        $this->booking = \App\Models\Booking::findOrFail($booking);

        if ($this->booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if ($this->booking->payment_status === 'paid') {
            return redirect()->route('filament.client.resources.bookings.edit', ['record' => $this->booking->id]);
        }
    }

    public function initializePayment()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        $orderId = 'BOOK-' . $this->booking->id . '-' . now()->format('YmdHis');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $this->booking->total_bayar,
            ],
            'customer_details' => [
                'first_name' => $this->booking->user->name,
                'email' => 'customer@gmail.com',
            ],
        ];

        try {
            $this->snapToken = Snap::getSnapToken($params);

            $this->booking->update([
                'snap_token' => $this->snapToken,
                'payment_order_id' => $orderId,
                'payment_status' => 'pending',
            ]);

            $this->isPaymentReady = true;

            // Trigger JavaScript to call snap.pay()
            $this->dispatchBrowserEvent('payment-ready', ['snapToken' => $this->snapToken]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Gagal inisialisasi pembayaran: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.client.booking-payment');
    }
}
