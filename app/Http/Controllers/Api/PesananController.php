<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Haversine;
use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Limbah;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use App\Models\Refund;
use App\Models\User;
use App\Notifications\UpdatePesanan;
use App\Support\FirstOrderPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    const MINIMAL_PEMBELIAN_DELIVERY = 15000;

    /**
     * Get list of orders for the authenticated user.
     */
    public function index(Request $request)
    {
        $orders = Pesanan::with(['detail', 'pembayaran', 'refund', 'reviews'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('tanggal_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }

    /**
     * Get detail of a specific order.
     */
    public function show(Request $request, $id)
    {
        $order = Pesanan::with(['detail', 'pembayaran', 'refund', 'reviews'])
            ->where('user_id', $request->user()->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('kode_pesanan', $id);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }

    /**
     * Create a new order.
     */
    public function store(Request $request)
    {
        // Normalisasi items jika dikirim dengan key 'jumlah' atau 'qty' (tanpa reference pointer agar bebas dari circular recursion)
        if ($request->has('items') && is_array($request->items)) {
            $normalizedItems = [];
            foreach ($request->items as $it) {
                if (is_array($it)) {
                    $it['qty'] = $it['qty'] ?? $it['jumlah'] ?? 1;
                    $it['jumlah'] = $it['jumlah'] ?? $it['qty'];
                    $normalizedItems[] = $it;
                }
            }
            $request->merge(['items' => $normalizedItems]);
        }

        $validated = $request->validate([
            'kode_pesanan'   => 'nullable|string',
            'subtotal'       => 'required|numeric|min:0',
            'ongkir'         => 'nullable|numeric|min:0',
            'total_harga'    => 'required|numeric|min:0',
            'payment_method' => 'required|in:COD,Transfer',
            'bank_tujuan'    => 'nullable|string',
            'delivery_type'  => 'required|string',
            'alamat'         => 'required|string|min:5',
            'no_telepon'     => 'nullable|string',
            'catatan'        => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer',
            'items.*.item_type' => 'nullable|string',
            'items.*.qty'       => 'required|integer|min:1',
            'items.*.harga'     => 'required|numeric|min:0',
            'lat_pin'        => 'nullable|numeric|between:-90,90',
            'lng_pin'        => 'nullable|numeric|between:-180,180',
            'kecamatan'      => 'nullable|string',
        ]);

        $deliveryTypeClean = str_contains(strtolower($validated['delivery_type']), 'tempat') || strtolower($validated['delivery_type']) === 'pickup'
            ? 'pickup'
            : 'delivery';

        // Hitung jarak & ongkir otoritatif di server — jangan percaya angka ongkir dari client
        $latTujuan = $request->lat_pin ?? null;
        $lngTujuan = $request->lng_pin ?? null;
        $pakaiPin = !empty($latTujuan) && !empty($lngTujuan);
        $jarak = 0;
        $ongkir = 0;

        if ($deliveryTypeClean === 'delivery') {
            $latToko = (float) config('toko.lat', -8.161482);
            $lngToko = (float) config('toko.lng', 113.724290);
            $radiusMaks = (float) config('toko.radius_maks_km', 10);
            $kecamatanList = config('toko.kecamatan', []);
            $kecInput = trim((string) ($request->kecamatan ?? ''));

            if ($pakaiPin) {
                $jarak = Haversine::distance($latToko, $lngToko, (float) $latTujuan, (float) $lngTujuan);
                if ($jarak > $radiusMaks) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Lokasi di luar jangkauan pengiriman. Maksimal {$radiusMaks} km dari toko (jarak: {$jarak} km).",
                    ], 422);
                }
                if ($jarak < 0.1) {
                    $jarak = 0.1;
                }
                $ongkir = Haversine::hitungOngkir($jarak);
            } elseif ($kecInput !== '' && array_key_exists($kecInput, $kecamatanList)) {
                $kecData = $kecamatanList[$kecInput];
                $latTujuan = (float) $kecData['lat'];
                $lngTujuan = (float) $kecData['lng'];
                $jarak = Haversine::distance($latToko, $lngToko, $latTujuan, $lngTujuan);
                if ($jarak > $radiusMaks) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Lokasi di luar jangkauan pengiriman. Maksimal {$radiusMaks} km dari toko (jarak: {$jarak} km).",
                    ], 422);
                }
                if ($jarak < 0.1) {
                    $jarak = 0.1;
                }
                $ongkir = Haversine::hitungOngkir($jarak);
            } else {
                $ongkirMinimal = (int) config('toko.ongkir_minimal', 5000);
                $ongkir = max($ongkirMinimal, (int) ($validated['ongkir'] ?? 0));
            }
        } else {
            $ongkir = 0;
        }

        // Urutkan item secara deterministik untuk mencegah deadlock saat row-level locking
        $sortedItems = collect($validated['items'])->sortBy(function ($item) {
            $isLimbah = (isset($item['item_type']) && in_array(strtolower($item['item_type']), ['limbah', 'app\\models\\limbah']));
            return ($isLimbah ? 'limbah' : 'produk') . '-' . str_pad((string) $item['produk_id'], 10, '0', STR_PAD_LEFT);
        })->values()->all();

        DB::beginTransaction();
        try {
            // Kunci baris produk/limbah & hitung subtotal otoritatif di server agar bebas race condition
            $serverSubtotal = 0;
            $resolvedItems = [];
            foreach ($sortedItems as $item) {
                $isLimbah = (isset($item['item_type']) && in_array(strtolower($item['item_type']), ['limbah', 'app\\models\\limbah']));
                $modelClass = $isLimbah ? Limbah::class : ProdukTahu::class;
                $model = $modelClass::where('id', $item['produk_id'])->lockForUpdate()->first();

                if (!$model) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Item dengan ID ' . $item['produk_id'] . ' tidak ditemukan.',
                    ], 404);
                }

                if ($model->stok < $item['qty']) {
                    DB::rollBack();
                    $nama = $isLimbah ? ($model->nama_limbah ?? 'Limbah') : ($model->nama_produk ?? 'Produk');
                    return response()->json([
                        'status' => 'error',
                        'message' => "Stok {$nama} tidak mencukupi (sisa: {$model->stok}).",
                    ], 422);
                }

                $hargaUnit = (int) round((float) $model->harga);
                $qty = (int) $item['qty'];
                $serverSubtotal += $hargaUnit * $qty;
                $resolvedItems[] = [
                    'model_class' => $modelClass,
                    'model'       => $model,
                    'is_limbah'   => $isLimbah,
                    'qty'         => $qty,
                    'harga_unit'  => $hargaUnit,
                ];
            }

            // Cek minimum pembelian untuk delivery
            if ($deliveryTypeClean === 'delivery' && $serverSubtotal < self::MINIMAL_PEMBELIAN_DELIVERY) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Minimum pembelian untuk pengiriman adalah Rp ' . number_format(self::MINIMAL_PEMBELIAN_DELIVERY, 0, ',', '.') . '.',
                ], 422);
            }

            $recipientPhoneRaw = $validated['no_telepon'] ?? $request->user()->no_telepon;
            $promo = FirstOrderPromo::evaluate($request->user(), $serverSubtotal, $recipientPhoneRaw);
            $diskon = $promo['applied'] ? (int) $promo['diskon'] : 0;
            $totalHarga = max(0, $serverSubtotal - $diskon) + $ongkir;

            $kodePesanan = !empty($validated['kode_pesanan'])
                ? $validated['kode_pesanan']
                : ('ETI' . date('ymdHis') . strtoupper(Str::random(4)));

            // Generate VA bila metode Transfer
            $vaNumber = null;
            $bankTujuan = $validated['bank_tujuan'] ?? null;
            if ($validated['payment_method'] === 'Transfer' && $bankTujuan) {
                $prefix = [
                    'BCA'     => '8808',
                    'BRI'     => '26215',
                    'Mandiri' => '8888',
                    'BNI'     => '8810',
                ][$bankTujuan] ?? '8888';

                $numericPart = preg_replace('/[^0-9]/', '', $kodePesanan);
                $vaNumber = $prefix . str_pad(substr($numericPart, -8), 8, '0', STR_PAD_LEFT);
            }

            $pesanan = Pesanan::create([
                'user_id'             => $request->user()->id,
                'kode_pesanan'        => $kodePesanan,
                'subtotal'            => $serverSubtotal,
                'diskon'              => $diskon,
                'ongkir'              => $ongkir,
                'jarak_km'            => $jarak,
                'lat_tujuan'          => $latTujuan,
                'lng_tujuan'          => $lngTujuan,
                'pakai_pin'           => $pakaiPin,
                'total_harga'         => $totalHarga,
                'payment_method'      => $validated['payment_method'],
                'delivery_type'       => $deliveryTypeClean,
                'bank_tujuan'         => $bankTujuan,
                'va_number'           => $vaNumber,
                'payment_status'      => 'pending',
                'order_status'        => 'pending',
                'alamat_pengiriman'   => $validated['alamat'],
                'no_telepon_penerima' => FirstOrderPromo::normalizePhone($recipientPhoneRaw),
                'kecamatan'           => $request->kecamatan,
                'catatan'             => $validated['catatan'] ?? null,
                'tanggal_order'       => now(),
                'expired_at'          => $validated['payment_method'] === 'Transfer' ? now()->addHours(24) : null,
            ]);

            // Buat Detail Pesanan & kurangi stok
            foreach ($resolvedItems as $resolved) {
                $model = $resolved['model'];
                $namaItem = $resolved['is_limbah'] ? ($model->nama_limbah ?? 'Limbah') : ($model->nama_produk ?? 'Produk');

                DetailPesanan::create([
                    'pesanan_id'   => $pesanan->id,
                    'item_type'    => $resolved['model_class'],
                    'item_id'      => $model->id,
                    'nama_item'    => $namaItem,
                    'harga_satuan' => $resolved['harga_unit'],
                    'jumlah'       => $resolved['qty'],
                    'subtotal'     => $resolved['harga_unit'] * $resolved['qty'],
                ]);

                $model->decrement('stok', $resolved['qty']);
            }

            // Buat Record Pembayaran
            Pembayaran::create([
                'pesanan_id'        => $pesanan->id,
                'metode_pembayaran' => $validated['payment_method'],
                'status_pembayaran' => 'pending',
                'jumlah_bayar'      => $totalHarga,
            ]);

            // Kirim notifikasi ke Admin
            $admins = User::whereHas('role', function ($q) {
                $q->where('nama_role', 'admin');
            })->get();

            if ($admins->count() > 0) {
                Notification::send($admins, new UpdatePesanan(
                    "Pesanan baru masuk #{$pesanan->kode_pesanan} dari {$request->user()->username}",
                    route('admin.pesanan.show', $pesanan->id)
                ));
            }

            // Kirim notifikasi ke User
            $request->user()->notify(new UpdatePesanan(
                "Pesanan #{$pesanan->kode_pesanan} berhasil dibuat. Silakan selesaikan pembayaran.",
                route('user.pesanan.show', $pesanan->kode_pesanan)
            ));

            DB::commit();
            \App\Support\CartStore::clearUser($request->user());

            $pesanan->load(['detail', 'pembayaran']);

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat!',
                'data' => $pesanan,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('API order store failed', ['user_id' => $request->user()?->id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat pesanan. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    /**
     * Cancel an order and request refund if applicable.
     */
    public function cancel(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan_batal'   => 'required|string|min:5|max:500',
            'nama_bank'      => 'nullable|regex:/^[a-zA-Z0-9\s\-]+$/|max:50',
            'nomor_rekening' => 'nullable|regex:/^[0-9]+$/|max:50',
            'atas_nama'      => 'nullable|regex:/^[a-zA-Z\s]+$/|max:100',
            'bukti_transfer' => 'nullable|string',
        ], [
            'alasan_batal.required' => 'Alasan pembatalan wajib diisi.',
            'nama_bank.regex'       => 'Nama bank/e-wallet hanya boleh berisi huruf, angka, spasi, atau tanda hubung.',
            'nomor_rekening.regex'  => 'Nomor rekening hanya boleh berisi angka.',
            'atas_nama.regex'       => 'Nama rekening hanya boleh berisi huruf dan spasi.',
        ]);

        $pesanan = Pesanan::with(['pembayaran', 'detail', 'refund'])
            ->where('user_id', $request->user()->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('kode_pesanan', $id);
            })
            ->first();

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if (!in_array($pesanan->order_status, ['pending', 'diproses'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak dapat dibatalkan karena sudah berstatus ' . $pesanan->order_status . '.',
            ], 422);
        }

        if ($pesanan->refund) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan ini sudah dibatalkan sebelumnya.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $pesanan->update([
                'order_status' => 'dibatalkan',
                'alasan_batal' => $validated['alasan_batal'],
            ]);

            // Kembalikan stok untuk semua item
            foreach ($pesanan->detail as $d) {
                if ($d->item_type === 'App\\Models\\ProdukTahu' || $d->item_type === ProdukTahu::class) {
                    ProdukTahu::where('id', $d->item_id)->increment('stok', $d->jumlah);
                } else {
                    Limbah::where('id', $d->item_id)->increment('stok', $d->jumlah);
                }
            }

            // Nominal refund jika transfer & sudah lunas
            $nominalRefund = 0;
            if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status === 'paid') {
                $nominalRefund = $pesanan->total_harga;
                $pesanan->update(['payment_status' => 'refunded']);
            }

            Refund::create([
                'pesanan_id'     => $pesanan->id,
                'nominal_refund' => $nominalRefund,
                'alasan_batal'   => $validated['alasan_batal'],
                'nama_bank'      => $pesanan->payment_method === 'Transfer' ? ($validated['nama_bank'] ?? null) : null,
                'nomor_rekening' => $pesanan->payment_method === 'Transfer' ? ($validated['nomor_rekening'] ?? null) : null,
                'atas_nama'      => $pesanan->payment_method === 'Transfer' ? ($validated['atas_nama'] ?? null) : null,
                'status_refund'  => 'pending',
            ]);

            // Kirim notifikasi ke Admin
            $admins = User::whereHas('role', function ($q) {
                $q->where('nama_role', 'admin');
            })->get();

            if ($admins->count() > 0) {
                Notification::send($admins, new UpdatePesanan(
                    "Pesanan #{$pesanan->kode_pesanan} dibatalkan oleh {$request->user()->username}",
                    route('admin.refund.index')
                ));
            }

            // Kirim notifikasi ke User
            $request->user()->notify(new UpdatePesanan(
                "Pesanan #{$pesanan->kode_pesanan} berhasil dibatalkan. Alasan: {$validated['alasan_batal']}",
                route('user.pesanan.show', $pesanan->kode_pesanan)
            ));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan. Pengajuan pengembalian telah dikirim.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('API order cancel failed', ['user_id' => $request->user()?->id, 'order_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan pesanan. Silakan coba beberapa saat lagi.',
            ], 500);
        }
    }

    /**
     * Upload payment proof.
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => \App\Support\MediaGuard::imageRules(true, 4096),
        ], [
            'bukti_transfer.required'  => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.image'     => 'Bukti transfer hanya boleh berupa file foto (JPG, PNG, WEBP).',
            'bukti_transfer.mimes'     => 'Format bukti transfer harus JPG, PNG, atau WEBP.',
            'bukti_transfer.mimetypes' => 'Bukti transfer hanya boleh berupa file foto asli.',
            'bukti_transfer.max'       => 'Ukuran bukti transfer maksimal 4MB.',
        ]);

        $pesanan = Pesanan::with('pembayaran')
            ->where('user_id', $request->user()->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('kode_pesanan', $id);
            })
            ->first();

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if ($pesanan->expired_at && $pesanan->expired_at->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Waktu pembayaran sudah habis.',
            ], 422);
        }

        if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer && Storage::disk('public')->exists($pesanan->pembayaran->bukti_transfer)) {
            Storage::disk('public')->delete($pesanan->pembayaran->bukti_transfer);
        }

        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');

        if ($pesanan->pembayaran) {
            $pesanan->pembayaran->update([
                'bukti_transfer'    => $path,
                'status_pembayaran' => 'pending',
            ]);
        } else {
            $pesanan->pembayaran()->create([
                'metode_pembayaran' => $pesanan->payment_method,
                'status_pembayaran' => 'pending',
                'jumlah_bayar'      => $pesanan->total_harga,
                'bukti_transfer'    => $path,
            ]);
        }

        // Beritahu Admin
        $admins = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'admin');
        })->get();

        if ($admins->count() > 0) {
            Notification::send($admins, new UpdatePesanan(
                "Bukti transfer baru diupload untuk #{$pesanan->kode_pesanan}",
                route('admin.pesanan.show', $pesanan->id)
            ));
        }

        // Beritahu User
        $request->user()->notify(new UpdatePesanan(
            "Bukti transfer untuk pesanan #{$pesanan->kode_pesanan} berhasil diunggah. Menunggu verifikasi admin.",
            route('user.pesanan.show', $pesanan->kode_pesanan)
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti transfer berhasil diupload. Menunggu verifikasi admin.',
            'data' => [
                'bukti_transfer' => $path,
                'url' => asset('storage/' . $path),
            ],
        ]);
    }

    /**
     * Real-time / Polling tracking for courier delivery.
     */
    public function tracking(Request $request, $id)
    {
        $pesanan = Pesanan::where('user_id', $request->user()->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('kode_pesanan', $id);
            })
            ->first();

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $active = $pesanan->order_status === 'dikirim'
               && $pesanan->lat_kurir !== null
               && $pesanan->lng_kurir !== null;

        return response()->json([
            'status' => 'success',
            'active' => $active,
            'order_status' => $pesanan->order_status,
            'estimasi_menit' => $pesanan->estimasi_menit,
            'estimasi_tiba' => $pesanan->estimasi_tiba?->toIso8601String(),
            'estimasi_tiba_wib' => $pesanan->estimasi_tiba?->timezone('Asia/Jakarta')->format('H:i'),
            'status_kurir' => $pesanan->status_kurir,
            'status_kurir_label' => $pesanan->status_kurir_label,
            'kurir' => $active ? [
                'lat' => (float) $pesanan->lat_kurir,
                'lng' => (float) $pesanan->lng_kurir,
                'updated_at' => $pesanan->lokasi_updated_at?->toIso8601String(),
            ] : null,
            'toko' => [
                'lat' => (float) config('toko.lat'),
                'lng' => (float) config('toko.lng'),
                'nama' => config('toko.nama'),
            ],
            'tujuan' => $pesanan->lat_tujuan && $pesanan->lng_tujuan ? [
                'lat' => (float) $pesanan->lat_tujuan,
                'lng' => (float) $pesanan->lng_tujuan,
            ] : null,
        ]);
    }
}
