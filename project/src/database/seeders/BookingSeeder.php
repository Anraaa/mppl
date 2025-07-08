<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Studio;
use App\Models\User; // Pastikan Anda memiliki model User
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon; // Untuk manipulasi tanggal dan waktu
use Faker\Factory as Faker; // Untuk data dummy

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan Faker dengan lokal Indonesia

        // Ambil ID user yang valid (antara 2 dan 6)
        $userIds = User::whereBetween('id', [2, 6])->pluck('id')->toArray();

        // Ambil semua ID studio
        $studioIds = Studio::pluck('id')->toArray();

        // Pastikan ada user dan studio yang tersedia
        if (empty($userIds) || empty($studioIds)) {
            echo "Pastikan Anda memiliki data user (ID 2-6) dan studio terlebih dahulu sebelum menjalankan BookingSeeder.\n";
            return;
        }

        // Hapus semua booking yang ada untuk menghindari duplikasi saat re-seed
        // Booking::truncate(); // Aktifkan jika Anda ingin menghapus semua data booking setiap kali seeder dijalankan

        // Generate hingga 50 data booking
        for ($i = 0; $i < 50; $i++) {
            // Pilih user_id secara acak dari yang tersedia
            $userId = $faker->randomElement($userIds);

            // Pilih studio_id secara acak
            $studio = Studio::find($faker->randomElement($studioIds));
            
            if (!$studio) {
                continue; // Lewati jika studio tidak ditemukan (misalnya jika ID studio di studioIds sudah tidak ada)
            }

            // Tentukan tanggal booking: di bulan Juni atau Juli 2025
            $tanggalBooking = $faker->dateTimeBetween('2025-06-01', '2025-07-31');
            $date = Carbon::parse($tanggalBooking);

            // Pastikan tanggalnya adalah Senin-Sabtu
            // Loop sampai mendapatkan hari yang sesuai
            while ($date->isSunday()) { // Jika hari Minggu, tambahkan 1 hari
                $date->addDay();
            }
            $tanggalBooking = $date->format('Y-m-d');


            // Tentukan jam mulai dan jam selesai dalam rentang 09:00 - 21:00
            // Durasi booking minimal 1 jam, maksimal 4 jam (misal)
            $jamMulaiHour = $faker->numberBetween(9, 18); // Max 18 agar ada ruang untuk jam selesai (21 - 3 = 18)
            $jamMulaiMinute = $faker->randomElement([0, 30]);
            $jamMulai = Carbon::createFromTime($jamMulaiHour, $jamMulaiMinute);

            $durasiJam = $faker->numberBetween(1, 3); // Booking antara 1 sampai 3 jam
            $jamSelesai = $jamMulai->copy()->addHours($durasiJam);

            // Pastikan jam selesai tidak melewati 21:00
            if ($jamSelesai->greaterThan(Carbon::createFromTime(21, 0))) {
                // Jika melewati 21:00, sesuaikan jam selesai
                // Atau pilih jam mulai yang lebih awal
                $jamSelesai = Carbon::createFromTime(21, 0);
                // Hitung ulang durasi jika diperlukan untuk total bayar yang akurat
                $durasiJam = $jamMulai->diffInHours($jamSelesai);
            }
            
            $totalBayar = $studio->harga_per_jam * $durasiJam;

            Booking::create([
                'user_id' => $userId,
                'studio_id' => $studio->id,
                'tanggal_booking' => $tanggalBooking,
                'jam_mulai' => $jamMulai->format('H:i:s'),
                'jam_selesai' => $jamSelesai->format('H:i:s'),
                'total_bayar' => $totalBayar,
                'status' => $faker->randomElement(['pending', 'confirmed', 'canceled']), // Bisa random atau selalu 'confirmed'
                'catatan' => $faker->sentence(8) . '.', // Catatan dummy
            ]);
        }
    }
}

