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
        Schema::create('invoices', function (Blueprint $table) {
           $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('dp', 12, 2)->default(0);
            $table->decimal('sisa_pembayaran', 12, 2)->default(0)->nullable();
            $table->decimal('pickup_dropOff', 12, 2)->default(0);
            $table->date('tanggal_invoice');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
