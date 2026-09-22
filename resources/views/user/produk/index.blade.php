@extends('layouts.user')
@section('title', 'Katalog Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Produk Tahu</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Katalog Produk Tahu</h1>
        <p class="text-gray-500">Temukan berbagai varian tahu segar berkualitas tinggi untuk keluarga Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- SIDEBAR FILTER --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4">Filter</h3>

                <form method="GET">
                    {{-- Search --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Cari Produk</label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tahu..."
                                   class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Kategori</label>
                        <div class="space-y-1">
                            <a href="{{ route('user.produk.index', array_merge(request()->except('kategori', 'page'), [])) }}"
                               class="block px-3 py-2 rounded-lg text-sm {{ !request('kategori') ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                Semua Kategori
                            </a>
                            @foreach ($kategori as $k)
                                <a href="{{ route('user.produk.index', array_merge(request()->except('page'), ['kategori' => $k->id])) }}"
                                   class="block px-3 py-2 rounded-lg text-sm {{ request('kategori') == $k->id ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    {{ $k->nama_kategori }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Urutkan</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Terbaru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </aside>

        {{-- PRODUCTS GRID --}}
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-gray-500">Menampilkan <span class="font-semibold text-gray-800">{{ $produk->total() }}</span> produk</p>
            </div>

            @if ($produk->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($produk as $p)
                        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                            <a href="{{ route('user.produk.show', $p->slug) }}" class="block">
                                <div class="aspect-square bg-gray-50 overflow-hidden">
                                    @if ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-6xl">🥛</div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                    {{ $p->kategori->nama_kategori ?? '-' }}
                                </span>
                                <h3 class="font-semibold text-gray-800 mt-2 mb-1 line-clamp-2 min-h-[44px]">
                                    {{ $p->nama_produk }}
                                </h3>
                                <p class="text-lg font-bold text-emerald-600 mb-3">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
                                <button type="button" onclick="addToCart('produk', {{ $p->id }})"
                                        class="w-full flex items-center justify-center gap-2 py-2 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 rounded-xl text-sm font-semibold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">{{ $produk->links() }}</div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="font-bold text-gray-800 mb-2">Produk tidak ditemukan</h3>
                    <p class="text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian.</p>
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
            } else showToast(d.message || 'Gagal', 'error');
        })
        .catch(() => showToast('Error', 'error'));
    }

    function showToast(msg, type = 'success') {
        const bg = type === 'success' ? 'bg-emerald-600' : 'bg-red-500';
        const t = document.createElement('div');
        t.className = `fixed top-20 right-4 z-50 ${bg} text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium`;
        t.style.transform = 'translateX(400px)';
        t.style.transition = 'all 0.3s';
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