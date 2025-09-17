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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom 'role' setelah kolom 'email'
            // Enum membatasi nilai hanya boleh 'admin' atau 'staff'
            // Defaultnya adalah 'staff' untuk pengguna baru
            $table->enum('role', ['admin', 'staff', 'superadmin'])->default('staff')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Logika untuk membatalkan migrasi (rollback)
            $table->dropColumn('role');
        });
    }
};
