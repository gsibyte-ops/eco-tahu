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
            'alamat' => 'Jl. Tahu No. 1, Bandung',
            'no_telepon' => '081234567890',
        ]);

        // Pelanggan dummy (role_id = 2)
        $pelanggan = [
            ['budi',  'budi@test.com',  '081111111111', 'Jl. Merdeka No. 10, Bandung'],
            ['rina',  'rina@test.com',  '082222222222', 'Jl. Sudirman No. 25, Bandung'],
            ['siti',  'siti@test.com',  '083333333333', 'Jl. Asia Afrika No. 5, Bandung'],
            ['andi',  'andi@test.com',  '084444444444', 'Jl. Riau No. 88, Bandung'],
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