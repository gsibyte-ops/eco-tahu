@extends('layouts.user')
@section('title', 'Limbah Ampas Tahu')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">Limbah Ampas Tahu</span>
    </nav>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-8 flex items-start gap-4">
        <div class="text-3xl">♻️</div>
        <div>
            <h2 class="font-bold text-amber-800 mb-1">Zero Waste Movement</h2>
            <p class="text-sm text-amber-700">Membeli ampas tahu = ikut mengurangi limbah industri & mendukung ekonomi sirkular. Cocok untuk pakan ternak, pupuk, dan olahan pangan.</p>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Katalog Ampas Tahu</h1>
        <p class="text-gray-500">Produk limbah bernilai tinggi, ramah lingkungan, dan ekonomis.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Sidebar --}}
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4">Filter</h3>
                <form method="GET">
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Cari</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari limbah..."
                               class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Kategori</label>
                        <div class="space-y-1">
                            <a href="{{ route('user.limbah.index', request()->except('kategori', 'page')) }}"
                               class="block px-3 py-2 rounded-lg text-sm {{ !request('kategori') ? 'bg-amber-50 text-amber-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                Semua
                            </a>
                            @foreach ($kategori as $k)
                                <a href="{{ route('user.limbah.index', array_merge(request()->except('page'), ['kategori' => $k->id])) }}"
                                   class="block px-3 py-2 rounded-lg text-sm {{ request('kategori') == $k->id ? 'bg-amber-50 text-amber-700 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
                                    {{ $k->nama_kategori }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Urutkan</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Terbaru</option>
                            <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                            <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </aside>

        {{-- Grid --}}
        <div class="lg:col-span-3">
            <p class="text-sm text-gray-500 mb-5">Menampilkan <span class="font-semibold text-gray-800">{{ $limbah->total() }}</span> produk</p>

            @if ($limbah->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($limbah as $l)
                        <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition">
                            <a href="{{ route('user.limbah.show', $l->slug) }}" class="block">
                                <div class="aspect-square bg-amber-50 overflow-hidden">
                                    @if ($l->gambar)
                                        <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-6xl">🌾</div>
                                    @endif
                                </div>
                            </a>
                            <div class="p-4">
                                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded">
                                    {{ $l->kategori->nama_kategori ?? 'Limbah' }}
                                </span>
                                <h3 class="font-semibold text-gray-800 mt-2 mb-1 line-clamp-2 min-h-[44px]">{{ $l->nama_limbah }}</h3>
                                <p class="text-lg font-bold text-amber-600 mb-3">
                                    Rp {{ number_format($l->harga, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-500">/ {{ $l->satuan }}</span>
                                </p>
                                <button type="button" onclick="addToCart('limbah', {{ $l->id }})"
                                        class="w-full flex items-center justify-center gap-2 py-2 bg-amber-50 hover:bg-amber-500 hover:text-white text-amber-700 rounded-xl text-sm font-semibold transition">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-8">{{ $limbah->links() }}</div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
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
                alert(d.message || 'Ditambahkan!');
                location.reload();
            } else alert(d.message || 'Gagal');
        });
    }
</script>
@endpush