<?php

namespace Database\Seeders;

use App\Models\Seo;
use App\Models\User;
use Filament\Pages\Page;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        

        $this->call([
            RoleSeeder::class,
            ProductSeeder::class,
            PageConfigSeeder::class,
            LogoSeeder::class,
            SeoSeeder::class,
            FooterSeeder::class,
            StudioSeeder::class,
        ]);
        $this->seedUsers();
    }

    private function seedUsers(): void
    {
        // Create Admin user if not exists
        $adminEmail = 'admin@admin.com';
        if (! User::where('email', $adminEmail)->exists()) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => bcrypt('password'),
            ]);
            $admin->assignRole('super_admin');
        };
    }
}