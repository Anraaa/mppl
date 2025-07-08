<?php

namespace App\Filament\Client\Resources\BookingResource\Pages;

use App\Filament\Client\Resources\BookingResource;
use Filament\Resources\Pages\Page;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use App\Models\Booking;

class PaymentBooking extends Page
{
    use WithFileUploads;

    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.client.resources.booking-resource.pages.payment-booking';

    public ?Booking $booking = null;

    public ?string $metode = null;

    public $bukti_transfer;

    // Gunakan mount dengan tipe data int/string, bisa juga Booking langsung jika route param otomatis resolve model
    public function mount($record): void
    {
        // $record adalah route param dari Filament otomatis sebagai bookingId
        $this->booking = Booking::findOrFail($record);
    }

    public function submitPayment()
    {
        $this->validate([
            'metode' => 'required|in:gopay,va_bca,va_bri,va_bni',
            'bukti_transfer' => 'required|image|max:2048',
        ]);

        $path = $this->bukti_transfer->store('bukti-transfer', 'public');

        if ($this->booking->payment) {
    $this->booking->payment->update([
        'metode' => $this->metode,
        'bukti_transfer' => $path,
        'status' => 'waiting_verification',
        'paid_at' => now(),
    ]);
} else {
    // Kalau belum ada payment, buat dulu record baru
    $this->booking->payments()->create([
        'metode' => $this->metode,
        'bukti_transfer' => $path,
        'status' => 'waiting_verification',
        'paid_at' => now(),
    ]);
}

        Notification::make()
            ->title('Bukti transfer berhasil diunggah. Menunggu verifikasi admin.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'payment' => $this->booking->payment,
            'booking' => $this->booking,
        ];
    }
}
