<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Booking baru telah dibuat',
            'booking_id' => $this->booking->id,
            'client_name' => $this->booking->user->name,
            'studio' => $this->booking->studio->nama_studio,
            'date' => $this->booking->tanggal_booking->format('d M Y'),
            'time' => Carbon::parse($this->booking->jam_mulai)->format('H:i') . ' - ' . 
                      Carbon::parse($this->booking->jam_selesai)->format('H:i'),
            'url' => route('filament.admin.resources.bookings.edit', $this->booking->id),
        ];
    }
}