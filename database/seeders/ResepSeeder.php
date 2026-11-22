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
                'thumbnail'         => 'resep/tahu-crispy-bawang-putih.jpg',
                'deskripsi'         => 'Camilan sekaligus lauk rumahan favorit dari tahu putih organik EcoTahu yang renyah keemasan di luar dan tetap lembut gurih di dalam dengan aroma bawang putih cincang yang menggugah selera.',
                'bahan'             => implode("\n", [
                    '2 pack Tahu Putih Organik EcoTahu, potong dadu 3 cm',
                    '5 sdm tepung terigu protein sedang',
                    '2 sdm tepung maizena agar ekstra renyah',
                    '1/2 sdt baking powder (rahasia keriting krispi)',
                    '4 siung bawang putih, haluskan',
                    '1/2 sdt kaldu jamur dan 1/2 sdt garam',
                    '1/4 sdt lada putih bubuk',
                    '250 ml air es untuk adonan pencelup encer',
                    '2 batang daun bawang dan 3 cabai rawit, iris tipis',
                    'Minyak goreng bersih secukupnya',
                ]),
                'langkah'           => implode("\n", [
                    'Tiriskan potongan tahu putih di atas tisu dapur selama 5 menit agar kadar air permukaan berkurang.',
                    'Campur tepung terigu, maizena, baking powder, bawang putih halus, garam, kaldu jamur, dan lada, lalu tuang air es hingga menjadi larutan encer.',
                    'Rendam potongan tahu dalam larutan bumbu selama 5 menit agar meresap sempurna, lalu tiriskan tahu dan simpan sisa air larutan tepung.',
                    'Panaskan minyak banyak dengan api sedang-besar, goreng tahu hingga berkulit kokoh kecokelatan.',
                    'Tuangkan 1 sendok sayur sisa larutan tepung encer dari jarak agak tinggi sambil diaduk cepat agar remahan krispi menempel di permukaan tahu. Ulangi 2-3 kali.',
                    'Angkat saat berwarna kuning keemasan dan kering renyah, tiriskan, lalu taburi irisan daun bawang serta cabai rawit.',
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
                'thumbnail'         => 'resep/pepes-tahu-kemangi.jpg',
                'deskripsi'         => 'Olahan tahu kukus sehat tanpa minyak goreng dengan aroma wangi daun kemangi segar, bumbu rempah Nusantara, dan balutan daun pisang panggang yang khas.',
                'bahan'             => implode("\n", [
                    '2 pack Tahu Putih Organik EcoTahu, haluskan',
                    '1 butir telur ayam, kocok lepas',
                    '1 ikat daun kemangi segar, petik daunnya',
                    '5 butir bawang merah dan 3 siung bawang putih, haluskan',
                    '3 buah cabai merah keriting, iris serong',
                    '2 butir kemiri sangrai dan 1 cm kunyit bakar, haluskan',
                    '1 buah tomat merah segar, potong dadu kecil',
                    '2 lembar daun salam dan 2 batang serai, iris bagian putihnya',
                    '1 sdt garam, 1/2 sdt gula pasir, dan 1/2 sdt kaldu jamur',
                    'Daun pisang dan tusuk lidi untuk membungkus',
                ]),
                'langkah'           => implode("\n", [
                    'Hancurkan tahu putih menggunakan garpu hingga lembut, lalu peras sedikit dengan kain bersih jika kadar airnya berlebih.',
                    'Campurkan tahu halus dengan bumbu halus, telur kocok, potongan tomat, irisan cabai, garam, gula, dan kaldu jamur hingga merata.',
                    'Masukkan daun kemangi segar lalu aduk perlahan agar daun tetap utuh dan tidak memar.',
                    'Siapkan selembar daun pisang yang sudah dilayukan sebentar di atas api, beri potongan daun salam dan serai, lalu letakkan 3 sdm adonan tahu.',
                    'Gulung daun pisang hingga padat dan semat kedua ujungnya dengan tusuk lidi.',
                    'Kukus pepes tahu selama 25 menit hingga matang sempurna, lalu panggang 3 menit di atas teflon agar aroma daun pisang semakin harum.',
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
                'thumbnail'         => 'resep/nugget-ampas-tahu-wortel.jpg',
                'deskripsi'         => 'Kreasi kuliner ekonomi sirkular (SDG 12) yang menyulap ampas tahu segar kaya serat menjadi nugget sayuran keemasan yang gurih, lembut di dalam, dan disukai anak-anak.',
                'bahan'             => implode("\n", [
                    '250 gram Ampas Tahu Basah Segar EcoTahu (atau 100 gram Ampas Tahu Kering Halus seduh air hangat)',
                    '1 buah wortel ukuran sedang, parut halus',
                    '2 batang daun bawang dan 1 batang seledri, iris halus',
                    '2 butir telur ayam (1 untuk adonan, 1 untuk pencelup)',
                    '4 sdm tepung terigu dan 2 sdm tepung tapioka',
                    '3 siung bawang putih, haluskan',
                    '1 sdt kaldu jamur, 1/2 sdt garam, dan 1/2 sdt lada putih bubuk',
                    '100 gram tepung panir / tepung roti kasar untuk baluran',
                ]),
                'langkah'           => implode("\n", [
                    'Kukus ampas tahu basah selama 10 menit untuk menjaga kesegaran dan menghilangkan aroma langu, lalu dinginkan dan peras air berlebihnya.',
                    'Aduk rata ampas tahu, wortel parut, daun bawang, seledri, bawang putih halus, 1 butir telur, tepung terigu, tapioka, garam, kaldu jamur, dan lada.',
                    'Tuang adonan ke dalam loyang kotak beroles tipis minyak sayur, padatkan permukaannya, lalu kukus selama 20 menit hingga matang.',
                    'Angkat dan dinginkan adonan hingga benar-benar set agar rapi saat dipotong kotak.',
                    'Celupkan setiap potongan nugget ke dalam kocokan telur, lalu gulingkan di atas tepung panir sambil ditekan ringan agar menempel kuat.',
                    'Simpan 15 menit di kulkas agar panir terkunci, lalu goreng dalam minyak panas api sedang hingga kuning keemasan.',
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
                'thumbnail'         => 'resep/sapo-tahu-susu-jamur.jpg',
                'deskripsi'         => 'Hidangan hangat berkuah kental gurih ala restoran oriental yang memadukan kelembutan Tahu Susu Lembut EcoTahu dengan jamur kancing, pakcoy segar, dan wortel.',
                'bahan'             => implode("\n", [
                    '2 pack Tahu Susu Lembut Bergizi EcoTahu, potong dadu tebal',
                    '100 gram jamur kancing segar atau jamur kuping, iris',
                    '1 ikat baby pakcoy dan 1 buah wortel, iris serong tipis',
                    '3 siung bawang putih dan 1 cm jahe segar, cincang halus',
                    '1/2 buah bawang bombay, iris memanjang',
                    '2 sdm saus tiram dan 1 sdm kecap asin',
                    '1 sdm minyak wijen dan 1/2 sdt lada putih bubuk',
                    '300 ml air kaldu hangat dan 1 sdm tepung maizena (larutkan dengan 2 sdm air)',
                ]),
                'langkah'           => implode("\n", [
                    'Goreng sebentar potongan Tahu Susu Lembut dalam minyak panas hingga berkulit tipis keemasan agar tidak mudah hancur saat ditumis, lalu tiriskan.',
                    'Panaskan 2 sdm minyak, tumis bawang putih cincang, jahe, dan bawang bombay hingga harum.',
                    'Masukkan irisan wortel dan jamur, tumis selama 2 menit, lalu tuangkan air kaldu hangat.',
                    'Bumbui dengan saus tiram, kecap asin, dan lada putih bubuk. Masak hingga wortel mulai empuk.',
                    'Masukkan baby pakcoy dan tahu susu goreng, aduk perlahan agar tahu tetap utuh.',
                    'Tuang larutan maizena sambil diaduk pelan hingga kuah mengental mengilap, beri minyak wijen sesaat sebelum diangkat, lalu sajikan hangat.',
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
            ProdukSeeder::syncSeedImage($item['thumbnail']);
            Resep::updateOrCreate(
                ['judul' => $item['judul']],
                array_merge($item, [
                    'user_id' => $admin->id,
                    'slug'    => Str::slug($item['judul']) . '-' . substr(md5($item['judul']), 0, 8),
                ])
            );
            $created++;
        }

        $this->command?->info("ResepSeeder: {$created} resep beserta thumbnail berhasil disinkronkan.");
    }
}
