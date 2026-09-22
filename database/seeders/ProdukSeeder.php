<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProdukTahu;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            ['kategori_id' => 1, 'nama_produk' => 'Tahu Putih Organik Premium', 'harga' => 12500, 'stok' => 100, 'deskripsi' => 'Tahu putih premium hasil kedelai organik pilihan.'],
            ['kategori_id' => 2, 'nama_produk' => 'Tahu Goreng Gurih Renyah', 'harga' => 14000, 'stok' => 80, 'deskripsi' => 'Tahu goreng dengan tekstur renyah di luar, lembut di dalam.'],
            ['kategori_id' => 3, 'nama_produk' => 'Tahu Susu Lembut Bergizi', 'harga' => 18500, 'stok' => 60, 'deskripsi' => 'Tahu susu dengan tekstur super lembut, kaya protein.'],
            ['kategori_id' => 4, 'nama_produk' => 'Tahu Bakso Spesial Daging Sapi', 'harga' => 16000, 'stok' => 50, 'deskripsi' => 'Tahu bakso isi daging sapi pilihan, cocok untuk lauk.'],
            ['kategori_id' => 1, 'nama_produk' => 'Tahu Putih Ekonomis', 'harga' => 8000, 'stok' => 150, 'deskripsi' => 'Tahu putih harga terjangkau kualitas tetap oke.'],
        ];

        foreach ($produk as $p) {
            ProdukTahu::create([
                'kategori_id' => $p['kategori_id'],
                'nama_produk' => $p['nama_produk'],
                'slug' => Str::slug($p['nama_produk']),
                'deskripsi' => $p['deskripsi'],
                'harga' => $p['harga'],
                'stok' => $p['stok'],
                'status' => 'aktif',
            ]);
        }
    }
}