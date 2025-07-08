<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom catatan jika belum ada
            if (!Schema::hasColumn('bookings', 'catatan')) {
                $table->text('catatan')->nullable()->after('status');
            }

            // Tambahkan composite index jika belum ada
            if (!Schema::hasIndex('bookings', ['studio_id', 'tanggal_booking'])) {
                $table->index(['studio_id', 'tanggal_booking']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Hapus kolom catatan
            $table->dropColumn('catatan');
            
            // Hapus index
            $table->dropIndex(['studio_id', 'tanggal_booking']);
        });
    }
};
