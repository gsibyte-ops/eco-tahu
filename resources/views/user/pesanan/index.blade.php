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
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
    @endif

    {{-- Filter Chips --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @php
            $chips = [
                '' => ['label' => 'Semua', 'count' => $stats['all'], 'dotClass' => 'bg-gray-500', 'textClass' => 'text-gray-700'],
                'pending' => ['label' => 'Pending', 'count' => $stats['pending'], 'dotClass' => 'bg-amber-500', 'textClass' => 'text-amber-700'],
                'diproses' => ['label' => 'Diproses', 'count' => $stats['diproses'], 'dotClass' => 'bg-blue-500', 'textClass' => 'text-blue-700'],
                'dikirim' => ['label' => 'Dikirim', 'count' => $stats['dikirim'], 'dotClass' => 'bg-indigo-500', 'textClass' => 'text-indigo-700'],
                'selesai' => ['label' => 'Selesai', 'count' => $stats['selesai'], 'dotClass' => 'bg-emerald-500', 'textClass' => 'text-emerald-700'],
                'dibatalkan' => ['label' => 'Dibatalkan', 'count' => $stats['dibatalkan'], 'dotClass' => 'bg-red-500', 'textClass' => 'text-red-700'],
            ];
        @endphp

        @foreach ($chips as $key => $c)
            @php $isActive = request('status', '') == $key; @endphp
            <a href="{{ route('user.pesanan.index', $key ? ['status' => $key] : []) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-xl border transition text-sm bg-white
                      {{ $isActive ? 'border-gray-300 shadow-sm' : 'border-gray-100 hover:border-gray-200' }}">
                <span class="w-2 h-2 rounded-full {{ $c['dotClass'] }}"></span>
                <span class="font-medium text-gray-700">{{ $c['label'] }}</span>
                <span class="font-bold {{ $c['textClass'] }}">{{ $c['count'] }}</span>
            </a>
        @endforeach
    </div>

    @if ($pesanan->count() > 0)
        <div class="space-y-4">
            @foreach ($pesanan as $p)
                @php
                    $statusClass = [
                        'pending'    => 'bg-amber-100 text-amber-700',
                        'diproses'   => 'bg-blue-100 text-blue-700',
                        'dikirim'    => 'bg-indigo-100 text-indigo-700',
                        'selesai'    => 'bg-emerald-100 text-emerald-700',
                        'dibatalkan' => 'bg-red-100 text-red-700',
                    ][$p->order_status] ?? 'bg-gray-100 text-gray-700';

                    $bolehCancel = in_array($p->order_status, ['pending', 'diproses'])
                                && !($p->payment_method === 'Transfer' && $p->payment_status === 'pending');

                    $menungguVerifikasi = $p->payment_method === 'Transfer'
                                        && $p->payment_status === 'pending'
                                        && !in_array($p->order_status, ['dibatalkan', 'selesai']);

                    $isExpired = $p->expired_at && $p->expired_at->isPast()
                              && $p->payment_status === 'pending'
                              && $p->order_status === 'pending';

                    $cancelData = [
                        'kode' => $p->kode_pesanan,
                        'total' => (int) $p->total_harga,
                        'payment_method' => $p->payment_method,
                        'payment_status' => $p->payment_status,
                        'route' => route('user.pesanan.cancel', $p->kode_pesanan),
                    ];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition">
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
                        <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $statusClass }}">{{ ucfirst($p->order_status) }}</span>
                    </div>

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
                                <span class="px-3 py-1.5 bg-gray-50 rounded-lg text-sm text-gray-500">+{{ $p->detail->count() - 3 }} item lainnya</span>
                            @endif
                        </div>

                        @if ($p->order_status === 'dibatalkan')
                            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3 text-sm">
                                <p class="font-bold text-red-800 mb-1">Pesanan Dibatalkan</p>
                                <p class="text-red-700">Alasan: {{ $p->alasan_batal ?? $p->refund->alasan_batal ?? 'Tidak ada alasan yang dicatat.' }}</p>
                            </div>
                        @endif

                        @if ($isExpired)
                            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-3 text-sm flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="text-red-700 font-medium">Waktu pembayaran sudah habis. Pesanan akan dibatalkan otomatis.</span>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-end justify-between gap-3 pt-4 border-t border-gray-100">
                            <div class="text-sm">
                                <p class="text-gray-500">
                                    {{ $p->payment_method }}
                                    @if($p->bank_tujuan) · {{ $p->bank_tujuan }} @endif
                                    · {{ $p->jarak_km > 0 ? $p->jarak_km . ' km' : 'Ambil di Tempat' }}
                                </p>
                                <p class="font-bold text-lg text-gray-800 mt-1">Total: <span class="text-emerald-600">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span></p>
                            </div>
                            <div class="flex gap-2">
                                @if ($bolehCancel)
                                    <button type="button"
                                            data-cancel="{{ json_encode($cancelData, JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                            onclick="openCancelModal(JSON.parse(this.dataset.cancel))"
                                            class="px-4 py-2 bg-white border border-red-200 hover:bg-red-50 text-red-600 text-sm font-semibold rounded-xl transition">
                                        Batalkan
                                    </button>
                                @elseif ($menungguVerifikasi)
                                    <button type="button"
                                            disabled
                                            title="Harap tunggu admin memverifikasi pembayaran Anda terlebih dahulu."
                                            class="px-4 py-2 bg-gray-100 border border-gray-200 text-gray-400 text-sm font-semibold rounded-xl cursor-not-allowed">
                                        Batalkan
                                    </button>
                                @endif
                                <a href="{{ route('user.pesanan.show', $p->kode_pesanan) }}"
                                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>

                        @if ($menungguVerifikasi)
                            <div class="mt-3 flex items-start gap-2 text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-lg p-2.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Pembayaran Anda sedang menunggu verifikasi admin. Harap tunggu sebelum membatalkan pesanan.</span>
                            </div>
                        @endif
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
            <a href="{{ route('user.produk.index') }}" class="inline-block px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-200 transition">Mulai Belanja</a>
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
                ⚠️ <strong>Setelah dibatalkan, pesanan tidak dapat dikembalikan ke status semula.</strong> Pastikan Anda yakin sebelum melanjutkan.
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
    console.log('✅ User Pesanan INDEX Script Loaded');

    function onFileSelected(input) {
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
        const input = document.getElementById('cancelBuktiInput');
        if (!input.files || !input.files[0]) return;

        document.getElementById('flagInput').value = '1';
        document.getElementById('previewBox').classList.add('hidden');
        document.getElementById('fileLabel').textContent = 'No file chosen';
        document.getElementById('fileLabel').classList.add('text-gray-400');
        document.getElementById('fileLabel').classList.remove('text-gray-800', 'font-medium');

        document.getElementById('undoFileName').textContent = input.files[0].name;
        document.getElementById('undoBanner').classList.remove('hidden');
    }

    function undoFile() {
        const input = document.getElementById('cancelBuktiInput');
        if (!input.files || !input.files[0]) {
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