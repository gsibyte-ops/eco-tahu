<?php

namespace Database\Seeders;

use App\Models\Edukasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EdukasiSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first()
            ?? User::first();

        if (!$admin) {
            $this->command?->warn('Tidak ada user, skip EdukasiSeeder.');
            return;
        }

        $artikels = [
            [
                'judul'        => 'Manfaat Ampas Tahu untuk Pupuk Organik Tanaman',
                'thumbnail'    => 'edukasi/manfaat-ampas-tahu-pupuk-organik.jpg',
                'konten'       => implode("\n\n", [
                    'Ampas tahu (okara) merupakan produk sampingan dari proses penggilingan dan pemerahan kedelai yang masih menyimpan kandungan nitrogen, fosfor, kalium, kalsium, serta protein organik dalam jumlah tinggi. Jika dibuang begitu saja ke saluran air, ampas tahu dapat memicu bau asam dan pencemaran lingkungan, namun melalui proses pengomposan yang tepat, bahan ini berubah menjadi pupuk organik padat berkualitas tinggi.',
                    'Untuk mengolah ampas tahu menjadi kompos, campurkan ampas tahu basah dengan dedak padi atau sekam bakar dengan perbandingan 3:1 untuk menjaga kelembapan, lalu semprotkan larutan dekomposer (EM4 pertanian) dan tetes tebu (molase). Tutup rapat di dalam wadah aerob/anaerob selama 10 hingga 14 hari sambil diaduk setiap tiga hari sekali hingga suhunya stabil dan beraroma tanah segar.',
                    'Pupuk kompos ampas tahu terbukti memperbaiki struktur tanah liat maupun berpasir, meningkatkan populasi mikroba baik di sekitar perakaran, serta mempercepat pertumbuhan daun pada tanaman cabai, sawi, tomat, dan tanaman hias tanpa risiko residu bahan kimia sintetis.',
                ]),
                'status'       => 'publish',
                'published_at' => now()->subDays(5),
            ],
            [
                'judul'        => 'Cara Mengolah Limbah Cair Tahu Menjadi Energi Biogas',
                'thumbnail'    => 'edukasi/cara-mengolah-limbah-tahu-biogas.jpg',
                'konten'       => implode("\n\n", [
                    'Setiap pembuatan satu kuintal kedelai menjadi tahu menghasilkan limbah cair (whey tahu) dalam jumlah besar yang kaya akan senyawa organik terlarut. Melalui teknologi biodigester anaerob, limbah cair tersebut dapat dikonversi menjadi energi biogas terbarukan yang ramah lingkungan sekaligus menurunkan kadar COD dan BOD sebelum air dialirkan kembali ke lingkungan.',
                    'Di dalam kubah reaktor biodigester kedap udara, bakteri metanogenik menguraikan senyawa organik pada limbah cair tahu dan menghasilkan gas metana (CH4) yang dapat disalurkan langsung melalui pipa menuju kompor gas di dapur produksi maupun rumah warga sekitar.',
                    'Penerapan instalasi biogas pada sentra produksi tahu seperti EcoTahu mendukung penuh prinsip Ekonomi Sirkular (SDG 12): menekan biaya bahan bakar perebusan kedelai hingga 40%, menghilangkan bau limbah cair di lingkungan pemukiman, serta menghasilkan lumpur sisa fermentasi (slurry) yang siap dimanfaatkan sebagai pupuk cair organik.',
                ]),
                'status'       => 'publish',
                'published_at' => now()->subDays(3),
            ],
            [
                'judul'        => 'Teknik Pengemasan dan Penyimpanan Tahu Segar agar Tahan Lama Tanpa Pengawet',
                'thumbnail'    => 'edukasi/teknik-pengemasan-tahu-higienis.jpg',
                'konten'       => implode("\n\n", [
                    'Karena diproduksi dari 100% kedelai murni tanpa bahan pengawet sintetis maupun formalin, tahu organik membutuhkan penanganan penyimpanan yang bersih dan bersuhu dingin agar kesegarannya bertahan hingga 5-7 hari di rumah.',
                    'Langkah pertama setelah membeli tahu segar adalah membilas permukaan tahu dengan air matang dingin, kemudian menyimpannya di dalam wadah food-grade kedap udara (BPA-free). Tuangkan air matang dingin atau air mineral baru hingga seluruh potongan tahu terendam sempurna untuk mencegah oksidasi dan pertumbuhan bakteri aerob di permukaan tahu.',
                    'Simpan wadah di rak kulkas bagian chiller (suhu 2-5 derajat Celcius, bukan freezer agar tekstur tahu tidak berongga beku) dan gantilah air rendaman setiap 1 hingga 2 hari sekali. Dengan metode sederhana ini, tahu tetap kenyal, harum kedelai segar, dan tidak mudah berlendir.',
                ]),
                'status'       => 'publish',
                'published_at' => now()->subDays(2),
            ],
            [
                'judul'        => 'Standar Produksi Tahu Organik Higienis dan Ekonomi Sirkular Zero-Waste',
                'thumbnail'    => 'edukasi/standar-produksi-tahu-organik.jpg',
                'konten'       => implode("\n\n", [
                    'Kualitas tahu yang sehat berawal dari pemilihan bahan baku dan sanitasi ruang produksi. EcoTahu menerapkan standar produksi higienis mulai dari penyortiran biji kedelai lokal non-GMO, perendaman dengan air terfiltrasi, hingga penggilingan dan perebusan menggunakan peralatan stainless steel food-grade yang dibersihkan setiap selesai siklus produksi.',
                    'Pada tahap penggumpalan (koagulasi), digunakan asam cuka alami terukur tanpa campuran bahan kimia berbahaya, menghasilkan tekstur tahu yang lembut, padat protein, dan aman dikonsumsi setiap hari oleh seluruh anggota keluarga termasuk balita dan lansia.',
                    'Seluruh residu produksi dikelola dengan konsep Zero-Waste: ampas tahu padat diolah menjadi bahan pangan bernilai gizi (seperti nugget ampas tahu dan tepung okara), pakan ternak berprotein tinggi, serta pupuk kompos organik, sehingga tidak ada limbah yang terbuang mencemari sungai.',
                ]),
                'status'       => 'publish',
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($artikels as $item) {
            ProdukSeeder::syncSeedImage($item['thumbnail']);
            Edukasi::create([
                'user_id'            => $admin->id,
                'judul'              => $item['judul'],
                'slug'               => Str::slug($item['judul']) . '-' . substr(md5($item['judul']), 0, 6),
                'konten'             => $item['konten'],
                'thumbnail'          => $item['thumbnail'],
                'media_type'         => 'image',
                'video_path'         => null,
                'youtube_url'        => null,
                'status'             => $item['status'],
                'scheduled_at'       => null,
                'published_at'       => $item['published_at'],
                'tanggal_mengunggah' => $item['published_at'],
            ]);
        }

        $this->command?->info('EdukasiSeeder: 4 artikel edukasi lengkap dengan thumbnail berhasil dibuat.');
    }
}
