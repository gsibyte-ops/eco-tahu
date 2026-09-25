@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
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
        <div class="rounded-2xl p-4 shadow-md text-white"
             style="background: linear-gradient(135deg, {{ $s['color1'] }} 0%, {{ $s['color2'] }} 100%);">
            <p class="text-sm font-semibold opacity-95 mb-1">{{ $s['label'] }}</p>
            <p class="text-3xl font-bold">{{ $stats[$key] ?? 0 }}</p>
        </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6" x-data="{ statusOpen: false, metodeOpen: false }">
    <form method="GET">
        <div class="flex flex-wrap gap-4 items-end">

            {{-- Search --}}
            <div class="flex-1 min-w-[240px]">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Pesanan</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Kode pesanan / nama pelanggan / nama produk..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
            </div>

            {{-- Dropdown Status --}}
            <div style="width: 200px;" class="relative" @click.away="statusOpen = false">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                <button type="button" @click="statusOpen = !statusOpen"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                    <span class="text-gray-700 whitespace-nowrap overflow-hidden text-ellipsis" id="statusLabel">
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
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="statusOpen" x-cloak
                     class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border border-gray-100 shadow-lg overflow-hidden">
                    @foreach (['' => 'Semua Status', 'pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                        <button type="button"
                                data-value="{{ $val }}"
                                data-label="{{ $label }}"
                                onclick="selectStatus(this)"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-50 transition
                                       {{ request('status') == $val ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                            <span>{{ $label }}</span>
                            @if (request('status') == $val)
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    @endforeach
                </div>

                <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
            </div>

            {{-- Dropdown Metode --}}
            <div style="width: 180px;" class="relative" @click.away="metodeOpen = false">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Bayar</label>
                <button type="button" @click="metodeOpen = !metodeOpen"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                    <span class="text-gray-700 whitespace-nowrap overflow-hidden text-ellipsis" id="metodeLabel">
                        {{ request('payment_method') ?: 'Semua Metode' }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" :class="metodeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="metodeOpen" x-cloak
                     class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border border-gray-100 shadow-lg overflow-hidden">
                    @foreach (['' => 'Semua Metode', 'COD' => 'COD', 'Transfer' => 'Transfer'] as $val => $label)
                        <button type="button"
                                data-value="{{ $val }}"
                                data-label="{{ $label }}"
                                onclick="selectMetode(this)"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-50 transition
                                       {{ request('payment_method') == $val ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                            <span>{{ $label }}</span>
                            @if (request('payment_method') == $val)
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    @endforeach
                </div>

                <input type="hidden" name="payment_method" id="inputMetode" value="{{ request('payment_method') }}">
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
                    Filter
                </button>

                @if (request()->hasAny(['q', 'status', 'payment_method']))
                    <a href="{{ route('admin.pesanan.index') }}"
                       class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60 border-b border-gray-100">
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
                        ];

                        $statusStyle = [
                            'pending'    => 'background:#fef3c7; color:#b45309;',
                            'diproses'   => 'background:#dbeafe; color:#1d4ed8;',
                            'dikirim'    => 'background:#ede9fe; color:#6d28d9;',
                            'selesai'    => 'background:#d1fae5; color:#047857;',
                            'dibatalkan' => 'background:#fee2e2; color:#b91c1c;',
                        ][$p->order_status] ?? 'background:#f3f4f6; color:#374151;';
                    @endphp
                    <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                        <td class="px-5 py-4 text-sm font-semibold text-gray-800 whitespace-nowrap">#{{ $p->kode_pesanan }}</td>
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-800">{{ $p->user->username ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $p->user->email ?? '' }}</p>
                        </td>

                        {{-- KOLOM PRODUK (BARU) --}}
                        <td class="px-5 py-4">
                            @if ($p->detail->count() > 0)
                                <div class="space-y-1 max-w-[280px]">
                                    @foreach ($p->detail->take(2) as $d)
                                        <div class="flex items-center gap-1.5 text-sm">
                                            <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                            <span class="text-gray-700 truncate">{{ $d->nama_item }}</span>
                                            <span class="text-gray-400 text-xs flex-shrink-0">×{{ $d->jumlah }}</span>
                                        </div>
                                    @endforeach
                                    @if ($p->detail->count() > 2)
                                        <p class="text-xs text-gray-400 pl-4">+{{ $p->detail->count() - 2 }} produk lainnya</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $p->tanggal_order->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-sm font-semibold text-gray-800 whitespace-nowrap">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
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
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                            @if (request('q'))
                                Tidak ada pesanan dengan kata kunci "<span class="font-semibold text-gray-600">{{ request('q') }}</span>"
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

{{-- ============================================ --}}
{{-- MODAL DETAIL --}}
{{-- ============================================ --}}
<div id="pesananModal"
     class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 480px; max-height: 80vh;"
         class="bg-white rounded-2xl overflow-hidden flex flex-col shadow-2xl">

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

            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Pelanggan</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-base flex-shrink-0" id="mAvatar">U</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800" id="mUsername"></p>
                        <p class="text-xs text-gray-500 truncate" id="mEmail"></p>
                        <p class="text-xs text-gray-500" id="mTelepon"></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Alamat Pengiriman</p>
                <p class="text-gray-700" id="mAlamat"></p>
                <p class="text-xs text-gray-500 mt-1" id="mCatatan"></p>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Item Pesanan</p>
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Produk</th>
                                <th class="px-3 py-2 text-center">Qty</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="mItemsBody"></tbody>
                        <tfoot class="bg-gray-50 text-sm">
                            <tr class="border-t border-gray-100">
                                <td colspan="2" class="px-3 py-2 text-right text-gray-500">Subtotal</td>
                                <td class="px-3 py-2 text-right font-medium text-gray-700" id="mSubtotal"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-3 py-2 text-right text-gray-500">
                                    Ongkir <span class="text-xs text-gray-400" id="mJarak"></span>
                                </td>
                                <td class="px-3 py-2 text-right font-medium text-gray-700" id="mOngkir"></td>
                            </tr>
                            <tr class="border-t-2 border-gray-100 bg-emerald-50">
                                <td colspan="2" class="px-3 py-2 text-right font-bold text-gray-700">Total</td>
                                <td class="px-3 py-2 text-right font-bold text-emerald-600" id="mTotal"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Metode</p>
                        <p class="font-semibold text-gray-800" id="mPaymentMethod"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-0.5">Status</p>
                        <p class="font-semibold" id="mPaymentStatus"></p>
                    </div>
                </div>
                <div id="mBuktiWrapper" class="mt-2 hidden">
                    <p class="text-xs text-gray-500 mb-1">Bukti Transfer</p>
                    <img id="mBukti" src="" alt="Bukti" class="w-full rounded-xl border border-gray-200">
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
                        <p class="text-xs text-red-600 mb-0.5">Nominal</p>
                        <p class="font-bold text-red-800" id="mRefundNominal"></p>
                    </div>
                    <div>
                        <p class="text-xs text-red-600 mb-0.5">Status</p>
                        <p class="font-bold text-red-800" id="mRefundStatus"></p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-red-600 mb-0.5">Alasan</p>
                        <p class="text-red-800" id="mRefundAlasan"></p>
                    </div>
                </div>
            </div>

            <div id="mFormStatusWrapper" class="bg-gray-50 rounded-xl p-3">
                <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Update Status</p>
                <form id="mFormStatus" method="POST" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="order_status" required
                            class="flex-1 px-3 py-2 rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        <option value="pending">Pending</option>
                        <option value="diproses">Diproses</option>
                        <option value="dikirim">Dikirim</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                    <button type="submit"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition"
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
<script>
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
                <tr class="border-b border-gray-50">
                    <td class="px-3 py-2 text-gray-800">
                        ${item.nama}
                        <p class="text-xs text-gray-500">${formatRupiah(item.harga)} × ${item.qty}</p>
                    </td>
                    <td class="px-3 py-2 text-center text-gray-600">${item.qty}</td>
                    <td class="px-3 py-2 text-right font-medium text-gray-800">${formatRupiah(item.subtotal)}</td>
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
            formStatus.action = data.route_update_status;
            formStatus.querySelector('select[name="order_status"]').value = data.order_status;
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
</script>
@endpush