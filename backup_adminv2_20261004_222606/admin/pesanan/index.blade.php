@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

@if (session('success'))
    <div class="rounded-2xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); background: rgb(var(--success-soft) / 0.6); border: 1px solid rgb(var(--success) / 0.3);">
        {{ session('success') }}
    </div>
@endif

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

{{-- Filter — 2 baris rapi --}}
<div class="glass-card p-5 mb-6" x-data="{ statusOpen: false, metodeOpen: false }">
    <form method="GET">

        {{-- Baris 1: Search --}}
        <div class="mb-4">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Cari Pesanan</label>
            <div class="auth-input-wrap" style="position: relative;">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Kode / pelanggan / produk..."
                       class="glass-input w-full pl-10 pr-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
            </div>
        </div>

        {{-- Baris 2: Filters --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
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

            {{-- Status dropdown --}}
            <div class="relative" @click.away="statusOpen = false">
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Status</label>
                <button type="button" @click="statusOpen = !statusOpen"
                        class="glass-input w-full flex items-center justify-between gap-2 py-3 text-sm text-left" style="color: rgb(var(--text-primary));">
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
                <div x-show="statusOpen" x-cloak class="absolute left-0 right-0 z-30 mt-2 rounded-xl overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                    @foreach (['' => 'Semua Status', 'pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                        <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectStatusFilter(this)"
                                class="w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10"
                                style="{{ request('status') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
            </div>

            {{-- Metode dropdown --}}
            <div class="relative" @click.away="metodeOpen = false">
                <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Metode</label>
                <button type="button" @click="metodeOpen = !metodeOpen"
                        class="glass-input w-full flex items-center justify-between gap-2 py-3 text-sm text-left" style="color: rgb(var(--text-primary));">
                    <span class="truncate" id="metodeLabel">{{ request('payment_method') ?: 'Semua Metode' }}</span>
                    <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="metodeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="metodeOpen" x-cloak class="absolute left-0 right-0 z-30 mt-2 rounded-xl overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                    @foreach (['' => 'Semua Metode', 'COD' => 'COD', 'Transfer' => 'Transfer'] as $val => $label)
                        <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectMetodeFilter(this)"
                                class="w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10"
                                style="{{ request('payment_method') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="payment_method" id="inputMetode" value="{{ request('payment_method') }}">
            </div>
        </div>

        {{-- Baris 3: Actions --}}
        <div class="flex flex-wrap gap-2 mt-4">
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
<div class="glass-card overflow-hidden">
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

<div class="mt-4">{{ $pesanan->links() }}</div>

{{-- MODAL DETAIL --}}
<div id="pesananModal"
     class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 480px; max-height: 85vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">
        <div class="flex items-center justify-between px-5 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Pesanan</p>
                <h3 class="text-base font-bold">#<span id="mKode"></span></h3>
                <p class="text-xs opacity-80 mt-0.5" id="mTanggal"></p>
            </div>
            <div class="flex items-center gap-3">
                <span id="mStatusBadge" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 text-white"></span>
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
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgb(var(--text-muted));">Metode</p>
                        <p class="font-semibold" style="color: rgb(var(--text-primary));" id="mPaymentMethod"></p>
                    </div>
                    <div>
                        <p class="text-xs mb-0.5" style="color: rgb(var(--text-muted));">Status</p>
                        <p class="font-semibold" id="mPaymentStatus"></p>
                    </div>
                </div>
                <div id="mBuktiWrapper" class="mt-2 hidden">
                    <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Bukti Transfer</p>
                    <img id="mBukti" src="" alt="Bukti" class="w-full rounded-xl" style="border: 1px solid rgb(var(--border-soft));">
                </div>
                <form id="mFormVerifikasi" method="POST" class="mt-2 hidden">
                    @csrf
                    <button type="submit" class="btn-primary w-full !py-2 !text-xs">✓ Verifikasi Pembayaran</button>
                </form>
            </div>

            <div id="mRefundBox" class="hidden rounded-xl p-3" style="background: rgb(var(--danger-soft) / 0.5); border: 1px solid rgb(var(--danger) / 0.3);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--danger));">⚠️ Pengajuan Refund</p>
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
                    <button type="button" id="mBtnStartTracking" onclick="handleStartTracking()"
                            class="flex-1 py-2 rounded-lg text-xs font-bold text-white" style="background: rgb(var(--info));">
                        🛵 Mulai Antar
                    </button>
                    <button type="button" id="mBtnStopTracking" onclick="handleStopTracking()"
                            class="hidden flex-1 py-2 rounded-lg text-xs font-bold text-white" style="background: rgb(var(--danger));">
                        ⏹ Selesai Antar
                    </button>
                </div>
                <p class="text-[10px] mt-2 leading-relaxed" style="color: rgb(var(--info));">
                    💡 GPS akan aktif saat Anda klik "Mulai Antar". Update tiap 30 detik.
                </p>
            </div>

            <div id="mFormStatusWrapper" class="rounded-xl p-3" style="background: rgb(var(--bg-secondary) / 0.6);">
                <p class="text-xs font-bold uppercase mb-2" style="color: rgb(var(--text-muted));">Update Status</p>
                <form id="mFormStatus" method="POST" class="flex flex-col gap-2">
                    @csrf
                    <input type="hidden" name="_method" id="mMethod" value="PATCH">
                    <div class="relative" x-data="{ statusModalOpen: false }" @click.away="statusModalOpen = false">
                        <button type="button" @click="statusModalOpen = !statusModalOpen"
                                class="glass-input w-full flex items-center justify-between gap-2 py-2 text-sm text-left" style="color: rgb(var(--text-primary));">
                            <span id="mStatusFormLabel">Pilih Status</span>
                            <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="statusModalOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="statusModalOpen" x-cloak
                             class="absolute left-0 right-0 z-30 bottom-full mb-2 rounded-xl overflow-hidden"
                             style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                            @foreach (['pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                                <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectOrderStatus(this)"
                                        class="order-status-option w-full text-left px-4 py-2.5 text-sm transition hover:bg-emerald-500/10"
                                        style="color: rgb(var(--text-secondary));">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="order_status" id="mInputOrderStatus" value="pending">
                    </div>

                    <div id="mAlasanWrapper" class="hidden">
                        <label class="block text-xs font-medium mb-1" style="color: rgb(var(--danger));">Alasan Pembatalan <span style="color: rgb(var(--danger));">*</span></label>
                        <input type="text" name="alasan_batal" id="mAlasanBatal" placeholder="Tulis alasan pembatalan..."
                               class="glass-input w-full py-2 text-sm" style="color: rgb(var(--text-primary));">
                    </div>

                    <button type="submit" class="btn-primary w-full !py-2 !text-xs" onclick="return confirm('Update status pesanan ini?')">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // ============================================================
    // STATE
    // ============================================================
    let currentTrackingPesanan = null;
    let trackingInterval = null;

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
    // ORDER STATUS SELECT
    // ============================================================
    function selectOrderStatus(el) {
        const value = el.dataset.value;
        const label = el.dataset.label;

        document.getElementById('mInputOrderStatus').value = value;
        document.getElementById('mStatusFormLabel').textContent = label;

        document.querySelectorAll('.order-status-option').forEach(btn => {
            btn.style.background = '';
            btn.style.color = 'rgb(var(--text-secondary))';
            btn.style.fontWeight = '';
        });
        el.style.background = 'rgb(var(--brand) / 0.1)';
        el.style.color = 'rgb(var(--brand))';
        el.style.fontWeight = '600';

        const alasanWrapper = document.getElementById('mAlasanWrapper');
        const alasanInput = document.getElementById('mAlasanBatal');
        const formStatus = document.getElementById('mFormStatus');
        const methodInput = document.getElementById('mMethod');

        if (value === 'dibatalkan') {
            alasanWrapper.classList.remove('hidden');
            alasanInput.required = true;
            formStatus.action = formStatus.getAttribute('data-batalkan-route');
            methodInput.value = 'PUT';
        } else {
            alasanWrapper.classList.add('hidden');
            alasanInput.required = false;
            alasanInput.value = '';
            formStatus.action = formStatus.getAttribute('data-update-route');
            methodInput.value = 'PATCH';
        }

        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.statusModalOpen = false;
    }

    // ============================================================
    // FLATPICKR
    // ============================================================
    const today = new Date();
    today.setHours(23, 59, 59, 999);
    let dariPicker, sampaiPicker;

    dariPicker = flatpickr("#dari", {
        dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        allowInput: false, monthSelectorType: "static", maxDate: today,
        onReady: function(_, __, fp) {
            fp.altInput.classList.add('glass-input');
            fp.altInput.style.color = 'rgb(var(--text-primary))';
        },
        onChange: function(d) { if (d[0] && sampaiPicker) sampaiPicker.set('minDate', d[0]); }
    });
    sampaiPicker = flatpickr("#sampai", {
        dateFormat: "Y-m-d", altInput: true, altFormat: "d M Y",
        allowInput: false, monthSelectorType: "static", maxDate: today,
        onReady: function(_, __, fp) {
            fp.altInput.classList.add('glass-input');
            fp.altInput.style.color = 'rgb(var(--text-primary))';
        },
        onChange: function(d) { if (d[0] && dariPicker) dariPicker.set('maxDate', d[0]); }
    });

    // ============================================================
    // MODAL HELPERS
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
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function openPesananModal(data) {
        const modal = document.getElementById('pesananModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
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

        const formVerif = document.getElementById('mFormVerifikasi');
        if (data.payment_method === 'Transfer' && data.payment_status !== 'paid' && data.pembayaran) {
            formVerif.action = data.route_verifikasi;
            formVerif.classList.remove('hidden');
        } else {
            formVerif.classList.add('hidden');
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

        const formStatusWrapper = document.getElementById('mFormStatusWrapper');
        const formStatus = document.getElementById('mFormStatus');
        if (data.order_status === 'selesai' || data.order_status === 'dibatalkan') {
            formStatusWrapper.classList.add('hidden');
        } else {
            formStatusWrapper.classList.remove('hidden');
            formStatus.setAttribute('data-update-route', data.route_update_status);
            formStatus.setAttribute('data-batalkan-route', data.route_batalkan);
            formStatus.action = data.route_update_status;
            document.getElementById('mAlasanWrapper').classList.add('hidden');
            document.getElementById('mAlasanBatal').value = '';
            document.getElementById('mAlasanBatal').required = false;
            document.getElementById('mInputOrderStatus').value = data.order_status;
            document.getElementById('mStatusFormLabel').textContent = data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1);

            document.querySelectorAll('.order-status-option').forEach(btn => {
                btn.style.background = '';
                btn.style.color = 'rgb(var(--text-secondary))';
                btn.style.fontWeight = '';
                if (btn.dataset.value === data.order_status) {
                    btn.style.background = 'rgb(var(--brand) / 0.1)';
                    btn.style.color = 'rgb(var(--brand))';
                    btn.style.fontWeight = '600';
                }
            });
        }

        // TRACKING
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
            infoEl.innerHTML = '<span class="font-bold">🛵 Sedang mengantar...</span><br>Lokasi Anda dibagikan ke pelanggan.';
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
        document.body.style.overflow = '';
    }

    document.getElementById('pesananModal').addEventListener('click', function(e) {
        if (e.target === this) closePesananModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePesananModal();
    });

    // ============================================================
    // TRACKING
    // ============================================================
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
                headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.success) {
                stopTrackingCleanup();
                setTrackingUI(false);
                alert('Tracking dihentikan. Jangan lupa ubah status ke "Selesai".');
            } else alert(data.message || 'Gagal.');
        } catch (e) { alert('Error: ' + e.message); }
    }

    async function kirimLokasi(route, lat, lng) {
        try {
            const res = await fetch(route, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
                body: JSON.stringify({ lat, lng }),
            });
            const data = await res.json();
            return !!data.success;
        } catch (e) { console.warn('Kirim lokasi error:', e); return false; }
    }
</script>
@endpush