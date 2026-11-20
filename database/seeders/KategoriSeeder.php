<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::firstOrCreate([
            'nama_kategori' => 'Tahu',
            'slug' => 'tahu',
        ]);

        Kategori::firstOrCreate([
            'nama_kategori' => 'Limbah',
            'slug' => 'limbah',
        ]);
    }
}
