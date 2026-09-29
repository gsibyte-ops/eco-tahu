<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Limbah;
use App\Models\User;
use Illuminate\Database\Seeder;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = User::where('role_id', 2)->get();
        $produk = ProdukTahu::where('status', 'aktif')->get();
        $limbah = Limbah::where('status', 'aktif')->get();

        if ($pelanggan->isEmpty() || $produk->isEmpty()) {
            return;
        }

        // Bikin 10 pesanan random
        for ($i = 0; $i < 10; $i++) {
            $user = $pelanggan->random();
            $tanggal = now()->subDays(rand(0, 14))->subHours(rand(1, 23));
            $paymentMethod = rand(0, 1) ? 'COD' : 'Transfer';
            $deliveryType = rand(0, 1) ? 'pickup' : 'delivery';
            $butuhOngkir = ($paymentMethod === 'Transfer') || ($paymentMethod === 'COD' && $deliveryType === 'delivery');

            $jarak = $butuhOngkir ? rand(1, 10) : 0;
            $ongkir = $butuhOngkir ? max($jarak * 2500, 5000) : 0;

            $subtotal = 0;
            $items = [];

            $jumlahItem = rand(1, 3);
            for ($j = 0; $j < $jumlahItem; $j++) {
                $pakaiLimbah = rand(0, 1) && $limbah->isNotEmpty();
                $produk_pilih = $pakaiLimbah ? $limbah->random() : $produk->random();
                $model = $pakaiLimbah ? Limbah::class : ProdukTahu::class;
                $nama = $pakaiLimbah ? $produk_pilih->nama_limbah : $produk_pilih->nama_produk;
                $qty = rand(1, 3);
                $itemSubtotal = $produk_pilih->harga * $qty;
                $subtotal += $itemSubtotal;

                $items[] = [
                    'item_type'    => $model,
                    'item_id'      => $produk_pilih->id,
                    'nama_item'    => $nama,
                    'harga_satuan' => $produk_pilih->harga,
                    'jumlah'       => $qty,
                    'subtotal'     => $itemSubtotal,
                ];
            }

            $total = $subtotal + $ongkir;

            $statusPilihan = ['pending', 'diproses', 'dikirim', 'selesai', 'selesai'];
            $orderStatus = $statusPilihan[array_rand($statusPilihan)];
            $paymentStatus = in_array($orderStatus, ['selesai']) ? 'paid' : ($orderStatus === 'pending' ? 'pending' : 'paid');

            $kodePesanan = 'ETI' . $tanggal->format('ymdHis') . rand(10, 99);

            $pesanan = Pesanan::create([
                'user_id'          => $user->id,
                'kode_pesanan'     => $kodePesanan,
                'subtotal'         => $subtotal,
                'ongkir'           => $ongkir,
                'jarak_km'         => $jarak,
                'total_harga'      => $total,
                'payment_method'   => $paymentMethod,
                'delivery_type'    => $deliveryType,
                'bank_tujuan'      => $paymentMethod === 'Transfer' ? ['BCA', 'BRI', 'Mandiri', 'BNI'][array_rand(['BCA', 'BRI', 'Mandiri', 'BNI'])] : null,
                'va_number'        => $paymentMethod === 'Transfer' ? '8808' . rand(10000000, 99999999) : null,
                'payment_status'   => $paymentStatus,
                'order_status'     => $orderStatus,
                'alamat_pengiriman' => $user->alamat . ' | Penerima: ' . $user->username,
                'tanggal_order'    => $tanggal,
                'expired_at'       => $paymentMethod === 'Transfer' ? $tanggal->copy()->addHours(24) : null,
            ]);

            foreach ($items as $item) {
                DetailPesanan::create(array_merge($item, ['pesanan_id' => $pesanan->id]));
            }

            Pembayaran::create([
                'pesanan_id'        => $pesanan->id,
                'metode_pembayaran' => $paymentMethod,
                'status_pembayaran' => $paymentStatus === 'paid' ? 'verified' : 'pending',
                'jumlah_bayar'      => $total,
                'tanggal_bayar'     => $paymentStatus === 'paid' ? $tanggal : null,
            ]);
        }
    }
}