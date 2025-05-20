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
            $table->string('snap_token')->nullable()->after('status');
            $table->string('payment_order_id')->nullable()->after('snap_token');
            $table->enum('payment_status', ['pending', 'paid', 'expired', 'failed'])
                  ->default('pending')
                  ->after('payment_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'payment_order_id', 'payment_status']);
        });
    }
};
