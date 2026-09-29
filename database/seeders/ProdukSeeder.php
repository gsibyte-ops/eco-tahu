<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Limbah;
use App\Models\ProdukTahu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriTahu   = Kategori::where('slug', 'tahu')->first();
        $kategoriLimbah = Kategori::where('slug', 'limbah')->first();

        // ============ PRODUK TAHU ============
        $tahu = [
            ['Tahu Putih Ekonomis',          8000,  150, 'Tahu putih harga terjangkau kualitas tetap oke.'],
            ['Tahu Bakso Spesial Daging Sapi', 18500, 46,  'Tahu bakso isi daging sapi pilihan.'],
            ['Tahu Susu Lembut Bergizi',     15000, 55,  'Tahu susu dengan tekstur lembut.'],
            ['Tahu Goreng Gurih Renyah',     10000, 80,  'Tahu goreng dengan tekstur renyah.'],
            ['Tahu Putih Organik Premium',   12500, 100, 'Tahu putih premium hasil kedelai organik.'],
        ];

        foreach ($tahu as $t) {
            ProdukTahu::create([
                'kategori_id' => $kategoriTahu->id,
                'nama_produk' => $t[0],
                'slug'        => Str::slug($t[0]) . '-' . uniqid(),
                'deskripsi'   => $t[3],
                'harga'       => $t[1],
                'stok'        => $t[2],
                'satuan'      => 'pcs',
                'status'      => 'aktif',
            ]);
        }

        // ============ LIMBAH ============
        $limbah = [
            ['Ampas Tahu Kering Premium',  5000, 120, 'kg', 'Ampas tahu kering kualitas premium.'],
            ['Ampas Tahu Kering Halus',    6000, 100, 'kg', 'Ampas tahu kering yang sudah dihaluskan.'],
            ['Ampas Tahu Basah Segar',     3000, 80,  'kg', 'Ampas tahu basah segar langsung dari produksi.'],
            ['Ampas Tahu Fermentasi',      8000, 60,  'kg', 'Ampas tahu fermentasi siap jadi pakan ternak.'],
        ];

        foreach ($limbah as $l) {
            Limbah::create([
                'kategori_id' => $kategoriLimbah->id,
                'nama_limbah' => $l[0],
                'slug'        => Str::slug($l[0]) . '-' . uniqid(),
                'deskripsi'   => $l[4],
                'harga'       => $l[1],
                'stok'        => $l[2],
                'satuan'      => $l[3],
                'status'      => 'aktif',
            ]);
        }
    }
}