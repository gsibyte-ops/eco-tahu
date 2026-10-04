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

        if (! $admin) {
            $this->command->warn('Tidak ada user, skip EdukasiSeeder.');
            return;
        }

        $artikels = [
            [
                'judul'        => 'Manfaat Ampas Tahu untuk Pupuk Organik',
                'konten'       => 'Ampas tahu mengandung protein dan nutrisi yang sangat baik untuk tanaman. Pupuk organik dari ampas tahu dapat meningkatkan kesuburan tanah dan mengurangi penggunaan pupuk kimia.',
                'status'       => 'publish',
                'published_at' => now()->subDays(5),
            ],
            [
                'judul'        => 'Cara Mengolah Limbah Tahu Menjadi Biogas',
                'konten'       => 'Limbah cair tahu dapat difermentasi menjadi biogas yang ramah lingkungan. Proses ini melibatkan bakteri metanogenik dalam kondisi anaerob untuk menghasilkan gas metana.',
                'status'       => 'publish',
                'published_at' => now()->subDays(2),
            ],
            [
                'judul'        => 'Teknik Pengemasan Tahu agar Tahan Lama',
                'konten'       => 'Pengemasan yang tepat dapat memperpanjang masa simpan tahu. Gunakan wadah kedap udara dan simpan pada suhu rendah untuk menjaga kualitas produk.',
                'status'       => 'scheduled',
                'scheduled_at' => now()->addDays(2),
            ],
            [
                'judul'        => 'Strategi Pemasaran Tahu di Era Digital',
                'konten'       => 'Artikel ini masih dalam proses penulisan. Akan membahas strategi pemasaran online untuk produk tahu dan cara memanfaatkan media sosial.',
                'status'       => 'draft',
            ],
        ];

        foreach ($artikels as $item) {
            Edukasi::create([
                'user_id'            => $admin->id,
                'judul'              => $item['judul'],
                'slug'               => Str::slug($item['judul']) . '-' . uniqid(),
                'konten'             => $item['konten'],
                'thumbnail'          => null,
                'status'             => $item['status'],
                'scheduled_at'       => $item['scheduled_at'] ?? null,
                'published_at'       => $item['published_at'] ?? null,
                'tanggal_mengunggah' => now(),
            ]);
        }

        $this->command->info('EdukasiSeeder: 4 artikel berhasil dibuat (2 publish, 1 scheduled, 1 draft).');
    }
}