<?php

namespace App\Http\Controllers\User;

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
    const ONGKIR_PER_KM = 5000;
    const MINIMAL_PEMBELIAN = 50000;

    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Keranjang kosong. Tambahkan produk dulu.');
        }

        $subtotal = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        if ($subtotal < self::MINIMAL_PEMBELIAN) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Minimal pembelian Rp 50.000. Tambah produk lagi.');
        }

        $ongkirPerKm = self::ONGKIR_PER_KM;
        $user = Auth::user();

        return view('user.checkout.index', compact('cart', 'subtotal', 'ongkirPerKm', 'user'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Keranjang kosong.');
        }

        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'no_telepon' => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string|min:10',
            'jarak_km' => 'required|numeric|min:1|max:100',
            'payment_method' => 'required|in:COD,Transfer',
            'catatan' => 'nullable|string|max:500',
        ]);

        $subtotal = collect($cart)->sum(fn ($i) => $i['harga'] * $i['qty']);

        if ($subtotal < self::MINIMAL_PEMBELIAN) {
            return back()->with('error', 'Minimal pembelian Rp 50.000.');
        }

        $ongkir = $validated['jarak_km'] * self::ONGKIR_PER_KM;
        $total = $subtotal + $ongkir;

        // Cek stok semua item dulu
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
            // 1. Buat Pesanan
            $pesanan = Pesanan::create([
                'user_id' => Auth::id(),
                'kode_pesanan' => 'ETI' . date('ymdHis') . rand(10, 99),
                'subtotal' => $subtotal,
                'ongkir' => $ongkir,
                'jarak_km' => $validated['jarak_km'],
                'total_harga' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'alamat_pengiriman' => $validated['alamat_pengiriman'] . ' | Penerima: ' . $validated['nama_penerima'] . ' | Telp: ' . $validated['no_telepon'],
                'catatan' => $validated['catatan'],
                'tanggal_order' => now(),
            ]);

            // 2. Buat Detail Pesanan + kurangi stok
            foreach ($cart as $item) {
                $model = $item['type'] === 'produk'
                    ? ProdukTahu::class
                    : Limbah::class;

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'item_type' => $model,
                    'item_id' => $item['id'],
                    'nama_item' => $item['nama'],
                    'harga_satuan' => $item['harga'],
                    'jumlah' => $item['qty'],
                    'subtotal' => $item['harga'] * $item['qty'],
                ]);

                // Kurangi stok
                if ($item['type'] === 'produk') {
                    ProdukTahu::where('id', $item['id'])->decrement('stok', $item['qty']);
                } else {
                    Limbah::where('id', $item['id'])->decrement('stok', $item['qty']);
                }
            }

            // 3. Buat Pembayaran
            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'metode_pembayaran' => $validated['payment_method'],
                'status_pembayaran' => 'pending',
                'jumlah_bayar' => $total,
            ]);

            DB::commit();

            session()->forget('cart');

            return redirect()->route('user.checkout.success', $pesanan->kode_pesanan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pesanan = Pesanan::where('kode_pesanan', $kode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$pesanan->pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Hapus bukti lama kalau ada
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