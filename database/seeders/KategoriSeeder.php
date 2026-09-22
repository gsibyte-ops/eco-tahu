<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Tahu Putih', 'tipe' => 'produk_tahu'],
            ['nama_kategori' => 'Tahu Goreng', 'tipe' => 'produk_tahu'],
            ['nama_kategori' => 'Tahu Susu', 'tipe' => 'produk_tahu'],
            ['nama_kategori' => 'Tahu Bakso', 'tipe' => 'produk_tahu'],
            ['nama_kategori' => 'Ampas Tahu Kering', 'tipe' => 'limbah'],
            ['nama_kategori' => 'Ampas Tahu Basah', 'tipe' => 'limbah'],
        ];

        foreach ($kategori as $k) {
            DB::table('kategori')->insert([
                'nama_kategori' => $k['nama_kategori'],
                'slug' => Str::slug($k['nama_kategori']),
                'tipe' => $k['tipe'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}