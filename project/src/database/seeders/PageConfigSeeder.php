<?php

namespace Database\Seeders;

use App\Models\PageConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(PageConfig::count()==0){
            PageConfig::create([
                'title' => 'Selamat Datang di Sistem Booking Studio Foto',
                'detail' => 'Pesan studio fotografi dengan mudah. Pilih waktu, bayar, dan nikmati layanan kami!',
                'image' => '',
            ]);
        } 
    }
}
