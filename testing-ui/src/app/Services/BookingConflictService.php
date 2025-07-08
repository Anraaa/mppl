<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingConflictService
{
    public static function checkConflicts($studioId, $tanggalBooking, $jamMulai, $jamSelesai)
{
    return Booking::where('studio_id', $studioId)
        ->where('tanggal_booking', $tanggalBooking)
        ->where(function($query) use ($jamMulai, $jamSelesai) {
            $query->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai)
                  // Memastikan tidak ada booking yang memiliki jam mulai dan selesai yang sama persis
                  ->orWhere(function($query) use ($jamMulai, $jamSelesai) {
                      $query->where('jam_mulai', '=', $jamSelesai)
                            ->where('jam_selesai', '=', $jamMulai);
                  });
        })
        ->exists();
}

    
}