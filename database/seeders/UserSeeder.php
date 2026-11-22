<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (role_id = 1)
        User::create([
            'role_id' => 1,
            'username' => 'admin',
            'email' => 'admin@ecotahu.id',
            'password' => Hash::make('password'),
            'alamat' => 'Jl. Mastrip No. 45, Sumbersari, Jember',
            'no_telepon' => '081234567890',
        ]);

        // Pelanggan (role_id = 2)
        $pelanggan = [
            ['budi', 'budi@test.com', '081234560101', 'Jl. Kalimantan No. 18, Sumbersari, Jember'],
            ['rina', 'rina@test.com', '081234560102', 'Jl. Jawa No. 24, Sumbersari, Jember'],
            ['siti', 'siti@test.com', '081234560103', 'Jl. Karimata No. 12, Sumbersari, Jember'],
            ['andi', 'andi@test.com', '081234560104', 'Jl. Gajah Mada No. 88, Kaliwates, Jember'],
        ];

        foreach ($pelanggan as $p) {
            User::create([
                'role_id' => 2,
                'username' => $p[0],
                'email' => $p[1],
                'password' => Hash::make('password'),
                'no_telepon' => $p[2],
                'alamat' => $p[3],
            ]);
        }
    }
}
