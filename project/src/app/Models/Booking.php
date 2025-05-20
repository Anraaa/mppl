<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'studio_id', 'tanggal_booking', 'jam_mulai', 'jam_selesai', 'total_bayar', 'status', 'catatan', 'snap_token', 'payment_order_id', 'payment_status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'tanggal_booking' => 'date',
        'jam_mulai' => 'datetime:H:i:s',
        'jam_selesai' => 'datetime:H:i:s',
    ];

    public function studio() {
        return $this->belongsTo(Studio::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }

    // Tambahkan mutator untuk handle input
public function setJamMulaiAttribute($value)
{
    $this->attributes['jam_mulai'] = Carbon::parse($value)->format('H:i:s');
}

public function setJamSelesaiAttribute($value)
{
    $this->attributes['jam_selesai'] = Carbon::parse($value)->format('H:i:s');
}

// Accessor untuk format tampilan
public function getJamMulaiFormattedAttribute()
{
    return Carbon::parse($this->jam_mulai)->format('H:i');
}

public function getJamSelesaiFormattedAttribute()
{
    return Carbon::parse($this->jam_selesai)->format('H:i');
}

public function getTimeRangeAttribute()
    {
        return Carbon::parse($this->jam_mulai)->format('H:i') . ' - ' . 
               Carbon::parse($this->jam_selesai)->format('H:i');
    }
}
