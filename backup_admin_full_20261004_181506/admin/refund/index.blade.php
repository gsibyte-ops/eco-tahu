@extends('layouts.admin')
@section('title', 'Kelola Refund')
@section('page-title', 'Kelola Refund')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 txt-brand-hover px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Chip Status --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    @php
        $statusList = [
            'pending'    => ['label' => 'Pending',    'color1' => '#fbbf24', 'color2' => '#f59e0b'],
            'diproses'   => ['label' => 'Diproses',   'color1' => '#38bdf8', 'color2' => '#0ea5e9'],
            'selesai'    => ['label' => 'Selesai',    'color1' => '#34d399', 'color2' => '#10b981'],
        ];
    @endphp

    @foreach ($statusList as $key => $s)
        <a href="{{ route('admin.refund.index', array_merge(request()->except('status', 'page'), ['status' => $key])) }}"
           class="rounded-2xl p-4 shadow-md text-white transition hover:shadow-lg hover:-translate-y-0.5
                  {{ request('status') == $key ? 'ring-2 ring-white ring-offset-2 ring-offset-gray-100' : '' }}"
           style="background: linear-gradient(135deg, {{ $s['color1'] }} 0%, {{ $s['color2'] }} 100%);">
            <p class="text-sm font-semibold opacity-95 mb-1">{{ $s['label'] }}</p>
            <p class="text-3xl font-bold">{{ $stats[$key] ?? 0 }}</p>
        </a>
    @endforeach

    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-emerald-400 to-emerald-600"></div>
        <div class="pl-3">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-6 h-6 rounded-lg bg-emerald-50 flex items-center justify-center text-sm">💰</span>
                <p class="text-sm font-semibold txt-secondary">Total Refund</p>
            </div>
            <p class="text-lg font-bold txt-brand">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="glass-card p-4 mb-6" x-data="{ statusOpen: false }">
    <form method="GET" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold txt-secondary uppercase tracking-wider mb-2">Cari</label>
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
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak',
                            default => 'Semua Status',
                        };
                    @endphp
                    {{ $statusLabel }}
                </span>
                <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="statusOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                @foreach (['' => 'Semua Status', 'pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                    <button type="button" data-value="{{ $val }}" data-label="{{ $label }}"
                            onclick="selectStatusFilter(this)"
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

        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
                Filter
            </button>

            @if (request()->hasAny(['q', 'status', 'dari', 'sampai']))
                <a href="{{ route('admin.refund.index') }}"
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
                            'pending'    => 'background:#fef3c7; color:#b45309;',
                            'diproses'   => 'background:#dbeafe; color:#1d4ed8;',
                            'selesai'    => 'background:#d1fae5; color:#047857;',
                            'ditolak'    => 'background:#fee2e2; color:#b91c1c;',
                        ][$r->status_refund] ?? 'background:#f3f4f6; color:#374151;';

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
                    <tr class="border-b bd-soft hover:bg-soft transition">
                        <td class="px-5 py-4 text-sm txt-secondary">#{{ $r->id }}</td>
                        <td class="px-5 py-4 text-sm font-semibold txt-primary whitespace-nowrap">#{{ $r->pesanan->kode_pesanan ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium txt-primary">{{ $r->pesanan->user->username ?? '-' }}</p>
                            <p class="text-xs txt-secondary">{{ $r->pesanan->user->email ?? '' }}</p>
                        </td>

                        <td class="px-5 py-4">
                            @if ($r->pesanan && $r->pesanan->detail->count() > 0)
                                <div class="space-y-1 max-w-[240px]">
                                    @foreach ($r->pesanan->detail->take(2) as $d)
                                        <div class="flex items-center gap-1.5 text-sm">
                                            <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                            <span class="txt-primary truncate">{{ $d->nama_item }}</span>
                                            <span class="txt-muted text-xs flex-shrink-0">×{{ $d->jumlah }}</span>
                                        </div>
                                    @endforeach
                                    @if ($r->pesanan->detail->count() > 2)
                                        <p class="text-xs txt-muted pl-4">+{{ $r->pesanan->detail->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs txt-muted">-</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm font-bold txt-brand-hover whitespace-nowrap">
                            Rp {{ number_format($r->nominal_refund, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-sm txt-secondary">
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
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 {{ $isUrgent ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 txt-brand-hover' }} rounded-lg text-xs font-bold transition whitespace-nowrap">
                                @if ($isUrgent)
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Proses
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                @endif
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center txt-muted">
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
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 960px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 bg-emerald-600 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Refund #<span id="mRefundId"></span></p>
                <h3 class="text-base font-bold">Pesanan #<span id="mKodePesanan"></span></h3>
                <p class="text-xs opacity-80 mt-0.5">Diajukan <span id="mTanggal"></span></p>
            </div>
            <div class="flex items-center gap-3">
                <span id="mStatusBadge" class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 text-white"></span>
                <button onclick="closeRefundModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <form id="refundForm" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 p-6">

                <div class="lg:col-span-2 space-y-4">

                    <div class="bg-soft rounded-xl p-4">
                        <h4 class="font-bold txt-primary mb-3 text-sm">Informasi Refund</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between py-1.5 border-b bd-default">
                                <span class="txt-secondary">Total Pesanan</span>
                                <span class="font-semibold txt-primary" id="mTotalPesanan">-</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b bd-default">
                                <span class="txt-secondary">Nominal Refund</span>
                                <span class="font-bold txt-brand-hover" id="mNominalRefund">-</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b bd-default">
                                <span class="txt-secondary">Metode Pembayaran</span>
                                <span class="font-semibold txt-primary" id="mPaymentMethod">-</span>
                            </div>
                            <div class="py-1.5">
                                <p class="txt-secondary mb-1">Alasan Pembatalan</p>
                                <p class="txt-primary bg-white p-2.5 rounded-lg text-sm" id="mAlasanBatal">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-soft rounded-xl p-4">
                        <h4 class="font-bold txt-primary mb-3 text-sm">Item yang Dipesan</h4>
                        <div class="border bd-default rounded-lg overflow-hidden bg-white">
                            <table class="w-full text-sm">
                                <thead class="bg-soft text-xs txt-secondary uppercase">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Produk</th>
                                        <th class="px-3 py-2 text-center">Qty</th>
                                        <th class="px-3 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="mItemsBody"></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Bukti Transfer dari User --}}
                    <div id="mBuktiUserWrapper" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h4 class="font-bold text-blue-800 mb-2 text-sm">📎 Bukti Transfer dari User</h4>
                        <p class="text-xs txt-info mb-3">Bukti ini diupload user saat checkout / pembatalan. Gunakan sebagai referensi verifikasi.</p>
                        <a id="mBuktiUserLink" href="#" target="_blank" class="block">
                            <img id="mBuktiUserImg" src="" alt="Bukti User" class="w-full max-w-xs rounded-xl border border-blue-200 hover:opacity-90 transition">
                        </a>
                        <p class="text-xs txt-info mt-2">Klik gambar untuk lihat full size.</p>
                    </div>

                    {{-- Bukti transfer balik dari admin --}}
                    <div id="mBuktiWrapper" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <h4 class="font-bold text-emerald-800 mb-2 text-sm">✓ Bukti Transfer Balik</h4>
                        <img id="mBuktiImg" src="" alt="Bukti" class="w-full max-w-xs rounded-xl border border-emerald-200">
                    </div>
                </div>

                <div class="space-y-4">

                    <div class="bg-soft rounded-xl p-4">
                        <h4 class="font-bold txt-primary mb-3 text-sm">Rekening Tujuan</h4>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-11 h-11 rounded-full bg-emerald-100 flex items-center justify-center txt-brand font-bold flex-shrink-0" id="mAvatar">U</div>
                            <div class="min-w-0">
                                <p class="font-semibold txt-primary text-sm truncate" id="mUsername">-</p>
                                <p class="text-xs txt-secondary truncate" id="mEmail">-</p>
                            </div>
                        </div>
                        <div class="text-xs">
                            <p class="txt-secondary">No. Telepon</p>
                            <p class="txt-primary font-medium" id="mTelepon">-</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-xl p-4">
                        <h4 class="font-bold txt-primary mb-3 text-sm">Proses Refund</h4>

                        <div class="space-y-3">

                            <div class="relative" x-data="{ modalStatusOpen: false }" @click.away="modalStatusOpen = false">
                                <label class="block text-xs font-medium txt-secondary mb-1">Status Refund</label>
                                <button type="button" @click="modalStatusOpen = !modalStatusOpen"
                                        class="w-full flex items-center justify-between gap-2 px-3 py-2 text-sm rounded-lg border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                                    <span class="txt-primary" id="mInputStatusLabel">Pending</span>
                                    <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="modalStatusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <div x-show="modalStatusOpen" x-cloak
                                     class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-lg border bd-soft shadow-lg overflow-hidden">
                                    @foreach (['pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                                        <button type="button"
                                                data-value="{{ $val }}"
                                                data-label="{{ $label }}"
                                                onclick="selectModalStatus(this)"
                                                class="status-option-modal w-full flex items-center justify-between gap-2 px-3 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                                                       {{ 'pending' == $val ? 'bg-emerald-50 txt-brand-hover font-semibold' : 'txt-primary' }}">
                                            <span>{{ $label }}</span>
                                            @if ('pending' == $val)
                                                <svg class="checkmark-svg w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>

                                <input type="hidden" name="status_refund" id="mInputStatus" value="pending">
                            </div>

                            <div>
                                <label class="block text-xs font-medium txt-secondary mb-1">Catatan untuk Pelanggan</label>
                                <textarea name="catatan_admin" id="mInputCatatan" rows="3" maxlength="1000"
                                          class="w-full px-3 py-2 text-sm rounded-lg border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                                          placeholder="Contoh: Dana sudah kami transfer ke rekening Anda..."></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-medium txt-secondary mb-1">Bukti Transfer Balik</label>
                                <input type="file" name="bukti_transfer_balik" accept="image/jpeg,image/jpg,image/png,image/webp"
                                       class="w-full px-3 py-2 rounded-lg border bd-default text-xs
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                              file:bg-emerald-50 file:txt-brand-hover hover:file:bg-emerald-100">
                                <p class="text-[10px] txt-muted mt-1">JPG, PNG, WEBP. Max 5MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-soft rounded-xl p-4 text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="txt-secondary">Tanggal Ajuan</span>
                            <span class="font-semibold txt-primary" id="mTanggalAjuan">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="txt-secondary">Status Saat Ini</span>
                            <span id="mStatusBadgeSmall" class="px-2 py-0.5 rounded-md font-bold">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="flex items-center justify-between gap-3 px-6 py-4 border-t bd-soft bg-soft flex-shrink-0">
            <p class="text-xs txt-secondary">⚠️ Pastikan data sudah benar sebelum menyimpan perubahan</p>
            <div class="flex gap-2">
                <button type="button" onclick="closeRefundModal()"
                        class="px-4 py-2 bg-white hover:bg-soft border bd-default txt-primary text-sm font-medium rounded-xl transition">
                    Batal
                </button>
                <button type="submit" form="refundForm"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    function selectStatusFilter(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        const container = el.closest('[x-data]');
        if (container && container.__x) {
            container.__x.$data.statusOpen = false;
        }
    }

    function selectModalStatus(el) {
        const value = el.dataset.value;
        const label = el.dataset.label;

        document.getElementById('mInputStatus').value = value;
        document.getElementById('mInputStatusLabel').textContent = label;

        document.querySelectorAll('.status-option-modal').forEach(function(btn) {
            btn.classList.remove('bg-emerald-50', 'txt-brand-hover', 'font-semibold');
            btn.classList.add('txt-primary');

            const check = btn.querySelector('.checkmark-svg');
            if (check) check.remove();
        });

        el.classList.remove('txt-primary');
        el.classList.add('bg-emerald-50', 'txt-brand-hover', 'font-semibold');

        if (!el.querySelector('.checkmark-svg')) {
            el.insertAdjacentHTML('beforeend', '<svg class="checkmark-svg w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>');
        }

        const container = el.closest('[x-data]');
        if (container && container.__x) {
            container.__x.$data.modalStatusOpen = false;
        }
    }

    // ==== FLATPICKR — VALIDASI TANGGAL ====
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

    // ==== MODAL REFUND ====
    const STATUS_COLORS = {
        pending: 'background:#fef3c7; color:#b45309;',
        diproses: 'background:#dbeafe; color:#1d4ed8;',
        selesai: 'background:#d1fae5; color:#047857;',
        ditolak: 'background:#fee2e2; color:#b91c1c;',
    };

    const STATUS_HEADER_COLORS = {
        pending: '#f59e0b',
        diproses: '#0ea5e9',
        selesai: '#10b981',
        ditolak: '#ef4444',
    };

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function openRefundModal(data) {
        const modal = document.getElementById('refundModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        const fileInput = modal.querySelector('input[name="bukti_transfer_balik"]');
        if (fileInput) fileInput.value = '';

        document.getElementById('mRefundId').textContent = data.id;
        document.getElementById('mKodePesanan').textContent = data.pesanan.kode;
        document.getElementById('mTanggal').textContent = data.tanggal;

        const statusLabel = data.status_refund.charAt(0).toUpperCase() + data.status_refund.slice(1);

        const badge = document.getElementById('mStatusBadge');
        badge.textContent = statusLabel;
        badge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold text-white';
        badge.style.background = STATUS_HEADER_COLORS[data.status_refund] || '#6b7280';

        document.getElementById('mTotalPesanan').textContent = formatRupiah(data.pesanan.total_harga);
        document.getElementById('mNominalRefund').textContent = formatRupiah(data.nominal_refund);
        document.getElementById('mPaymentMethod').textContent = data.pesanan.payment_method;
        document.getElementById('mAlasanBatal').textContent = data.alasan_batal;

        const itemsBody = document.getElementById('mItemsBody');
        itemsBody.innerHTML = '';
        data.items.forEach(function(item) {
            itemsBody.innerHTML += `
                <tr class="border-b bd-soft last:border-0">
                    <td class="px-3 py-2 txt-primary">${item.nama}</td>
                    <td class="px-3 py-2 text-center txt-secondary">${item.qty}</td>
                    <td class="px-3 py-2 text-right font-medium txt-primary">${formatRupiah(item.subtotal)}</td>
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

        document.querySelectorAll('.status-option-modal').forEach(function(btn) {
            btn.classList.remove('bg-emerald-50', 'txt-brand-hover', 'font-semibold');
            btn.classList.add('txt-primary');

            const check = btn.querySelector('.checkmark-svg');
            if (check) check.remove();

            if (btn.dataset.value === data.status_refund) {
                btn.classList.remove('txt-primary');
                btn.classList.add('bg-emerald-50', 'txt-brand-hover', 'font-semibold');
                btn.insertAdjacentHTML('beforeend', '<svg class="checkmark-svg w-4 h-4 txt-brand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>');
            }
        });

        document.getElementById('mTanggalAjuan').textContent = data.tanggal;

        const smallBadge = document.getElementById('mStatusBadgeSmall');
        smallBadge.textContent = statusLabel;
        smallBadge.style.cssText = STATUS_COLORS[data.status_refund] || 'background:#f3f4f6; color:#374151;';
    }

    function closeRefundModal() {
        const modal = document.getElementById('refundModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
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