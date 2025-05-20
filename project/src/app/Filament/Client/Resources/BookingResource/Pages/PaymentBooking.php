<?php

namespace App\Filament\Client\Resources\BookingResource\Pages;

use App\Filament\Client\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class PaymentBooking extends Page
{
    protected static string $resource = BookingResource::class;
    protected static string $view = 'filament.client.resources.booking-resource.pages.payment-booking';
    
    public $booking;
    public $snapToken;
    public $isPaymentReady = false;


    protected $listeners = [
        'payment-success' => 'handlePaymentSuccess',
        'payment-pending' => 'handlePaymentPending',
        'payment-error' => 'handlePaymentError'
    ];

    public function mount($record): void
    {
        $this->booking = \App\Models\Booking::findOrFail($record);
    
        if ($this->booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    
        if ($this->booking->payment_status === 'paid') {
            $this->redirect(BookingResource::getUrl('edit', ['record' => $this->booking->id]));
            return;
        }
    
        $this->initializePayment();
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
                'email' => $this->booking->user->email,
                'phone' => $this->booking->user->phone ?? '',
            ]
        ];

        try {
            $this->snapToken = Snap::getSnapToken($params);
            
            $this->booking->update([
                'snap_token' => $this->snapToken,
                'payment_order_id' => $orderId,
                'payment_status' => 'pending',
            ]);

            $this->isPaymentReady = true;

        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error initializing payment: ' . $e->getMessage()
            );
        }
    }

    public function verifyPaymentStatus()
    {
        // Ambil data terbaru dari database
        $this->booking->refresh();
        
        if ($this->booking->payment_status === 'paid') {
            return $this->redirect(BookingResource::getUrl('edit', ['record' => $this->booking->id]));
        }
        
        $this->dispatch('notify', 
            type: 'warning',
            message: 'Pembayaran masih dalam proses verifikasi'
        );
    }

    public function paymentSuccess($result)
    {
        // Update status pembayaran langsung dari frontend callback
        $this->booking->update([
            'payment_status' => 'paid',
            'payment_method' => $result['payment_type'] ?? 'unknown',
            'payment_metadata' => $result,
            'paid_at' => now(),
        ]);
        
        $this->redirect(BookingResource::getUrl('edit', ['record' => $this->booking->id]));
    }
    
    public function paymentPending($result)
    {
        $this->booking->update([
            'payment_status' => 'pending',
            'payment_method' => $result['payment_type'] ?? 'unknown',
            'payment_metadata' => $result,
        ]);
        
        $this->dispatch('notify', 
            type: 'warning', 
            message: 'Pembayaran Anda sedang diproses. Silakan refresh halaman ini setelah beberapa saat.'
        );
    }
    
    public function paymentError($result)
    {
        $this->booking->update([
            'payment_status' => 'failed',
            'payment_metadata' => $result,
        ]);
        
        $this->dispatch('notify', 
            type: 'error', 
            message: 'Pembayaran gagal. Silakan coba lagi.'
        );
    }
    
    public function paymentClosed()
    {
        $this->dispatch('notify', 
            type: 'info', 
            message: 'Anda menutup halaman pembayaran.'
        );
    }

    public function handlePaymentSuccess($result)
{
    $this->booking->update([
        'payment_status' => 'paid',
        'paid_at' => now(),
        'payment_metadata' => $result
    ]);
    
    $this->dispatch('notify', type: 'success', message: 'Pembayaran berhasil!');
}

public function handlePaymentPending($result)
{
    $this->booking->update([
        'payment_status' => 'pending',
        'payment_metadata' => $result
    ]);
    
    $this->dispatch('notify', type: 'warning', message: 'Pembayaran dalam proses...');
}

public function handlePaymentError($error)
{
    $this->booking->update([
        'payment_status' => 'failed',
        'payment_metadata' => $error
    ]);
    
    $this->dispatch('notify', type: 'error', message: 'Pembayaran gagal: '.$error['message']);
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali ke Booking')
                ->url(BookingResource::getUrl('edit', ['record' => $this->booking->id])),
                
            Actions\Action::make('verify')
                ->label('Verifikasi Pembayaran')
                ->action('verifyPaymentStatus')
                ->color('success'),
        ];
    }
}