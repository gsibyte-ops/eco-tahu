<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Limbah;
use App\Models\ProdukTahu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    public static function syncSeedImage(string $relativePath): string
    {
        $src = public_path('images/seed/' . $relativePath);
        $dst = storage_path('app/public/' . $relativePath);
        if (File::exists($src)) {
            File::ensureDirectoryExists(dirname($dst));
            File::copy($src, $dst);
        }
        return $relativePath;
    }

    public function run(): void
    {
        $kategoriTahu   = Kategori::where('slug', 'like', 'tahu%')->first() ?? Kategori::first();
        $kategoriLimbah = Kategori::where('slug', 'like', 'limbah%')->first() ?? Kategori::skip(1)->first() ?? $kategoriTahu;

        // ============ PRODUK TAHU ============
        $tahu = [
            [
                'nama'      => 'Tahu Putih Ekonomis',
                'harga'     => 8000,
                'stok'      => 150,
                'gambar'    => 'produk-tahu/tahu-putih-ekonomis.jpg',
                'deskripsi' => 'Tahu putih segar hasil olahan kedelai lokal pilihan yang diproses secara higienis setiap pagi tanpa bahan pengawet maupun pewarna buatan. Memiliki tekstur padat namun tetap lembut di bagian dalam, sangat cocok untuk lauk harian rumah tangga, gorengan, sayur berkuah, maupun usaha kuliner dengan harga ekonomis dan gizi terjaga.',
            ],
            [
                'nama'      => 'Tahu Bakso Spesial Daging Sapi',
                'harga'     => 18500,
                'stok'      => 50,
                'gambar'    => 'produk-tahu/tahu-bakso-spesial.jpg',
                'deskripsi' => 'Perpaduan tahu goreng keemasan EcoTahu dengan isian adonan bakso daging sapi segar pilihan yang kenyal, padat daging, dan berbumbu bawang putih serta rempah alami. Praktis disajikan dengan cara dikukus hangat maupun digoreng renyah sebagai camilan keluarga dan pelengkap hidangan berkuah.',
            ],
            [
                'nama'      => 'Tahu Susu Lembut Bergizi',
                'harga'     => 15000,
                'stok'      => 60,
                'gambar'    => 'produk-tahu/tahu-susu-lembut.jpg',
                'deskripsi' => 'Tahu spesial bertekstur ekstra lembut dan lumer di mulut dengan cita rasa gurih creamy alami. Saat digoreng menghasilkan kulit luar yang tipis renyah sementara bagian dalamnya tetap selembut puding susu kedelai, kaya protein nabati dan disukai anak-anak hingga orang dewasa.',
            ],
            [
                'nama'      => 'Tahu Goreng Gurih Renyah',
                'harga'     => 10000,
                'stok'      => 80,
                'gambar'    => 'produk-tahu/tahu-goreng-gurih-renyah.jpg',
                'deskripsi' => 'Tahu goreng siap santap yang digoreng menggunakan minyak nabati bersih berkualitas hingga berkulit renyah kecokelatan dan berongga lembut di dalam. Gurih alami dari sari kedelai segar tanpa MSG berlebih, nikmat disantap langsung bersama cabai rawit, sambal kecap, atau petis.',
            ],
            [
                'nama'      => 'Tahu Putih Organik Premium',
                'harga'     => 12500,
                'stok'      => 100,
                'gambar'    => 'produk-tahu/tahu-putih-organik-premium.jpg',
                'deskripsi' => 'Tahu putih kualitas tertinggi dari 100% biji kedelai organik non-GMO bersertifikat dan air filtrasi murni, dikentalkan dengan koagulan alami food-grade. Bertekstur halus, padat nutrisi, bebas residu kimia, serta ideal untuk menu MPASI, diet sehat tinggi protein, pepes, maupun masakan tumis premium.',
            ],
        ];

        foreach ($tahu as $t) {
            self::syncSeedImage($t['gambar']);
            ProdukTahu::create([
                'kategori_id' => $kategoriTahu->id,
                'nama_produk' => $t['nama'],
                'slug'        => Str::slug($t['nama']) . '-' . substr(md5($t['nama']), 0, 6),
                'deskripsi'   => $t['deskripsi'],
                'harga'       => $t['harga'],
                'stok'        => $t['stok'],
                'satuan'      => 'pcs',
                'gambar'      => $t['gambar'],
                'status'      => 'aktif',
            ]);
        }

        // ============ LIMBAH ============
        $limbah = [
            [
                'nama'      => 'Ampas Tahu Kering Premium',
                'harga'     => 5000,
                'stok'      => 120,
                'satuan'    => 'kg',
                'gambar'    => 'limbah/ampas-tahu-kering-premium.jpg',
                'deskripsi' => 'Ampas tahu (okara) pilihan yang dikeringkan secara higienis dengan kadar air rendah sehingga tahan disimpan lebih lama tanpa mudah berjamur. Mengandung serat kasar dan protein nabati tinggi (20-24%), sangat ekonomis sebagai campuran konsentrat pakan sapi, kambing, domba, dan unggas untuk mempercepat pertambahan bobot ternak.',
            ],
            [
                'nama'      => 'Ampas Tahu Kering Halus',
                'harga'     => 6000,
                'stok'      => 100,
                'satuan'    => 'kg',
                'gambar'    => 'limbah/ampas-tahu-kering-halus.jpg',
                'deskripsi' => 'Tepung ampas tahu (okara flour) hasil pengeringan suhu terkontrol dan penggilingan halus dari ampas kedelai segar. Tinggi serat pangan, rendah karbohidrat, dan bebas gluten, cocok diolah kembali menjadi bahan campuran nugget sayur, cookies sehat, kerupuk ampas tahu, maupun pelet pakan ikan dan unggas berkualitas.',
            ],
            [
                'nama'      => 'Ampas Tahu Basah Segar',
                'harga'     => 3000,
                'stok'      => 80,
                'satuan'    => 'kg',
                'gambar'    => 'limbah/ampas-tahu-basah-segar.jpg',
                'deskripsi' => 'Ampas tahu basah segar yang diambil langsung dari sisa pemerahan susu kedelai produksi pagi hari di pabrik EcoTahu. Beraroma kedelai segar dan tidak asam, siap digunakan langsung sebagai bahan baku tempe gembus, nugget ampas tahu, campuran pakan ternak harian, maupun bahan dasar kompos organik.',
            ],
            [
                'nama'      => 'Ampas Tahu Fermentasi',
                'harga'     => 8000,
                'stok'      => 60,
                'satuan'    => 'kg',
                'gambar'    => 'limbah/ampas-tahu-fermentasi.jpg',
                'deskripsi' => 'Olahan ampas tahu yang telah difermentasi secara anaerob bersama mikroorganisme probiotik (EM4 dan molase) sehingga memiliki daya cerna nutrisi lebih tinggi dan aroma fermentasi segar yang disukai ternak. Siap pakai sebagai pakan probiotik ternak ruminansia maupun pupuk kompos padat penyubur tanaman pertanian dan perkebunan.',
            ],
        ];

        foreach ($limbah as $l) {
            self::syncSeedImage($l['gambar']);
            Limbah::create([
                'kategori_id' => $kategoriLimbah->id,
                'nama_limbah' => $l['nama'],
                'slug'        => Str::slug($l['nama']) . '-' . substr(md5($l['nama']), 0, 6),
                'deskripsi'   => $l['deskripsi'],
                'harga'       => $l['harga'],
                'stok'        => $l['stok'],
                'satuan'      => $l['satuan'],
                'gambar'      => $l['gambar'],
                'status'      => 'aktif',
            ]);
        }
    }
}
