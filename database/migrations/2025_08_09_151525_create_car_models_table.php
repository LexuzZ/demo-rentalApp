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
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel 'brands'
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Nama model mobil
            $table->timestamps();

            // Mencegah ada nama model yang sama untuk merek yang sama
            $table->unique(['brand_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
};
