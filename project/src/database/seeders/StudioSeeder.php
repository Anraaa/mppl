<?php

namespace Database\Seeders;

use App\Models\Studio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan tabel kosong sebelum menjalankan seeder, atau gunakan pengecekan jika diperlukan
        // if (Studio::count() == 0) { // Anda bisa mengaktifkan ini jika ingin mencegah duplikasi saat run berulang
            $studiosData = [
                [
                    'nama_studio' => 'Studio Classic',
                    'deskripsi' => 'Studio dengan nuansa klasik, cocok untuk potret formal dan pernikahan.',
                    'harga_per_jam' => 300000,
                    'foto' => 'studio-classic.jpg',
                    'fasilitas' => 'AC, WiFi, Props Klasik, Kamera Pro, Lighting Studio',
                    'kapasitas' => 10,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Minimalist',
                    'deskripsi' => 'Desain modern minimalis dengan berbagai pilihan background polos.',
                    'harga_per_jam' => 250000,
                    'foto' => 'studio-minimalist.jpg',
                    'fasilitas' => 'AC, WiFi, Background Beragam, Lighting LED, Ruang Ganti',
                    'kapasitas' => 8,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Green Screen',
                    'deskripsi' => 'Dilengkapi green screen penuh untuk keperluan video atau efek khusus.',
                    'harga_per_jam' => 350000,
                    'foto' => 'studio-greenscreen.jpg',
                    'fasilitas' => 'AC, WiFi, Green Screen, Blue Screen, Lighting Chroma Key, Software Editing',
                    'kapasitas' => 12,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Outdoor Vibes',
                    'deskripsi' => 'Setting studio yang menyerupai suasana outdoor dengan taman dan properti alami.',
                    'harga_per_jam' => 280000,
                    'foto' => 'studio-outdoor.jpg',
                    'fasilitas' => 'AC, WiFi, Properti Taman, Filter Cahaya Alami, Area Lounge',
                    'kapasitas' => 15,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Glamour',
                    'deskripsi' => 'Khusus untuk sesi foto fashion dan editorial dengan backdrop mewah.',
                    'harga_per_jam' => 400000,
                    'foto' => 'studio-glamour.jpg',
                    'fasilitas' => 'AC, WiFi, Backdrop Mewah, Lighting Fashion, Ruang Make Up Profesional',
                    'kapasitas' => 7,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Rustic',
                    'deskripsi' => 'Studio dengan sentuhan kayu dan dekorasi pedesaan yang hangat.',
                    'harga_per_jam' => 270000,
                    'foto' => 'studio-rustic.jpg',
                    'fasilitas' => 'AC, WiFi, Properti Kayu, Lighting Hangat, Koleksi Baju Tradisional',
                    'kapasitas' => 9,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Family',
                    'deskripsi' => 'Ruangan luas dengan banyak properti ramah anak dan keluarga.',
                    'harga_per_jam' => 320000,
                    'foto' => 'studio-family.jpg',
                    'fasilitas' => 'AC, WiFi, Mainan Anak, Area Bermain, Kursi Bayi, Peralatan Keluarga',
                    'kapasitas' => 20,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Product Shot',
                    'deskripsi' => 'Dirancang khusus untuk fotografi produk dengan meja dan background khusus.',
                    'harga_per_jam' => 200000,
                    'foto' => 'studio-product.jpg',
                    'fasilitas' => 'AC, WiFi, Meja Putar, Light Box, Background Produk, Tripod Khusus',
                    'kapasitas' => 5,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio Vintage',
                    'deskripsi' => 'Dekorasi dan properti bergaya vintage dari era 70-an dan 80-an.',
                    'harga_per_jam' => 290000,
                    'foto' => 'studio-vintage.jpg',
                    'fasilitas' => 'AC, WiFi, Properti Vintage, Kostum Era Lama, Pencahayaan Soft',
                    'kapasitas' => 8,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
                [
                    'nama_studio' => 'Studio White Box',
                    'deskripsi' => 'Studio serba putih dengan pencahayaan yang sangat fleksibel.',
                    'harga_per_jam' => 260000,
                    'foto' => 'studio-whitebox.jpg',
                    'fasilitas' => 'AC, WiFi, Background Putih Seamless, Lighting DMX, Diffuser Besar',
                    'kapasitas' => 10,
                    'jam_operasional' => '09:00 - 21:00',
                    'hari_operasional' => 'Senin-Sabtu'
                ],
            ];

            foreach ($studiosData as $data) {
                Studio::create($data);
            }
        // }
    }
}

