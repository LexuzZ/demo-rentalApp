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
        Schema::create('cars', function (Blueprint $table) {
           $table->id();
            $table->string('nopol')->unique();
            $table->enum('merek', ['toyota', 'mitsubishi', 'suzuki', 'daihatsu', 'honda'])->default('toyota'); // Nomor Polisi                  // Merek
            $table->string('nama_mobil');                    // Tipe Mobil
            $table->string('warna');                    // Tipe Mobil
            $table->enum('transmisi', ['matic', 'manual', ])->default('matic');                    // Tipe Mobil
            $table->string('garasi');                    // Lokasi Garasi            
            $table->year('year');                      // Tahun
            $table->enum('status', ['ready', 'disewa', 'perawatan', 'nonaktif'])->default('ready');
            $table->decimal('harga_pokok', 10, 2)->default(0);
            $table->decimal('harga_harian', 10, 2)->default(0);
            $table->decimal('harga_bulanan', 10, 2)->nullable()->default(0);
            $table->string('photo')->nullable();       // Path ke file foto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
