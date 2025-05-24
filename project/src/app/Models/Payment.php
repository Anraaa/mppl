<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'metode', 'bukti_transfer', 'status', 'paid_at'
    ];


    public function booking() {
        return $this->belongsTo(Booking::class);
    }
}
