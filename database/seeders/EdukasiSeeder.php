<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Edukasi;
use Illuminate\Support\Str;

class EdukasiSeeder extends Seeder
{
    public function run(): void
    {
        $artikel = [
            [
                'judul' => 'Cara Mengolah Ampas Tahu Menjadi Pakan Ternak Berkualitas',
                'konten' => 'Ampas tahu merupakan limbah padat dari proses pembuatan tahu yang masih mengandung protein cukup tinggi. Ampas tahu dapat diolah menjadi pakan ternak berkualitas dengan beberapa langkah sederhana...',
            ],
            [
                'judul' => 'Manfaat Ampas Tahu untuk Pupuk Organik Tanaman',
                'konten' => 'Ampas tahu mengandung nitrogen, fosfor, dan kalium yang baik untuk tanaman. Dengan fermentasi sederhana, ampas tahu bisa menjadi pupuk organik ramah lingkungan...',
            ],
            [
                'judul' => 'Mendukung SDG 12: Konsumsi dan Produksi yang Bertanggung Jawab',
                'konten' => 'SDG 12 adalah salah satu tujuan pembangunan berkelanjutan yang menekankan pentingnya pola konsumsi dan produksi yang bertanggung jawab. Industri tahu dapat berkontribusi dengan mengolah limbahnya...',
            ],
            [
                'judul' => 'Resep Olahan Tahu Sehat untuk Keluarga',
                'konten' => 'Tahu merupakan sumber protein nabati yang murah dan mudah diolah. Berikut beberapa resep olahan tahu sehat yang bisa Anda coba di rumah...',
            ],
        ];

        foreach ($artikel as $a) {
            Edukasi::create([
                'user_id' => 1,
                'judul' => $a['judul'],
                'slug' => Str::slug($a['judul']),
                'konten' => $a['konten'],
                'status' => 'publish',
            ]);
        }
    }
}