<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat pengguna Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
            'role' => 'superadmin',
        ]);

        // Membuat pengguna Staff
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
            'role' => 'staff',
        ]);
    }
}
