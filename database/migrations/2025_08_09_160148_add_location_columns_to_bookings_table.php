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
            // Tambahkan dua kolom string baru yang bisa null
            // Kita letakkan setelah kolom 'paket' agar rapi
            $table->string('lokasi_pengantaran')->nullable()->after('paket');
            $table->string('lokasi_pengembalian')->nullable()->after('lokasi_pengantaran');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Logika untuk membatalkan migrasi (rollback)
            $table->dropColumn(['lokasi_pengantaran', 'lokasi_pengembalian']);
        });
    }
};
