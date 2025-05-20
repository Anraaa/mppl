<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ResetClientPanel extends Command
{
    protected $signature = 'panel:reset-client';
    protected $description = 'Hapus permission, policy, dan role panel client secara otomatis';

    public function handle()
    {
        $this->info('🧹 Menghapus permission terkait panel client...');

        Permission::where('name', 'like', '%booking')->delete();
        Permission::where('name', 'like', '%studio')->delete();
        Permission::where('name', 'like', 'page_%')->delete();

        $this->info('✅ Permission dihapus.');

        $this->info('🧹 Menghapus role "client" (jika ada)...');
        Role::where('name', 'client')->delete();
        $this->info('✅ Role dihapus.');

        $this->info('🧹 Menghapus policy client (jika ada)...');
        $policyPath = app_path('Policies');
        $deleted = false;

        $files = ['BookingPolicy.php', 'StudioPolicy.php'];

        foreach ($files as $file) {
            $fullPath = $policyPath . '/' . $file;
            if (File::exists($fullPath)) {
                File::delete($fullPath);
                $this->line("🗑️ Dihapus: $file");
                $deleted = true;
            }
        }

        if (!$deleted) {
            $this->line('ℹ️ Tidak ada file policy yang ditemukan atau sudah dihapus.');
        }

        $this->info('🎉 Panel client berhasil di-reset!');
    }
}
