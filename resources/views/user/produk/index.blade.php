@extends('layouts.user')
@section('title', 'Katalog Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-xs sm:text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">Produk Tahu</span>
    </nav>

    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Katalog Produk Tahu</h1>
        <p class="mt-1 sm:mt-2 text-xs sm:text-base" style="color: rgb(var(--text-secondary));">Temukan berbagai varian tahu segar berkualitas tinggi untuk keluarga Anda.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[270px_minmax(0,1fr)]">

        <aside class="glass-card p-4 sm:p-5 h-fit xl:sticky xl:top-24">
            <div class="mb-3 sm:mb-4">
                <h3 class="text-base sm:text-lg font-bold" style="color: rgb(var(--text-primary));">Filter</h3>
            </div>
            <form method="GET" class="space-y-3 sm:space-y-4">
                <div>
                    <label class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-wide" style="color: rgb(var(--text-muted));">Cari Produk</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tahu..."
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                           pattern="[a-zA-Z\s]+" title="Hanya huruf dan spasi"
                           class="glass-input w-full px-3 py-1.5 sm:py-2 text-xs sm:text-sm" style="color: rgb(var(--text-primary));">
                </div>
                <div x-data="{ open: false, selected: '{{ request('sort') }}', options: [{ id: '', name: 'Terbaru' }, { id: 'termurah', name: 'Harga Termurah' }, { id: 'termahal', name: 'Harga Termahal' }] }" @click.away="open = false" class="relative">
                    <label class="mb-1.5 block text-[10px] sm:text-[11px] font-bold uppercase tracking-wide" style="color: rgb(var(--text-muted));">Urutkan</label>
                    <button type="button" @click="open = !open" class="glass-input w-full px-3 py-1.5 sm:py-2 text-xs sm:text-sm flex justify-between items-center text-left">
                        <span x-text="options.find(o => o.id == selected)?.name || 'Terbaru'" :class="selected ? 'font-semibold' : ''" style="color: rgb(var(--text-primary));"></span>
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 opacity-50 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <input type="hidden" name="sort" x-model="selected">
                    <div x-show="open" x-cloak x-transition.origin.top class="absolute z-20 w-full mt-1 rounded-xl shadow-lg overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft));">
                        <template x-for="option in options" :key="option.id">
                            <button type="button" @click="selected = option.id; open = false" class="w-full text-left px-4 py-2 text-xs sm:text-sm transition hover:bg-emerald-500/10" :class="selected == option.id ? 'bg-emerald-500/10 font-bold' : ''" :style="selected == option.id ? 'color: rgb(var(--brand-hover));' : 'color: rgb(var(--text-secondary));'">
                                <span x-text="option.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <button type="submit" class="glass-btn-primary w-full py-2 text-xs sm:text-sm mt-2">Terapkan Filter</button>
            </form>
        </aside>

        <div class="space-y-4 sm:space-y-5">
            <div class="glass-card inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2">
                <p class="text-[10px] sm:text-sm" style="color: rgb(var(--text-secondary));">Menampilkan <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">{{ $produk->total() }}</span> produk</p>
            </div>

            @if ($produk->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
                    @foreach ($produk as $p)
                        <div class="surface flex flex-col rounded-xl sm:rounded-2xl overflow-hidden">
                            <a href="{{ route('user.produk.show', $p->slug) }}" class="block relative overflow-hidden aspect-square">
                                <div class="w-full h-full flex items-center justify-center" style="background: rgb(var(--bg-secondary));">
                                    @if ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="text-4xl sm:text-5xl">🥛</div>
                                    @endif
                                </div>
                            </a>

                            <div class="p-2.5 sm:p-4 flex flex-col flex-1">
                                <span class="pill text-[8px] sm:text-[10px] self-start mb-1.5">{{ $p->kategori->nama_kategori ?? '-' }}</span>
                                <p class="font-bold text-[15px] sm:text-base leading-snug line-clamp-2 min-h-[42px] sm:min-h-[48px] mb-1.5 text-left" style="color: rgb(var(--text-primary));">{{ $p->nama_produk }}</p>

                                @php $avg = $p->averageRating(); $cnt = $p->reviewCount(); @endphp
                                <div class="flex flex-wrap items-center gap-0.5 sm:gap-1 text-[10px] sm:text-xs mb-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 {{ $i <= round($avg) ? 'text-amber-400 fill-current' : 'fill-current' }}" style="{{ $i > round($avg) ? 'color: rgb(var(--text-faint));' : '' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endfor
                                    <span class="ml-0.5" style="color: rgb(var(--text-muted));">
                                        @if ($cnt > 0) {{ number_format($avg, 1) }} ({{ $cnt }}) @else Baru @endif
                                    </span>
                                </div>

                                <p class="text-left text-base sm:text-lg price mb-2 mt-auto">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>

                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <button type="button" onclick="window.showAdminNotif()" class="btn-primary w-full !text-[10px] sm:!text-xs flex items-center justify-center gap-1 opacity-80 py-1.5 sm:py-2">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Tambah
                                    </button>
                                @elseif(auth()->check())
                                    <button type="button" onclick="window.addToCart('produk', {{ $p->id }})" class="btn-primary w-full !text-[10px] sm:!text-xs flex items-center justify-center gap-1 py-1.5 sm:py-2">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Tambah
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="btn-primary w-full !text-[10px] sm:!text-xs flex items-center justify-center gap-1 py-1.5 sm:py-2">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Tambah
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">{{ $produk->links() }}</div>
            @else
                <div class="glass-card p-10 sm:p-12 text-center">
                    <div class="mb-3 text-4xl sm:text-5xl">🔍</div>
                    <h3 class="text-lg sm:text-xl font-bold" style="color: rgb(var(--text-primary));">Produk tidak ditemukan</h3>
                    <p class="mt-1 text-xs sm:text-sm" style="color: rgb(var(--text-secondary));">Coba ubah filter atau kata kunci pencarian.</p>
                </div>
            @endif
        </div>
    </div>
    
    {{-- FIX: Tombol Lihat Semua Produk yang bocor --}}
    <div class="mt-8 text-center sm:hidden">
        <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.showAdminNotif = function() {
        window.showToast('Admin tidak dapat melakukan pemesanan.', 'error');
    }

    window.addToCart = function(type, id) {
        fetch('{{ route('user.cart.add') }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                'Accept': 'application/json' 
            },
            body: JSON.stringify({ type: type, id: id, qty: 1 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.showToast(data.message || 'Ditambahkan ke keranjang!');
                if (data.cart_count !== undefined && typeof window.setCartBadge === 'function') {
                    window.setCartBadge(data.cart_count);
                }
            } else {
                window.showToast(data.message || 'Gagal menambahkan', 'error');
            }
        })
        .catch(err => { 
            console.error(err); 
            window.showToast('Terjadi kesalahan', 'error'); 
        });
    }

    window.showToast = function(message, type = 'success') {
        const isSuccess = type === 'success';
        const toast = document.createElement('div');
        toast.className = 'fixed top-24 right-4 z-[999] text-white px-5 py-3 rounded-xl shadow-2xl text-xs sm:text-sm font-semibold flex items-center gap-2';
        toast.style.background = isSuccess ? 'linear-gradient(135deg, #10b981 0%, #047857 100%)' : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        toast.style.transform = 'translateX(400px)'; 
        toast.style.transition = 'transform 0.35s cubic-bezier(0.4, 0, 0.2, 1)'; 
        toast.style.boxShadow = isSuccess ? '0 12px 32px rgb(16 185 129 / 0.4)' : '0 12px 32px rgb(239 68 68 / 0.4)';
        toast.innerHTML = (isSuccess 
            ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' 
            : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>'
        ) + '<span>' + message + '</span>';
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 50);
        setTimeout(() => { 
            toast.style.transform = 'translateX(400px)'; 
            setTimeout(() => toast.remove(), 350); 
        }, 2500);
    }
</script>
@endpush