@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* CSS KHUSUS BIAR SIDEBAR & HEADER ILANG PAS MODAL MUNCUL */
    body.modal-active #adminSidebar { display: none !important; }
    body.modal-active .sticky.top-0 { display: none !important; }
    body.modal-active .lg\:ml-\[17rem\] { margin-left: 0 !important; }

    /* FIX PADDING & ALIGNMENT FILTER */
    .filter-grid-item {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .filter-grid-item label {
        height: 20px; /* Paksa tinggi label biar sejajar */
        display: flex;
        align-items: flex-end;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@section('content')

@if (session('success'))
    {{-- Notif sukses dari controller (pake PHP session) --}}
    <div class="rounded-2xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); background: rgb(var(--success-soft) / 0.6); border: 1px solid rgb(var(--success) / 0.3);">
        {{ session('success') }}
    </div>
@endif

{{-- Container buat Toast Notification --}}
<div id="toastContainer" class="fixed top-24 right-6 z-[99999] flex flex-col gap-2"></div>

{{-- Chip Status --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    @php
        $statusList = [
            'pending'    => ['label' => 'Pending',    'accent' => 'warning'],
            'diproses'   => ['label' => 'Diproses',   'accent' => 'info'],
            'dikirim'    => ['label' => 'Dikirim',    'accent' => 'brand'],
            'selesai'    => ['label' => 'Selesai',    'accent' => 'success'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'accent' => 'danger'],
        ];
    @endphp

    @foreach ($statusList as $key => $s)
        @php $active = request('status') == $key; @endphp
        <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => $key])) }}"
           class="glass-card p-4 transition hover:-translate-y-0.5 relative overflow-hidden"
           style="{{ $active ? 'border-color: rgb(var(--' . $s['accent'] . ')); box-shadow: 0 12px 32px -12px rgb(var(--' . $s['accent'] . ') / 0.5);' : '' }}">
            <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--{{ $s['accent'] }}));"></div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--{{ $s['accent'] }}));">{{ $s['label'] }}</p>
            <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats[$key] ?? 0 }}</p>
        </a>
    @endforeach
</div>

{{-- Filter --}}
<div class="glass-card filter-card p-6 mb-6 relative z-40" x-data="{ statusOpen: false, metodeOpen: false }">
    <form method="GET" id="filterForm">
        <div class="mb-5">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Cari Pesanan</label>
            <div class="auth-input-wrap" style="position: relative;">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kode / pelanggan / produk..." class="glass-input w-full pl-10 pr-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="filter-grid-item">
                <label class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted));">Dari Tanggal</label>
                <input type="text" name="dari" id="dari" value="{{ request('dari') }}" readonly class="glass-input w-full py-3 px-4 text-sm cursor-pointer" style="color: rgb(var(--text-primary));">
            </div>
            
            <div class="filter-grid-item">
                <label class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted));">Sampai Tanggal</label>
                <input type="text" name="sampai" id="sampai" value="{{ request('sampai') }}" readonly class="glass-input w-full py-3 px-4 text-sm cursor-pointer" style="color: rgb(var(--text-primary));">
            </div>

            <div class="relative filter-grid-item" @click.away="statusOpen = false">
                <label class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted));">Status</label>
                <button type="button" @click="statusOpen = !statusOpen" class="glass-input w-full flex items-center justify-between gap-2 py-3 px-4 text-sm text-left" style="color: rgb(var(--text-primary));">
                    <span class="truncate" id="statusLabel">
                        @php
                            $statusLabel = match(request('status')) {
                                'pending' => 'Pending', 'diproses' => 'Diproses',
                                'dikirim' => 'Dikirim', 'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan', default => 'Semua Status',
                            };
                        @endphp
                        {{ $statusLabel }}
                    </span>
                    <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="statusOpen" x-cloak class="absolute left-0 right-0 z-50 mt-2 rounded-xl overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                    @foreach (['' => 'Semua Status', 'pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                        <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectStatusFilter(this)" class="w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10" style="{{ request('status') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
            </div>

            <div class="relative filter-grid-item" @click.away="metodeOpen = false">
                <label class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted));">Metode</label>
                <button type="button" @click="metodeOpen = !metodeOpen" class="glass-input w-full flex items-center justify-between gap-2 py-3 px-4 text-sm text-left" style="color: rgb(var(--text-primary));">
                    <span class="truncate" id="metodeLabel">{{ request('payment_method') ?: 'Semua Metode' }}</span>
                    <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="metodeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="metodeOpen" x-cloak class="absolute left-0 right-0 z-50 mt-2 rounded-xl overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                    @foreach (['' => 'Semua Metode', 'COD' => 'COD', 'Transfer' => 'Transfer'] as $val => $label)
                        <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectMetodeFilter(this)" class="w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10" style="{{ request('payment_method') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="payment_method" id="inputMetode" value="{{ request('payment_method') }}">
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mt-5">
            <button type="submit" class="btn-primary !py-2.5 !px-6">Terapkan Filter</button>
            @if (request()->hasAny(['q', 'status', 'payment_method', 'dari', 'sampai']))
                <a href="{{ route('admin.pesanan.index') }}" class="glass-btn !py-2.5 !px-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="glass-card overflow-hidden relative z-0">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
                    <th class="px-5 py-3">Kode Pesanan</th>
                    <th class="px-5 py-3">Pelanggan</th>
                    <th class="px-5 py-3">Produk</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3">Metode</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesanan as $p)
                    @php
                        $modalData = [
                            'id' => $p->id,
                            'kode' => $p->kode_pesanan,
                            'order_status' => $p->order_status,
                            'tanggal' => $p->tanggal_order->format('d M Y, H:i'),
                            'alamat' => $p->alamat_pengiriman,
                            'catatan' => $p->catatan,
                            'subtotal' => (int) $p->subtotal,
                            'ongkir' => (int) $p->ongkir,
                            'jarak_km' => (float) $p->jarak_km,
                            'total_harga' => (int) $p->total_harga,
                            'payment_method' => $p->payment_method,
                            'payment_status' => $p->payment_status,
                            'delivery_type' => $p->delivery_type,
                            'delivery_label' => $p->delivery_label,
                            'is_tracking_active' => $p->lat_kurir !== null && $p->lng_kurir !== null,
                            'lokasi_updated_at' => $p->lokasi_updated_at?->toIso8601String(),
                            'user' => [
                                'username' => $p->user->username ?? '-',
                                'email' => $p->user->email ?? '-',
                                'no_telepon' => $p->user->no_telepon ?? '-',
                            ],
                            'items' => $p->detail->map(fn ($d) => [
                                'nama' => $d->nama_item,
                                'harga' => (int) $d->harga_satuan,
                                'qty' => $d->jumlah,
                                'subtotal' => (int) $d->subtotal,
                            ])->values()->toArray(),
                            'pembayaran' => $p->pembayaran ? [
                                'status' => $p->pembayaran->status_pembayaran,
                                'bukti' => $p->pembayaran->bukti_transfer ? asset('storage/' . $p->pembayaran->bukti_transfer) : null,
                            ] : null,
                            'refund' => $p->refund ? [
                                'nominal' => (int) $p->refund->nominal_refund,
                                'alasan' => $p->refund->alasan_batal,
                                'status' => $p->refund->status_refund,
                            ] : null,
                            'route_update_status' => route('admin.pesanan.updateStatus', $p->id),
                            'route_verifikasi' => route('admin.pesanan.verifikasi', $p->id),
                            'route_batalkan' => route('admin.pesanan.batalkan', $p->id),
                            'route_start_tracking' => route('admin.pesanan.startTracking', $p->id),
                            'route_update_lokasi' => route('admin.pesanan.updateLokasi', $p->id),
                            'route_stop_tracking' => route('admin.pesanan.stopTracking', $p->id),
                        ];

                        $statusStyle = [
                            'pending'    => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
                            'diproses'   => 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
                            'dikirim'    => 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
                            'selesai'    => 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
                            'dibatalkan' => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
                        ][$p->order_status] ?? 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));';
                    @endphp
                    <tr class="transition" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        <td class="px-5 py-4 text-sm font-semibold whitespace-nowrap" style="color: rgb(var(--text-primary));">
                            #{{ $p->kode_pesanan }}
                            @if ($p->lat_kurir && $p->lng_kurir && $p->order_status === 'dikirim')
                                <span class="inline-flex items-center gap-1 ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: rgb(var(--info));"></span>
                                    LIVE
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium" style="color: rgb(var(--text-primary));">{{ $p->user->username ?? '-' }}</p>
                            <p class="text-xs" style="color: rgb(var(--text-muted));">{{ $p->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if ($p->detail->count() > 0)
                                <div class="space-y-1 max-w-[280px]">
                                    @foreach ($p->detail->take(2) as $d)
                                        <div class="flex items-center gap-1.5 text-sm">
                                            <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                            <span class="truncate" style="color: rgb(var(--text-secondary));">{{ $d->nama_item }}</span>
                                            <span class="text-xs flex-shrink-0" style="color: rgb(var(--text-muted));">×{{ $d->jumlah }}</span>
                                        </div>
                                    @endforeach
                                    @if ($p->detail->count() > 2)
                                        <p class="text-xs pl-4" style="color: rgb(var(--text-muted));">+{{ $p->detail->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs" style="color: rgb(var(--text-muted));">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm whitespace-nowrap" style="color: rgb(var(--text-secondary));">{{ $p->tanggal_order->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-sm font-semibold whitespace-nowrap" style="color: rgb(var(--text-primary));">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if ($p->payment_method === 'COD')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">COD</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">Transfer</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold whitespace-nowrap" style="{{ $statusStyle }}">{{ ucfirst($p->order_status) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button"
                                    onclick='openPesananModal(@json($modalData, JSON_HEX_APOS | JSON_HEX_QUOT))'
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition"
                                    style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm" style="color: rgb(var(--text-muted));">
                            @if (request()->hasAny(['q', 'status', 'dari', 'sampai', 'payment_method']))
                                Tidak ada pesanan yang sesuai filter
                            @else
                                Belum ada pesanan
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- PAGINASI MANUAL (TANPA FILE BARU) --}}
@if ($pesanan->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
        <div class="text-xs md:text-sm" style="color: rgb(var(--text-muted));">
            Menampilkan <span class="font-bold" style="color: rgb(var(--text-primary));">{{ $pesanan->firstItem() ?? 0 }}</span> 
            - <span class="font-bold" style="color: rgb(var(--text-primary));">{{ $pesanan->lastItem() ?? 0 }}</span> 
            dari <span class="font-bold" style="color: rgb(var(--text-primary));">{{ $pesanan->total() }}</span> pesanan
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            {{-- Tombol Sebelumnya --}}
            @if ($pesanan->onFirstPage())
                <span class="flex-1 sm:flex-none text-center glass-btn !py-2 !px-4 opacity-40 cursor-not-allowed text-xs md:text-sm">
                    &laquo; Sebelumnya
                </span>
            @else
                <a href="{{ $pesanan->previousPageUrl() }}" class="flex-1 sm:flex-none text-center glass-btn !py-2 !px-4 text-xs md:text-sm hover:-translate-y-0.5">
                    &laquo; Sebelumnya
                </a>
            @endif

            {{-- Tombol Selanjutnya --}}
            @if ($pesanan->hasMorePages())
                <a href="{{ $pesanan->nextPageUrl() }}" class="flex-1 sm:flex-none text-center glass-btn !py-2 !px-4 text-xs md:text-sm hover:-translate-y-0.5">
                    Selanjutnya &raquo;
                </a>
            @else
                <span class="flex-1 sm:flex-none text-center glass-btn !py-2 !px-4 opacity-40 cursor-not-allowed text-xs md:text-sm">
                    Selanjutnya &raquo;
                </span>
            @endif
        </div>
    </div>
@endif

{{-- MODAL DETAIL PESANAN --}}
<div id="pesananModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 480px; max-height: 85vh;" class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">
        <div class="flex items-start justify-between gap-4 px-5 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div class="min-w-0 flex-1">
                <p class="text-xs opacity-80 leading-tight">Pesanan</p>
                <h3 class="text-base font-bold leading-tight mt-0.5">#<span id="mKode"></span></h3>
                <p class="text-xs opacity-80 mt-1" id="mTanggal"></p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 pt-1">
                <span id="mStatusBadge" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 text-white whitespace-nowrap"></span>
                <button onclick="closePesananModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3 text-sm">
            <div class="rounded-xl p-3" style="background: rgb(var(--bg-secondary) / 0.6);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Pelanggan</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-base flex-shrink-0 text-white" style="background: var(--gradient-brand);" id="mAvatar">U</div>
                    <div class="min-w-0">
                        <p class="font-semibold" style="color: rgb(var(--text-primary));" id="mUsername"></p>
                        <p class="text-xs truncate" style="color: rgb(var(--text-muted));" id="mEmail"></p>
                        <p class="text-xs" style="color: rgb(var(--text-muted));" id="mTelepon"></p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl p-3" style="background: rgb(var(--bg-secondary) / 0.6);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Alamat Pengiriman</p>
                <p style="color: rgb(var(--text-primary));" id="mAlamat"></p>
                <p class="text-xs mt-1" style="color: rgb(var(--text-muted));" id="mCatatan"></p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Item Pesanan</p>
                <div class="rounded-xl overflow-hidden" style="border: 1px solid rgb(var(--border-soft));">
                    <table class="w-full text-sm">
                        <thead style="background: rgb(var(--bg-secondary) / 0.6);">
                            <tr class="text-xs uppercase" style="color: rgb(var(--text-muted));">
                                <th class="px-3 py-2 text-left">Produk</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="mItemsBody"></tbody>
                        <tfoot style="background: rgb(var(--bg-secondary) / 0.4);">
                            <tr style="border-top: 1px solid rgb(var(--border-soft));">
                                <td colspan="2" class="px-3 py-2 text-right text-xs" style="color: rgb(var(--text-muted));">Subtotal</td>
                                <td class="px-3 py-2 text-right font-medium" style="color: rgb(var(--text-primary));" id="mSubtotal"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-right text-xs" style="color: rgb(var(--text-muted));">Ongkir <span id="mJarak"></span></td>
                                <td class="px-3 py-2 text-right font-medium" style="color: rgb(var(--text-primary));" id="mOngkir"></td>
                            </tr>
                            <tr style="border-top: 2px solid rgb(var(--border)); background: rgb(var(--brand-soft) / 0.5);">
                                <td colspan="2" class="px-3 py-2 text-right font-bold" style="color: rgb(var(--text-primary));">Total</td>
                                <td class="px-3 py-2 text-right font-bold" style="color: rgb(var(--brand-strong));" id="mTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="rounded-xl p-3" style="background: rgb(var(--bg-secondary) / 0.6);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Detail Transaksi</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgb(var(--text-muted));">Metode Pembayaran</p>
                        <p class="font-semibold" style="color: rgb(var(--text-primary));" id="mPaymentMethod"></p>
                    </div>
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgb(var(--text-muted));">Tipe Pengambilan</p>
                        <p class="font-semibold" style="color: rgb(var(--text-primary));" id="mDeliveryType"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs mb-0.5" style="color: rgb(var(--text-muted));">Status Pembayaran</p>
                        <p class="font-semibold" id="mPaymentStatus"></p>
                    </div>
                </div>
                <div id="mBuktiWrapper" class="mt-2 hidden">
                    <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Bukti Transfer (Klik untuk perbesar)</p>
                    <img id="mBukti" src="" alt="Bukti" onclick="openBuktiLightbox()" class="w-full rounded-xl cursor-zoom-in hover:opacity-90 transition" style="border: 1px solid rgb(var(--border-soft));" title="Klik untuk lihat ukuran penuh">
                </div>
            </div>
            <div id="mRefundBox" class="hidden rounded-xl p-3" style="background: rgb(var(--danger-soft) / 0.5); border: 1px solid rgb(var(--danger) / 0.3);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--danger));">Pengajuan Refund</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgb(var(--danger));">Nominal</p>
                        <p class="font-bold" style="color: rgb(var(--danger));" id="mRefundNominal"></p>
                    </div>
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgb(var(--danger));">Status</p>
                        <p class="font-bold" style="color: rgb(var(--danger));" id="mRefundStatus"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs mb-0.5" style="color: rgb(var(--danger));">Alasan</p>
                        <p style="color: rgb(var(--danger));" id="mRefundAlasan"></p>
                    </div>
                </div>
            </div>
            <div id="mTrackingBox" class="hidden rounded-xl p-3" style="background: rgb(var(--info-soft) / 0.4); border: 1px solid rgb(var(--info) / 0.3);">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold uppercase flex items-center gap-1.5" style="color: rgb(var(--info));">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background: rgb(var(--info));"></span>
                        Tracking Kurir
                    </p>
                    <span id="mTrackingStatus" class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));">OFF</span>
                </div>
                <p id="mTrackingInfo" class="text-xs mb-3 leading-relaxed" style="color: rgb(var(--info));">
                    Mulai tracking untuk mengirim lokasi Anda ke pelanggan secara real-time.
                </p>
                <div class="flex gap-2">
                    <button type="button" id="mBtnStartTracking" onclick="handleStartTracking()" class="flex-1 py-2 rounded-lg text-xs font-bold text-white" style="background: rgb(var(--info));">Mulai Antar</button>
                    <button type="button" id="mBtnStopTracking" onclick="handleStopTracking()" class="hidden flex-1 py-2 rounded-lg text-xs font-bold text-white" style="background: rgb(var(--danger));">Selesai Antar</button>
                </div>
                <p class="text-[10px] mt-2 leading-relaxed" style="color: rgb(var(--info));">
                    GPS akan aktif saat Anda klik "Mulai Antar". Update tiap 30 detik.
                </p>
            </div>
            <div id="mActionWrapper" class="rounded-xl p-3" style="background: rgb(var(--bg-secondary) / 0.6);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Aksi Cepat</p>
                <div id="mActionBtnContainer"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL INPUT ALASAN PEMBATALAN (PENGGANTI PROMPT BAWAAN) --}}
<div id="cancelModal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div class="glass-card rounded-2xl w-full max-w-sm overflow-hidden shadow-2xl">
        <div class="px-5 py-4" style="border-bottom: 1px solid rgb(var(--border-soft));">
            <h3 class="font-bold text-lg" style="color: rgb(var(--text-primary));">Batalkan Pesanan</h3>
            <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Berikan alasan mengapa pesanan ini dibatalkan.</p>
        </div>
        <div class="p-5">
            <textarea id="cancelReason" rows="3" class="glass-input w-full px-4 py-2 text-sm" style="color: rgb(var(--text-primary));" placeholder="Contoh: Stok habis, pembeli salah input..."></textarea>
            <p id="cancelError" class="text-xs mt-2 hidden" style="color: rgb(var(--danger));">Alasan pembatalan wajib diisi!</p>
        </div>
        <div class="px-5 py-4 flex justify-end gap-2" style="background: rgb(var(--bg-secondary) / 0.6); border-top: 1px solid rgb(var(--border-soft));">
            <button type="button" onclick="closeCancelModal()" class="glass-btn px-4 py-2 text-sm">Batal</button>
            <button type="button" onclick="submitCancel()" class="btn-primary px-4 py-2 text-sm" style="background: rgb(var(--danger));">Ya, Batalkan</button>
        </div>
    </div>
</div>

{{-- LIGHTBOX PREVIEW BUKTI TRANSFER --}}
<div id="buktiLightbox" class="fixed inset-0 z-[1001] hidden"
     style="background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);">
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;">
        <img id="buktiLightboxImg" src="" alt="Preview Bukti"
             onclick="event.stopPropagation()"
             style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
    </div>
    <button type="button" onclick="closeBuktiLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 48px; height: 48px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
            title="Tutup">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // ============================================================
    // TOAST NOTIFICATION (PENGGANTI ALERT)
    // ============================================================
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        // Style berdasarkan tipe
        const bgStyle = type === 'success' 
            ? 'background: rgb(var(--success)); color: white;' 
            : 'background: rgb(var(--danger)); color: white;';
            
        const icon = type === 'success' 
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';

        toast.className = 'flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium transform transition-all duration-300 translate-x-full opacity-0';
        toast.style.cssText = bgStyle;
        toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        // Animasi masuk
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        // Hapus setelah 3 detik
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ============================================================
    // GLOBAL GUARD FLATPICKR
    // ============================================================
    (function yearInputGuard() {
        const ALLOWED_KEYS = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End', 'Enter'];
        document.addEventListener('keydown', function (e) {
            if (!e.target?.classList?.contains('cur-year')) return;
            if (ALLOWED_KEYS.includes(e.key)) return;
            if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) return;
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }, true);
    })();

    // ============================================================
    // STATE
    // ============================================================
    let currentTrackingPesanan = null;
    let trackingInterval = null;
    let cancelRoute = null;
    let cancelCsrf = null;

    // ============================================================
    // FILTER HANDLERS
    // ============================================================
    function selectStatusFilter(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.statusOpen = false;
    }
    function selectMetodeFilter(el) {
        document.getElementById('inputMetode').value = el.dataset.value;
        document.getElementById('metodeLabel').textContent = el.dataset.label;
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.metodeOpen = false;
    }

    // ============================================================
    // FLATPICKR (SIMPEL & ANTI BENTROK)
    // ============================================================
    let dariPicker, sampaiPicker;

    dariPicker = flatpickr("#dari", {
        dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        allowInput: false, monthSelectorType: "static", maxDate: "today",
        onReady: function(_, __, fp) {
            fp.altInput.classList.add('glass-input');
            fp.altInput.style.color = 'rgb(var(--text-primary))';
        }
    });

    sampaiPicker = flatpickr("#sampai", {
        dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        allowInput: false, monthSelectorType: "static", maxDate: "today",
        onReady: function(_, __, fp) {
            fp.altInput.classList.add('glass-input');
            fp.altInput.style.color = 'rgb(var(--text-primary))';
        }
    });

    // Validasi sebelum form submit (PAKE TOAST)
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        const d = dariPicker?.selectedDates[0];
        const s = sampaiPicker?.selectedDates[0];

        if (d && s && d.getTime() > s.getTime()) {
            e.preventDefault(); 
            showToast('Rentang tanggal tidak valid. Tanggal "Sampai" tidak boleh mendahului tanggal "Dari".', 'error');
            return false;
        }
    });

    // ============================================================
    // MODAL PEMBATALAN CUSTOM (PENGGANTI PROMPT)
    // ============================================================
    function promptCancel(route, csrf) {
        cancelRoute = route;
        cancelCsrf = csrf;
        document.getElementById('cancelReason').value = '';
        document.getElementById('cancelError').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('hidden');
        document.getElementById('cancelModal').classList.add('flex');
        document.body.classList.add('modal-active');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
        document.getElementById('cancelModal').classList.remove('flex');
        cancelRoute = null;

        const pesananModal = document.getElementById('pesananModal');
        const isPesananOpen = pesananModal && !pesananModal.classList.contains('hidden');
        if (!isPesananOpen) {
            document.body.classList.remove('modal-active');
        }
    }

    function submitCancel() {
        const alasan = document.getElementById('cancelReason').value.trim();
        if (alasan === "") {
            document.getElementById('cancelError').classList.remove('hidden');
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = cancelRoute;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${cancelCsrf}">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="alasan_batal" value="${alasan}">
        `;
        document.body.appendChild(form);
        form.submit();
    }

    // ============================================================
    // MODAL DETAIL PESANAN
    // ============================================================
    const STATUS_COLORS = {
        pending: 'rgb(var(--warning))',
        diproses: 'rgb(var(--info))',
        dikirim: 'rgb(var(--info))',
        selesai: 'rgb(var(--success))',
        dibatalkan: 'rgb(var(--danger))',
    };
    const PAYMENT_STATUS_COLORS = {
        paid: 'rgb(var(--success))',
        pending: 'rgb(var(--warning))',
        failed: 'rgb(var(--danger))',
        refunded: 'rgb(var(--text-muted))',
    };

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function openPesananModal(data) {
        const modal = document.getElementById('pesananModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-active');
        document.body.style.overflow = 'hidden';

        document.getElementById('mKode').textContent = data.kode;
        document.getElementById('mTanggal').textContent = data.tanggal;

        const badge = document.getElementById('mStatusBadge');
        badge.textContent = data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1);
        badge.style.background = STATUS_COLORS[data.order_status] || 'rgb(var(--text-muted))';

        document.getElementById('mAvatar').textContent = data.user.username.charAt(0).toUpperCase();
        document.getElementById('mUsername').textContent = data.user.username;
        document.getElementById('mEmail').textContent = data.user.email;
        document.getElementById('mTelepon').textContent = data.user.no_telepon || '-';

        document.getElementById('mAlamat').textContent = data.alamat || '-';
        const catatan = document.getElementById('mCatatan');
        catatan.textContent = data.catatan ? 'Catatan: ' + data.catatan : '';

        const itemsBody = document.getElementById('mItemsBody');
        itemsBody.innerHTML = '';
        data.items.forEach(item => {
            itemsBody.innerHTML += `
                <tr style="border-bottom: 1px solid rgb(var(--border-soft));">
                    <td class="px-3 py-2" style="color: rgb(var(--text-primary));">
                        ${item.nama}
                        <p class="text-xs" style="color: rgb(var(--text-muted));">${formatRupiah(item.harga)} × ${item.qty}</p>
                    </td>
                    <td class="px-3 py-2 text-center" style="color: rgb(var(--text-secondary));">${item.qty}</td>
                    <td class="px-3 py-2 text-right font-medium" style="color: rgb(var(--text-primary));">${formatRupiah(item.subtotal)}</td>
                </tr>
            `;
        });

        document.getElementById('mSubtotal').textContent = formatRupiah(data.subtotal);
        document.getElementById('mOngkir').textContent = formatRupiah(data.ongkir);
        document.getElementById('mJarak').textContent = '(' + data.jarak_km + ' km)';
        document.getElementById('mTotal').textContent = formatRupiah(data.total_harga);

        document.getElementById('mPaymentMethod').textContent = data.payment_method;
        document.getElementById('mDeliveryType').textContent = data.delivery_label;
        const payStatus = document.getElementById('mPaymentStatus');
        payStatus.textContent = data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1);
        payStatus.style.color = PAYMENT_STATUS_COLORS[data.payment_status] || 'rgb(var(--text-muted))';

        const buktiWrapper = document.getElementById('mBuktiWrapper');
        if (data.pembayaran && data.pembayaran.bukti) {
            document.getElementById('mBukti').src = data.pembayaran.bukti;
            buktiWrapper.classList.remove('hidden');
        } else {
            buktiWrapper.classList.add('hidden');
        }

        const refundBox = document.getElementById('mRefundBox');
        if (data.refund) {
            document.getElementById('mRefundNominal').textContent = formatRupiah(data.refund.nominal);
            document.getElementById('mRefundAlasan').textContent = data.refund.alasan;
            document.getElementById('mRefundStatus').textContent = data.refund.status.charAt(0).toUpperCase() + data.refund.status.slice(1);
            refundBox.classList.remove('hidden');
        } else {
            refundBox.classList.add('hidden');
        }

        const trackingBox = document.getElementById('mTrackingBox');
        if (data.order_status === 'dikirim') {
            trackingBox.classList.remove('hidden');
            currentTrackingPesanan = {
                pesanan_id: data.id,
                route_start_tracking: data.route_start_tracking,
                route_update_lokasi: data.route_update_lokasi,
                route_stop_tracking: data.route_stop_tracking,
            };
            setTrackingUI(data.is_tracking_active, data.lokasi_updated_at);
        } else {
            trackingBox.classList.add('hidden');
            currentTrackingPesanan = null;
            stopTrackingCleanup();
        }

        renderActionButton(data);
    }

    // === FUNGSI BARU: NENTUIN TOMBOL BERDASARKAN STATUS & TIPE (PAKE FORM HTML BIAR PASTI JALAN) ===
    function renderActionButton(data) {
        const container = document.getElementById('mActionBtnContainer');
        let html = '';
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        if (data.order_status === 'selesai') {
            html = `
                <div class="text-center py-4">
                    <p class="text-sm font-bold" style="color: rgb(var(--success));">Pesanan Selesai</p>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Terima kasih, transaksi telah berhasil.</p>
                </div>`;
        }
        else if (data.order_status === 'dibatalkan') {
            html = `
                <div class="text-center py-4">
                    <p class="text-sm font-bold" style="color: rgb(var(--danger));">Pesanan Dibatalkan</p>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Pesanan ini sudah tidak berlaku.</p>
                </div>`;
        }
        else if (data.order_status === 'pending') {
            if (data.payment_method === 'Transfer') {
                html = `
                <form action="${data.route_verifikasi}" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini dan ubah status ke Diproses?')">
                    <input type="hidden" name="_token" value="${csrf}">
                    <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-bold text-white transition hover:opacity-90" style="background: rgb(var(--brand));">
                        Verifikasi Pembayaran & Proses
                    </button>
                </form>`;
            } else {
                html = `
                <form action="${data.route_update_status}" method="POST" onsubmit="return confirm('Proses pesanan ini?')">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="order_status" value="diproses">
                    <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-bold text-white transition hover:opacity-90" style="background: rgb(var(--brand));">
                        Proses Pesanan (COD)
                    </button>
                </form>`;
            }
        }
        else if (data.order_status === 'diproses') {
            if (data.delivery_type === 'delivery') {
                html = `
                <form action="${data.route_update_status}" method="POST" onsubmit="return confirm('Kirim pesanan ini?')">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="order_status" value="dikirim">
                    <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-bold text-white transition hover:opacity-90" style="background: rgb(var(--info));">
                        Kirim Pesanan (Mulai Antar)
                    </button>
                </form>`;
            } else {
                html = `
                <form action="${data.route_update_status}" method="POST" onsubmit="return confirm('Selesaikan pesanan ini? Pastikan pelanggan sudah mengambil barang.')">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="order_status" value="selesai">
                    <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-bold text-white transition hover:opacity-90" style="background: rgb(var(--success));">
                        Selesaikan Pesanan (Sudah Diambil)
                    </button>
                </form>`;
            }
        }
        else if (data.order_status === 'dikirim') {
            let label = data.payment_method === 'COD' ? 'Terima Uang & Selesaikan' : 'Selesaikan Pesanan';
            html = `
            <form action="${data.route_update_status}" method="POST" onsubmit="return confirm('${label}?')">
                <input type="hidden" name="_token" value="${csrf}">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="order_status" value="selesai">
                <button type="submit" class="w-full py-2.5 rounded-lg text-sm font-bold text-white transition hover:opacity-90" style="background: rgb(var(--success));">
                    ${label}
                </button>
            </form>`;
        }

        // Tombol Batal pake Modal Kustom
        if (data.order_status !== 'selesai' && data.order_status !== 'dibatalkan') {
            html += `
            <button type="button" onclick="promptCancel('${data.route_batalkan}', '${csrf}')" class="w-full mt-2 py-2 rounded-lg text-xs font-bold transition hover:opacity-80" style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                Batalkan Pesanan
            </button>`;
        }

        container.innerHTML = html;
    }

    function setTrackingUI(active, updatedAt = null) {
        const statusEl = document.getElementById('mTrackingStatus');
        const btnStart = document.getElementById('mBtnStartTracking');
        const btnStop = document.getElementById('mBtnStopTracking');
        const infoEl = document.getElementById('mTrackingInfo');
        if (active) {
            statusEl.textContent = 'LIVE';
            statusEl.style.background = 'rgb(var(--info))';
            statusEl.style.color = 'white';
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');
            infoEl.innerHTML = '<span class="font-bold">Sedang mengantar...</span><br>Lokasi Anda dibagikan ke pelanggan.';
        } else {
            statusEl.textContent = 'OFF';
            statusEl.style.background = 'rgb(var(--bg-secondary))';
            statusEl.style.color = 'rgb(var(--text-secondary))';
            btnStart.classList.remove('hidden');
            btnStop.classList.add('hidden');
            infoEl.innerHTML = 'Mulai tracking untuk mengirim lokasi Anda ke pelanggan secara real-time.';
        }
    }

    function closePesananModal() {
        const modal = document.getElementById('pesananModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('modal-active');
        document.body.style.overflow = '';
    }

    // ==== LIGHTBOX PREVIEW BUKTI ====
    function openBuktiLightbox() {
        const src = document.getElementById('mBukti').src;
        if (!src) return;
        document.getElementById('buktiLightboxImg').src = src;
        document.getElementById('buktiLightbox').classList.remove('hidden');
        document.body.classList.add('modal-active');
    }

    function closeBuktiLightbox() {
        const lb = document.getElementById('buktiLightbox');
        if (lb) lb.classList.add('hidden');
        document.getElementById('buktiLightboxImg').src = '';
        const pesananModal = document.getElementById('pesananModal');
        if (!pesananModal || pesananModal.classList.contains('hidden')) {
            document.body.classList.remove('modal-active');
        }
    }

    document.getElementById('buktiLightbox')?.addEventListener('click', function(e) {
        if (e.target === this) closeBuktiLightbox();
    });

    document.getElementById('pesananModal').addEventListener('click', function(e) {
        if (e.target === this) closePesananModal();
    });
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeCancelModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const buktiLb = document.getElementById('buktiLightbox');
            if (buktiLb && !buktiLb.classList.contains('hidden')) {
                closeBuktiLightbox();
                return;
            }
            const cancelModal = document.getElementById('cancelModal');
            if (cancelModal && !cancelModal.classList.contains('hidden')) {
                closeCancelModal();
                return;
            }
            closePesananModal();
        }
    });

    function stopTrackingCleanup() {
        if (trackingInterval) { clearInterval(trackingInterval); trackingInterval = null; }
    }

    async function handleStartTracking() {
        if (!currentTrackingPesanan) return alert('Pesanan tidak valid.');
        if (!navigator.geolocation) return alert('Browser tidak mendukung GPS.');
        
        if (!confirm('Mulai antar pesanan ini? GPS Anda akan aktif.')) return;

        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                const ok = await kirimLokasi(currentTrackingPesanan.route_start_tracking, pos.coords.latitude, pos.coords.longitude);
                if (!ok) return alert('Gagal memulai tracking.');
                setTrackingUI(true);
                trackingInterval = setInterval(() => {
                    navigator.geolocation.getCurrentPosition(
                        async (p) => await kirimLokasi(currentTrackingPesanan.route_update_lokasi, p.coords.latitude, p.coords.longitude),
                        (err) => console.warn('GPS error:', err),
                        { enableHighAccuracy: true, timeout: 20000, maximumAge: 10000 }
                    );
                }, 30000);
            },
            (err) => {
                const msg = {1: 'Izin lokasi ditolak.', 2: 'Lokasi tidak tersedia.', 3: 'Timeout.'}[err.code] || 'Gagal mendapatkan lokasi.';
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 10000 }
        );
    }

    async function handleStopTracking() {
        if (!currentTrackingPesanan) return;
        if (!confirm('Selesai antar pesanan ini?')) return;
        try {
            const res = await fetch(currentTrackingPesanan.route_stop_tracking, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.success) {
                stopTrackingCleanup();
                setTrackingUI(false);
                showToast('Tracking dihentikan.', 'success');
            } else showToast(data.message || 'Gagal.', 'error');
        } catch (e) { showToast('Error: ' + e.message, 'error'); }
    }

    async function kirimLokasi(route, lat, lng) {
        try {
            const res = await fetch(route, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ lat, lng }),
            });
            const data = await res.json();
            return !!data.success;
        } catch (e) { console.warn('Kirim lokasi error:', e); return false; }
    }
</script>
@endpush