@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 txt-brand-hover px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Chip Status --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    @php
        $statusList = [
            'pending'    => ['label' => 'Pending',    'color1' => '#fbbf24', 'color2' => '#f59e0b'],
            'diproses'   => ['label' => 'Diproses',   'color1' => '#38bdf8', 'color2' => '#0ea5e9'],
            'dikirim'    => ['label' => 'Dikirim',    'color1' => '#a78bfa', 'color2' => '#8b5cf6'],
            'selesai'    => ['label' => 'Selesai',    'color1' => '#34d399', 'color2' => '#10b981'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'color1' => '#fb7185', 'color2' => '#f43f5e'],
        ];
    @endphp

    @foreach ($statusList as $key => $s)
        <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => $key])) }}"
           class="rounded-2xl p-4 shadow-md text-white transition hover:shadow-lg hover:-translate-y-0.5
                  {{ request('status') == $key ? 'ring-2 ring-white ring-offset-2 ring-offset-gray-100' : '' }}"
           style="background: linear-gradient(135deg, {{ $s['color1'] }} 0%, {{ $s['color2'] }} 100%);">
            <p class="text-sm font-semibold opacity-95 mb-1">{{ $s['label'] }}</p>
            <p class="text-3xl font-bold">{{ $stats[$key] ?? 0 }}</p>
        </a>
    @endforeach
</div>

{{-- Filter --}}
<div class="glass-card p-4 mb-6" x-data="{ statusOpen: false, metodeOpen: false }">
    <form method="GET" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Cari Pesanan</label>
            <div class="relative">
                <svg class="w-4 h-4 txt-muted absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Kode / pelanggan / produk..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Dari Tanggal</label>
            <input type="text" name="dari" id="dari" value="{{ request('dari') }}" readonly
                   class="datepicker w-36 px-3 py-2.5 text-sm rounded-xl border bd-default bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
        </div>

        <div>
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Sampai Tanggal</label>
            <input type="text" name="sampai" id="sampai" value="{{ request('sampai') }}" readonly
                   class="datepicker w-36 px-3 py-2.5 text-sm rounded-xl border bd-default bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
        </div>

        <div style="width: 160px;" class="relative" @click.away="statusOpen = false">
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Status</label>
            <button type="button" @click="statusOpen = !statusOpen"
                    class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                <span class="txt-primary whitespace-nowrap overflow-hidden text-ellipsis" id="statusLabel">
                    @php
                        $statusLabel = match(request('status')) {
                            'pending' => 'Pending',
                            'diproses' => 'Diproses',
                            'dikirim' => 'Dikirim',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                            default => 'Semua Status',
                        };
                    @endphp
                    {{ $statusLabel }}
                </span>
                <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="statusOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                @foreach (['' => 'Semua Status', 'pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                    <button type="button"
                            data-value="{{ $val }}"
                            data-label="{{ $label }}"
                            onclick="selectStatus(this)"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-50 transition
                                   {{ request('status') == $val ? 'bg-emerald-50 txt-brand-hover font-semibold' : 'txt-primary' }}">
                        <span>{{ $label }}</span>
                        @if (request('status') == $val)
                            <svg class="w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
        </div>

        <div style="width: 160px;" class="relative" @click.away="metodeOpen = false">
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Metode</label>
            <button type="button" @click="metodeOpen = !metodeOpen"
                    class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                <span class="txt-primary whitespace-nowrap overflow-hidden text-ellipsis" id="metodeLabel">
                    {{ request('payment_method') ?: 'Semua Metode' }}
                </span>
                <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="metodeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="metodeOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                @foreach (['' => 'Semua Metode', 'COD' => 'COD', 'Transfer' => 'Transfer'] as $val => $label)
                    <button type="button"
                            data-value="{{ $val }}"
                            data-label="{{ $label }}"
                            onclick="selectMetode(this)"
                            class="w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-50 transition
                                   {{ request('payment_method') == $val ? 'bg-emerald-50 txt-brand-hover font-semibold' : 'txt-primary' }}">
                        <span>{{ $label }}</span>
                        @if (request('payment_method') == $val)
                            <svg class="w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="payment_method" id="inputMetode" value="{{ request('payment_method') }}">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
                Filter
            </button>

            @if (request()->hasAny(['q', 'status', 'payment_method', 'dari', 'sampai']))
                <a href="{{ route('admin.pesanan.index') }}"
                   class="px-4 py-2.5 bg-soft hover:bg-gray-200 txt-primary text-sm font-medium rounded-xl transition flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                <tr class="text-xs font-semibold txt-muted uppercase tracking-wider bg-soft border-b bd-soft">
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
                            'pending'    => 'background:#fef3c7; color:#b45309;',
                            'diproses'   => 'background:#dbeafe; color:#1d4ed8;',
                            'dikirim'    => 'background:#ede9fe; color:#6d28d9;',
                            'selesai'    => 'background:#d1fae5; color:#047857;',
                            'dibatalkan' => 'background:#fee2e2; color:#b91c1c;',
                        ][$p->order_status] ?? 'background:#f3f4f6; color:#374151;';
                    @endphp
                    <tr class="border-b bd-soft hover:bg-soft transition">
                        <td class="px-5 py-4 text-sm font-semibold txt-primary whitespace-nowrap">
                            #{{ $p->kode_pesanan }}
                            @if ($p->lat_kurir && $p->lng_kurir && $p->order_status === 'dikirim')
                                <span class="inline-flex items-center gap-1 ml-1 text-[10px] font-bold px-1.5 py-0.5 bg-violet-100 text-violet-700 rounded">
                                    <span class="w-1.5 h-1.5 rounded-full bg-violet-500 animate-pulse"></span>
                                    LIVE
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium txt-primary">{{ $p->user->username ?? '-' }}</p>
                            <p class="text-xs txt-secondary">{{ $p->user->email ?? '' }}</p>
                        </td>

                        <td class="px-5 py-4">
                            @if ($p->detail->count() > 0)
                                <div class="space-y-1 max-w-[280px]">
                                    @foreach ($p->detail->take(2) as $d)
                                        <div class="flex items-center gap-1.5 text-sm">
                                            <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                            <span class="txt-primary truncate">{{ $d->nama_item }}</span>
                                            <span class="txt-muted text-xs flex-shrink-0">×{{ $d->jumlah }}</span>
                                        </div>
                                    @endforeach
                                    @if ($p->detail->count() > 2)
                                        <p class="text-xs txt-muted pl-4">+{{ $p->detail->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs txt-muted">-</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm txt-secondary whitespace-nowrap">{{ $p->tanggal_order->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-sm font-semibold txt-primary whitespace-nowrap">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if ($p->payment_method === 'COD')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#dbeafe; color:#1d4ed8;">COD</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#ede9fe; color:#6d28d9;">Transfer</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="{{ $statusStyle }}">{{ ucfirst($p->order_status) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button type="button"
                                    onclick='openPesananModal(@json($modalData, JSON_HEX_APOS | JSON_HEX_QUOT))'
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 txt-brand-hover rounded-lg text-xs font-semibold transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center txt-muted">
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
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 480px; max-height: 85vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-5 py-4 bg-emerald-600 text-white flex-shrink-0">
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

            <div class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Pelanggan</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center txt-brand font-bold text-base flex-shrink-0" id="mAvatar">U</div>
                    <div class="min-w-0">
                        <p class="font-semibold txt-primary" id="mUsername"></p>
                        <p class="text-xs txt-secondary truncate" id="mEmail"></p>
                        <p class="text-xs txt-secondary" id="mTelepon"></p>
                    </div>
                </div>
            </div>

            <div class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Alamat Pengiriman</p>
                <p class="txt-primary" id="mAlamat"></p>
                <p class="text-xs txt-secondary mt-1" id="mCatatan"></p>
            </div>

            <div>
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Item Pesanan</p>
                <div class="border bd-soft rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-soft text-xs txt-secondary uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Produk</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="mItemsBody"></tbody>
                        <tfoot class="bg-soft text-sm">
                            <tr class="border-t bd-soft">
                                <td colspan="2" class="px-3 py-2 text-right txt-secondary">Subtotal</td>
                                <td class="px-3 py-2 text-right font-medium txt-primary" id="mSubtotal"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-right txt-secondary">
                                    Ongkir <span class="text-xs txt-muted" id="mJarak"></span>
                                </td>
                                <td class="px-3 py-2 text-right font-medium txt-primary" id="mOngkir"></td>
                            </tr>
                            <tr class="border-t-2 bd-soft bg-emerald-50">
                                <td colspan="2" class="px-3 py-2 text-right font-bold txt-primary">Total</td>
                                <td class="px-3 py-2 text-right font-bold txt-brand" id="mTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs txt-secondary mb-0.5">Metode</p>
                        <p class="font-semibold txt-primary" id="mPaymentMethod"></p>
                    </div>
                    <div>
                        <p class="text-xs txt-secondary mb-0.5">Status</p>
                        <p class="font-semibold" id="mPaymentStatus"></p>
                    </div>
                </div>
                <div id="mBuktiWrapper" class="mt-2 hidden">
                    <p class="text-xs txt-secondary mb-1">Bukti Transfer</p>
                    <img id="mBukti" src="" alt="Bukti" class="w-full rounded-xl border bd-default">
                </div>

                <form id="mFormVerifikasi" method="POST" class="mt-2 hidden">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                        ✓ Verifikasi Pembayaran
                    </button>
                </form>
            </div>

            <div id="mRefundBox" class="hidden bg-red-50 border border-red-200 rounded-xl p-3">
                <p class="text-xs font-semibold text-red-500 uppercase mb-2">⚠️ Pengajuan Refund</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs txt-danger mb-0.5">Nominal</p>
                        <p class="font-bold text-red-800" id="mRefundNominal"></p>
                    </div>
                    <div>
                        <p class="text-xs txt-danger mb-0.5">Status</p>
                        <p class="font-bold text-red-800" id="mRefundStatus"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs txt-danger mb-0.5">Alasan</p>
                        <p class="text-red-800" id="mRefundAlasan"></p>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TRACKING KURIR — muncul kalau status = dikirim --}}
            {{-- ============================================ --}}
            <div id="mTrackingBox" class="hidden bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-200 rounded-xl p-3">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-semibold text-violet-700 uppercase flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></span>
                        Tracking Kurir
                    </p>
                    <span id="mTrackingStatus" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-200 txt-secondary">OFF</span>
                </div>

                <p id="mTrackingInfo" class="text-xs text-violet-800 mb-3 leading-relaxed">
                    Mulai tracking untuk mengirim lokasi Anda ke pelanggan secara real-time.
                </p>

                <div class="flex gap-2">
                    <button type="button" id="mBtnStartTracking"
                            onclick="handleStartTracking()"
                            class="flex-1 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-lg transition">
                        🛵 Mulai Antar
                    </button>
                    <button type="button" id="mBtnStopTracking"
                            onclick="handleStopTracking()"
                            class="hidden flex-1 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition">
                        ⏹ Selesai Antar
                    </button>
                </div>

                <p class="text-[10px] text-violet-600 mt-2 leading-relaxed">
                    💡 GPS akan aktif saat Anda klik "Mulai Antar". Pastikan browser Anda mengizinkan akses lokasi. Update posisi tiap 30 detik.
                </p>
            </div>

            {{-- UPDATE STATUS --}}
            <div id="mFormStatusWrapper" class="bg-soft rounded-xl p-3">
                <p class="text-xs font-semibold txt-muted uppercase mb-2">Update Status</p>

                <form id="mFormStatus" method="POST" class="flex flex-col gap-2">
                    @csrf
                    <input type="hidden" name="_method" id="mMethod" value="PATCH">

                    <div class="relative" x-data="{ statusModalOpen: false }" @click.away="statusModalOpen = false">
                        <button type="button" @click="statusModalOpen = !statusModalOpen"
                                class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                            <span class="txt-primary" id="mStatusFormLabel">Pilih Status</span>
                            <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="statusModalOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="statusModalOpen" x-cloak
                             class="absolute left-0 right-0 z-30 bottom-full mb-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                            @foreach (['pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                                <button type="button"
                                        data-value="{{ $val }}"
                                        data-label="{{ $label }}"
                                        onclick="selectOrderStatus(this)"
                                        class="order-status-option w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                                               {{ 'pending' == $val ? 'bg-emerald-50 txt-brand-hover font-semibold' : 'txt-primary' }}">
                                    <span>{{ $label }}</span>
                                    @if ('pending' == $val)
                                        <svg class="checkmark-order-svg w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>

                        <input type="hidden" name="order_status" id="mInputOrderStatus" value="pending">
                    </div>

                    <div id="mAlasanWrapper" class="hidden">
                        <label class="block text-xs font-medium txt-danger mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                        <input type="text" name="alasan_batal" id="mAlasanBatal" placeholder="Tulis alasan pembatalan..."
                               class="w-full px-3 py-2 rounded-xl border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm">
                    </div>

                    <button type="submit"
                            class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition"
                            onclick="return confirm('Update status pesanan ini?')">
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
    // STATE TRACKING (GLOBAL)
    // ============================================================
    let currentTrackingPesanan = null;   // { pesanan_id, route_update_lokasi, route_stop_tracking }
    let trackingInterval = null;
    let trackingStatusInterval = null;

    // ============================================================
    // FILTER HANDLERS
    // ============================================================
    function selectStatus(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        document.querySelector('[x-data]').__x.$data.statusOpen = false;
    }

    function selectMetode(el) {
        document.getElementById('inputMetode').value = el.dataset.value;
        document.getElementById('metodeLabel').textContent = el.dataset.label;
        document.querySelector('[x-data]').__x.$data.metodeOpen = false;
    }

    // ============================================================
    // ORDER STATUS DROPDOWN (MODAL)
    // ============================================================
    function selectOrderStatus(el) {
        const value = el.dataset.value;
        const label = el.dataset.label;

        document.getElementById('mInputOrderStatus').value = value;
        document.getElementById('mStatusFormLabel').textContent = label;

        document.querySelectorAll('.order-status-option').forEach(function(btn) {
            btn.classList.remove('bg-emerald-50', 'txt-brand-hover', 'font-semibold');
            btn.classList.add('txt-primary');

            const check = btn.querySelector('.checkmark-order-svg');
            if (check) check.remove();
        });

        el.classList.remove('txt-primary');
        el.classList.add('bg-emerald-50', 'txt-brand-hover', 'font-semibold');

        if (!el.querySelector('.checkmark-order-svg')) {
            el.insertAdjacentHTML('beforeend', '<svg class="checkmark-order-svg w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>');
        }

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

        const container = el.closest('[x-data]');
        if (container && container.__x) {
            container.__x.$data.statusModalOpen = false;
        }
    }

    // ============================================================
    // FLATPICKR
    // ============================================================
    const today = new Date();
    today.setHours(23, 59, 59, 999);

    let dariPicker, sampaiPicker;

    dariPicker = flatpickr("#dari", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        allowInput: false,
        monthSelectorType: "static",
        maxDate: today,
        onReady: function(selectedDates, dateStr, instance) {
            const yearInput = instance.currentYearElement;
            if (yearInput) {
                yearInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
                yearInput.addEventListener('keydown', function(e) {
                    if (['e', 'E', '+', '-', '.', ','].includes(e.key)) e.preventDefault();
                });
            }
            instance.altInput.setAttribute('readonly', 'readonly');
        },
        onChange: function(selectedDates) {
            if (selectedDates[0] && sampaiPicker) {
                sampaiPicker.set('minDate', selectedDates[0]);
                sampaiPicker.set('maxDate', today);
            }
        }
    });

    sampaiPicker = flatpickr("#sampai", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        allowInput: false,
        monthSelectorType: "static",
        maxDate: today,
        onReady: function(selectedDates, dateStr, instance) {
            const yearInput = instance.currentYearElement;
            if (yearInput) {
                yearInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
                yearInput.addEventListener('keydown', function(e) {
                    if (['e', 'E', '+', '-', '.', ','].includes(e.key)) e.preventDefault();
                });
            }
            instance.altInput.setAttribute('readonly', 'readonly');
        },
        onChange: function(selectedDates) {
            if (selectedDates[0] && dariPicker) {
                dariPicker.set('maxDate', selectedDates[0]);
            }
        }
    });

    // ============================================================
    // MODAL HELPERS
    // ============================================================
    const STATUS_COLORS = {
        pending: '#f59e0b',
        diproses: '#0ea5e9',
        dikirim: '#8b5cf6',
        selesai: '#10b981',
        dibatalkan: '#f43f5e',
    };

    const PAYMENT_STATUS_COLORS = {
        paid: '#10b981',
        pending: '#f59e0b',
        failed: '#ef4444',
        refunded: '#6b7280',
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
        badge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold text-white';
        badge.style.background = STATUS_COLORS[data.order_status] || '#6b7280';

        document.getElementById('mAvatar').textContent = data.user.username.charAt(0).toUpperCase();
        document.getElementById('mUsername').textContent = data.user.username;
        document.getElementById('mEmail').textContent = data.user.email;
        document.getElementById('mTelepon').textContent = data.user.no_telepon || '-';

        document.getElementById('mAlamat').textContent = data.alamat || '-';
        const catatan = document.getElementById('mCatatan');
        catatan.textContent = data.catatan ? 'Catatan: ' + data.catatan : '';

        const itemsBody = document.getElementById('mItemsBody');
        itemsBody.innerHTML = '';
        data.items.forEach(function(item) {
            itemsBody.innerHTML += `
                <tr class="border-b bd-soft">
                    <td class="px-3 py-2 txt-primary">
                        ${item.nama}
                        <p class="text-xs txt-secondary">${formatRupiah(item.harga)} × ${item.qty}</p>
                    </td>
                    <td class="px-3 py-2 text-center txt-secondary">${item.qty}</td>
                    <td class="px-3 py-2 text-right font-medium txt-primary">${formatRupiah(item.subtotal)}</td>
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
        payStatus.style.color = PAYMENT_STATUS_COLORS[data.payment_status] || '#6b7280';

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

            const alasanWrapper = document.getElementById('mAlasanWrapper');
            const alasanInput = document.getElementById('mAlasanBatal');
            alasanWrapper.classList.add('hidden');
            alasanInput.value = '';
            alasanInput.required = false;

            const statusLabelForm = data.order_status.charAt(0).toUpperCase() + data.order_status.slice(1);
            document.getElementById('mInputOrderStatus').value = data.order_status;
            document.getElementById('mStatusFormLabel').textContent = statusLabelForm;

            document.querySelectorAll('.order-status-option').forEach(function(btn) {
                btn.classList.remove('bg-emerald-50', 'txt-brand-hover', 'font-semibold');
                btn.classList.add('txt-primary');

                const check = btn.querySelector('.checkmark-order-svg');
                if (check) check.remove();

                if (btn.dataset.value === data.order_status) {
                    btn.classList.remove('txt-primary');
                    btn.classList.add('bg-emerald-50', 'txt-brand-hover', 'font-semibold');
                    btn.insertAdjacentHTML('beforeend', '<svg class="checkmark-order-svg w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>');
                }
            });
        }

        // ============================================================
        // TRACKING SECTION
        // ============================================================
        const trackingBox = document.getElementById('mTrackingBox');
        if (data.order_status === 'dikirim') {
            trackingBox.classList.remove('hidden');

            // Simpan state tracking
            currentTrackingPesanan = {
                pesanan_id: data.id,
                route_start_tracking: data.route_start_tracking,
                route_update_lokasi: data.route_update_lokasi,
                route_stop_tracking: data.route_stop_tracking,
            };

            // Set UI berdasarkan apakah tracking aktif
            if (data.is_tracking_active) {
                setTrackingUI(true, data.lokasi_updated_at);
            } else {
                setTrackingUI(false);
            }
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
            statusEl.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-violet-600 text-white';
            btnStart.classList.add('hidden');
            btnStop.classList.remove('hidden');
            infoEl.innerHTML = '<span class="font-bold">🛵 Sedang mengantar...</span><br>Lokasi Anda dibagikan ke pelanggan.';
        } else {
            statusEl.textContent = 'OFF';
            statusEl.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-gray-200 txt-secondary';
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

        // NOTE: Kita TIDAK stop interval pas modal ditutup,
        // supaya kalau admin buka lagi, tracking tetap jalan.
        // Kalau mau stop, ubah di sini:
        // stopTrackingCleanup();
    }

    document.getElementById('pesananModal').addEventListener('click', function(e) {
        if (e.target === this) closePesananModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePesananModal();
    });

    // ============================================================
    // TRACKING HANDLERS
    // ============================================================

    function stopTrackingCleanup() {
        if (trackingInterval) {
            clearInterval(trackingInterval);
            trackingInterval = null;
        }
        if (trackingStatusInterval) {
            clearInterval(trackingStatusInterval);
            trackingStatusInterval = null;
        }
    }

    async function handleStartTracking() {
        if (!currentTrackingPesanan) {
            alert('Pesanan tidak valid.');
            return;
        }

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung GPS. Silakan gunakan browser modern (Chrome/Safari/Edge).');
            return;
        }

        if (!confirm('Mulai antar pesanan ini? GPS Anda akan aktif dan lokasi akan dikirim ke pelanggan.')) {
            return;
        }

        // Get posisi pertama
        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                const ok = await kirimLokasi(currentTrackingPesanan.route_start_tracking, lat, lng);

                if (!ok) {
                    alert('Gagal memulai tracking. Coba lagi.');
                    return;
                }

                setTrackingUI(true);

                // Mulai interval update tiap 30 detik
                trackingInterval = setInterval(() => {
                    navigator.geolocation.getCurrentPosition(
                        async (p) => {
                            await kirimLokasi(currentTrackingPesanan.route_update_lokasi, p.coords.latitude, p.coords.longitude);
                        },
                        (err) => {
                            console.warn('GPS update error:', err);
                            // Kalau error, jangan stop interval, coba lagi di tick berikutnya
                        },
                        { enableHighAccuracy: true, timeout: 20000, maximumAge: 10000 }
                    );
                }, 30000);

            },
            (err) => {
                const msg = {
                    1: 'Izin lokasi ditolak. Silakan aktifkan GPS di pengaturan browser.',
                    2: 'Lokasi tidak tersedia. Pastikan GPS aktif.',
                    3: 'Timeout mendapatkan lokasi. Coba lagi.',
                }[err.code] || 'Gagal mendapatkan lokasi.';
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 10000 }
        );
    }

    async function handleStopTracking() {
        if (!currentTrackingPesanan) return;

        if (!confirm('Selesai antar pesanan ini? Tracking akan dihentikan.')) {
            return;
        }

        try {
            const res = await fetch(currentTrackingPesanan.route_stop_tracking, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();

            if (data.success) {
                stopTrackingCleanup();
                setTrackingUI(false);
                alert('Tracking dihentikan. Jangan lupa ubah status pesanan ke "Selesai".');
            } else {
                alert(data.message || 'Gagal menghentikan tracking.');
            }
        } catch (e) {
            alert('Error: ' + e.message);
        }
    }

    async function kirimLokasi(route, lat, lng) {
        try {
            const res = await fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ lat, lng }),
            });
            const data = await res.json();
            return !!data.success;
        } catch (e) {
            console.warn('Kirim lokasi error:', e);
            return false;
        }
    }
</script>
@endpush