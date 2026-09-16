<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Owner
        User::updateOrCreate(
            ['email' => 'owner@toko.com'],
            [
                'name' => 'Bos Owner',
                'password' => Hash::make('admin'),
                'role' => 'owner',
            ]
        );

        // 2. Akun Admin
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