@extends('layouts.user')
@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Success Header --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Berhasil Dibuat!</h1>
        <p class="text-gray-500">Terima kasih sudah berbelanja di EcoTahu 🌿</p>
    </div>

    {{-- Order Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
            <div>
                <p class="text-xs text-gray-500">Kode Pesanan</p>
                <p class="text-lg font-bold text-gray-800">#{{ $pesanan->kode_pesanan }}</p>
            </div>
            <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold">Pending</span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-medium text-gray-800">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Metode Bayar</span>
                <span class="font-medium text-gray-800">{{ $pesanan->payment_method }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jarak</span>
                <span class="font-medium text-gray-800">{{ $pesanan->jarak_km }} km</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <span class="font-bold text-gray-800">Total</span>
                <span class="text-xl font-bold text-emerald-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Kalau Transfer, minta upload bukti --}}
    @if ($pesanan->payment_method === 'Transfer')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-5">
            <h3 class="font-bold text-blue-800 mb-2">📎 Upload Bukti Transfer</h3>
            <p class="text-sm text-blue-700 mb-4">
                Transfer ke rekening berikut, lalu upload buktinya di bawah ini.
            </p>

            <div class="bg-white rounded-xl p-4 mb-4">
                <p class="text-xs text-gray-500 mb-1">Bank BCA</p>
                <p class="text-lg font-bold text-gray-800 tracking-wider">1234567890</p>
                <p class="text-sm text-gray-600">a/n EcoTahu Indonesia</p>
                <p class="text-xs text-gray-500 mt-2">Nominal: <strong class="text-emerald-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></p>
            </div>

            @if ($pesanan->pembayaran->bukti_transfer)
                <div class="bg-white rounded-xl p-4 mb-4">
                    <p class="text-xs text-gray-500 mb-2">Bukti yang sudah diupload:</p>
                    <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}"
                         class="w-full max-w-xs rounded-xl border border-gray-100">
                    <p class="text-xs text-emerald-600 mt-2 font-medium">✓ Menunggu verifikasi admin</p>
                </div>
            @endif

            <form method="POST" action="{{ route('user.checkout.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                  file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                </div>
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                    Upload Bukti Transfer
                </button>
            </form>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 mb-5 text-sm text-emerald-800">
            💵 <strong>Metode COD</strong> — Bayar tunai ke kurir saat barang tiba.<br>
            Total yang perlu dibayar: <strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ route('user.pesanan.show', $pesanan->kode_pesanan) }}"
           class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
            Lihat Detail Pesanan
        </a>
        <a href="{{ route('home') }}"
           class="px-6 py-3 bg-white border border-gray-200 hover:border-emerald-300 text-gray-700 font-semibold rounded-xl transition">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection