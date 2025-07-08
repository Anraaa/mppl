<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->integer('kapasitas')
                ->after('foto')
                ->default(10)
                ->comment('Kapasitas maksimum orang dalam studio');
                
            $table->string('jam_operasional')
                ->after('kapasitas')
                ->default('09:00 - 21:00')
                ->comment('Jam operasional studio, contoh: 09:00-21:00');
                
            $table->string('hari_operasional')
                ->after('jam_operasional')
                ->default('Senin-Minggu')
                ->comment('Hari operasional studio, contoh: Senin-Minggu');
        });
    }

    public function down()
    {
        Schema::table('studios', function (Blueprint $table) {
            $table->dropColumn(['kapasitas', 'jam_operasional', 'hari_operasional']);
        });
    }
};