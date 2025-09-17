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
        // Modifikasi tabel 'cars' yang sudah ada
        Schema::table('cars', function (Blueprint $table) {
            // 1. Tambahkan kolom foreign key baru untuk car_model_id
            //    Kita tempatkan setelah kolom 'id' agar rapi.
            $table->foreignId('car_model_id')->nullable()->constrained()->after('id');

            // 2. Hapus kolom 'merek' dan 'nama_mobil' yang lama karena sudah tidak relevan
            $table->dropColumn(['merek', 'nama_mobil']);
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Logika untuk membatalkan migrasi (rollback)
            $table->dropConstrainedForeignId('car_model_id');
            $table->enum('merek', ['toyota', 'mitsubishi', 'suzuki', 'daihatsu', 'honda'])->default('toyota')->after('nopol');
            $table->string('nama_mobil')->after('merek');
        });
    }
};
