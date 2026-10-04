<?php

namespace App\Http\Controllers\User;

use App\Helpers\Haversine;
use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Limbah;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\ProdukTahu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    // Minimum pembelian HANYA berlaku kalau delivery (dikirim)
    const MINIMAL_PEMBELIAN_DELIVERY = 15000;

    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Keranjang kosong. Tambahkan produk dulu.');
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);
        $user = Auth::user();
        $toko = config('toko');
        $kecamatanList = config('toko.kecamatan');
        $minimalDelivery = self::MINIMAL_PEMBELIAN_DELIVERY;

        return view('user.checkout.index', compact('cart', 'subtotal', 'user', 'toko', 'kecamatanList', 'minimalDelivery'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('user.cart.index')->with('error', 'Keranjang kosong.');
        }

        $rules = [
            'nama_penerima'     => 'required|string|max:100',
            'no_telepon'        => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string|min:10',
            'payment_method'    => 'required|in:COD,Transfer',
            'delivery_type'     => 'required|in:pickup,delivery',
            'catatan'           => 'nullable|string|max:500',
        ];

        $butuhOngkir = $request->delivery_type === 'delivery';

        if ($butuhOngkir) {
            $rules['kecamatan'] = 'required|string';
            $rules['lat_pin']   = 'nullable|numeric|between:-90,90';
            $rules['lng_pin']   = 'nullable|numeric|between:-180,180';
        }

        if ($request->payment_method === 'Transfer') {
            $rules['bank_tujuan'] = 'required|in:BCA,BRI,Mandiri,BNI';
        }

        $validated = $request->validate($rules, [
            'kecamatan.required'    => 'Kecamatan wajib dipilih untuk pengiriman.',
            'alamat_pengiriman.min' => 'Alamat minimal 10 karakter.',
        ]);

        // Cek kecamatan valid
        if ($butuhOngkir) {
            $kecamatanList = config('toko.kecamatan', []);
            if (! array_key_exists($validated['kecamatan'], $kecamatanList)) {
                return back()->withInput()->with('error', 'Kecamatan tidak valid.');
            }
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        // Cek minimum pembelian HANYA untuk delivery
        if ($butuhOngkir && $subtotal < self::MINIMAL_PEMBELIAN_DELIVERY) {
            $kurang = self::MINIMAL_PEMBELIAN_DELIVERY - $subtotal;
            return back()->withInput()->with(
                'error',
                'Minimum pembelian untuk pengiriman adalah Rp ' . number_format(self::MINIMAL_PEMBELIAN_DELIVERY, 0, ',', '.') .
                '. Tambah Rp ' . number_format($kurang, 0, ',', '.') . ' lagi, atau pilih "Ambil di Tempat".'
            );
        }

        $latTujuan = null;
        $lngTujuan = null;
        $jarak = 0;
        $ongkir = 0;
        $pakaiPin = false;
        $kecamatan = null;

        if ($butuhOngkir) {
            $latToko = (float) config('toko.lat');
            $lngToko = (float) config('toko.lng');

            $kecamatan = $validated['kecamatan'];

            // Kalau user pin lokasi → pakai koordinat pin
            if (! empty($validated['lat_pin']) && ! empty($validated['lng_pin'])) {
                $latTujuan = (float) $validated['lat_pin'];
                $lngTujuan = (float) $validated['lng_pin'];
                $pakaiPin = true;
            } else {
                // Fallback: pakai koordinat tengah kecamatan
                $kecData = config('toko.kecamatan')[$kecamatan];
                $latTujuan = (float) $kecData['lat'];
                $lngTujuan = (float) $kecData['lng'];
                $pakaiPin = false;
            }

            // Server hitung ulang jarak — JANGAN percaya client
            $jarak = Haversine::distance($latToko, $lngToko, $latTujuan, $lngTujuan);

            // Cek radius
            $radiusMaks = (float) config('toko.radius_maks_km', 10);
            if ($jarak > $radiusMaks) {
                return back()
                    ->withInput()
                    ->with('error', 'Lokasi Anda di luar jangkauan pengiriman. Maksimal ' . $radiusMaks . ' km dari toko. Jarak: ' . $jarak . ' km.');
            }

            // Minimal jarak 0.1 km
            if ($jarak < 0.1) {
                $jarak = 0.1;
            }

            $ongkir = Haversine::hitungOngkir($jarak);
        }

        $total = $subtotal + $ongkir;

        // Cek stok
        foreach ($cart as $item) {
            $produk = $item['type'] === 'produk'
                ? ProdukTahu::find($item['id'])
                : Limbah::find($item['id']);

            if (!$produk || $produk->stok < $item['qty']) {
                return back()->with('error', "Stok {$item['nama']} tidak mencukupi.");
            }
        }

        DB::beginTransaction();

        try {
            $kodePesanan = 'ETI' . date('ymdHis') . rand(10, 99);

            $vaNumber = null;
            $bankTujuan = null;
            if ($validated['payment_method'] === 'Transfer') {
                $bankTujuan = $validated['bank_tujuan'];
                $vaNumber = $this->generateVA($bankTujuan, $kodePesanan);
            }

            $pesanan = Pesanan::create([
                'user_id'           => Auth::id(),
                'kode_pesanan'      => $kodePesanan,
                'subtotal'          => $subtotal,
                'ongkir'            => $ongkir,
                'jarak_km'          => $jarak,
                'lat_tujuan'        => $latTujuan,
                'lng_tujuan'        => $lngTujuan,
                'pakai_pin'         => $pakaiPin,
                'total_harga'       => $total,
                'payment_method'    => $validated['payment_method'],
                'delivery_type'     => $validated['delivery_type'],
                'bank_tujuan'       => $bankTujuan,
                'va_number'         => $vaNumber,
                'payment_status'    => 'pending',
                'order_status'      => 'pending',
                'alamat_pengiriman' => $validated['alamat_pengiriman'] . ' | Penerima: ' . $validated['nama_penerima'] . ' | Telp: ' . $validated['no_telepon'],
                'kecamatan'         => $kecamatan,
                'catatan'           => $validated['catatan'],
                'tanggal_order'     => now(),
                'expired_at'        => $validated['payment_method'] === 'Transfer' ? now()->addHours(24) : null,
            ]);

            foreach ($cart as $item) {
                $model = $item['type'] === 'produk' ? ProdukTahu::class : Limbah::class;

                DetailPesanan::create([
                    'pesanan_id'   => $pesanan->id,
                    'item_type'    => $model,
                    'item_id'      => $item['id'],
                    'nama_item'    => $item['nama'],
                    'harga_satuan' => $item['harga'],
                    'jumlah'       => $item['qty'],
                    'subtotal'     => $item['harga'] * $item['qty'],
                ]);

                if ($item['type'] === 'produk') {
                    ProdukTahu::where('id', $item['id'])->decrement('stok', $item['qty']);
                } else {
                    Limbah::where('id', $item['id'])->decrement('stok', $item['qty']);
                }
            }

            Pembayaran::create([
                'pesanan_id'        => $pesanan->id,
                'metode_pembayaran' => $validated['payment_method'],
                'status_pembayaran' => 'pending',
                'jumlah_bayar'      => $total,
            ]);

            DB::commit();
            session()->forget('cart');

            return redirect()->route('user.checkout.success', $pesanan->kode_pesanan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateVA($bank, $kodePesanan)
    {
        $prefix = [
            'BCA'     => '8808',
            'BRI'     => '26215',
            'Mandiri' => '8888',
            'BNI'     => '8810',
        ][$bank] ?? '8888';

        $numericPart = preg_replace('/[^0-9]/', '', $kodePesanan);
        $uniquePart = str_pad(substr($numericPart, -8), 8, '0', STR_PAD_LEFT);

        return $prefix . $uniquePart;
    }

    public function success($kode)
    {
        $pesanan = Pesanan::with(['detail', 'pembayaran'])
            ->where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('user.checkout.success', compact('pesanan'));
    }

    public function uploadBukti(Request $request, $kode)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $pesanan = Pesanan::where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$pesanan->pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($pesanan->expired_at && $pesanan->expired_at->isPast()) {
            return back()->with('error', 'Waktu pembayaran sudah habis.');
        }

        if ($pesanan->pembayaran->bukti_transfer && Storage::disk('public')->exists($pesanan->pembayaran->bukti_transfer)) {
            Storage::disk('public')->delete($pesanan->pembayaran->bukti_transfer);
        }

        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');

        $pesanan->pembayaran->update([
            'bukti_transfer' => $path,
        ]);

        return back()->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin.');
    }
}