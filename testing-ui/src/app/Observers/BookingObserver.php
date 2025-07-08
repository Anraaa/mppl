<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\NewBookingNotification;
use Filament\Notifications\Notification;

class BookingObserver
{
    public function created(Booking $booking):void
    {
        Notification::make()
            ->title('Booking baru telah dibuat')
            ->body('Booking baru telah dibuat oleh ' . $booking->user->name)
            //->action('Lihat Booking', route('filament.admin.resources.bookings.edit', $booking->id))
            ->success()
            ->sendToDatabase($booking->user);
    }
}