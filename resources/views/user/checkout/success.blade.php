@extends('layouts.user')
@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if (session('success'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-emerald-700 border-emerald-300/50">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-red-700 border-red-300/50">{{ session('error') }}</div>
    @endif

    {{-- Success Header --}}
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-500/40">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-display text-gray-800 mb-2">Pesanan Berhasil Dibuat!</h1>
        <p class="text-gray-500">Terima kasih sudah berbelanja di EcoTahu 🌿</p>
    </div>

    {{-- Order Info --}}
    <div class="glass-card p-6 mb-5">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/50">
            <div>
                <p class="text-xs text-gray-500">Kode Pesanan</p>
                <p class="text-lg font-bold text-gray-800 tabular-nums">#{{ $pesanan->kode_pesanan }}</p>
            </div>
            @php
                $statusBadge = [
                    'pending' => 'bg-amber-500/20 text-amber-800 border-amber-300/50',
                    'diproses' => 'bg-blue-500/20 text-blue-800 border-blue-300/50',
                    'dikirim' => 'bg-indigo-500/20 text-indigo-800 border-indigo-300/50',
                    'selesai' => 'bg-emerald-500/20 text-emerald-800 border-emerald-300/50',
                    'dibatalkan' => 'bg-red-500/20 text-red-800 border-red-300/50',
                ][$pesanan->order_status] ?? 'bg-gray-500/20 text-gray-800 border-gray-300/50';
            @endphp
            <span class="px-3 py-1 {{ $statusBadge }} border rounded-lg text-xs font-bold">{{ ucfirst($pesanan->order_status) }}</span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-medium text-gray-800 tabular-nums">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</span>
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
                <span class="font-medium text-gray-800 tabular-nums">{{ $pesanan->jarak_km > 0 ? $pesanan->jarak_km . ' km' : 'Ambil di Tempat' }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-white/50">
                <span class="font-bold text-gray-800">Total</span>
                <span class="text-xl price text-gradient-green">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if ($pesanan->payment_method === 'Transfer')

        @if ($pesanan->payment_status === 'paid')
            <div class="rounded-2xl p-6 mb-5 text-white shadow-xl"
                 style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-md">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold">Pembayaran Berhasil! 🎉</p>
                        <p class="text-sm opacity-90">Pembayaran Anda sudah kami terima dan terverifikasi.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl p-6 text-white mb-5 shadow-xl"
                 style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="font-bold text-sm tracking-wide">{{ $pesanan->bank_tujuan }} Virtual Account</span>
                    </div>
                    <span class="text-xs bg-white/20 backdrop-blur-md px-2.5 py-1 rounded-lg">{{ $pesanan->bank_tujuan }}</span>
                </div>

                <p class="text-xs opacity-80 mb-1">Nomor Virtual Account</p>
                <div class="flex items-center gap-2 mb-4">
                    <p id="vaNumber" class="text-2xl font-bold tracking-widest font-mono tabular-nums">{{ $pesanan->va_number }}</p>
                    <button type="button" onclick="copyVA()" class="ml-auto bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copyLabel">Copy</span>
                    </button>
                </div>

                <div class="border-t border-white/30 pt-3">
                    <p class="text-xs opacity-80 mb-1">Total Transfer</p>
                    <p class="text-2xl font-bold tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>

            @if ($pesanan->expired_at && $pesanan->expired_at->isFuture())
                <div class="glass rounded-2xl p-4 mb-5 text-center border-red-300/50"
                     id="countdownBox"
                     data-expired="{{ $pesanan->expired_at->toIso8601String() }}">
                    <p class="text-xs text-red-600 uppercase font-bold mb-1">⏰ Selesaikan Pembayaran Dalam</p>
                    <p id="countdown" class="text-2xl font-bold text-red-700 font-mono tabular-nums">--:--:--</p>
                </div>
            @endif

            <div class="glass rounded-2xl p-6 mb-5 border-blue-300/50">
                <h3 class="font-bold text-blue-800 mb-2">📎 Sudah Transfer? Upload Bukti</h3>
                <p class="text-sm text-blue-700 mb-4">Upload bukti transfer biar admin bisa verifikasi pembayaran Anda.</p>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="glass-card p-4 mb-4">
                        <p class="text-xs text-gray-500 mb-2">Bukti yang sudah diupload:</p>
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl border border-white/60">
                        <p class="text-xs text-emerald-600 mt-2 font-medium">✓ Menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.checkout.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="glass-input w-full px-3 py-2 text-sm mb-3 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="glass-btn-primary w-full py-2.5 bg-gradient-to-br from-blue-500 to-blue-700 shadow-blue-500/40">
                        Upload Bukti Transfer
                    </button>
                </form>
            </div>
        @endif

    @else
        <div class="glass rounded-2xl p-6 mb-5 text-sm text-emerald-800 border-emerald-300/50">
            💵 <strong>Metode COD</strong> — Bayar tunai ke kurir saat barang tiba.<br>
            Total yang perlu dibayar: <strong class="tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong>
        </div>
    @endif

    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ route('user.pesanan.show', $pesanan->kode_pesanan) }}"
           class="glass-btn-primary">
            Lihat Detail Pesanan
        </a>
        <a href="{{ route('home') }}"
           class="glass-btn px-6 py-3">
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