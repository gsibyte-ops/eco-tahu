<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Limbah;
use Illuminate\Support\Str;

class LimbahSeeder extends Seeder
{
    public function run(): void
    {
        $limbah = [
            ['kategori_id' => 5, 'nama_limbah' => 'Ampas Tahu Kering Premium', 'harga' => 5000, 'stok' => 200, 'deskripsi' => 'Ampas tahu kering, cocok untuk pakan ternak atau olahan pangan.'],
            ['kategori_id' => 5, 'nama_limbah' => 'Ampas Tahu Kering Halus', 'harga' => 6000, 'stok' => 150, 'deskripsi' => 'Ampas tahu kering dengan tekstur halus, siap olah.'],
            ['kategori_id' => 6, 'nama_limbah' => 'Ampas Tahu Basah Segar', 'harga' => 3000, 'stok' => 100, 'deskripsi' => 'Ampas tahu basah segar baru dari proses produksi.'],
            ['kategori_id' => 6, 'nama_limbah' => 'Ampas Tahu Basah Fermentasi', 'harga' => 4500, 'stok' => 80, 'deskripsi' => 'Ampas tahu basah yang sudah difermentasi, cocok untuk pupuk.'],
        ];

        foreach ($limbah as $l) {
            Limbah::create([
                'kategori_id' => $l['kategori_id'],
                'nama_limbah' => $l['nama_limbah'],
                'slug' => Str::slug($l['nama_limbah']),
                'deskripsi' => $l['deskripsi'],
                'harga' => $l['harga'],
                'stok' => $l['stok'],
                'satuan' => 'kg',
                'status' => 'aktif',
            ]);
        }
    }
}