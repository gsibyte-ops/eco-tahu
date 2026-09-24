@extends('layouts.user')
@section('title', 'Katalog Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <nav class="mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-emerald-600">Beranda</a>
        <span class="mx-2">/</span>
        <span class="font-medium text-gray-700">Produk Tahu</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-bold tracking-tight text-gray-800">Katalog Produk Tahu</h1>
        <p class="mt-2 text-gray-500">Temukan berbagai varian tahu segar berkualitas tinggi untuk keluarga Anda.</p>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-[270px_minmax(0,1fr)]">
        <aside class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200/80">
            <div class="mb-5 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800">Filter</h3>
            </div>

            <form method="GET" class="space-y-5">
                <div>
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-gray-500">Cari Produk</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tahu..."
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-9 pr-3 text-sm text-gray-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-gray-500">Kategori</label>
                    <div class="space-y-1">
                        <a href="{{ route('user.produk.index', array_merge(request()->except('kategori', 'page'), [])) }}"
                           class="block rounded-lg px-3 py-2 text-sm {{ !request('kategori') ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            Semua Kategori
                        </a>
                        @foreach ($kategori as $k)
                            <a href="{{ route('user.produk.index', array_merge(request()->except('page'), ['kategori' => $k->id])) }}"
                               class="block rounded-lg px-3 py-2 text-sm {{ request('kategori') == $k->id ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                {{ $k->nama_kategori }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-[11px] font-bold uppercase tracking-wide text-gray-500">Urutkan</label>
                    <select name="sort" onchange="this.form.submit()" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                        <option value="">Terbaru</option>
                        <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                        <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
                    </select>
                </div>

                <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                    Terapkan Filter
                </button>
            </form>
        </aside>

        <div class="space-y-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Menampilkan <span class="font-semibold text-gray-700">{{ $produk->total() }}</span> produk</p>
            </div>

            @if ($produk->count() > 0)
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($produk as $p)
                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <a href="{{ route('user.produk.show', $p->slug) }}" class="block">
                                <div class="flex aspect-square items-center justify-center overflow-hidden rounded-xl bg-gray-50">
                                    @if ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="text-5xl">🥛</div>
                                    @endif
                                </div>
                            </a>

                            <div class="mt-3 space-y-2">
                                <div class="text-center">
                                    <p class="text-base font-semibold text-gray-800">{{ $p->nama_produk }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ $p->kategori->nama_kategori ?? '-' }}</p>
                                </div>

                                <p class="text-center text-xl font-bold text-emerald-600">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>

                                <button type="button" onclick="addToCart('produk', {{ $p->id }})"
                                        class="w-full rounded-xl bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-600 hover:text-white">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">{{ $produk->links() }}</div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                    <div class="mb-4 text-5xl">🔍</div>
                    <h3 class="text-xl font-bold text-gray-800">Produk tidak ditemukan</h3>
                    <p class="mt-2 text-sm text-gray-500">Coba ubah filter atau kata kunci pencarian.</p>
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