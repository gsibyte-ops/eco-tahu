@extends('layouts.user')
@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Pesanan Saya</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Saya</h1>
        <p class="text-gray-500">Pantau status pesanan Anda di sini.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter Chips --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @php
            $chips = [
                '' => ['label' => 'Semua', 'count' => $stats['all'], 'color' => 'gray'],
                'pending' => ['label' => 'Pending', 'count' => $stats['pending'], 'color' => 'amber'],
                'diproses' => ['label' => 'Diproses', 'count' => $stats['diproses'], 'color' => 'sky'],
                'dikirim' => ['label' => 'Dikirim', 'count' => $stats['dikirim'], 'color' => 'violet'],
                'selesai' => ['label' => 'Selesai', 'count' => $stats['selesai'], 'color' => 'emerald'],
                'dibatalkan' => ['label' => 'Dibatalkan', 'count' => $stats['dibatalkan'], 'color' => 'rose'],
            ];
            $colorMap = [
                'gray' => ['#f3f4f6', '#374151'],
                'amber' => ['#fef3c7', '#b45309'],
                'sky' => ['#dbeafe', '#1d4ed8'],
                'violet' => ['#ede9fe', '#6d28d9'],
                'emerald' => ['#d1fae5', '#047857'],
                'rose' => ['#fee2e2', '#b91c1c'],
            ];
        @endphp

        @foreach ($chips as $key => $c)
            @php
                $isActive = request('status', '') == $key;
                [$bg, $text] = $colorMap[$c['color']];
            @endphp
            <a href="{{ route('user.pesanan.index', $key ? ['status' => $key] : []) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl border transition text-sm
                      {{ $isActive ? 'border-gray-300 shadow-sm' : 'border-gray-100 hover:border-gray-200' }} bg-white">
                <span class="w-2 h-2 rounded-full" style="background: {{ $text }};"></span>
                <span class="font-medium text-gray-700">{{ $c['label'] }}</span>
                <span class="font-bold" style="color: {{ $text }};">{{ $c['count'] }}</span>
            </a>
        @endforeach
    </div>

    @if ($pesanan->count() > 0)
        <div class="space-y-4">
            @foreach ($pesanan as $p)
                @php
                    $statusColor = [
                        'pending' => ['#fef3c7', '#b45309'],
                        'diproses' => ['#dbeafe', '#1d4ed8'],
                        'dikirim' => ['#ede9fe', '#6d28d9'],
                        'selesai' => ['#d1fae5', '#047857'],
                        'dibatalkan' => ['#fee2e2', '#b91c1c'],
                    ][$p->order_status] ?? ['#f3f4f6', '#374151'];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
                    {{-- Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 bg-gray-50/60 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Kode Pesanan</p>
                                <p class="font-bold text-gray-800">#{{ $p->kode_pesanan }}</p>
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-xs text-gray-500">Tanggal</p>
                                <p class="font-medium text-gray-700 text-sm">{{ $p->tanggal_order->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-lg text-xs font-bold" style="background: {{ $statusColor[0] }}; color: {{ $statusColor[1] }};">
                            {{ ucfirst($p->order_status) }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-5">
                        <div class="flex flex-wrap gap-3 mb-4">
                            @foreach ($p->detail->take(3) as $d)
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg text-sm">
                                    <span class="text-xs">{{ $d->item_type === 'App\\Models\\ProdukTahu' ? '🥛' : '🌾' }}</span>
                                    <span class="text-gray-700">{{ $d->nama_item }}</span>
                                    <span class="text-gray-400">×{{ $d->jumlah }}</span>
                                </div>
                            @endforeach
                            @if ($p->detail->count() > 3)
                                <span class="px-3 py-1.5 bg-gray-50 rounded-lg text-sm text-gray-500">
                                    +{{ $p->detail->count() - 3 }} item lainnya
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-end justify-between gap-3 pt-4 border-t border-gray-100">
                            <div class="text-sm">
                                <p class="text-gray-500">{{ $p->payment_method }} · {{ $p->jarak_km }} km</p>
                                <p class="font-bold text-lg text-gray-800 mt-1">
                                    Total: <span class="text-emerald-600">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                                </p>
                            </div>
                            <div class="flex gap-2">
                                @if (in_array($p->order_status, ['pending', 'diproses']))
                                    <button type="button"
                                            onclick='openCancelModal(@json([
                                                "kode" => $p->kode_pesanan,
                                                "total" => $p->total_harga,
                                                "payment_method" => $p->payment_method,
                                                "payment_status" => $p->payment_status,
                                                "route" => route("user.pesanan.cancel", $p->kode_pesanan),
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT))'
                                            class="px-4 py-2 bg-white border border-red-200 hover:bg-red-50 text-red-600 text-sm font-semibold rounded-xl transition">
                                        Batalkan
                                    </button>
                                @endif
                                <a href="{{ route('user.pesanan.show', $p->kode_pesanan) }}"
                                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $pesanan->links() }}</div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center max-w-md mx-auto">
            <div class="text-7xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Pesanan</h3>
            <p class="text-sm text-gray-500 mb-6">Yuk mulai belanja tahu & ampas tahu berkualitas!</p>
            <a href="{{ route('user.produk.index') }}"
               class="inline-block px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>

{{-- MODAL CANCEL --}}
<div id="cancelModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">
    <div style="width: 100%; max-width: 460px;" class="bg-white rounded-2xl overflow-hidden shadow-2xl">

        {{-- Header --}}
        <div class="px-5 py-4 bg-red-500 text-white">
            <h3 class="text-lg font-bold">Batalkan Pesanan?</h3>
            <p class="text-xs opacity-90 mt-0.5">Pesanan #<span id="cKode"></span></p>
        </div>

        {{-- Body --}}
        <form id="cancelForm" method="POST" class="p-5 space-y-4">
            @csrf
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800">
                ⚠️ Pesanan yang sudah dibatalkan tidak dapat dikembalikan.
            </div>

            <div id="refundInfo" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-800">
                💰 Dana sebesar <strong>Rp <span id="cTotal"></span></strong> akan dikembalikan ke rekening Anda dalam 1x24 jam setelah diverifikasi admin.
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Pembatalan <span class="text-red-500">*</span></label>
                <textarea name="alasan_batal" required rows="3" minlength="10" maxlength="500"
                          class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm"
                          placeholder="Contoh: Salah pesan produk, ingin ganti, dll (min 10 karakter)">{{ old('alasan_batal') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-sm transition">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCancelModal(data) {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        document.getElementById('cKode').textContent = data.kode;
        document.getElementById('cTotal').textContent = Number(data.total).toLocaleString('id-ID');
        document.getElementById('cancelForm').action = data.route;

        // Tampilkan info refund kalau Transfer + paid
        if (data.payment_method === 'Transfer' && data.payment_status === 'paid') {
            document.getElementById('refundInfo').classList.remove('hidden');
        } else {
            document.getElementById('refundInfo').classList.add('hidden');
        }
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
        if (e.key === 'Escape') closeCancelModal();
    });
</script>
@endpush