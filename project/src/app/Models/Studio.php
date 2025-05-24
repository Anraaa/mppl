<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    use HasFactory;

    protected $fillable = ['nama_studio', 'deskripsi', 'harga_per_jam', 'foto', 'fasilitas', 'kapasitas', 'jam_operasional', 'hari_operasional'];

    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
{
    return $query->where('is_active', true); // atau sesuaikan dengan field aktif yang kamu pakai
}
}
