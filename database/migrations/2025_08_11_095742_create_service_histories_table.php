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
        Schema::create('service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->date('service_date'); // Tgl Service
            $table->unsignedInteger('current_km'); // Km Saat ini
            $table->text('description'); // Jenis pekerjaan (Ganti oli, dll.)
            $table->string('workshop')->nullable(); // Nama Bengkel (opsional)
            $table->unsignedInteger('next_km')->nullable(); // Km Berikutnya (opsional)
            $table->date('next_service_date')->nullable(); // Service berikutnya (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_histories');
    }
};
