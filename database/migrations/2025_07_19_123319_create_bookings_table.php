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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null'); // opsional
            $table->enum('paket', ['lepas_kunci', 'dengan_driver', 'tour'])->nullable();
            $table->date('tanggal_keluar');
            $table->date('tanggal_kembali');
            $table->time('waktu_keluar')->nullable();
            $table->time('waktu_kembali')->nullable();
            $table->string('total_hari')->default(1);
            $table->decimal('estimasi_biaya', 12, 2)->default(0);
            $table->decimal('harga_harian', 12, 2)->default(0)->nullable();
           
            $table->enum('status', ['booking', 'aktif', 'selesai', 'batal'])->default('booking');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
