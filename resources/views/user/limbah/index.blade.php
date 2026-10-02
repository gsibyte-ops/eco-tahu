@extends('layouts.user')
@section('title', 'Limbah Ampas Tahu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition">Beranda</a>
        <span class="mx-2 text-amber-400">/</span>
        <span class="text-gray-800 font-medium">Limbah Ampas Tahu</span>
    </nav>

    {{-- Zero Waste Banner --}}
    <div class="glass-amber rounded-2xl p-5 mb-8 flex items-start gap-4">
        <div class="text-3xl">♻️</div>
        <div>
            <h2 class="font-bold text-amber-800 mb-1">Zero Waste Movement</h2>
            <p class="text-sm text-amber-700">Membeli ampas tahu = ikut mengurangi limbah industri & mendukung ekonomi sirkular. Cocok untuk pakan ternak, pupuk, dan olahan pangan.</p>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-4xl font-display text-gray-800">Katalog Ampas Tahu</h1>
        <p class="text-gray-500 mt-2">Produk limbah bernilai tinggi, ramah lingkungan, dan ekonomis.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- SIDEBAR FILTER --}}
        <aside class="lg:col-span-1">
            <div class="glass-card p-5 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4">Filter</h3>
                <form method="GET">
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wide">Cari</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari limbah..."
                               class="glass-input w-full px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wide">Kategori</label>
                        <div class="space-y-1">
                            <a href="{{ route('user.limbah.index', request()->except('kategori', 'page')) }}"
                               class="block px-3 py-2 rounded-lg text-sm transition {{ !request('kategori') ? 'bg-amber-500/20 text-amber-700 font-semibold border border-amber-300/50' : 'text-gray-600 hover:bg-white/50' }}">
                                Semua
                            </a>
                            @foreach ($kategori as $k)
                                <a href="{{ route('user.limbah.index', array_merge(request()->except('page'), ['kategori' => $k->id])) }}"
                                   class="block px-3 py-2 rounded-lg text-sm transition {{ request('kategori') == $k->id ? 'bg-amber-500/20 text-amber-700 font-semibold border border-amber-300/50' : 'text-gray-600 hover:bg-white/50' }}">
                                    {{ $k->nama_kategori }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wide">Urutkan</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="glass-input w-full px-3 py-2.5 text-sm text-gray-700">
                            <option value="">Terbaru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                        </select>
                    </div>

                    <button type="submit" class="glass-btn-amber w-full py-3 text-sm">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </aside>

        {{-- GRID LIMBAH --}}
        <div class="lg:col-span-3">
            <div class="glass-card inline-flex items-center px-4 py-2 mb-5">
                <p class="text-sm text-gray-500">Menampilkan <span class="font-semibold text-gray-800 tabular-nums">{{ $limbah->total() }}</span> produk</p>
            </div>

            @if ($limbah->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($limbah as $l)
                        <div class="glass-amber rounded-2xl overflow-hidden transition hover:-translate-y-1.5 hover:shadow-xl hover:shadow-amber-500/20">
                            <a href="{{ route('user.limbah.show', $l->slug) }}" class="block">
                                <div class="aspect-square bg-amber-50/50 overflow-hidden">
                                    @if ($l->gambar)
                                        <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-6xl">🌾</div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <span class="inline-block text-xs font-medium text-amber-700 bg-amber-500/15 px-2 py-0.5 rounded border border-amber-300/40">
                                    {{ $l->kategori->nama_kategori ?? 'Limbah' }}
                                </span>
                                <h3 class="font-semibold text-gray-800 mt-2 mb-1 line-clamp-2 min-h-[44px]">{{ $l->nama_limbah }}</h3>
                                <p class="text-lg price text-amber-600 mb-3">
                                    Rp {{ number_format($l->harga, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-500">/ {{ $l->satuan }}</span>
                                </p>
                                <button type="button" onclick="addToCart('limbah', {{ $l->id }})"
                                        class="glass-btn-amber w-full py-2.5 text-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $limbah->links() }}</div>
            @else
                <div class="glass-card p-12 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="font-bold text-gray-800 mb-2">Belum ada limbah</h3>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function addToCart(type, id) {
        fetch('{{ route('user.cart.add') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ type, id, qty: 1 })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast(d.message || 'Ditambahkan!');
                setTimeout(() => location.reload(), 600);
            } else {
                showToast(d.message || 'Gagal', 'error');
            }
        })
        .catch(() => showToast('Error', 'error'));
    }

    function showToast(msg, type = 'success') {
        const bg = type === 'success'
            ? 'linear-gradient(135deg, rgba(245,158,11,0.95), rgba(217,119,6,0.95))'
            : 'linear-gradient(135deg, rgba(239,68,68,0.95), rgba(220,38,38,0.95))';
        const t = document.createElement('div');
        t.className = 'fixed top-20 right-4 z-50 text-white px-5 py-3 rounded-xl shadow-2xl text-sm font-medium';
        t.style.background = bg;
        t.style.backdropFilter = 'blur(12px)';
        t.style.border = '1px solid rgba(255,255,255,0.25)';
        t.style.transform = 'translateX(400px)';
        t.style.transition = 'all 0.3s cubic-bezier(0.4,0,0.2,1)';
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.style.transform = 'translateX(0)', 50);
        setTimeout(() => {
            t.style.transform = 'translateX(400px)';
            setTimeout(() => t.remove(), 300);
        }, 2500);
    }
</script>
@endpush