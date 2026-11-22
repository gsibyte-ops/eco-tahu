<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Limbah;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Review;
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

        $selesaiPedidos = [];

        // Bikin 10 pesanan realistis
        for ($i = 0; $i < 10; $i++) {
            $user = $pelanggan[$i % $pelanggan->count()];
            $tanggal = now()->subDays(10 - $i)->subHours(rand(1, 12));
            $paymentMethod = ($i % 2 === 0) ? 'COD' : 'Transfer';
            $deliveryType = ($i % 3 === 0) ? 'pickup' : 'delivery';
            $butuhOngkir = ($paymentMethod === 'Transfer') || ($paymentMethod === 'COD' && $deliveryType === 'delivery');

            $jarak = $butuhOngkir ? rand(1, 6) : 0;
            $ongkir = $butuhOngkir ? ($jarak <= 2 ? 5000 : $jarak * 2500) : 0;

            $subtotal = 0;
            $items = [];

            $jumlahItem = ($i % 2) + 1;
            for ($j = 0; $j < $jumlahItem; $j++) {
                $pakaiLimbah = ($j === 1 && $limbah->isNotEmpty());
                $produk_pilih = $pakaiLimbah ? $limbah[$i % $limbah->count()] : $produk[($i + $j) % $produk->count()];
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

            $statusPilihan = ['selesai', 'selesai', 'dikirim', 'diproses', 'pending'];
            $orderStatus = $statusPilihan[$i % count($statusPilihan)];
            $paymentStatus = ($orderStatus === 'pending') ? 'pending' : 'paid';

            $kodePesanan = 'ETI' . $tanggal->format('ymdHis') . str_pad((string) ($i + 10), 2, '0', STR_PAD_LEFT);
            $banks = ['BCA', 'BRI', 'Mandiri', 'BNI'];

            $pesanan = Pesanan::create([
                'user_id'           => $user->id,
                'kode_pesanan'      => $kodePesanan,
                'subtotal'          => $subtotal,
                'ongkir'            => $ongkir,
                'jarak_km'          => $jarak,
                'total_harga'       => $total,
                'payment_method'    => $paymentMethod,
                'delivery_type'     => $deliveryType,
                'bank_tujuan'       => $paymentMethod === 'Transfer' ? $banks[$i % count($banks)] : null,
                'va_number'         => $paymentMethod === 'Transfer' ? '8808' . rand(10000000, 99999999) : null,
                'payment_status'    => $paymentStatus,
                'order_status'      => $orderStatus,
                'alamat_pengiriman' => ($user->alamat ?: 'Jl. Kalimantan No. 18, Sumbersari, Jember') . ' | Penerima: ' . $user->username . ' | Telp: ' . ($user->no_telepon ?: '081234560101'),
                'tanggal_order'     => $tanggal,
                'expired_at'        => $paymentMethod === 'Transfer' ? $tanggal->copy()->addHours(24) : null,
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

            if ($orderStatus === 'selesai') {
                $selesaiPedidos[] = $pesanan;
            }
        }

        // Seed ulasan pelanggan yang natural & sesuai produk
        if (!empty($selesaiPedidos)) {
            $tahuReviews = [
                'Tahu Putih Ekonomis'          => [5, 'Tahu putihnya segar banget, teksturnya padat dan tidak asam sama sekali. Cocok buat stok masak sayur dan gorengan di rumah.'],
                'Tahu Bakso Spesial Daging Sapi' => [5, 'Isian bakso sapinya tebal dan berasa dagingnya, bukan cuma tepung. Dikukus hangat atau digoreng sama-sama enak untuk camilan keluarga.'],
                'Tahu Susu Lembut Bergizi'     => [5, 'Sesuai namanya, bagian dalamnya lembut lumer banget dan luarnya renyah pas digoreng hangat. Anak-anak di rumah suka sekali.'],
                'Tahu Goreng Gurih Renyah'     => [5, 'Kulit tahunya garing bersih dan gurih alami, tidak berminyak berlebihan. Praktis tinggal santap pakai cabai rawit atau sambal kecap.'],
                'Tahu Putih Organik Premium'   => [5, 'Kualitas kedelai organiknya terasa beda, tahu halus dan harum kedelai segar. Aman dan tenang dipakai untuk campuran menu MPASI anak.'],
            ];

            $limbahReviews = [
                'Ampas Tahu Kering Premium' => [5, 'Ampas tahu keringnya bersih, kering merata, dan bebas bau apek. Sapi dan kambing di kandang lahap semua, sangat menghemat biaya pakan.'],
                'Ampas Tahu Kering Halus'   => [5, 'Gilingannya halus seperti tepung, praktis langsung dicampur untuk bahan olahan nugget sayur maupun campuran pelet unggas.'],
                'Ampas Tahu Basah Segar'    => [5, 'Ampas tahunya benar-benar masih hangat dan segar dari produksi pagi. Dibuat nugget ampas tahu dan tempe gembus hasilnya bagus sekali.'],
                'Ampas Tahu Fermentasi'     => [5, 'Aroma fermentasi probiotiknya segar dan disukai ternak, daya simpan juga jauh lebih awet. Sangat membantu peternak lokal di Jember.'],
            ];

            $idx = 0;
            foreach ($produk as $p) {
                $meta = $tahuReviews[$p->nama_produk] ?? [5, 'Produk tahu segar, bersih, dan pengiriman tepat waktu.'];
                $order = $selesaiPedidos[$idx % count($selesaiPedidos)];
                $user = $pelanggan[$idx % $pelanggan->count()];
                Review::create([
                    'user_id'         => $user->id,
                    'pesanan_id'      => $order->id,
                    'reviewable_type' => ProdukTahu::class,
                    'reviewable_id'   => $p->id,
                    'rating'          => $meta[0],
                    'komentar'        => $meta[1],
                ]);
                $idx++;
            }

            foreach ($limbah as $l) {
                $meta = $limbahReviews[$l->nama_limbah] ?? [5, 'Kualitas olahan ampas tahu sangat baik dan bersih.'];
                $order = $selesaiPedidos[$idx % count($selesaiPedidos)];
                $user = $pelanggan[$idx % $pelanggan->count()];
                Review::create([
                    'user_id'         => $user->id,
                    'pesanan_id'      => $order->id,
                    'reviewable_type' => Limbah::class,
                    'reviewable_id'   => $l->id,
                    'rating'          => $meta[0],
                    'komentar'        => $meta[1],
                ]);
                $idx++;
            }
        }
    }
}
