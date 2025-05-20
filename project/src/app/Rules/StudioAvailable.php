<?php

namespace App\Rules;

use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonInterface;

class StudioAvailable implements ValidationRule
{
    public function __construct(
        protected ?int $studioId,
        protected ?string $bookingDate,
        protected ?string $startTime,
        protected ?string $endTime,
        protected ?int $bookingId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->studioId || !$this->bookingDate || !$this->startTime || !$this->endTime) {
            $fail('Semua field waktu booking harus diisi.');
            return;
        }

        try {
            // Convert to string if it's a Carbon instance
        $startTimeStr = is_object($this->startTime) ? $this->startTime->format('H:i') : $this->startTime;
        $endTimeStr = is_object($this->endTime) ? $this->endTime->format('H:i') : $this->endTime;

        // Clean input (remove seconds if present)
        $startTimeStr = preg_replace('/:\d+$/', '', $startTimeStr);
        $endTimeStr = preg_replace('/:\d+$/', '', $endTimeStr);

        // Validasi format waktu
        if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $startTimeStr) || 
            !preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $endTimeStr)) {
            $fail('Format jam harus HH:MM (24 jam). Contoh: 09:00 atau 21:00');
            return;
        }

        // Validasi tanggal
        if (!Carbon::hasFormat($this->bookingDate, 'Y-m-d')) {
            $fail('Format tanggal booking tidak valid.');
            return;
        }

        $bookingDate = Carbon::createFromFormat('Y-m-d', $this->bookingDate);
        if ($bookingDate->isPast() && !$bookingDate->isToday()) {
            $fail('Tanggal booking tidak boleh di masa lalu.');
            return;
        }

        // Parsing waktu
        $start = Carbon::createFromFormat('Y-m-d H:i', $this->bookingDate . ' ' . $startTimeStr);
        $end = Carbon::createFromFormat('Y-m-d H:i', $this->bookingDate . ' ' . $endTimeStr);
            // Jam operasional (09:00 - 21:00)
            $openingTime = $bookingDate->copy()->setTime(9, 0);
            $closingTime = $bookingDate->copy()->setTime(21, 0);

            if ($start->lt($openingTime)) {
                $fail('Studio buka mulai jam 09:00.');
                return;
            }

            if ($end->gt($closingTime)) {
                $fail('Studio tutup jam 21:00.');
                return;
            }

            // Validasi durasi
            $duration = $start->floatDiffInHours($end);
            if ($duration < 1) {
                $fail('Durasi booking minimal 1 jam.');
                return;
            }
            if ($duration > 6) {
                $fail('Durasi booking maksimal 6 jam.');
                return;
            }

            // Cek booking bentrok
            $overlapping = Booking::where('studio_id', $this->studioId)
                ->where('tanggal_booking', $this->bookingDate)
                ->where(function ($query) use ($start, $end) {
                    $query->where(function ($q) use ($start, $end) {
                        $q->where('jam_mulai', '<', $end->format('H:i'))
                          ->where('jam_selesai', '>', $start->format('H:i'));
                    });
                });

            if ($this->bookingId) {
                $overlapping->where('id', '!=', $this->bookingId);
            }

            if ($overlapping->exists()) {
                $fail('Studio sudah dibooking pada waktu tersebut. Silakan pilih waktu lain.');
            }

        } catch (\Exception $e) {
            Log::error('Booking validation error: ' . $e->getMessage());
            $fail('Terjadi kesalahan dalam validasi waktu. Silakan coba lagi.');
        }
    }
}