<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Refund;
use App\Models\ProdukTahu;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        $dataPesanan = [
            ['user_id' => 2, 'kode' => 'ETI123456', 'items' => [[1, 2], [2, 2]], 'payment_method' => 'Transfer', 'payment_status' => 'pending', 'order_status' => 'pending', 'jarak_km' => 5, 'refund' => false],
            ['user_id' => 3, 'kode' => 'ETI123455', 'items' => [[1, 3]], 'payment_method' => 'COD', 'payment_status' => 'pending', 'order_status' => 'diproses', 'jarak_km' => 3, 'refund' => false],
            ['user_id' => 4, 'kode' => 'ETI123454', 'items' => [[3, 2], [4, 2]], 'payment_method' => 'Transfer', 'payment_status' => 'paid', 'order_status' => 'selesai', 'jarak_km' => 8, 'refund' => false],
            ['user_id' => 5, 'kode' => 'ETI123453', 'items' => [[2, 2]], 'payment_method' => 'Transfer', 'payment_status' => 'paid', 'order_status' => 'dibatalkan', 'jarak_km' => 4, 'refund' => true],
        ];

        foreach ($dataPesanan as $dp) {
            $subtotal = 0;
            $details = [];

            foreach ($dp['items'] as $item) {
                $produk = ProdukTahu::find($item[0]);
                $sub = $produk->harga * $item[1];
                $subtotal += $sub;
                $details[] = [
                    'item_type' => ProdukTahu::class,
                    'item_id' => $produk->id,
                    'nama_item' => $produk->nama_produk,
                    'harga_satuan' => $produk->harga,
                    'jumlah' => $item[1],
                    'subtotal' => $sub,
                ];
            }

            $ongkir = $dp['jarak_km'] * 5000;
            $total = $subtotal + $ongkir;

            $pesanan = Pesanan::create([
                'user_id' => $dp['user_id'],
                'kode_pesanan' => $dp['kode'],
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'jarak_km' => $dp['jarak_km'],
                'total_harga' => $total,
                'payment_method' => $dp['payment_method'],
                'payment_status' => $dp['payment_status'],
                'order_status' => $dp['order_status'],
                'alamat_pengiriman' => 'Alamat dummy untuk ' . $dp['kode'],
                'tanggal_order' => now()->subDays(rand(1, 7)),
            ]);

            foreach ($details as $d) {
                $d['pesanan_id'] = $pesanan->id;
                DetailPesanan::create($d);
            }

            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode_pembayaran' => $dp['payment_method'],
                'status_pembayaran' => $dp['payment_status'] === 'paid' ? 'verified' : 'pending',
                'jumlah_bayar' => $total,
                'tanggal_bayar' => $dp['payment_status'] === 'paid' ? now() : null,
            ]);

            // Bikin refund untuk pesanan yang dibatalkan
            if ($dp['refund']) {
                Refund::create([
                    'pesanan_id' => $pesanan->id,
                    'nominal_refund' => $total,
                    'alasan_batal' => 'Berubah pikiran, ingin ganti produk lain.',
                    'status_refund' => 'pending',
                ]);
            }
        }
    }
}