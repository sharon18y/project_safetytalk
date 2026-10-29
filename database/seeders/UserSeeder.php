<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin K3
        User::create([
            'name' => 'Admin K3',
            'email' => 'admin@biringkassi.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Akun Koordinator Contoh
        User::create([
            'name' => 'Koordinator Lapangan',
            'email' => 'koordinator@biringkassi.com',
            'password' => Hash::make('password123'),
            'role' => 'koordinator',
        ]);
    }
}