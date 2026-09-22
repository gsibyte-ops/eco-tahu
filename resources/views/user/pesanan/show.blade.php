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
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    @php
        $statusColor = [
            'pending' => ['#fef3c7', '#b45309'],
            'diproses' => ['#dbeafe', '#1d4ed8'],
            'dikirim' => ['#ede9fe', '#6d28d9'],
            'selesai' => ['#d1fae5', '#047857'],
            'dibatalkan' => ['#fee2e2', '#b91c1c'],
        ][$pesanan->order_status] ?? ['#f3f4f6', '#374151'];
    @endphp

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
            <div>
                <p class="text-xs text-gray-500 mb-1">Kode Pesanan</p>
                <p class="text-2xl font-bold text-gray-800">#{{ $pesanan->kode_pesanan }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $pesanan->tanggal_order->format('d M Y, H:i') }}</p>
            </div>
            <span class="px-3 py-1.5 rounded-lg text-sm font-bold" style="background: {{ $statusColor[0] }}; color: {{ $statusColor[1] }};">
                {{ ucfirst($pesanan->order_status) }}
            </span>
        </div>

        {{-- Kalau dibatalkan, tampilkan info refund --}}
        @if ($pesanan->order_status === 'dibatalkan' && $pesanan->refund)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-xs font-bold text-red-600 uppercase mb-2">💰 Info Refund</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-red-500">Nominal</p>
                        <p class="font-bold text-red-800">Rp {{ number_format($pesanan->refund->nominal_refund, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-red-500">Status Refund</p>
                        @php
                            $refundLabel = [
                                'pending' => 'Menunggu Diproses',
                                'diproses' => 'Sedang Diproses',
                                'selesai' => 'Sudah Ditransfer',
                                'ditolak' => 'Ditolak',
                            ][$pesanan->refund->status_refund] ?? '-';
                        @endphp
                        <p class="font-bold text-red-800">{{ $refundLabel }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-red-500">Alasan Pembatalan</p>
                        <p class="text-red-800">{{ $pesanan->refund->alasan_batal }}</p>
                    </div>
                    @if ($pesanan->refund->bukti_transfer_balik)
                        <div class="col-span-2">
                            <p class="text-xs text-red-500 mb-1">Bukti Transfer Balik</p>
                            <img src="{{ asset('storage/' . $pesanan->refund->bukti_transfer_balik) }}"
                                 class="w-full max-w-xs rounded-xl border border-red-200">
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Info Pengiriman --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-3">📦 Info Pengiriman</h3>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $pesanan->alamat_pengiriman }}</p>
        @if ($pesanan->catatan)
            <p class="text-xs text-gray-500 mt-2"><strong>Catatan:</strong> {{ $pesanan->catatan }}</p>
        @endif
    </div>

    {{-- Item --}}
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

    {{-- Pembayaran --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
        <h3 class="font-bold text-gray-800 mb-4">💳 Pembayaran</h3>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Metode</span>
                <span class="font-semibold text-gray-800">{{ $pesanan->payment_method }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Status Bayar</span>
                @php
                    $payStatus = [
                        'pending' => ['Menunggu', '#b45309'],
                        'paid' => ['Lunas', '#047857'],
                        'failed' => ['Gagal', '#b91c1c'],
                        'refunded' => ['Dana Dikembalikan', '#6b7280'],
                    ][$pesanan->payment_status] ?? ['-', '#6b7280'];
                @endphp
                <span class="font-semibold" style="color: {{ $payStatus[1] }};">{{ $payStatus[0] }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Ongkir ({{ $pesanan->jarak_km }} km)</span>
                <span class="font-semibold text-gray-800">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pt-3 border-t-2 border-gray-100">
                <span class="font-bold text-gray-800">Total</span>
                <span class="text-xl font-bold text-emerald-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Upload bukti transfer --}}
        @if ($pesanan->payment_method === 'Transfer' && $pesanan->payment_status !== 'paid' && $pesanan->order_status !== 'dibatalkan')
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Upload Bukti Transfer</p>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3 text-xs text-blue-800">
                    Transfer ke <strong>BCA 1234567890</strong> a/n EcoTahu Indonesia. Nominal: <strong>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong>
                </div>

                @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}"
                             class="w-full max-w-xs rounded-xl border border-gray-200">
                        <p class="text-xs text-emerald-600 mt-2 font-medium">✓ Bukti sudah diupload, menunggu verifikasi admin</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.pesanan.uploadBukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="bukti_transfer" accept="image/*" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm mb-3
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                  file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition">
                        Upload Bukti
                    </button>
                </form>
            </div>
        @endif

        @if ($pesanan->pembayaran && $pesanan->pembayaran->bukti_transfer && $pesanan->payment_status === 'paid')
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Bukti Transfer Anda</p>
                <img src="{{ asset('storage/' . $pesanan->pembayaran->bukti_transfer) }}"
                     class="w-full max-w-xs rounded-xl border border-gray-200">
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3">
        @if (in_array($pesanan->order_status, ['pending', 'diproses']))
            <button type="button"
                    onclick='openCancelModal(@json([
                        "kode" => $pesanan->kode_pesanan,
                        "total" => $pesanan->total_harga,
                        "payment_method" => $pesanan->payment_method,
                        "payment_status" => $pesanan->payment_status,
                        "route" => route("user.pesanan.cancel", $pesanan->kode_pesanan),
                    ], JSON_HEX_APOS | JSON_HEX_QUOT))'
                    class="px-5 py-2.5 bg-white border border-red-200 hover:bg-red-50 text-red-600 font-semibold rounded-xl transition">
                Batalkan Pesanan
            </button>
        @endif
        <a href="{{ route('user.pesanan.index') }}"
           class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition">
            Kembali ke Daftar
        </a>
    </div>
</div>

{{-- MODAL CANCEL (sama seperti index) --}}
<div id="cancelModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">
    <div style="width: 100%; max-width: 460px;" class="bg-white rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-5 py-4 bg-red-500 text-white">
            <h3 class="text-lg font-bold">Batalkan Pesanan?</h3>
            <p class="text-xs opacity-90 mt-0.5">Pesanan #<span id="cKode"></span></p>
        </div>
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
                          placeholder="Min 10 karakter">{{ old('alasan_batal') }}</textarea>
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