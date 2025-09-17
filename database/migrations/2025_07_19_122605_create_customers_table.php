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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('ktp')->unique(); // KTP / SIM
            $table->string('lisence')->unique()->nullable(); // KTP / SIM
            $table->string('identity_file')->nullable(); // upload KTP
             $table->string('lisence_file')->nullable(); // upload SIM
            $table->string('no_telp')->unique(); // Nomor Telepon
            $table->text('alamat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
