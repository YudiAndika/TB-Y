<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Owner (Bos)
        User::updateOrCreate(
            ['email' => 'owner@toko.com'], // Ini yang dipakai untuk login (sebagai username)
            [
                'name' => 'Bos Owner',
                'password' => Hash::make('admin'), // Password diset: admin
                'role' => 'owner',
            ]
        );

        // 2. Akun Admin (Gudang)
        User::updateOrCreate(
            ['email' => 'admin@toko.com'],
            [
                'name' => 'Admin Gudang',
                'password' => Hash::make('admin'),
                'role' => 'admin',
            ]
        );

        // 3. Akun Kasir
        User::updateOrCreate(
            ['email' => 'kasir@toko.com'],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('admin'),
                'role' => 'kasir',
            ]
        );
    }
}