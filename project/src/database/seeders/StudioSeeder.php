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
        if(Studio::count()==0){
            Studio::create([
                'nama_studio' => 'Studio A',
                'deskripsi' => 'Studio A dengan lighting natural dan background putih.',
                'harga_per_jam' => 250000,
                'foto' => 'studio-a.jpg',
                'fasilitas' => 'AC, WiFi, Kamera, Lighting'
            ]);
        } 
    }
}
