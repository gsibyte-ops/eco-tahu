@extends('layouts.user')
@section('title', 'Pesanan Berhasil')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if (session('success'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); border: 1px solid rgb(var(--success) / 0.3); background: rgb(var(--success-soft));">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">{{ session('error') }}</div>
    @endif

    <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl"
             style="background: var(--gradient-brand); box-shadow: 0 12px 32px -8px rgb(var(--brand) / 0.5);">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Pesanan Berhasil Dibuat!</h1>
        <p style="color: rgb(var(--text-secondary));">Terima kasih sudah berbelanja di EcoTahu 🌿</p>
    </div>

    <div class="glass-card p-6 mb-5">
        <div class="flex items-center justify-between mb-4 pb-4" style="border-bottom: 1px solid rgb(var(--border-soft));">
            <div>
                <p class="text-xs" style="color: rgb(var(--text-muted));">Kode Pesanan</p>
                <p class="text-lg font-bold tabular-nums" style="color: rgb(var(--text-primary));">#{{ $pesanan->kode_pesanan }}</p>
            </div>
            @php
                $statusStyle = [
                    'pending'    => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning)); border-color: rgb(var(--warning) / 0.3);',
                    'diproses'   => 'background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);',
                    'dikirim'    => 'background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);',
                    'selesai'    => 'background: rgb(var(--success-soft)); color: rgb(var(--success)); border-color: rgb(var(--success) / 0.3);',
                    'dibatalkan' => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger)); border-color: rgb(var(--danger) / 0.3);',
                ][$pesanan->order_status] ?? 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary)); border-color: rgb(var(--border));';
            @endphp
            <span class="px-3 py-1 border rounded-lg text-xs font-bold" style="{{ $statusStyle }}">{{ ucfirst($pesanan->order_status) }}</span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Tanggal</span>
                <span class="font-medium tabular-nums" style="color: rgb(var(--text-primary));">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Metode Bayar</span>
                <span class="font-medium" style="color: rgb(var(--text-primary));">
                    {{ $pesanan->payment_method }}
                    @if ($pesanan->bank_tujuan) - {{ $pesanan->bank_tujuan }} @endif
                </span>
            </div>
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Tipe Pengiriman</span>
                <span class="font-medium" style="color: rgb(var(--text-primary));">{{ $pesanan->delivery_label }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Jarak</span>
                <span class="font-medium tabular-nums" style="color: rgb(var(--text-primary));">{{ $pesanan->jarak_km > 0 ? $pesanan->jarak_km . ' km' : 'Ambil di Tempat' }}</span>
            </div>
            <div class="flex justify-between pt-3" style="border-top: 1px solid rgb(var(--border-soft));">
                <span class="font-bold" style="color: rgb(var(--text-primary));">Total</span>
                <span class="text-xl price text-gradient-green">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if ($pesanan->payment_method === 'Transfer')

        @if ($pesanan->payment_status === 'paid')
            <div class="rounded-2xl p-6 mb-5 text-white shadow-xl" style="background: var(--gradient-brand); box-shadow: 0 12px 32px -8px rgb(var(--brand) / 0.5);">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgb(255 255 255 / 0.2); backdrop-filter: blur(20px);">
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-xl font-bold">Pembayaran Berhasil! 🎉</p>
                        <p class="text-sm opacity-90">Pembayaran Anda sudah kami terima dan terverifikasi.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl p-6 text-white mb-5 shadow-xl" style="background: var(--gradient-info); box-shadow: 0 12px 32px -8px rgb(var(--info) / 0.5);">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span class="font-bold text-sm tracking-wide">{{ $pesanan->bank_tujuan }} Virtual Account</span>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-lg" style="background: rgb(255 255 255 / 0.2); backdrop-filter: blur(20px);">{{ $pesanan->bank_tujuan }}</span>
                </div>

                <p class="text-xs opacity-80 mb-1">Nomor Virtual Account</p>
                <div class="flex items-center gap-2 mb-4">
                    <p id="vaNumber" class="text-2xl font-bold tracking-widest font-mono tabular-nums">{{ $pesanan->va_number }}</p>
                    <button type="button" onclick="copyVA()" class="ml-auto px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition" style="background: rgb(255 255 255 / 0.2); backdrop-filter: blur(20px);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copyLabel">Copy</span>
                    </button>
                </div>

                <div class="pt-3" style="border-top: 1px solid rgb(255 255 255 / 0.3);">
                    <p class="text-xs opacity-80 mb-1">Total Transfer</p>
                    <p class="text-2xl font-bold tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                </div>
            </div>

            @if ($pesanan->expired_at && $pesanan->expired_at->isFuture())
                <div class="glass rounded-2xl p-4 mb-5 text-center" id="countdownBox"
                     data-expired="{{ $pesanan->expired_at->toIso8601String() }}"
                     style="border-color: rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">
                    <p class="text-xs uppercase font-bold mb-1" style="color: rgb(var(--danger));">⏰ Selesaikan Pembayaran Dalam</p>
                    <p id="countdown" class="text-2xl font-bold font-mono tabular-nums" style="color: rgb(var(--danger));">--:--:--</p>
                </div>
            @endif

            <div class="glass rounded-2xl p-6 mb-5" style="border-color: rgb(var(--info) / 0.3); background: rgb(var(--info-soft));">
                <h3 class="font-bold mb-2" style="color: rgb(var(--info));">📎 Sudah Transfer? Upload Bukti</h3>
                <p class="text-sm mb-4" style="color: rgb(var(--info));">Upload bukti transfer biar admin bisa verifikasi pembayaran Anda.</p>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="glass-card p-4 mb-4">
                        <p class="text-xs mb-2" style="color: rgb(var(--text-muted));">Bukti yang sudah diupload:</p>
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl" style="border: 1px solid rgb(var(--border));">
                        <p class="text-xs mt-2 font-medium" style="color: rgb(var(--success));">✓ Menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.checkout.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="glass-input w-full px-3 py-2 text-sm mb-3 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="btn-primary w-full py-2.5" style="background: var(--gradient-info);">
                        Upload Bukti Transfer
                    </button>
                </form>
            </div>
        @endif

    @else
        <div class="glass rounded-2xl p-6 mb-5 text-sm" style="color: rgb(var(--success)); border-color: rgb(var(--success) / 0.3); background: rgb(var(--success-soft));">
            💵 <strong>Metode COD</strong> — Bayar tunai ke kurir saat barang tiba.<br>
            Total yang perlu dibayar: <strong class="tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong>
        </div>
    @endif

    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ route('user.pesanan.show', $pesanan->kode_pesanan) }}" class="btn-primary">
            Lihat Detail Pesanan
        </a>
        <a href="{{ route('home') }}" class="glass-btn px-6 py-3">
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