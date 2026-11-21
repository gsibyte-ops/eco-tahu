<?php

namespace Database\Seeders;

use App\Models\Resep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResepSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first()
            ?? User::whereHas('role', fn ($q) => $q->where('nama_role', 'admin'))->first()
            ?? User::first();

        if (!$admin) {
            $this->command?->warn('Tidak ada user, skip ResepSeeder.');
            return;
        }

        $reseps = [
            [
                'judul'             => 'Tahu Crispy Bawang Putih Renyah',
                'deskripsi'         => 'Camilan sekaligus lauk rumahan favorit dari tahu putih organik EcoTahu yang renyah keemasan di luar dan tetap lembut gurih di dalam.',
                'bahan'             => implode("\n", [
                    '2 pack Tahu Putih Organik EcoTahu, potong dadu',
                    '5 sdm tepung terigu protein sedang',
                    '2 sdm tepung maizena agar ekstra renyah',
                    '4 siung bawang putih, haluskan',
                    '1/2 sdt kaldu jamur dan 1/2 sdt garam',
                    '1/4 sdt lada putih bubuk',
                    '150 ml air es untuk adonan pencelup',
                    '2 batang daun bawang, iris tipis',
                    'Minyak goreng secukupnya',
                ]),
                'langkah'           => implode("\n", [
                    'Tiriskan potongan tahu putih di atas tisu dapur selama 5 menit agar kadar air berkurang.',
                    'Campur tepung terigu, maizena, bawang putih halus, garam, kaldu jamur, dan lada, lalu tuang air es sedikit demi sedikit hingga cair encer.',
                    'Rendam potongan tahu dalam larutan bumbu selama 5 menit agar meresap.',
                    'Panaskan minyak banyak dengan api sedang-besar, goreng tahu hingga berkulit kokoh.',
                    'Siramkan sisa larutan tepung encer secara bertahap sambil diaduk agar terbentuk remahan krispi.',
                    'Angkat saat berwarna kuning keemasan, tiriskan, lalu taburi irisan daun bawang dan sajikan bersama sambal kecap.',
                ]),
                'durasi_menit'      => 15,
                'porsi'             => 4,
                'tingkat_kesulitan' => 'mudah',
                'media_type'        => 'image',
                'status'            => 'publish',
                'published_at'      => now()->subDays(4),
            ],
            [
                'judul'             => 'Pepes Tahu Kemangi Harum Daun Pisang',
                'deskripsi'         => 'Olahan tahu kukus sehat tanpa minyak goreng berlebih dengan aroma wangi daun kemangi segar dan bumbu rempah Nusantara.',
                'bahan'             => implode("\n", [
                    '2 pack Tahu Putih Organik EcoTahu, haluskan',
                    '1 butir telur ayam, kocok lepas',
                    '1 ikat daun kemangi segar, petik daunnya',
                    '5 butir bawang merah dan 3 siung bawang putih, haluskan',
                    '3 buah cabai merah keriting, iris serong',
                    '2 butir kemiri sangrai, haluskan',
                    '1 buah tomat merah, potong dadu kecil',
                    '1 sdt garam, 1/2 sdt gula, dan 1/2 sdt kaldu jamur',
                    'Daun pisang dan tusuk lidi untuk membungkus',
                ]),
                'langkah'           => implode("\n", [
                    'Hancurkan tahu putih menggunakan garpu hingga lembut, lalu peras sedikit jika terlalu berair.',
                    'Campurkan tahu halus dengan bumbu halus, telur kocok, potongan tomat, cabai, garam, gula, dan kaldu jamur hingga rata.',
                    'Masukkan daun kemangi segar lalu aduk perlahan agar daun tidak layu hancur.',
                    'Ambil 2-3 sdm adonan, letakkan di atas daun pisang yang sudah dilayukan di atas api kecil, lalu gulung dan semat ujungnya dengan lidi.',
                    'Kukus pepes selama 25 menit hingga matang dan wangi.',
                    'Opsional: panggang sebentar di atas teflon agar aroma daun pisang semakin harum sebelum disajikan.',
                ]),
                'durasi_menit'      => 30,
                'porsi'             => 4,
                'tingkat_kesulitan' => 'mudah',
                'media_type'        => 'image',
                'status'            => 'publish',
                'published_at'      => now()->subDays(3),
            ],
            [
                'judul'             => 'Nugget Ampas Tahu Wortel Zero-Waste',
                'deskripsi'         => 'Kreasi olahan ekonomi sirkular (SDG 12) dari ampas tahu segar kaya serat menjadi nugget sayuran gurih yang hemat dan disukai anak-anak.',
                'bahan'             => implode("\n", [
                    '250 gram Ampas Tahu Basah Segar EcoTahu',
                    '1 buah wortel ukuran sedang, parut halus',
                    '2 batang daun bawang, iris halus',
                    '2 butir telur ayam',
                    '4 sdm tepung terigu dan 2 sdm tepung tapioka',
                    '3 siung bawang putih, haluskan',
                    '1 sdt kaldu jamur, 1/2 sdt garam, dan 1/2 sdt lada bubuk',
                    '100 gram tepung panir / tepung roti untuk baluran',
                ]),
                'langkah'           => implode("\n", [
                    'Kukus ampas tahu basah selama 10 menit untuk menghilangkan aroma langu, lalu dinginkan dan peras air berlebihnya.',
                    'Aduk rata ampas tahu, wortel parut, daun bawang, bawang putih halus, 1 butir telur, tepung terigu, tapioka, dan bumbu.',
                    'Tuang adonan ke dalam loyang beroles tipis minyak, ratakan, lalu kukus selama 20 menit hingga padat.',
                    'Dinginkan adonan, kemudian potong kotak sesuai selera.',
                    'Celupkan potongan nugget ke dalam 1 butir telur kocok, lalu gulingkan di atas tepung panir hingga terbalut rata.',
                    'Goreng dalam minyak panas hingga keemasan atau simpan di freezer sebagai stok lauk praktis.',
                ]),
                'durasi_menit'      => 35,
                'porsi'             => 5,
                'tingkat_kesulitan' => 'sedang',
                'media_type'        => 'image',
                'status'            => 'publish',
                'published_at'      => now()->subDays(2),
            ],
            [
                'judul'             => 'Sapo Tahu Susu Saus Tiram Jamur',
                'deskripsi'         => 'Hidangan hangat berkuah kental gurih ala restoran yang memadukan kelembutan Tahu Susu EcoTahu dengan aneka sayuran segar.',
                'bahan'             => implode("\n", [
                    '2 pack Tahu Susu Lembut EcoTahu, potong tebal',
                    '100 gram jamur kancing atau jamur kuping, iris',
                    '1 ikat pakcoy dan 1 buah wortel, potong sesuai selera',
                    '3 siung bawang putih, cincang halus',
                    '1/2 buah bawang bombay, iris memanjang',
                    '2 sdm saus tiram dan 1 sdm kecap asin',
                    '1 sdm minyak wijen dan 1/2 sdt lada putih',
                    '250 ml air kaldu dan 1 sdm maizena larutkan dengan sedikit air',
                ]),
                'langkah'           => implode("\n", [
                    'Goreng sebentar potongan tahu susu hingga bagian luar berkulit ringan agar tidak mudah hancur saat dimasak, lalu sisihkan.',
                    'Tumis bawang putih dan bawang bombay hingga harum dengan sedikit minyak.',
                    'Masukkan wortel dan jamur, aduk rata, lalu tuang air kaldu, saus tiram, kecap asin, dan lada.',
                    'Masak hingga wortel mulai empuk, lalu masukkan pakcoy dan tahu susu goreng.',
                    'Tuang larutan maizena perlahan sambil diaduk pelan hingga kuah mengental mengilap.',
                    'Tambahkan minyak wijen sesaat sebelum api dimatikan, lalu sajikan hangat bersama nasi putih.',
                ]),
                'durasi_menit'      => 25,
                'porsi'             => 3,
                'tingkat_kesulitan' => 'sedang',
                'media_type'        => 'image',
                'status'            => 'publish',
                'published_at'      => now()->subDay(),
            ],
        ];

        $created = 0;
        foreach ($reseps as $item) {
            $exists = Resep::where('judul', $item['judul'])->exists();
            if ($exists) {
                continue;
            }

            Resep::create(array_merge($item, [
                'user_id' => $admin->id,
                'slug'    => Str::slug($item['judul']) . '-' . substr(md5($item['judul']), 0, 8),
            ]));
            $created++;
        }

        $this->command?->info("ResepSeeder: {$created} resep baru ditambahkan (total: " . Resep::count() . ').');
    }
}
