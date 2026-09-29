@extends('layouts.user')
@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <a href="{{ route('user.pesanan.index') }}" class="hover:text-emerald-600">Pesanan Saya</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">#{{ $pesanan->kode_pesanan }}</span>
    </nav>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    @php
        $statusClass = [
            'pending'    => 'bg-amber-100 text-amber-700',
            'diproses'   => 'bg-blue-100 text-blue-700',
            'dikirim'    => 'bg-indigo-100 text-indigo-700',
            'selesai'    => 'bg-emerald-100 text-emerald-700',
            'dibatalkan' => 'bg-red-100 text-red-700',
        ][$pesanan->order_status] ?? 'bg-gray-100 text-gray-700';

        $bolehCancel = in_array($pesanan->order_status, ['pending', 'diproses']);
        $menungguVerifikasi = $pesanan->payment_method === 'Transfer'
                            && $pesanan->payment_status === 'pending'
                            && !in_array($pesanan->order_status, ['dibatalkan', 'selesai']);
    @endphp

    @if ($pesanan->payment_status === 'paid' && $pesanan->order_status !== 'dibatalkan')
        <div class="rounded-2xl p-6 mb-5 shadow-lg text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-lg font-bold mb-1">Pembayaran Berhasil! 🎉</p>
                    <p class="text-sm opacity-90">Pembayaran Anda sudah kami terima dan terverifikasi.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Kode Pesanan</p>
                <p class="text-2xl font-bold text-gray-800">#{{ $pesanan->kode_pesanan }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-lg text-sm font-bold {{ $statusClass }}">{{ ucfirst($pesanan->order_status) }}</span>
        </div>

        @if ($pesanan->order_status === 'dibatalkan')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                <p class="text-sm font-bold text-red-800 mb-1">Pesanan Dibatalkan</p>
                <p class="text-sm text-red-700">Alasan: {{ $pesanan->alasan_batal ?? $pesanan->refund->alasan_batal ?? 'Tidak ada alasan yang dicatat.' }}</p>
            </div>

            @if ($pesanan->refund)
                <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-red-600 uppercase mb-3">💰 Info Refund</p>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-red-500">Nominal</p>
                            <p class="font-bold text-red-800">Rp {{ number_format($pesanan->refund->nominal_refund, 0, ',', '.') }}</p>
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
                                <div class="bg-white border border-red-200 rounded-xl p-3">
                                    <p class="text-red-800 leading-relaxed">{{ $pesanan->refund->catatan_admin }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($pesanan->refund->bukti_transfer_balik)
                            <div class="col-span-2">
                                <p class="text-xs text-red-500 mb-1">Bukti Transfer Balik</p>
                                <img src="{{ asset('storage/' . $pesanan->refund->bukti_transfer_balik) }}" class="w-full max-w-xs rounded-xl border border-red-200">
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-3">📦 Info Pengiriman</h3>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $pesanan->alamat_pengiriman }}</p>
        <p class="text-xs text-gray-500 mt-2"><strong>Tipe:</strong> {{ $pesanan->delivery_label }}</p>
        @if ($pesanan->catatan)
            <p class="text-xs text-gray-500 mt-2"><strong>Catatan:</strong> {{ $pesanan->catatan }}</p>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-4">🛒 Item Pesanan</h3>
        <div class="space-y-3">
            @foreach ($pesanan->detail as $d)
                <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-lg flex-shrink-0">
                        {{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $d->nama_item }}</p>
                        <p class="text-xs text-gray-500">{{ $d->jumlah }} × Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
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
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Ongkir ({{ $pesanan->jarak_km > 0 ? $pesanan->jarak_km . ' km' : 'Ambil di Tempat' }})</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100">
                <span class="font-bold text-gray-800">Total</span>
                <span class="text-xl font-bold text-emerald-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        @if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status !== 'paid' && $pesanan->order_status !== 'dibatalkan')
            <div class="mt-5 pt-5 border-t border-gray-100">
                <div style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);" class="rounded-2xl p-5 text-white mb-4 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="font-bold text-sm tracking-wide">{{ $pesanan->bank_tujuan }} Virtual Account</span>
                        </div>
                        <span class="text-xs bg-white/20 px-2.5 py-1 rounded-lg">{{ $pesanan->bank_tujuan }}</span>
                    </div>
                    <p class="text-xs opacity-80 mb-1">Nomor Virtual Account</p>
                    <div class="flex items-center gap-2 mb-4">
                        <p id="vaNumberDetail" class="text-2xl font-bold tracking-widest font-mono">{{ $pesanan->va_number }}</p>
                        <button type="button" onclick="copyVADetail()" class="ml-auto bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span id="copyLabelDetail">Copy</span>
                        </button>
                    </div>
                    <div class="border-t border-white/30 pt-3">
                        <p class="text-xs opacity-80 mb-1">Total Transfer</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if ($pesanan->expired_at)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-700 mb-4">
                        ⏰ Selesaikan pembayaran sebelum <strong>{{ $pesanan->expired_at->format('d M Y, H:i') }}</strong> WIB
                    </div>
                @endif

                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Upload Bukti Transfer</p>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl border border-gray-200">
                        <p class="text-xs text-emerald-600 mt-2 font-medium">✓ Bukti sudah diupload, menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.pesanan.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm mb-3 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                        Upload Bukti
                    </button>
                </form>
            </div>
        @endif

        @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer && $pesanan->payment_status === 'paid')
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Bukti Transfer Anda</p>
                <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl border border-gray-200">
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
                    class="px-5 py-2.5 bg-white border border-red-200 hover:bg-red-50 text-red-600 font-semibold rounded-xl transition">
                Batalkan Pesanan
            </button>
        @elseif ($menungguVerifikasi)
            <button type="button" disabled
                    title="Harap tunggu admin memverifikasi pembayaran Anda terlebih dahulu."
                    class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-400 font-semibold rounded-xl cursor-not-allowed">
                Batalkan Pesanan
            </button>
        @endif
        <a href="{{ route('user.pesanan.index') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition">
            Kembali ke Daftar
        </a>
    </div>

    @if ($menungguVerifikasi)
        <div class="mt-4 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-sm">
                <p class="font-bold mb-1">Menunggu Verifikasi Pembayaran</p>
                <p class="opacity-90">Pembayaran Anda sedang menunggu verifikasi admin. Harap tunggu sebelum membatalkan pesanan.</p>
            </div>
        </div>
    @endif
</div>

{{-- MODAL CANCEL --}}
<div id="cancelModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.6);">
    <div style="width: 100%; max-width: 460px; max-height: 90vh;" class="bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col">
        <div class="px-5 py-4 bg-red-500 text-white flex-shrink-0">
            <h3 class="text-lg font-bold">Batalkan Pesanan?</h3>
            <p class="text-xs opacity-90 mt-0.5">Pesanan #<span id="cKode"></span></p>
        </div>
        <form id="cancelForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 overflow-y-auto">
            @csrf
            <input type="hidden" name="batalkan_bukti" id="flagInput" value="0">

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                ⚠️ <strong>Setelah dibatalkan, pesanan tidak dapat dikembalikan ke status semula.</strong>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                <textarea name="alasan_batal" required rows="3" minlength="10" maxlength="500"
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                          placeholder="Min 10 karakter">{{ old('alasan_batal') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer (Opsional)</label>
                <p class="text-xs text-gray-500 mb-2">Kalau sudah transfer sebelumnya, upload bukti biar admin bisa proses refund lebih cepat.</p>

                <input type="file" id="cancelBuktiInput" name="bukti_transfer"
                       accept="image/jpeg,image/jpg,image/png,image/webp"
                       onchange="onFileSelected(this)" class="hidden">

                {{-- Undo Banner --}}
                <div id="undoBanner" class="hidden mb-3 flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="flex-1 text-xs text-amber-800 truncate">
                        File <strong id="undoFileName"></strong> dibatalkan
                    </span>
                    <button type="button" onclick="undoFile()"
                            class="text-xs text-emerald-600 hover:text-emerald-700 font-bold underline flex-shrink-0">
                        Urungkan
                    </button>
                </div>

                {{-- Preview --}}
                <div id="previewBox" class="hidden mb-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
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

                <div class="flex items-center gap-3 px-3 py-2 rounded-xl border border-gray-200 bg-white focus-within:ring-2 focus-within:ring-blue-500 transition">
                    <button type="button" onclick="document.getElementById('cancelBuktiInput').click()"
                            class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition flex-shrink-0">
                        Choose File
                    </button>
                    <span id="fileLabel" class="flex-1 min-w-0 text-xs break-all text-gray-400">No file chosen</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">Batal</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-sm transition">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

{{-- LIGHTBOX --}}
<div id="lightboxModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 9999; background-color: rgba(0,0,0,0.9);">
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
    console.log('✅ User Pesanan Cancel Script Loaded');

    function onFileSelected(input) {
        console.log('📁 File selected:', input.files[0] ? input.files[0].name : 'none');
        const file = input.files[0];
        if (!file) return;

        const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
            alert('⚠️ Hanya file gambar (JPG, PNG, WEBP).');
            input.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert('⚠️ Ukuran maksimal 2MB.');
            input.value = '';
            return;
        }

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
        console.log('❌ Cancel file clicked');
        const input = document.getElementById('cancelBuktiInput');

        if (!input.files || !input.files[0]) {
            console.warn('No file to cancel');
            return;
        }

        document.getElementById('flagInput').value = '1';
        document.getElementById('previewBox').classList.add('hidden');
        document.getElementById('fileLabel').textContent = 'No file chosen';
        document.getElementById('fileLabel').classList.add('text-gray-400');
        document.getElementById('fileLabel').classList.remove('text-gray-800', 'font-medium');

        document.getElementById('undoFileName').textContent = input.files[0].name;
        document.getElementById('undoBanner').classList.remove('hidden');
    }

    function undoFile() {
        console.log('↩️ Undo clicked');
        const input = document.getElementById('cancelBuktiInput');

        if (!input.files || !input.files[0]) {
            console.warn('⚠️ File sudah tidak ada di input');
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
        console.log('✅ Preview restored:', file.name);
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