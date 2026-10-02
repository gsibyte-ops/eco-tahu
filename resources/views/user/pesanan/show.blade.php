@extends('layouts.user')
@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Beranda</a>
        <span class="mx-2 text-emerald-400">/</span>
        <a href="{{ route('user.pesanan.index') }}" class="hover:text-emerald-600 transition">Pesanan Saya</a>
        <span class="mx-2 text-emerald-400">/</span>
        <span class="text-gray-800 font-medium tabular-nums">#{{ $pesanan->kode_pesanan }}</span>
    </nav>

    @if (session('success'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-emerald-700 border-emerald-300/50">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-red-700 border-red-300/50">{{ session('error') }}</div>
    @endif

    @php
        $statusClass = [
            'pending'    => 'bg-amber-500/20 text-amber-800 border-amber-300/50',
            'diproses'   => 'bg-blue-500/20 text-blue-800 border-blue-300/50',
            'dikirim'    => 'bg-indigo-500/20 text-indigo-800 border-indigo-300/50',
            'selesai'    => 'bg-emerald-500/20 text-emerald-800 border-emerald-300/50',
            'dibatalkan' => 'bg-red-500/20 text-red-800 border-red-300/50',
        ][$pesanan->order_status] ?? 'bg-gray-500/20 text-gray-800 border-gray-300/50';

        $bolehCancel = in_array($pesanan->order_status, ['pending', 'diproses']);
        $menungguVerifikasi = $pesanan->payment_method === 'Transfer'
                            && $pesanan->payment_status === 'pending'
                            && !in_array($pesanan->order_status, ['dibatalkan', 'selesai']);
    @endphp

    @if ($pesanan->payment_status === 'paid' && $pesanan->order_status !== 'dibatalkan')
        <div class="rounded-2xl p-6 mb-5 text-white shadow-xl"
             style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-lg font-bold mb-1">Pembayaran Berhasil! 🎉</p>
                    <p class="text-sm opacity-90">Pembayaran Anda sudah kami terima dan terverifikasi.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="glass-card p-6 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Kode Pesanan</p>
                <p class="text-2xl font-display text-gray-800 tabular-nums">#{{ $pesanan->kode_pesanan }}</p>
                <p class="text-sm text-gray-500 mt-1 tabular-nums">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-lg text-sm font-bold border {{ $statusClass }}">{{ ucfirst($pesanan->order_status) }}</span>
        </div>

        @if ($pesanan->order_status === 'dibatalkan')
            <div class="glass rounded-xl p-4 mb-4 border-red-300/50">
                <p class="text-sm font-bold text-red-800 mb-1">Pesanan Dibatalkan</p>
                <p class="text-sm text-red-700">Alasan: {{ $pesanan->alasan_batal ?? $pesanan->refund->alasan_batal ?? 'Tidak ada alasan yang dicatat.' }}</p>
            </div>

            @if ($pesanan->refund)
                <div class="glass rounded-xl p-4 border-red-300/50">
                    <p class="text-xs font-bold text-red-600 uppercase mb-3">💰 Info Refund</p>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-red-500">Nominal</p>
                            <p class="font-bold text-red-800 tabular-nums">Rp {{ number_format($pesanan->refund->nominal_refund, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-red-500">Status Refund</p>
                            @php
                                $refundLabel = [
                                    'pending' => '⏳ Menunggu Diproses',
                                    'diproses' => '🔄 Sedang Diproses',
                                    'selesai' => '✅ Sudah Ditransfer',
                                    'ditolak' => '❌ Ditolak',
                                ][$pesanan->refund->status_refund] ?? '-';
                            @endphp
                            <p class="font-bold text-red-800">{{ $refundLabel }}</p>
                        </div>
                        @if ($pesanan->refund->catatan_admin)
                            <div class="col-span-2">
                                <p class="text-xs text-red-500 mb-1">💬 Pesan dari Admin</p>
                                <div class="glass-card p-3">
                                    <p class="text-red-800 leading-relaxed">{{ $pesanan->refund->catatan_admin }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($pesanan->refund->bukti_transfer_balik)
                            <div class="col-span-2">
                                <p class="text-xs text-red-500 mb-1">Bukti Transfer Balik</p>
                                <img src="{{ asset('storage/' . $pesanan->refund->bukti_transfer_balik) }}" class="w-full max-w-xs rounded-xl border border-red-300/50">
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>

    <div class="glass-card p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-3">📦 Info Pengiriman</h3>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $pesanan->alamat_pengiriman }}</p>
        <p class="text-xs text-gray-500 mt-2"><strong>Tipe:</strong> {{ $pesanan->delivery_label }}</p>
        @if ($pesanan->catatan)
            <p class="text-xs text-gray-500 mt-2"><strong>Catatan:</strong> {{ $pesanan->catatan }}</p>
        @endif
    </div>

    <div class="glass-card p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-4">🛒 Item Pesanan</h3>
        <div class="space-y-3">
            @foreach ($pesanan->detail as $d)
                <div class="flex items-center gap-3 py-2 border-b border-white/40 last:border-0">
                    <div class="w-10 h-10 rounded-lg bg-white/40 flex items-center justify-center text-lg flex-shrink-0">
                        {{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $d->nama_item }}</p>
                        <p class="text-xs text-gray-500 tabular-nums">{{ $d->jumlah }} × Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 tabular-nums">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass-card p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-4">💳 Pembayaran</h3>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Metode</span>
                <span class="font-semibold text-gray-800">{{ $pesanan->payment_method }} @if ($pesanan->bank_tujuan) - {{ $pesanan->bank_tujuan }} @endif</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status Bayar</span>
                @php
                    $payStatusClass = ['pending' => 'text-amber-700','paid' => 'text-emerald-700','failed' => 'text-red-700','refunded' => 'text-gray-500'][$pesanan->payment_status] ?? 'text-gray-500';
                    $payStatusLabel = ['pending' => 'Menunggu','paid' => 'Lunas','failed' => 'Gagal','refunded' => 'Dana Dikembalikan'][$pesanan->payment_status] ?? '-';
                @endphp
                <span class="font-semibold {{ $payStatusClass }}">{{ $payStatusLabel }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-white/50">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-semibold text-gray-800 tabular-nums">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Ongkir ({{ $pesanan->jarak_km > 0 ? $pesanan->jarak_km . ' km' : 'Ambil di Tempat' }})</span>
                <span class="font-semibold text-gray-800 tabular-nums">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t-2 border-white/50">
                <span class="font-bold text-gray-800">Total</span>
                <span class="text-xl price text-gradient-green tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        @if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status !== 'paid' && $pesanan->order_status !== 'dibatalkan')
            <div class="mt-5 pt-5 border-t border-white/50">
                <div style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);" class="rounded-2xl p-5 text-white mb-4 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="font-bold text-sm tracking-wide">{{ $pesanan->bank_tujuan }} Virtual Account</span>
                        </div>
                        <span class="text-xs bg-white/20 backdrop-blur-md px-2.5 py-1 rounded-lg">{{ $pesanan->bank_tujuan }}</span>
                    </div>
                    <p class="text-xs opacity-80 mb-1">Nomor Virtual Account</p>
                    <div class="flex items-center gap-2 mb-4">
                        <p id="vaNumberDetail" class="text-2xl font-bold tracking-widest font-mono tabular-nums">{{ $pesanan->va_number }}</p>
                        <button type="button" onclick="copyVADetail()" class="ml-auto bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span id="copyLabelDetail">Copy</span>
                        </button>
                    </div>
                    <div class="border-t border-white/30 pt-3">
                        <p class="text-xs opacity-80 mb-1">Total Transfer</p>
                        <p class="text-2xl font-bold tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if ($pesanan->expired_at)
                    <div class="glass rounded-xl p-3 text-xs text-red-700 mb-4 border-red-300/50 tabular-nums">
                        ⏰ Selesaikan pembayaran sebelum <strong>{{ $pesanan->expired_at->format('d M Y, H:i') }}</strong> WIB
                    </div>
                @endif

                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Upload Bukti Transfer</p>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl border border-white/60">
                        <p class="text-xs text-emerald-600 mt-2 font-medium">✓ Bukti sudah diupload, menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.pesanan.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="glass-input w-full px-3 py-2 text-sm mb-3 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="glass-btn-primary w-full py-2.5 bg-gradient-to-br from-blue-500 to-blue-700 shadow-blue-500/40">
                        Upload Bukti
                    </button>
                </form>
            </div>
        @endif

        @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer && $pesanan->payment_status === 'paid')
            <div class="mt-5 pt-5 border-t border-white/50">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Bukti Transfer Anda</p>
                <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl border border-white/60">
            </div>
        @endif
    </div>

    @if ($bolehCancel)
        @php
            $cancelData = [
                'kode' => $pesanan->kode_pesanan,
                'total' => (int) $pesanan->total_harga,
                'payment_method' => $pesanan->payment_method,
                'payment_status' => $pesanan->payment_status,
                'route' => route('user.pesanan.cancel', $pesanan->kode_pesanan),
            ];
        @endphp
    @endif

    <div class="flex flex-wrap gap-3">
        @if ($bolehCancel)
            <button type="button"
                    data-cancel="{{ json_encode($cancelData, JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                    onclick="openCancelModal(JSON.parse(this.dataset.cancel))"
                    class="glass-btn px-5 py-2.5 text-red-600 border-red-300/50 hover:bg-red-500/10">
                Batalkan Pesanan
            </button>
        @elseif ($menungguVerifikasi)
            <button type="button" disabled
                    title="Harap tunggu admin memverifikasi pembayaran Anda terlebih dahulu."
                    class="glass-input px-5 py-2.5 text-gray-400 cursor-not-allowed font-semibold">
                Batalkan Pesanan
            </button>
        @endif
        <a href="{{ route('user.pesanan.index') }}" class="glass-btn-primary px-5 py-2.5">
            Kembali ke Daftar
        </a>
    </div>

    @if ($menungguVerifikasi)
        <div class="mt-4 flex items-start gap-3 glass-amber text-amber-800 rounded-xl p-4">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-sm">
                <p class="font-bold mb-1">Menunggu Verifikasi Pembayaran</p>
                <p class="opacity-90">Pembayaran Anda sedang menunggu verifikasi admin. Harap tunggu sebelum membatalkan pesanan.</p>
            </div>
        </div>
    @endif
</div>

{{-- MODAL CANCEL (sama persis dengan index) --}}
<div id="cancelModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 460px; max-height: 90vh;" class="glass-card overflow-hidden shadow-2xl flex flex-col" onclick="event.stopPropagation()">
        <div class="px-5 py-4 bg-gradient-to-br from-red-500 to-red-600 text-white flex-shrink-0">
            <h3 class="text-lg font-bold">Batalkan Pesanan?</h3>
            <p class="text-xs opacity-90 mt-0.5">Pesanan #<span id="cKode"></span></p>
        </div>
        <form id="cancelForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 overflow-y-auto">
            @csrf
            <input type="hidden" name="batalkan_bukti" id="flagInput" value="0">

            <div class="glass-amber rounded-xl p-3 text-xs text-amber-800">
                ⚠️ <strong>Setelah dibatalkan, pesanan tidak dapat dikembalikan ke status semula.</strong>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                <textarea name="alasan_batal" required rows="3" minlength="10" maxlength="500"
                          class="glass-input w-full px-4 py-2.5 text-sm text-gray-700"
                          placeholder="Min 10 karakter">{{ old('alasan_batal') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer (Opsional)</label>
                <p class="text-xs text-gray-500 mb-2">Kalau sudah transfer sebelumnya, upload bukti biar admin bisa proses refund lebih cepat.</p>

                <input type="file" id="cancelBuktiInput" name="bukti_transfer"
                       accept="image/jpeg,image/jpg,image/png,image/webp"
                       onchange="onFileSelected(this)" class="hidden">

                <div id="undoBanner" class="hidden mb-3 flex items-center gap-3 px-3 py-2 glass-amber rounded-xl">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="flex-1 text-xs text-amber-800 truncate">
                        File <strong id="undoFileName"></strong> dibatalkan
                    </span>
                    <button type="button" onclick="undoFile()"
                            class="text-xs text-emerald-600 hover:text-emerald-700 font-bold underline flex-shrink-0">
                        Urungkan
                    </button>
                </div>

                <div id="previewBox" class="hidden mb-3 p-3 glass rounded-xl border-emerald-300/50">
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="position: relative; flex-shrink: 0;">
                            <div onclick="openLightbox(document.getElementById('previewImg').src)"
                                 style="position: relative; width: 120px; height: 120px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
                                 onmouseover="this.querySelector('.zoom-overlay').style.opacity='1'"
                                 onmouseout="this.querySelector('.zoom-overlay').style.opacity='0'">
                                <img id="previewImg" src="" alt="Preview"
                                     style="width: 120px; height: 120px; object-fit: cover; display: block;">
                                <div class="zoom-overlay"
                                     style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                </div>
                            </div>
                            <button type="button" onclick="cancelFile()"
                                    style="position: absolute; top: -10px; right: -10px; width: 28px; height: 28px; background: rgba(255,255,255,0.98); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.2); padding: 0; transition: all 0.15s;"
                                    onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.background='rgba(255,255,255,0.98)'; this.style.color='#ef4444'; this.style.transform='scale(1)'"
                                    title="Batalkan file">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div style="flex: 1; min-width: 0; padding-top: 4px;">
                            <p class="text-[11px] font-bold text-emerald-700 uppercase mb-1">Preview</p>
                            <p id="fileNamePreview" class="text-sm text-gray-800 font-medium break-all"></p>
                            <p class="text-xs text-gray-500 mt-1">Klik gambar untuk memperbesar.</p>
                        </div>
                    </div>
                </div>

                <div class="glass-input flex items-center gap-3 px-3 py-2">
                    <button type="button" onclick="document.getElementById('cancelBuktiInput').click()"
                            class="px-3 py-1.5 bg-blue-500/15 hover:bg-blue-500/25 text-blue-700 rounded-lg text-xs font-semibold transition flex-shrink-0">
                        Choose File
                    </button>
                    <span id="fileLabel" class="flex-1 min-w-0 text-xs break-all text-gray-400">No file chosen</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" class="glass-btn flex-1 py-2.5">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-br from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg shadow-red-500/30 transition hover:-translate-y-0.5">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

{{-- LIGHTBOX --}}
<div id="lightboxModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 9999; background-color: rgba(0,0,0,0.9); backdrop-filter: blur(12px);">
    <button type="button" onclick="closeLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 44px; height: 44px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img id="lightboxImg" src="" alt="Preview"
         onclick="event.stopPropagation()"
         style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
</div>
@endsection

@push('scripts')
<script>
    function onFileSelected(input) {
        const file = input.files[0];
        if (!file) return;
        const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) { alert('⚠️ Hanya file gambar (JPG, PNG, WEBP).'); input.value = ''; return; }
        if (file.size > 2 * 1024 * 1024) { alert('⚠️ Ukuran maksimal 2MB.'); input.value = ''; return; }

        document.getElementById('flagInput').value = '0';
        document.getElementById('previewImg').src = URL.createObjectURL(file);
        document.getElementById('previewBox').classList.remove('hidden');
        document.getElementById('fileNamePreview').textContent = file.name;
        document.getElementById('fileLabel').textContent = file.name;
        document.getElementById('fileLabel').classList.remove('text-gray-400');
        document.getElementById('fileLabel').classList.add('text-gray-800', 'font-medium');
        document.getElementById('undoBanner').classList.add('hidden');
    }

    function cancelFile() {
        const input = document.getElementById('cancelBuktiInput');
        if (!input.files || !input.files[0]) return;
        document.getElementById('flagInput').value = '1';
        document.getElementById('previewBox').classList.add('hidden');
        document.getElementById('fileLabel').textContent = 'No file chosen';
        document.getElementById('fileLabel').classList.add('text-gray-400');
        document.getElementById('fileLabel').classList.remove('text-gray-800', 'font-medium');
        document.getElementById('undoFileName').textContent = input.files[0].name;
        document.getElementById('undoBanner').classList.remove('hidden');
    }

    function undoFile() {
        const input = document.getElementById('cancelBuktiInput');
        if (!input.files || !input.files[0]) {
            document.getElementById('undoBanner').classList.add('hidden');
            return;
        }
        document.getElementById('flagInput').value = '0';
        const file = input.files[0];
        document.getElementById('previewImg').src = URL.createObjectURL(file);
        document.getElementById('previewBox').classList.remove('hidden');
        document.getElementById('fileNamePreview').textContent = file.name;
        document.getElementById('fileLabel').textContent = file.name;
        document.getElementById('fileLabel').classList.remove('text-gray-400');
        document.getElementById('fileLabel').classList.add('text-gray-800', 'font-medium');
        document.getElementById('undoBanner').classList.add('hidden');
    }

    function openLightbox(url) {
        if (!url) return;
        const lb = document.getElementById('lightboxModal');
        document.getElementById('lightboxImg').src = url;
        lb.classList.remove('hidden');
        lb.classList.add('flex');
    }

    function closeLightbox() {
        const lb = document.getElementById('lightboxModal');
        lb.classList.add('hidden');
        lb.classList.remove('flex');
        document.getElementById('lightboxImg').src = '';
    }

    document.getElementById('lightboxModal').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });

    function copyVADetail() {
        const el = document.getElementById('vaNumberDetail');
        if (!el) return;
        navigator.clipboard.writeText(el.textContent.trim()).then(() => {
            const label = document.getElementById('copyLabelDetail');
            label.textContent = 'Copied!';
            setTimeout(() => { label.textContent = 'Copy'; }, 1500);
        });
    }

    function openCancelModal(data) {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.getElementById('cKode').textContent = data.kode;
        document.getElementById('cancelForm').action = data.route;

        const input = document.getElementById('cancelBuktiInput');
        if (input) input.value = '';
        document.getElementById('flagInput').value = '0';
        document.getElementById('previewBox').classList.add('hidden');
        document.getElementById('undoBanner').classList.add('hidden');
        document.getElementById('fileLabel').textContent = 'No file chosen';
        document.getElementById('fileLabel').classList.add('text-gray-400');
        document.getElementById('fileLabel').classList.remove('text-gray-800', 'font-medium');
    }

    function closeCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            if (!document.getElementById('lightboxModal').classList.contains('hidden')) {
                closeLightbox();
                return;
            }
            closeCancelModal();
        }
    });
</script>
@endpush