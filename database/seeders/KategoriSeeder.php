<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::create([
            'nama_kategori' => 'Tahu',
            'slug' => 'tahu',
        ]);

        Kategori::create([
            'nama_kategori' => 'Limbah',
            'slug' => 'limbah',
        ]);
    }
}