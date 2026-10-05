@extends('layouts.admin')
@section('title', 'Kelola Refund')
@section('page-title', 'Kelola Refund')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

@if (session('success'))
    <div class="rounded-2xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); background: rgb(var(--success-soft) / 0.6); border: 1px solid rgb(var(--success) / 0.3);">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="rounded-2xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--danger)); background: rgb(var(--danger-soft) / 0.6); border: 1px solid rgb(var(--danger) / 0.3);">{{ session('error') }}</div>
@endif

{{-- Chip Status --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    @php
        $statusList = [
            'pending'  => ['label' => 'Pending',  'accent' => 'warning'],
            'diproses' => ['label' => 'Diproses', 'accent' => 'info'],
            'selesai'  => ['label' => 'Selesai',  'accent' => 'success'],
        ];
    @endphp

    @foreach ($statusList as $key => $s)
        @php $active = request('status') == $key; @endphp
        <a href="{{ route('admin.refund.index', array_merge(request()->except('status', 'page'), ['status' => $key])) }}"
           class="glass-card p-4 transition hover:-translate-y-0.5 relative overflow-hidden"
           style="{{ $active ? 'border-color: rgb(var(--' . $s['accent'] . ')); box-shadow: 0 12px 32px -12px rgb(var(--' . $s['accent'] . ') / 0.5);' : '' }}">
            <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--{{ $s['accent'] }}));"></div>
            <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--{{ $s['accent'] }}));">{{ $s['label'] }}</p>
            <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats[$key] ?? 0 }}</p>
        </a>
    @endforeach

    {{-- Total Refund --}}
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--brand));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--brand));">Total Nominal</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="glass-card filter-card p-6 mb-6 relative z-40" x-data="{ statusOpen: false }">
    <form method="GET" id="filterForm">
        <div class="mb-5">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Cari Refund</label>
            <div style="position: relative;">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Kode / pelanggan / produk..."
                       class="glass-input w-full pl-10 pr-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Dari</label>
                <input type="text" name="dari" id="dari" value="{{ request('dari') }}" readonly
                       class="datepicker glass-input w-full py-3 text-sm cursor-pointer" style="color: rgb(var(--text-primary));">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Sampai</label>
                <input type="text" name="sampai" id="sampai" value="{{ request('sampai') }}" readonly
                       class="datepicker glass-input w-full py-3 text-sm cursor-pointer" style="color: rgb(var(--text-primary));">
            </div>

            <div class="relative" @click.away="statusOpen = false">
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Status</label>
                <button type="button" @click="statusOpen = !statusOpen"
                        class="glass-input w-full flex items-center justify-between gap-2 py-3 text-sm text-left" style="color: rgb(var(--text-primary));">
                    <span class="truncate" id="statusLabel">
                        @php
                            $statusLabel = match(request('status')) {
                                'pending' => 'Pending', 'diproses' => 'Diproses',
                                'selesai' => 'Selesai', 'ditolak' => 'Ditolak',
                                default => 'Semua Status',
                            };
                        @endphp
                        {{ $statusLabel }}
                    </span>
                    <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="statusOpen" x-cloak class="absolute left-0 right-0 z-50 mt-2 rounded-xl overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                    @foreach (['' => 'Semua Status', 'pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                        <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectStatusFilter(this)"
                                class="w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10"
                                style="{{ request('status') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
            </div>
        </div>

        <div class="flex flex-wrap gap-3 mt-5">
            <button type="submit" class="btn-primary !py-2.5 !px-6">Terapkan Filter</button>
            @if (request()->hasAny(['q', 'status', 'dari', 'sampai']))
                <a href="{{ route('admin.refund.index') }}" class="glass-btn !py-2.5 !px-5">
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
                    <th class="px-5 py-3">ID</th>
                    <th class="px-5 py-3">Kode Pesanan</th>
                    <th class="px-5 py-3">Pelanggan</th>
                    <th class="px-5 py-3">Produk</th>
                    <th class="px-5 py-3">Nominal</th>
                    <th class="px-5 py-3">Alasan</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($refund as $r)
                    @php
                        $statusStyle = [
                            'pending'  => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
                            'diproses' => 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
                            'selesai'  => 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
                            'ditolak'  => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
                        ][$r->status_refund] ?? 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));';

                        $isUrgent = $r->status_refund === 'pending';
                        $buktiUser = null;
                        if ($r->pesanan && $r->pesanan->pembayaran && $r->pesanan->pembayaran->bukti_transfer) {
                            $buktiUser = asset('storage/' . $r->pesanan->pembayaran->bukti_transfer);
                        }

                        $modalData = [
                            'id' => $r->id,
                            'tanggal' => $r->tanggal_refund->format('d M Y, H:i'),
                            'status_refund' => $r->status_refund,
                            'nominal_refund' => (int) $r->nominal_refund,
                            'alasan_batal' => $r->alasan_batal,
                            'catatan_admin' => $r->catatan_admin,
                            'bukti_transfer_balik' => $r->bukti_transfer_balik ? asset('storage/' . $r->bukti_transfer_balik) : null,
                            'bukti_transfer_user' => $buktiUser,
                            'pesanan' => [
                                'kode' => $r->pesanan->kode_pesanan ?? '-',
                                'total_harga' => (int) ($r->pesanan->total_harga ?? 0),
                                'payment_method' => $r->pesanan->payment_method ?? '-',
                            ],
                            'user' => [
                                'username' => $r->pesanan->user->username ?? '-',
                                'email' => $r->pesanan->user->email ?? '-',
                                'no_telepon' => $r->pesanan->user->no_telepon ?? '-',
                            ],
                            'items' => $r->pesanan->detail->map(fn ($d) => [
                                'nama' => $d->nama_item,
                                'qty' => $d->jumlah,
                                'subtotal' => (int) $d->subtotal,
                            ])->values()->toArray(),
                            'route_update' => route('admin.refund.update', $r->id),
                        ];
                    @endphp
                    <tr class="transition" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        <td class="px-5 py-4 text-sm" style="color: rgb(var(--text-muted));">#{{ $r->id }}</td>
                        <td class="px-5 py-4 text-sm font-semibold whitespace-nowrap" style="color: rgb(var(--text-primary));">#{{ $r->pesanan->kode_pesanan ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium" style="color: rgb(var(--text-primary));">{{ $r->pesanan->user->username ?? '-' }}</p>
                            <p class="text-xs" style="color: rgb(var(--text-muted));">{{ $r->pesanan->user->email ?? '' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if ($r->pesanan && $r->pesanan->detail->count() > 0)
                                <div class="space-y-1 max-w-[240px]">
                                    @foreach ($r->pesanan->detail->take(2) as $d)
                                        <div class="flex items-center gap-1.5 text-sm">
                                            <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                            <span class="truncate" style="color: rgb(var(--text-secondary));">{{ $d->nama_item }}</span>
                                            <span class="text-xs flex-shrink-0" style="color: rgb(var(--text-muted));">×{{ $d->jumlah }}</span>
                                        </div>
                                    @endforeach
                                    @if ($r->pesanan->detail->count() > 2)
                                        <p class="text-xs pl-4" style="color: rgb(var(--text-muted));">+{{ $r->pesanan->detail->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs" style="color: rgb(var(--text-muted));">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm font-bold whitespace-nowrap" style="color: rgb(var(--brand));">
                            Rp {{ number_format($r->nominal_refund, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-sm" style="color: rgb(var(--text-secondary));">
                            <p class="line-clamp-2 max-w-xs">{{ $r->alasan_batal }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold whitespace-nowrap" style="{{ $statusStyle }}">
                                {{ ucfirst($r->status_refund) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button"
                                    data-refund="{{ json_encode($modalData, JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                    onclick="openRefundModal(JSON.parse(this.dataset.refund))"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition"
                                    style="{{ $isUrgent ? 'background: rgb(var(--warning)); color: white;' : 'background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));' }}">
                                @if ($isUrgent)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Proses
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                @endif
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm" style="color: rgb(var(--text-muted));">
                            @if (request()->hasAny(['q', 'status', 'dari', 'sampai']))
                                Tidak ada refund yang sesuai filter
                            @else
                                Belum ada pengajuan refund
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $refund->links() }}</div>

{{-- MODAL PROSES REFUND --}}
<div id="refundModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 960px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-start justify-between gap-4 px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div class="min-w-0 flex-1">
                <p class="text-xs opacity-80 leading-tight">Refund #<span id="mRefundId"></span></p>
                <h3 class="text-base font-bold leading-tight mt-0.5">Pesanan #<span id="mKodePesanan"></span></h3>
                <p class="text-xs opacity-80 mt-1">Diajukan <span id="mTanggal"></span></p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 pt-1">
                <span id="mStatusBadge" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 text-white whitespace-nowrap"></span>
                <button onclick="closeRefundModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <form id="refundForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 p-6">

                <div class="lg:col-span-2 space-y-4">
                    <div class="rounded-xl p-4" style="background: rgb(var(--bg-secondary) / 0.6);">
                        <h4 class="font-bold mb-3 text-sm" style="color: rgb(var(--text-primary));">Informasi Refund</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1.5" style="border-bottom: 1px solid rgb(var(--border-soft));">
                                <span style="color: rgb(var(--text-secondary));">Total Pesanan</span>
                                <span class="font-semibold" style="color: rgb(var(--text-primary));" id="mTotalPesanan">-</span>
                            </div>
                            <div class="flex justify-between py-1.5" style="border-bottom: 1px solid rgb(var(--border-soft));">
                                <span style="color: rgb(var(--text-secondary));">Nominal Refund</span>
                                <span class="font-bold" style="color: rgb(var(--brand));" id="mNominalRefund">-</span>
                            </div>
                            <div class="flex justify-between py-1.5" style="border-bottom: 1px solid rgb(var(--border-soft));">
                                <span style="color: rgb(var(--text-secondary));">Metode Pembayaran</span>
                                <span class="font-semibold" style="color: rgb(var(--text-primary));" id="mPaymentMethod">-</span>
                            </div>
                            <div class="py-1.5">
                                <p class="mb-1" style="color: rgb(var(--text-secondary));">Alasan Pembatalan</p>
                                <p class="p-2.5 rounded-lg text-sm" style="color: rgb(var(--text-primary)); background: rgb(var(--surface));" id="mAlasanBatal">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl p-4" style="background: rgb(var(--bg-secondary) / 0.6);">
                        <h4 class="font-bold mb-3 text-sm" style="color: rgb(var(--text-primary));">Item yang Dipesan</h4>
                        <div class="rounded-lg overflow-hidden" style="border: 1px solid rgb(var(--border-soft)); background: rgb(var(--surface));">
                            <table class="w-full text-sm">
                                <thead style="background: rgb(var(--bg-secondary) / 0.6);">
                                    <tr class="text-xs uppercase" style="color: rgb(var(--text-muted));">
                                        <th class="px-3 py-2 text-left">Produk</th>
                                        <th class="px-3 py-2 text-center">Qty</th>
                                        <th class="px-3 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="mItemsBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="mBuktiUserWrapper" class="hidden rounded-xl p-4" style="background: rgb(var(--info-soft) / 0.4); border: 1px solid rgb(var(--info) / 0.3);">
                        <h4 class="font-bold mb-2 text-sm" style="color: rgb(var(--info));">📎 Bukti Transfer dari User</h4>
                        <p class="text-xs mb-3" style="color: rgb(var(--info));">Bukti dari user saat checkout / pembatalan.</p>
                        <a id="mBuktiUserLink" href="#" target="_blank" class="block">
                            <img id="mBuktiUserImg" src="" alt="Bukti User" class="w-full max-w-xs rounded-xl hover:opacity-90 transition" style="border: 1px solid rgb(var(--info) / 0.3);">
                        </a>
                    </div>

                    <div id="mBuktiWrapper" class="hidden rounded-xl p-4" style="background: rgb(var(--success-soft) / 0.5); border: 1px solid rgb(var(--success) / 0.3);">
                        <h4 class="font-bold mb-2 text-sm" style="color: rgb(var(--success));">✓ Bukti Transfer Balik</h4>
                        <img id="mBuktiImg" src="" alt="Bukti" class="w-full max-w-xs rounded-xl" style="border: 1px solid rgb(var(--success) / 0.3);">
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-xl p-4" style="background: rgb(var(--bg-secondary) / 0.6);">
                        <h4 class="font-bold mb-3 text-sm" style="color: rgb(var(--text-primary));">Rekening Tujuan</h4>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0" style="background: var(--gradient-brand);" id="mAvatar">U</div>
                            <div class="min-w-0">
                                <p class="font-semibold text-sm truncate" style="color: rgb(var(--text-primary));" id="mUsername">-</p>
                                <p class="text-xs truncate" style="color: rgb(var(--text-muted));" id="mEmail">-</p>
                            </div>
                        </div>
                        <div class="text-xs">
                            <p style="color: rgb(var(--text-muted));">No. Telepon</p>
                            <p class="font-medium" style="color: rgb(var(--text-primary));" id="mTelepon">-</p>
                        </div>
                    </div>

                    <div class="rounded-xl p-4" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft));">
                        <h4 class="font-bold mb-3 text-sm" style="color: rgb(var(--text-primary));">Proses Refund</h4>
                        <div class="space-y-3">
                            <div class="relative" x-data="{ modalStatusOpen: false }" @click.away="modalStatusOpen = false">
                                <label class="block text-xs font-medium mb-1" style="color: rgb(var(--text-secondary));">Status Refund</label>
                                <button type="button" @click="modalStatusOpen = !modalStatusOpen"
                                        class="glass-input w-full flex items-center justify-between gap-2 py-2 text-sm text-left" style="color: rgb(var(--text-primary));">
                                    <span id="mInputStatusLabel">Pending</span>
                                    <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="modalStatusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="modalStatusOpen" x-cloak class="absolute left-0 right-0 z-30 mt-2 rounded-lg overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                                    @foreach (['pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                                        <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectModalStatus(this)"
                                                class="status-option-modal w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10"
                                                style="color: rgb(var(--text-secondary));">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="status_refund" id="mInputStatus" value="pending">
                            </div>

                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: rgb(var(--text-secondary));">Catatan untuk Pelanggan</label>
                                <textarea name="catatan_admin" id="mInputCatatan" rows="3" maxlength="1000"
                                          class="glass-input w-full py-2 text-sm resize-none" style="color: rgb(var(--text-primary));"
                                          placeholder="Contoh: Dana sudah kami transfer..."></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-medium mb-1" style="color: rgb(var(--text-secondary));">Bukti Transfer Balik</label>
                                <input type="file" name="bukti_transfer_balik" accept="image/jpeg,image/jpg,image/png,image/webp"
                                       class="glass-input w-full px-3 py-2 text-xs" style="color: rgb(var(--text-primary));">
                                <p class="text-[10px] mt-1" style="color: rgb(var(--text-muted));">JPG, PNG, WEBP · Max 5MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl p-4 text-xs space-y-2" style="background: rgb(var(--bg-secondary) / 0.6);">
                        <div class="flex justify-between">
                            <span style="color: rgb(var(--text-secondary));">Tanggal Ajuan</span>
                            <span class="font-semibold" style="color: rgb(var(--text-primary));" id="mTanggalAjuan">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="color: rgb(var(--text-secondary));">Status Saat Ini</span>
                            <span id="mStatusBadgeSmall" class="px-2 py-0.5 rounded-md font-bold">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="flex items-center justify-between gap-3 px-6 py-4 flex-shrink-0" style="border-top: 1px solid rgb(var(--border-soft)); background: rgb(var(--bg-secondary) / 0.5);">
            <p class="text-xs" style="color: rgb(var(--text-muted));">⚠️ Pastikan data sudah benar sebelum menyimpan</p>
            <div class="flex gap-2">
                <button type="button" onclick="closeRefundModal()" class="glass-btn">Batal</button>
                <button type="submit" form="refundForm" class="btn-primary">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // ============================================================
    // GLOBAL GUARD: INPUT TAHUN FLATPICKR CUMA BOLEH ANGKA 0-9
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

        document.addEventListener('paste', function (e) {
            if (!e.target?.classList?.contains('cur-year')) return;
            e.preventDefault();
            e.stopImmediatePropagation();
            const pasted = ((e.clipboardData || window.clipboardData).getData('text') || '').replace(/\D/g, '').slice(0, 4);
            if (pasted) {
                e.target.value = pasted;
                e.target.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }, true);

        document.addEventListener('input', function (e) {
            if (!e.target?.classList?.contains('cur-year')) return;
            const cleaned = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (e.target.value !== cleaned) {
                e.target.value = cleaned;
            }
        }, true);

        new MutationObserver(function () {
            document.querySelectorAll('.flatpickr-calendar .cur-year').forEach(function (input) {
                if (input.type !== 'text') {
                    input.type = 'text';
                    input.setAttribute('inputmode', 'numeric');
                    input.setAttribute('pattern', '[0-9]*');
                }
            });
        }).observe(document.body, { childList: true, subtree: true });
    })();

    // ============================================================
    // FILTER HANDLERS
    // ============================================================
    function selectStatusFilter(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.statusOpen = false;
    }

    function selectModalStatus(el) {
        document.getElementById('mInputStatus').value = el.dataset.value;
        document.getElementById('mInputStatusLabel').textContent = el.dataset.label;
        document.querySelectorAll('.status-option-modal').forEach(btn => {
            btn.style.background = '';
            btn.style.color = 'rgb(var(--text-secondary))';
            btn.style.fontWeight = '';
        });
        el.style.background = 'rgb(var(--brand) / 0.1)';
        el.style.color = 'rgb(var(--brand))';
        el.style.fontWeight = '600';
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.modalStatusOpen = false;
    }

    // ============================================================
    // FLATPICKR — LOGIKA: FIELD YANG LAMA DI-CLEAR SAAT KONFLIK
    // ============================================================
    const today = new Date(); today.setHours(23, 59, 59, 999);
    let dariPicker, sampaiPicker;
    let isClearing = false;

    dariPicker = flatpickr("#dari", {
        dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        allowInput: false, monthSelectorType: "static", maxDate: today,
        onReady: (_, __, fp) => { fp.altInput.classList.add('glass-input'); fp.altInput.style.color = 'rgb(var(--text-primary))'; },
        onChange: function(selectedDates) {
            if (isClearing) return;
            const d = selectedDates[0];
            if (!d || !sampaiPicker) return;

            const s = sampaiPicker.selectedDates[0];
            if (s && d > s) {
                // Konflik: dari (baru) > sampai (lama). Clear SAMPAI (yang lama).
                isClearing = true;
                sampaiPicker.clear();
                if (sampaiPicker.altInput) sampaiPicker.altInput.value = '';
                isClearing = false;
            }
        }
    });

    sampaiPicker = flatpickr("#sampai", {
        dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        allowInput: false, monthSelectorType: "static", maxDate: today,
        onReady: (_, __, fp) => { fp.altInput.classList.add('glass-input'); fp.altInput.style.color = 'rgb(var(--text-primary))'; },
        onChange: function(selectedDates) {
            if (isClearing) return;
            const s = selectedDates[0];
            if (!s || !dariPicker) return;

            const d = dariPicker.selectedDates[0];
            if (d && d > s) {
                // Konflik: sampai (baru) < dari (lama). Clear DARI (yang lama).
                isClearing = true;
                dariPicker.clear();
                if (dariPicker.altInput) dariPicker.altInput.value = '';
                isClearing = false;
            }
        }
    });

    // ============================================================
    // SAFETY NET: Validasi sebelum form submit
    // ============================================================
    document.getElementById('filterForm')?.addEventListener('submit', function(e) {
        const d = dariPicker?.selectedDates[0];
        const s = sampaiPicker?.selectedDates[0];

        if (d && s && d > s) {
            e.preventDefault();
            alert('⚠️ Tanggal "Dari" tidak boleh lebih besar dari "Sampai".');
            return false;
        }
    });

    // ============================================================
    // MODAL HELPERS
    // ============================================================
    const STATUS_COLORS = {
        pending: 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
        diproses: 'background: rgb(var(--info-soft)); color: rgb(var(--info));',
        selesai: 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
        ditolak: 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
    };
    const STATUS_HEADER_COLORS = {
        pending: 'rgb(var(--warning))',
        diproses: 'rgb(var(--info))',
        selesai: 'rgb(var(--success))',
        ditolak: 'rgb(var(--danger))',
    };

    function formatRupiah(num) { return 'Rp ' + Number(num).toLocaleString('id-ID'); }

    function openRefundModal(data) {
        const modal = document.getElementById('refundModal');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        const fileInput = modal.querySelector('input[name="bukti_transfer_balik"]');
        if (fileInput) fileInput.value = '';

        document.getElementById('mRefundId').textContent = data.id;
        document.getElementById('mKodePesanan').textContent = data.pesanan.kode;
        document.getElementById('mTanggal').textContent = data.tanggal;

        const statusLabel = data.status_refund.charAt(0).toUpperCase() + data.status_refund.slice(1);
        const badge = document.getElementById('mStatusBadge');
        badge.textContent = statusLabel;
        badge.style.background = STATUS_HEADER_COLORS[data.status_refund] || 'rgb(var(--text-muted))';

        document.getElementById('mTotalPesanan').textContent = formatRupiah(data.pesanan.total_harga);
        document.getElementById('mNominalRefund').textContent = formatRupiah(data.nominal_refund);
        document.getElementById('mPaymentMethod').textContent = data.pesanan.payment_method;
        document.getElementById('mAlasanBatal').textContent = data.alasan_batal;

        const itemsBody = document.getElementById('mItemsBody');
        itemsBody.innerHTML = '';
        data.items.forEach(item => {
            itemsBody.innerHTML += `
                <tr style="border-bottom: 1px solid rgb(var(--border-soft));">
                    <td class="px-3 py-2" style="color: rgb(var(--text-primary));">${item.nama}</td>
                    <td class="px-3 py-2 text-center" style="color: rgb(var(--text-secondary));">${item.qty}</td>
                    <td class="px-3 py-2 text-right font-medium" style="color: rgb(var(--text-primary));">${formatRupiah(item.subtotal)}</td>
                </tr>
            `;
        });

        const buktiUserWrapper = document.getElementById('mBuktiUserWrapper');
        if (data.bukti_transfer_user) {
            document.getElementById('mBuktiUserImg').src = data.bukti_transfer_user;
            document.getElementById('mBuktiUserLink').href = data.bukti_transfer_user;
            buktiUserWrapper.classList.remove('hidden');
        } else {
            buktiUserWrapper.classList.add('hidden');
        }

        const buktiWrapper = document.getElementById('mBuktiWrapper');
        if (data.bukti_transfer_balik) {
            document.getElementById('mBuktiImg').src = data.bukti_transfer_balik;
            buktiWrapper.classList.remove('hidden');
        } else {
            buktiWrapper.classList.add('hidden');
        }

        document.getElementById('mAvatar').textContent = data.user.username.charAt(0).toUpperCase();
        document.getElementById('mUsername').textContent = data.user.username;
        document.getElementById('mEmail').textContent = data.user.email;
        document.getElementById('mTelepon').textContent = data.user.no_telepon || '-';

        document.getElementById('refundForm').action = data.route_update;
        document.getElementById('mInputStatus').value = data.status_refund;
        document.getElementById('mInputStatusLabel').textContent = statusLabel;
        document.getElementById('mInputCatatan').value = data.catatan_admin || '';

        document.querySelectorAll('.status-option-modal').forEach(btn => {
            btn.style.background = '';
            btn.style.color = 'rgb(var(--text-secondary))';
            btn.style.fontWeight = '';
            if (btn.dataset.value === data.status_refund) {
                btn.style.background = 'rgb(var(--brand) / 0.1)';
                btn.style.color = 'rgb(var(--brand))';
                btn.style.fontWeight = '600';
            }
        });

        document.getElementById('mTanggalAjuan').textContent = data.tanggal;
        const smallBadge = document.getElementById('mStatusBadgeSmall');
        smallBadge.textContent = statusLabel;
        smallBadge.style.cssText = STATUS_COLORS[data.status_refund] || 'background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));';
    }

    function closeRefundModal() {
        const modal = document.getElementById('refundModal');
        modal.classList.add('hidden'); modal.classList.remove('flex');
        document.body.style.overflow = '';
        const fileInput = modal.querySelector('input[name="bukti_transfer_balik"]');
        if (fileInput) fileInput.value = '';
    }

    document.getElementById('refundModal').addEventListener('click', function(e) {
        if (e.target === this) closeRefundModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRefundModal();
    });
</script>
@endpush