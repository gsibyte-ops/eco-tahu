@extends('layouts.user')
@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
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
            @php
                $statusBadge = [
                    'pending' => 'bg-amber-50 text-amber-700',
                    'diproses' => 'bg-blue-50 text-blue-700',
                    'dikirim' => 'bg-indigo-50 text-indigo-700',
                    'selesai' => 'bg-emerald-50 text-emerald-700',
                    'dibatalkan' => 'bg-red-50 text-red-700',
                ][$pesanan->order_status] ?? 'bg-gray-50 text-gray-700';
            @endphp
            <span class="px-3 py-1 {{ $statusBadge }} rounded-lg text-xs font-bold">{{ ucfirst($pesanan->order_status) }}</span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-medium text-gray-800">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Metode Bayar</span>
                <span class="font-medium text-gray-800">
                    {{ $pesanan->payment_method }}
                    @if ($pesanan->bank_tujuan) - {{ $pesanan->bank_tujuan }} @endif
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tipe Pengiriman</span>
                <span class="font-medium text-gray-800">{{ $pesanan->delivery_label }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jarak</span>
                <span class="font-medium text-gray-800">{{ $pesanan->jarak_km > 0 ? $pesanan->jarak_km . ' km' : 'Ambil di Tempat' }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <span class="font-bold text-gray-800">Total</span>
                <span class="text-xl font-bold text-emerald-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if ($pesanan->payment_method === 'Transfer')

        {{-- KALAU UDAH LUNAS --}}
        @if ($pesanan->payment_status === 'paid')
            <div class="rounded-2xl p-6 mb-5 shadow-lg text-white"
                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold">Pembayaran Berhasil! 🎉</p>
                        <p class="text-sm opacity-90">Pembayaran Anda sudah kami terima dan terverifikasi.</p>
                    </div>
                </div>
            </div>
        @else
            {{-- VA DISPLAY + COUNTDOWN --}}
            <div style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);"
                 class="rounded-2xl p-6 text-white mb-5 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="font-bold text-sm tracking-wide">{{ $pesanan->bank_tujuan }} Virtual Account</span>
                    </div>
                    <span class="text-xs bg-white/20 px-2.5 py-1 rounded-lg">{{ $pesanan->bank_tujuan }}</span>
                </div>

                <p class="text-xs opacity-80 mb-1">Nomor Virtual Account</p>
                <div class="flex items-center gap-2 mb-4">
                    <p id="vaNumber" class="text-2xl font-bold tracking-widest font-mono">{{ $pesanan->va_number }}</p>
                    <button type="button" onclick="copyVA()" class="ml-auto bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copyLabel">Copy</span>
                    </button>
                </div>

                <div class="border-t border-white/30 pt-3">
                    <p class="text-xs opacity-80 mb-1">Total Transfer</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Countdown --}}
            @if ($pesanan->expired_at && $pesanan->expired_at->isFuture())
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-5 text-center"
                     id="countdownBox"
                     data-expired="{{ $pesanan->expired_at->toIso8601String() }}">
                    <p class="text-xs text-red-600 uppercase font-bold mb-1">⏰ Selesaikan Pembayaran Dalam</p>
                    <p id="countdown" class="text-2xl font-bold text-red-700 font-mono">--:--:--</p>
                </div>
            @endif

            {{-- Upload Bukti --}}
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-5">
                <h3 class="font-bold text-blue-800 mb-2">📎 Sudah Transfer? Upload Bukti</h3>
                <p class="text-sm text-blue-700 mb-4">Upload bukti transfer biar admin bisa verifikasi pembayaran Anda.</p>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="bg-white rounded-xl p-4 mb-4">
                        <p class="text-xs text-gray-500 mb-2">Bukti yang sudah diupload:</p>
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl border border-gray-100">
                        <p class="text-xs text-emerald-600 mt-2 font-medium">✓ Menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.checkout.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm mb-3 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                        Upload Bukti Transfer
                    </button>
                </form>
            </div>
        @endif

    @else
        {{-- COD --}}
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

@push('scripts')
<script>
    function copyVA() {
        const vaEl = document.getElementById('vaNumber');
        if (!vaEl) return;
        navigator.clipboard.writeText(vaEl.textContent.trim()).then(() => {
            const label = document.getElementById('copyLabel');
            label.textContent = 'Copied!';
            setTimeout(() => { label.textContent = 'Copy'; }, 1500);
        });
    }

    // Countdown Timer
    const countdownBox = document.getElementById('countdownBox');
    if (countdownBox) {
        const expiredAt = new Date(countdownBox.dataset.expired).getTime();
        const countdownEl = document.getElementById('countdown');
        const tick = setInterval(function() {
            const now = new Date().getTime();
            const diff = expiredAt - now;
            if (diff <= 0) {
                clearInterval(tick);
                if (countdownEl) countdownEl.textContent = 'WAKTU HABIS';
                return;
            }
            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);
            if (countdownEl) {
                countdownEl.textContent =
                    String(h).padStart(2, '0') + ':' +
                    String(m).padStart(2, '0') + ':' +
                    String(s).padStart(2, '0');
            }
        }, 1000);
    }
</script>
@endpush