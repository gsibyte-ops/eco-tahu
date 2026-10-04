@extends('layouts.user')
@section('title', 'Detail Pesanan #' . $pesanan->kode_pesanan)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <a href="{{ route('user.pesanan.index') }}" class="hover:text-emerald-500 transition">Pesanan Saya</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium tabular-nums" style="color: rgb(var(--text-primary));">#{{ $pesanan->kode_pesanan }}</span>
    </nav>

    @if (session('success'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); border: 1px solid rgb(var(--success) / 0.3); background: rgb(var(--success-soft));">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">{{ session('error') }}</div>
    @endif

    @php
        $statusStyle = [
            'pending'    => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning)); border-color: rgb(var(--warning) / 0.3);',
            'diproses'   => 'background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);',
            'dikirim'    => 'background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);',
            'selesai'    => 'background: rgb(var(--success-soft)); color: rgb(var(--success)); border-color: rgb(var(--success) / 0.3);',
            'dibatalkan' => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger)); border-color: rgb(var(--danger) / 0.3);',
        ][$pesanan->order_status] ?? 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary)); border-color: rgb(var(--border));';

        $bolehCancel = in_array($pesanan->order_status, ['pending', 'diproses']);
        $menungguVerifikasi = $pesanan->payment_method === 'Transfer'
                            && $pesanan->payment_status === 'pending'
                            && !in_array($pesanan->order_status, ['dibatalkan', 'selesai']);

        $trackingAktif = $pesanan->order_status === 'dikirim'
                      && $pesanan->lat_kurir !== null
                      && $pesanan->lng_kurir !== null;
    @endphp

    @if ($pesanan->payment_status === 'paid' && $pesanan->order_status !== 'dibatalkan')
        <div class="rounded-2xl p-6 mb-5 text-white shadow-xl" style="background: var(--gradient-brand); box-shadow: 0 12px 32px -8px rgb(var(--brand) / 0.5);">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgb(255 255 255 / 0.2); backdrop-filter: blur(20px);">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-lg font-bold mb-1">Pembayaran Berhasil! 🎉</p>
                    <p class="text-sm opacity-90">Pembayaran Anda sudah kami terima dan terverifikasi.</p>
                </div>
            </div>
        </div>
    @endif

    @if ($pesanan->order_status === 'dikirim')
        <div id="trackingSection" class="glass-card p-5 mb-5">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <div>
                    <h3 class="font-bold flex items-center gap-2" style="color: rgb(var(--text-primary));">
                        <span class="w-2.5 h-2.5 rounded-full bg-violet-500 animate-pulse"></span>
                        Lacak Kurir
                    </h3>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));" id="trackingSubtitle">
                        @if ($trackingAktif) Kurir sedang dalam perjalanan ke lokasi Anda.
                        @else Menunggu kurir memulai pengiriman... @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="trackingStatusBadge"
                          class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $trackingAktif ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        {{ $trackingAktif ? 'LIVE' : 'MENUNGGU' }}
                    </span>
                </div>
            </div>

            <div id="userTrackingMap"
                 style="width: 100%; height: 350px; border-radius: 1rem; overflow: hidden; background: rgb(var(--bg-secondary)); position: relative;"
                 data-tracking-active="{{ $trackingAktif ? '1' : '0' }}"
                 data-lat-toko="{{ config('toko.lat') }}"
                 data-lng-toko="{{ config('toko.lng') }}"
                 data-nama-toko="{{ config('toko.nama') }}"
                 data-lat-tujuan="{{ $pesanan->lat_tujuan ?? '' }}"
                 data-lng-tujuan="{{ $pesanan->lng_tujuan ?? '' }}"
                 data-lat-kurir="{{ $pesanan->lat_kurir ?? '' }}"
                 data-lng-kurir="{{ $pesanan->lng_kurir ?? '' }}"
                 data-route-tracking="{{ route('user.pesanan.tracking', $pesanan->kode_pesanan) }}"></div>

            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="glass rounded-xl p-3">
                    <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Update Terakhir</p>
                    <p id="trackingUpdateTime" class="text-sm font-bold tabular-nums" style="color: rgb(var(--text-primary));">
                        @if ($pesanan->lokasi_updated_at) {{ $pesanan->lokasi_updated_at->format('H:i:s') }} WIB @else - @endif
                    </p>
                </div>
                <div class="glass rounded-xl p-3">
                    <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Status</p>
                    <p id="trackingStatusText" class="text-sm font-bold" style="color: rgb(var(--text-primary));">
                        @if ($trackingAktif) Sedang di jalan @else Menunggu kurir @endif
                    </p>
                </div>
            </div>

            @if (!$trackingAktif)
                <div class="mt-3 glass rounded-xl p-3 text-xs" style="color: rgb(var(--accent)); border-color: rgb(var(--accent) / 0.3); background: rgb(var(--accent-soft));">
                    ⏳ Pesanan Anda sedang disiapkan untuk pengiriman. Peta akan muncul begitu kurir mulai bergerak.
                </div>
            @endif
        </div>
    @endif

    <div class="glass-card p-6 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Kode Pesanan</p>
                <p class="text-2xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">#{{ $pesanan->kode_pesanan }}</p>
                <p class="text-sm mt-1 tabular-nums" style="color: rgb(var(--text-secondary));">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-lg text-sm font-bold border" style="{{ $statusStyle }}">{{ ucfirst($pesanan->order_status) }}</span>
        </div>

        @if ($pesanan->order_status === 'dibatalkan')
            <div class="glass rounded-xl p-4 mb-4" style="border-color: rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">
                <p class="text-sm font-bold mb-1" style="color: rgb(var(--danger));">Pesanan Dibatalkan</p>
                <p class="text-sm" style="color: rgb(var(--danger));">Alasan: {{ $pesanan->alasan_batal ?? $pesanan->refund->alasan_batal ?? 'Tidak ada alasan yang dicatat.' }}</p>
            </div>

            @if ($pesanan->refund)
                <div class="glass rounded-xl p-4" style="border-color: rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">
                    <p class="text-xs font-bold uppercase mb-3" style="color: rgb(var(--danger));">💰 Info Refund</p>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs" style="color: rgb(var(--danger));">Nominal</p>
                            <p class="font-bold tabular-nums" style="color: rgb(var(--danger));">Rp {{ number_format($pesanan->refund->nominal_refund, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs" style="color: rgb(var(--danger));">Status Refund</p>
                            @php
                                $refundLabel = [
                                    'pending' => '⏳ Menunggu Diproses',
                                    'diproses' => '🔄 Sedang Diproses',
                                    'selesai' => '✅ Sudah Ditransfer',
                                    'ditolak' => '❌ Ditolak',
                                ][$pesanan->refund->status_refund] ?? '-';
                            @endphp
                            <p class="font-bold" style="color: rgb(var(--danger));">{{ $refundLabel }}</p>
                        </div>
                        @if ($pesanan->refund->catatan_admin)
                            <div class="col-span-2">
                                <p class="text-xs mb-1" style="color: rgb(var(--danger));">💬 Pesan dari Admin</p>
                                <div class="glass-card p-3">
                                    <p class="leading-relaxed" style="color: rgb(var(--text-primary));">{{ $pesanan->refund->catatan_admin }}</p>
                                </div>
                            </div>
                        @endif
                        @if ($pesanan->refund->bukti_transfer_balik)
                            <div class="col-span-2">
                                <p class="text-xs mb-1" style="color: rgb(var(--danger));">Bukti Transfer Balik</p>
                                <img src="{{ asset('storage/' . $pesanan->refund->bukti_transfer_balik) }}" class="w-full max-w-xs rounded-xl" style="border: 1px solid rgb(var(--danger) / 0.3);">
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif
    </div>

    <div class="glass-card p-6 mb-5">
        <h3 class="font-bold mb-3" style="color: rgb(var(--text-primary));">📦 Info Pengiriman</h3>
        <p class="text-sm leading-relaxed" style="color: rgb(var(--text-secondary));">{{ $pesanan->alamat_pengiriman }}</p>
        <p class="text-xs mt-2" style="color: rgb(var(--text-secondary));"><strong style="color: rgb(var(--text-primary));">Tipe:</strong> {{ $pesanan->delivery_label }}</p>
        @if ($pesanan->kecamatan)
            <p class="text-xs mt-2" style="color: rgb(var(--text-secondary));"><strong style="color: rgb(var(--text-primary));">Kecamatan:</strong> {{ $pesanan->kecamatan }}</p>
        @endif
        @if ($pesanan->catatan)
            <p class="text-xs mt-2" style="color: rgb(var(--text-secondary));"><strong style="color: rgb(var(--text-primary));">Catatan:</strong> {{ $pesanan->catatan }}</p>
        @endif
    </div>

    <div class="glass-card p-6 mb-5">
        <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">🛒 Item Pesanan</h3>
        <div class="space-y-3">
            @foreach ($pesanan->detail as $d)
                <div class="flex items-center gap-3 py-2 last:border-0" style="border-bottom: 1px solid rgb(var(--border-soft));">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg flex-shrink-0" style="background: rgb(var(--bg-secondary));">
                        {{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color: rgb(var(--text-primary));">{{ $d->nama_item }}</p>
                        <p class="text-xs tabular-nums" style="color: rgb(var(--text-secondary));">{{ $d->jumlah }} × Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass-card p-6 mb-5">
        <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">💳 Pembayaran</h3>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Metode</span>
                <span class="font-semibold" style="color: rgb(var(--text-primary));">{{ $pesanan->payment_method }} @if ($pesanan->bank_tujuan) - {{ $pesanan->bank_tujuan }} @endif</span>
            </div>
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Status Bayar</span>
                @php
                    $payStatusStyle = ['pending' => 'color: rgb(var(--warning));','paid' => 'color: rgb(var(--success));','failed' => 'color: rgb(var(--danger));','refunded' => 'color: rgb(var(--text-muted));'][$pesanan->payment_status] ?? 'color: rgb(var(--text-muted));';
                    $payStatusLabel = ['pending' => 'Menunggu','paid' => 'Lunas','failed' => 'Gagal','refunded' => 'Dana Dikembalikan'][$pesanan->payment_status] ?? '-';
                @endphp
                <span class="font-semibold" style="{{ $payStatusStyle }}">{{ $payStatusLabel }}</span>
            </div>
            <div class="flex justify-between pt-3" style="border-top: 1px solid rgb(var(--border-soft));">
                <span style="color: rgb(var(--text-secondary));">Subtotal</span>
                <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color: rgb(var(--text-secondary));">Ongkir ({{ $pesanan->jarak_km > 0 ? $pesanan->jarak_km . ' km' : 'Ambil di Tempat' }})</span>
                <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pt-3" style="border-top: 1px solid rgb(var(--border-soft));">
                <span class="font-bold" style="color: rgb(var(--text-primary));">Total</span>
                <span class="text-xl price text-gradient-green tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        @if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status !== 'paid' && $pesanan->order_status !== 'dibatalkan')
            <div class="mt-5 pt-5" style="border-top: 1px solid rgb(var(--border-soft));">
                <div style="background: var(--gradient-info);" class="rounded-2xl p-5 text-white mb-4 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="font-bold text-sm tracking-wide">{{ $pesanan->bank_tujuan }} Virtual Account</span>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-lg" style="background: rgb(255 255 255 / 0.2); backdrop-filter: blur(20px);">{{ $pesanan->bank_tujuan }}</span>
                    </div>
                    <p class="text-xs opacity-80 mb-1">Nomor Virtual Account</p>
                    <div class="flex items-center gap-2 mb-4">
                        <p id="vaNumberDetail" class="text-2xl font-bold tracking-widest font-mono tabular-nums">{{ $pesanan->va_number }}</p>
                        <button type="button" onclick="copyVADetail()" class="ml-auto px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1 transition" style="background: rgb(255 255 255 / 0.2); backdrop-filter: blur(20px);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span id="copyLabelDetail">Copy</span>
                        </button>
                    </div>
                    <div class="pt-3" style="border-top: 1px solid rgb(255 255 255 / 0.3);">
                        <p class="text-xs opacity-80 mb-1">Total Transfer</p>
                        <p class="text-2xl font-bold tabular-nums">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if ($pesanan->expired_at)
                    <div class="glass rounded-xl p-3 text-xs mb-4 tabular-nums" style="color: rgb(var(--danger)); border-color: rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">
                        ⏰ Selesaikan pembayaran sebelum <strong>{{ $pesanan->expired_at->format('d M Y, H:i') }}</strong> WIB
                    </div>
                @endif

                <p class="text-xs font-semibold uppercase mb-3" style="color: rgb(var(--text-muted));">Upload Bukti Transfer</p>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl" style="border: 1px solid rgb(var(--border));">
                        <p class="text-xs mt-2 font-medium" style="color: rgb(var(--success));">✓ Bukti sudah diupload, menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.pesanan.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="glass-input w-full px-3 py-2 text-sm mb-3 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="btn-primary w-full py-2.5" style="background: var(--gradient-info);">
                        Upload Bukti
                    </button>
                </form>
            </div>
        @endif

        @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer && $pesanan->payment_status === 'paid')
            <div class="mt-5 pt-5" style="border-top: 1px solid rgb(var(--border-soft));">
                <p class="text-xs font-semibold uppercase mb-3" style="color: rgb(var(--text-muted));">Bukti Transfer Anda</p>
                <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}" class="w-full max-w-xs rounded-xl" style="border: 1px solid rgb(var(--border));">
            </div>
        @endif
    </div>

    @if ($pesanan->order_status === 'selesai')
        <div class="glass-amber rounded-2xl p-5 mb-5">
            <p class="font-bold mb-2" style="color: rgb(var(--accent));">⭐ Beri Ulasan</p>
            <p class="text-sm mb-3" style="color: rgb(var(--accent));">Sudah menerima produk? Bagikan pengalaman Anda dengan memberi ulasan.</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($pesanan->detail as $d)
                    @php
                        $slug = null;
                        $itemType = null;
                        if ($d->item_type === 'App\\Models\\ProdukTahu') {
                            $slug = \App\Models\ProdukTahu::find($d->item_id)?->slug;
                            $itemType = 'produk';
                        } else {
                            $slug = \App\Models\Limbah::find($d->item_id)?->slug;
                            $itemType = 'limbah';
                        }
                    @endphp
                    @if ($slug)
                        <a href="{{ $itemType === 'produk' ? route('user.produk.show', $slug) : route('user.limbah.show', $slug) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition"
                           style="background: rgb(var(--surface)); color: rgb(var(--accent)); border: 1px solid rgb(var(--accent) / 0.3);">
                            ⭐ Ulas: {{ $d->nama_item }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

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
                    class="glass-btn px-5 py-2.5" style="color: rgb(var(--danger)); border-color: rgb(var(--danger) / 0.3);">
                Batalkan Pesanan
            </button>
        @elseif ($menungguVerifikasi)
            <button type="button" disabled
                    title="Harap tunggu admin memverifikasi pembayaran Anda terlebih dahulu."
                    class="glass-input px-5 py-2.5 font-semibold cursor-not-allowed" style="color: rgb(var(--text-muted));">
                Batalkan Pesanan
            </button>
        @endif
        <a href="{{ route('user.pesanan.index') }}" class="btn-primary !py-2.5 !px-5">
            Kembali ke Daftar
        </a>
    </div>

    @if ($menungguVerifikasi)
        <div class="mt-4 flex items-start gap-3 glass-amber rounded-xl p-4" style="color: rgb(var(--accent));">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-sm">
                <p class="font-bold mb-1">Menunggu Verifikasi Pembayaran</p>
                <p class="opacity-90">Pembayaran Anda sedang menunggu verifikasi admin. Harap tunggu sebelum membatalkan pesanan.</p>
            </div>
        </div>
    @endif
</div>

{{-- MODAL CANCEL + LIGHTBOX — sama kayak index.blade.php --}}
<div id="cancelModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 460px; max-height: 90vh;" class="glass-card overflow-hidden shadow-2xl flex flex-col" onclick="event.stopPropagation()">
        <div class="px-5 py-4 text-white flex-shrink-0" style="background: var(--gradient-danger);">
            <h3 class="text-lg font-bold">Batalkan Pesanan?</h3>
            <p class="text-xs opacity-90 mt-0.5">Pesanan #<span id="cKode"></span></p>
        </div>
        <form id="cancelForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 overflow-y-auto">
            @csrf
            <input type="hidden" name="batalkan_bukti" id="flagInput" value="0">

            <div class="glass-amber rounded-xl p-3 text-xs" style="color: rgb(var(--accent));">
                ⚠️ <strong>Setelah dibatalkan, pesanan tidak dapat dikembalikan ke status semula.</strong>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Alasan Pembatalan <span style="color: rgb(var(--danger));">*</span></label>
                <textarea name="alasan_batal" required rows="3" minlength="10" maxlength="500"
                          class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));"
                          placeholder="Min 10 karakter">{{ old('alasan_batal') }}</textarea>
            </div>

            <div id="buktiTransferWrap">
                <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Bukti Transfer (Opsional)</label>
                <p class="text-xs mb-2" style="color: rgb(var(--text-secondary));">Kalau sudah transfer sebelumnya, upload bukti biar admin bisa proses refund lebih cepat.</p>

                <input type="file" id="cancelBuktiInput" name="bukti_transfer"
                       accept="image/jpeg,image/jpg,image/png,image/webp"
                       onchange="onFileSelected(this)" class="hidden">

                <div id="undoBanner" class="hidden mb-3 flex items-center gap-3 px-3 py-2 glass-amber rounded-xl">
                    <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--accent));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="flex-1 text-xs truncate" style="color: rgb(var(--accent));">
                        File <strong id="undoFileName"></strong> dibatalkan
                    </span>
                    <button type="button" onclick="undoFile()"
                            class="text-xs font-bold underline flex-shrink-0" style="color: rgb(var(--brand));">
                        Urungkan
                    </button>
                </div>

                <div id="previewBox" class="hidden mb-3 p-3 glass rounded-xl" style="border-color: rgb(var(--success) / 0.3);">
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="position: relative; flex-shrink: 0;">
                            <div onclick="openLightbox(document.getElementById('previewImg').src)"
                                 style="position: relative; width: 120px; height: 120px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid rgb(var(--border)); box-shadow: var(--shadow-sm);">
                                <img id="previewImg" src="" alt="Preview"
                                     style="width: 120px; height: 120px; object-fit: cover; display: block;">
                            </div>
                            <button type="button" onclick="cancelFile()"
                                    style="position: absolute; top: -10px; right: -10px; width: 28px; height: 28px; background: rgb(var(--surface)); color: rgb(var(--danger)); border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: var(--shadow-md); padding: 0;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div style="flex: 1; min-width: 0; padding-top: 4px;">
                            <p class="text-[11px] font-bold uppercase mb-1" style="color: rgb(var(--brand));">Preview</p>
                            <p id="fileNamePreview" class="text-sm font-medium break-all" style="color: rgb(var(--text-primary));"></p>
                        </div>
                    </div>
                </div>

                <div class="glass-input flex items-center gap-3 px-3 py-2">
                    <button type="button" onclick="document.getElementById('cancelBuktiInput').click()"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0"
                            style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                        Choose File
                    </button>
                    <span id="fileLabel" class="flex-1 min-w-0 text-xs break-all" style="color: rgb(var(--text-muted));">No file chosen</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()" class="glass-btn flex-1 py-2.5">Batal</button>
                <button type="submit" class="flex-1 py-2.5 text-white font-semibold rounded-xl transition hover:-translate-y-0.5" style="background: var(--gradient-danger); box-shadow: var(--shadow-md);">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

<div id="lightboxModal" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 9999; background-color: rgba(0,0,0,0.9); backdrop-filter: blur(12px);">
    <button type="button" onclick="closeLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 44px; height: 44px; background: rgb(var(--danger)); color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-lg);">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <img id="lightboxImg" src="" alt="Preview"
         onclick="event.stopPropagation()"
         style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
        document.getElementById('fileLabel').style.color = 'rgb(var(--text-primary))';
        document.getElementById('undoBanner').classList.add('hidden');
    }

    function cancelFile() {
        const input = document.getElementById('cancelBuktiInput');
        if (!input.files || !input.files[0]) return;
        document.getElementById('flagInput').value = '1';
        document.getElementById('previewBox').classList.add('hidden');
        document.getElementById('fileLabel').textContent = 'No file chosen';
        document.getElementById('fileLabel').style.color = 'rgb(var(--text-muted))';
        document.getElementById('undoFileName').textContent = input.files[0].name;
        document.getElementById('undoBanner').classList.remove('hidden');
    }

    function undoFile() {
        const input = document.getElementById('cancelBuktiInput');
        if (!input.files || !input.files[0]) { document.getElementById('undoBanner').classList.add('hidden'); return; }
        document.getElementById('flagInput').value = '0';
        const file = input.files[0];
        document.getElementById('previewImg').src = URL.createObjectURL(file);
        document.getElementById('previewBox').classList.remove('hidden');
        document.getElementById('fileNamePreview').textContent = file.name;
        document.getElementById('fileLabel').textContent = file.name;
        document.getElementById('fileLabel').style.color = 'rgb(var(--text-primary))';
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

        const buktiWrap = document.getElementById('buktiTransferWrap');
        if (buktiWrap) buktiWrap.style.display = (data.payment_method === 'COD') ? 'none' : '';

        const input = document.getElementById('cancelBuktiInput');
        if (input) input.value = '';
        document.getElementById('flagInput').value = '0';
        document.getElementById('previewBox').classList.add('hidden');
        document.getElementById('undoBanner').classList.add('hidden');
        document.getElementById('fileLabel').textContent = 'No file chosen';
        document.getElementById('fileLabel').style.color = 'rgb(var(--text-muted))';
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
            if (!document.getElementById('lightboxModal').classList.contains('hidden')) { closeLightbox(); return; }
            closeCancelModal();
        }
    });

    // ============================================================
    // TRACKING KURIR
    // ============================================================
    (function() {
        const mapEl = document.getElementById('userTrackingMap');
        if (!mapEl) return;

        const latToko = parseFloat(mapEl.dataset.latToko);
        const lngToko = parseFloat(mapEl.dataset.lngToko);
        const namaToko = mapEl.dataset.namaToko;
        const latTujuan = mapEl.dataset.latTujuan ? parseFloat(mapEl.dataset.latTujuan) : null;
        const lngTujuan = mapEl.dataset.lngTujuan ? parseFloat(mapEl.dataset.lngTujuan) : null;
        const routeTracking = mapEl.dataset.routeTracking;
        const namaPenerima = @json($pesanan->user->username ?? 'Anda');

        let map = null;
        let markerKurir = null;
        let markerToko = null;
        let markerTujuan = null;
        let lineKeToko = null;
        let pollingInterval = null;

        function initMap() {
            const centerLat = latTujuan ?? latToko;
            const centerLng = lngTujuan ?? lngToko;

            map = L.map('userTrackingMap', { zoomControl: true, scrollWheelZoom: false }).setView([centerLat, centerLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap', maxZoom: 19,
            }).addTo(map);

            const tokoIcon = L.divIcon({
                className: 'toko-marker',
                html: '<div style="background:#10b981; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow: 0 4px 12px rgba(16,185,129,0.5); border: 3px solid white;">🏭</div>',
                iconSize: [34, 34], iconAnchor: [17, 17],
            });
            markerToko = L.marker([latToko, lngToko], { icon: tokoIcon }).addTo(map)
                .bindPopup('<strong>' + namaToko + '</strong><br><span style="font-size:11px;">Toko</span>');

            if (latTujuan && lngTujuan) {
                const tujuanIcon = L.divIcon({
                    className: 'tujuan-marker',
                    html: '<div style="background:#f59e0b; color:white; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow: 0 4px 12px rgba(245,158,11,0.5); border: 3px solid white;">🏠</div>',
                    iconSize: [34, 34], iconAnchor: [17, 17],
                });
                markerTujuan = L.marker([latTujuan, lngTujuan], { icon: tujuanIcon }).addTo(map)
                    .bindPopup('<strong>' + namaPenerima + '</strong><br><span style="font-size:11px;">Alamat Anda</span>');

                L.polyline([[latToko, lngToko], [latTujuan, lngTujuan]], { color: '#10b981', weight: 2, opacity: 0.4, dashArray: '6, 8' }).addTo(map);
            }

            if (latTujuan && lngTujuan) {
                const bounds = L.latLngBounds([[latToko, lngToko], [latTujuan, lngTujuan]]);
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
            }

            setTimeout(() => { if (map) map.invalidateSize({ animate: false }); }, 300);
        }

        function updateKurir(lat, lng) {
            const kurirIcon = L.divIcon({
                className: 'kurir-marker',
                html: '<div style="position:relative;"><div style="background:#8b5cf6; color:white; width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow: 0 4px 16px rgba(139,92,246,0.6); border: 3px solid white;">🛵</div><div style="position:absolute; inset:-8px; border-radius:50%; background:rgba(139,92,246,0.3); animation:pulse-ring 1.5s ease-out infinite; pointer-events:none;"></div></div>',
                iconSize: [38, 38], iconAnchor: [19, 19],
            });

            if (!markerKurir) {
                markerKurir = L.marker([lat, lng], { icon: kurirIcon }).addTo(map);
                markerKurir.bindPopup('<strong>Kurir</strong><br><span style="font-size:11px;">Sedang dalam perjalanan</span>');
            } else {
                markerKurir.setLatLng([lat, lng]);
            }

            if (lineKeToko) map.removeLayer(lineKeToko);
            if (latTujuan && lngTujuan) {
                lineKeToko = L.polyline([[lat, lng], [latTujuan, lngTujuan]], { color: '#8b5cf6', weight: 3, opacity: 0.7 }).addTo(map);
            }
        }

        function updateInfoTime(isoString) {
            const el = document.getElementById('trackingUpdateTime');
            if (!el || !isoString) return;
            const d = new Date(isoString);
            el.textContent = String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0') + ':' + String(d.getSeconds()).padStart(2, '0') + ' WIB';
        }

        function setStatusLive(isLive) {
            const badge = document.getElementById('trackingStatusBadge');
            const text = document.getElementById('trackingStatusText');
            const subtitle = document.getElementById('trackingSubtitle');
            if (isLive) {
                badge.textContent = 'LIVE';
                badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-violet-600 text-white';
                text.textContent = 'Sedang di jalan';
                subtitle.textContent = 'Kurir sedang dalam perjalanan ke lokasi Anda.';
            } else {
                badge.textContent = 'MENUNGGU';
                badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full bg-gray-200 text-gray-600';
                text.textContent = 'Menunggu kurir';
                subtitle.textContent = 'Menunggu kurir memulai pengiriman...';
            }
        }

        async function fetchTracking() {
            try {
                const res = await fetch(routeTracking, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const data = await res.json();
                if (data.order_status !== 'dikirim') {
                    if (pollingInterval) clearInterval(pollingInterval);
                    setTimeout(() => location.reload(), 500);
                    return;
                }
                if (data.active && data.kurir) {
                    setStatusLive(true);
                    updateKurir(data.kurir.lat, data.kurir.lng);
                    updateInfoTime(data.kurir.updated_at);
                    if (!markerKurir._alreadyZoomed) {
                        const bounds = L.latLngBounds([[latToko, lngToko], [data.kurir.lat, data.kurir.lng]]);
                        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
                        markerKurir._alreadyZoomed = true;
                    }
                } else {
                    setStatusLive(false);
                }
            } catch (e) { console.warn('Fetch tracking error:', e); }
        }

        initMap();
        fetchTracking();
        pollingInterval = setInterval(fetchTracking, 10000);
        window.addEventListener('resize', () => { if (map) map.invalidateSize({ animate: false }); });
    })();
</script>

<style>
    @keyframes pulse-ring {
        0%   { transform: scale(0.8); opacity: 0.7; }
        100% { transform: scale(1.6); opacity: 0; }
    }
</style>
@endpush