<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'role_id' => 1,
            'username' => 'admin',
            'email' => 'admin@ecotahu.id',
            'password' => Hash::make('password'),
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Hijau Lestari No. 88, Bandung',
        ]);

        // Pelanggan dummy
        $pelanggan = [
            ['username' => 'budi', 'email' => 'budi@test.com', 'alamat' => 'Jl. Merdeka No. 10, Bandung', 'no_telepon' => '081111111111'],
            ['username' => 'siti', 'email' => 'siti@test.com', 'alamat' => 'Jl. Sudirman No. 25, Bandung', 'no_telepon' => '082222222222'],
            ['username' => 'andi', 'email' => 'andi@test.com', 'alamat' => 'Jl. Asia Afrika No. 5, Bandung', 'no_telepon' => '083333333333'],
            ['username' => 'rina', 'email' => 'rina@test.com', 'alamat' => 'Jl. Dago No. 100, Bandung', 'no_telepon' => '084444444444'],
        ];

        foreach ($pelanggan as $p) {
            User::create([
                'role_id' => 2,
                'username' => $p['username'],
                'email' => $p['email'],
                'password' => Hash::make('password'),
                'alamat' => $p['alamat'],
                'no_telepon' => $p['no_telepon'],
            ]);
        }
    }
}