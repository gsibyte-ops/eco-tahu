<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Limbah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LimbahSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriLimbah = Kategori::where('slug', 'like', 'limbah%')->first() ?? Kategori::first();

        $limbah = [
            [
                'nama_limbah' => 'Ampas Tahu Kering Premium',
                'harga'       => 5000,
                'stok'        => 120,
                'gambar'      => 'limbah/ampas-tahu-kering-premium.jpg',
                'deskripsi'   => 'Ampas tahu (okara) pilihan yang dikeringkan secara higienis dengan kadar air rendah sehingga tahan disimpan lebih lama tanpa mudah berjamur. Mengandung serat kasar dan protein nabati tinggi (20-24%), sangat ekonomis sebagai campuran konsentrat pakan sapi, kambing, domba, dan unggas untuk mempercepat pertambahan bobot ternak.',
            ],
            [
                'nama_limbah' => 'Ampas Tahu Kering Halus',
                'harga'       => 6000,
                'stok'        => 100,
                'gambar'      => 'limbah/ampas-tahu-kering-halus.jpg',
                'deskripsi'   => 'Tepung ampas tahu (okara flour) hasil pengeringan suhu terkontrol dan penggilingan halus dari ampas kedelai segar. Tinggi serat pangan, rendah karbohidrat, dan bebas gluten, cocok diolah kembali menjadi bahan campuran nugget sayur, cookies sehat, kerupuk ampas tahu, maupun pelet pakan ikan dan unggas berkualitas.',
            ],
            [
                'nama_limbah' => 'Ampas Tahu Basah Segar',
                'harga'       => 3000,
                'stok'        => 80,
                'gambar'      => 'limbah/ampas-tahu-basah-segar.jpg',
                'deskripsi'   => 'Ampas tahu basah segar yang diambil langsung dari sisa pemerahan susu kedelai produksi pagi hari di pabrik EcoTahu. Beraroma kedelai segar dan tidak asam, siap digunakan langsung sebagai bahan baku tempe gembus, nugget ampas tahu, campuran pakan ternak harian, maupun bahan dasar kompos organik.',
            ],
            [
                'nama_limbah' => 'Ampas Tahu Fermentasi',
                'harga'       => 8000,
                'stok'        => 60,
                'gambar'      => 'limbah/ampas-tahu-fermentasi.jpg',
                'deskripsi'   => 'Olahan ampas tahu yang telah difermentasi secara anaerob bersama mikroorganisme probiotik (EM4 dan molase) sehingga memiliki daya cerna nutrisi lebih tinggi dan aroma fermentasi segar yang disukai ternak. Siap pakai sebagai pakan probiotik ternak ruminansia maupun pupuk kompos padat penyubur tanaman pertanian dan perkebunan.',
            ],
        ];

        foreach ($limbah as $l) {
            ProdukSeeder::syncSeedImage($l['gambar']);
            Limbah::updateOrCreate(
                ['nama_limbah' => $l['nama_limbah']],
                [
                    'kategori_id' => $kategoriLimbah?->id ?? 2,
                    'slug'        => Str::slug($l['nama_limbah']) . '-' . substr(md5($l['nama_limbah']), 0, 6),
                    'deskripsi'   => $l['deskripsi'],
                    'harga'       => $l['harga'],
                    'stok'        => $l['stok'],
                    'satuan'      => 'kg',
                    'gambar'      => $l['gambar'],
                    'status'      => 'aktif',
                ]
            );
        }
    }
}
