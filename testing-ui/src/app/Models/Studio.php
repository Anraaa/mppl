<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

    public function isOperationalDay(Carbon $date): bool
    {
        $dayName = $date->locale('id')->isoFormat('dddd');
        $operationalDays = $this->hari_operasional;
        
        $daysRange = array_map('trim', explode('-', $operationalDays));
        $startDay = $daysRange[0];
        $endDay = $daysRange[1] ?? $startDay;
        
        $allDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $startIndex = array_search($startDay, $allDays);
        $endIndex = array_search($endDay, $allDays);
        
        if ($startIndex === false || $endIndex === false) {
            return false;
        }
        
        if ($startIndex <= $endIndex) {
            return $startIndex <= array_search($dayName, $allDays) && 
                array_search($dayName, $allDays) <= $endIndex;
        }
        
        return array_search($dayName, $allDays) >= $startIndex || 
            array_search($dayName, $allDays) <= $endIndex;
    }
}
