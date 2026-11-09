@extends('layouts.user')
@section('title', 'Keranjang Belanja')

@section('content')
<div id="cartData"
     data-route-update="{{ route('user.cart.update') }}"
     data-route-remove="{{ route('user.cart.remove') }}"
     data-csrf="{{ csrf_token() }}"
     class="hidden"></div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">Keranjang</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Keranjang Belanja</h1>
        <p class="mt-2" style="color: rgb(var(--text-secondary));">Cek kembali produk pilihanmu sebelum checkout.</p>
    </div>

    @if (session('success'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); border: 1px solid rgb(var(--success) / 0.3); background: rgb(var(--success-soft));">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">{{ session('error') }}</div>
    @endif

    @if (count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ showCheckoutModal: false }">

            {{-- ITEM LIST --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach ($cart as $key => $item)
                    {{-- TAMBAHAN: data-nama buat bikin format WA --}}
                    <div class="glass-card p-4 flex gap-4 cart-item" id="item-{{ $key }}"
                         data-harga="{{ $item['harga'] }}"
                         data-nama="{{ $item['nama'] }}"
                         data-stok-max="{{ $item['stok_max'] }}">
                        <div class="w-20 h-20 rounded-2xl flex-shrink-0 overflow-hidden" style="background: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border-soft));">
                            @if ($item['gambar'])
                                <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama'] }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl">{{ $item['type'] === 'produk' ? '🥛' : '🌾' }}</div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2">
                                <div class="min-w-0">
                                    @if ($item['type'] === 'produk')
                                        <span class="inline-block text-[10px] font-semibold uppercase tracking-wide px-2 py-1 rounded-full"
                                              style="color: rgb(var(--brand-strong)); background: rgb(var(--brand-soft)); border: 1px solid rgb(var(--brand) / 0.3);">
                                            Produk Tahu
                                        </span>
                                    @else
                                        <span class="inline-block text-[10px] font-semibold uppercase tracking-wide px-2 py-1 rounded-full"
                                              style="color: rgb(var(--accent-hover)); background: rgb(var(--accent-soft)); border: 1px solid rgb(var(--accent) / 0.3);">
                                            Limbah
                                        </span>
                                    @endif
                                    <h3 class="font-semibold mt-2 truncate" style="color: rgb(var(--text-primary));">{{ $item['nama'] }}</h3>
                                    <p class="text-sm mt-1 tabular-nums" style="color: rgb(var(--text-secondary));">
                                        Rp {{ number_format($item['harga'], 0, ',', '.') }}
                                        @if ($item['satuan']) / {{ $item['satuan'] }} @endif
                                    </p>
                                </div>

                                <button type="button" onclick="removeItem('{{ $key }}')"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl transition flex-shrink-0"
                                        style="color: rgb(var(--text-muted));"
                                        onmouseover="this.style.background='rgb(var(--danger-soft))'; this.style.color='rgb(var(--danger))';"
                                        onmouseout="this.style.background=''; this.style.color='rgb(var(--text-muted))';">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <div class="glass-input flex items-center overflow-hidden">
                                    <button type="button" onclick="updateQty('{{ $key }}', -1)" class="w-9 h-9 flex items-center justify-center font-bold" style="color: rgb(var(--text-secondary));">−</button>
                                    <span class="w-10 text-center font-semibold text-sm tabular-nums" style="color: rgb(var(--text-primary));" id="qty-{{ $key }}">{{ $item['qty'] }}</span>
                                    <button type="button" onclick="updateQty('{{ $key }}', 1)" class="w-9 h-9 flex items-center justify-center font-bold" style="color: rgb(var(--text-secondary));">+</button>
                                </div>

                                <p class="price" style="color: rgb(var(--text-primary));" id="subtotal-{{ $key }}">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between items-center pt-2 flex-wrap gap-3">
                    <a href="{{ route('user.produk.index') }}" class="text-sm font-medium flex items-center gap-1" style="color: rgb(var(--brand));">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Lanjut Belanja
                    </a>
                    <form method="POST" action="{{ route('user.cart.clear') }}" onsubmit="return confirm('Kosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium" style="color: rgb(var(--danger));">Kosongkan Keranjang</button>
                    </form>
                </div>
            </div>

            {{-- SUMMARY --}}
            <div class="lg:col-span-1">
                <div class="glass-card p-5 sticky top-24">
                    <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">Ringkasan Belanja</h3>

                    <div class="space-y-2 mb-4 pb-4" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        <div class="flex justify-between text-sm">
                            <span style="color: rgb(var(--text-secondary));">Total Item</span>
                            <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));" id="totalItems">{{ collect($cart)->sum('qty') }} item</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color: rgb(var(--text-secondary));">Subtotal</span>
                            <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));" id="subtotalDisplay">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Tombol Checkout sekarang membuka Modal --}}
                    <button type="button" @click="updateWaLink(); showCheckoutModal = true" class="block w-full text-center py-3 btn-primary">
                        Lanjut ke Checkout
                    </button>

                    <p class="text-xs text-center mt-3" style="color: rgb(var(--text-muted));">
                        Ongkir & minimum pembelian dicek saat checkout.
                    </p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- MODAL PILIHAN CHECKOUT --}}
            {{-- ============================================================ --}}
            <div x-show="showCheckoutModal" x-cloak
                 class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <div class="glass-card w-full max-w-md p-6 sm:p-8 relative transform transition-all"
                     @click.away="showCheckoutModal = false"
                     x-show="showCheckoutModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">

                    <button @click="showCheckoutModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="text-center mb-6">
                        <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-xl font-extrabold mb-2" style="color: rgb(var(--text-primary));">Pilih Metode Checkout</h3>
                        <p class="text-sm" style="color: rgb(var(--text-secondary));">Silakan pilih cara Anda menyelesaikan pesanan ini.</p>
                    </div>

                    <div class="space-y-3">
                        {{-- Opsi 1: WhatsApp --}}
                        <a id="waCheckoutBtn" href="#" target="_blank" class="w-full flex items-center justify-between p-4 rounded-xl transition border hover:-translate-y-0.5"
                           style="background: rgb(37 211 102 / 0.1); border-color: rgb(37 211 102 / 0.3);">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6" style="color: #25D366;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                <div class="text-left">
                                    <p class="font-bold text-sm" style="color: rgb(var(--text-primary));">Pesan via WhatsApp</p>
                                    <p class="text-xs mt-0.5" style="color: rgb(var(--text-secondary));">Langsung chat Admin, pesanan otomatis terisi.</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        {{-- Opsi 2: Website --}}
                        <a href="{{ route('user.checkout.index') }}" class="w-full flex items-center justify-between p-4 rounded-xl transition border hover:-translate-y-0.5"
                           style="background: rgb(var(--brand-soft) / 0.5); border-color: rgb(var(--brand) / 0.3);">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 rounded-lg flex items-center justify-center text-white" style="background: var(--gradient-brand);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-sm" style="color: rgb(var(--brand-hover));">Checkout di Website</p>
                                    <p class="text-xs mt-0.5" style="color: rgb(var(--text-secondary));">Pilih alamat, ongkir otomatis, & bayar.</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div class="glass-card p-16 text-center max-w-md mx-auto">
            <div class="text-7xl mb-4">🛒</div>
            <h2 class="text-xl font-extrabold mb-2" style="color: rgb(var(--text-primary));">Keranjang Kosong</h2>
            <p class="text-sm mb-6" style="color: rgb(var(--text-secondary));">Yuk mulai belanja tahu segar dan ampas tahu bermanfaat!</p>
            <div class="flex gap-3 justify-center flex-wrap">
                <a href="{{ route('user.produk.index') }}" class="btn-primary">Belanja Tahu</a>
                <a href="{{ route('user.limbah.index') }}" class="glass-btn-amber">Belanja Limbah</a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const cartData = document.getElementById('cartData');
    const ROUTE_UPDATE = cartData.dataset.routeUpdate;
    const ROUTE_REMOVE = cartData.dataset.routeRemove;
    const CSRF_TOKEN = cartData.dataset.csrf;

    function fmtRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function updateQty(key, delta) {
        const el = document.getElementById('qty-' + key);
        const itemEl = document.getElementById('item-' + key);
        if (!el || !itemEl) return;

        const currentQty = parseInt(el.textContent);
        const stokMax = parseInt(itemEl.dataset.stokMax) || 999;
        const harga = parseInt(itemEl.dataset.harga);

        let newQty = currentQty + delta;
        if (newQty < 1) return;
        if (newQty > stokMax) {
            showToast('Stok tidak mencukupi. Maksimal: ' + stokMax, 'error');
            return;
        }

        const oldQty = currentQty;
        el.textContent = newQty;
        document.getElementById('subtotal-' + key).textContent = fmtRp(harga * newQty);
        recalcTotals();

        fetch(ROUTE_UPDATE, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ key: key, qty: newQty })
        })
        .then(r => r.json())
        .then(d => {
            if (!d.success) {
                el.textContent = oldQty;
                document.getElementById('subtotal-' + key).textContent = fmtRp(harga * oldQty);
                recalcTotals();
                showToast(d.message || 'Gagal', 'error');
            }
        })
        .catch(() => {
            el.textContent = oldQty;
            document.getElementById('subtotal-' + key).textContent = fmtRp(harga * oldQty);
            recalcTotals();
            showToast('Error', 'error');
        });
    }

    function removeItem(key) {
        if (!confirm('Hapus item ini dari keranjang?')) return;

        fetch(ROUTE_REMOVE, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ key: key })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                const el = document.getElementById('item-' + key);
                if (el) {
                    el.style.transition = 'opacity .3s ease, transform .3s ease, max-height .3s ease, margin .3s ease, padding .3s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(-20px)';
                    el.style.maxHeight = el.scrollHeight + 'px';
                    setTimeout(() => {
                        el.style.maxHeight = '0';
                        el.style.paddingTop = '0';
                        el.style.paddingBottom = '0';
                        el.style.marginTop = '0';
                        el.style.marginBottom = '0';
                        el.style.overflow = 'hidden';
                    }, 100);
                    setTimeout(() => {
                        el.remove();
                        recalcTotals();
                        const remaining = document.querySelectorAll('[id^="item-"]');
                        if (remaining.length === 0) location.reload();
                    }, 400);
                }
                if (typeof window.setCartBadge === 'function' && d.cart_count !== undefined) {
                    window.setCartBadge(d.cart_count);
                }
                showToast('Item dihapus dari keranjang', 'success');
            } else {
                showToast(d.message || 'Gagal menghapus', 'error');
            }
        })
        .catch(() => showToast('Error', 'error'));
    }

    function recalcTotals() {
        let totalQty = 0;
        let totalPrice = 0;

        document.querySelectorAll('.cart-item').forEach(el => {
            const key = el.id.replace('item-', '');
            const qtyEl = document.getElementById('qty-' + key);
            const harga = parseInt(el.dataset.harga) || 0;
            if (qtyEl) {
                const qty = parseInt(qtyEl.textContent) || 0;
                totalQty += qty;
                totalPrice += harga * qty;
            }
        });

        const totalItemsEl = document.getElementById('totalItems');
        const subtotalEl = document.getElementById('subtotalDisplay');
        if (totalItemsEl) totalItemsEl.textContent = totalQty + ' item';
        if (subtotalEl) subtotalEl.textContent = fmtRp(totalPrice);
    }

    // FUNGSI BARU: Generate link WA dinamis sesuai isi keranjang terbaru
    function updateWaLink() {
        let text = "Halo *EcoTahu*, saya ingin memesan:\n\n";
        let total = 0;
        let count = 1;

        document.querySelectorAll('.cart-item').forEach(el => {
            const key = el.id.replace('item-', '');
            const nama = el.dataset.nama;
            const harga = parseInt(el.dataset.harga) || 0;
            const qtyEl = document.getElementById('qty-' + key);
            const qty = qtyEl ? parseInt(qtyEl.textContent) : 0;
            const subtotal = harga * qty;
            total += subtotal;

            text += `${count}. *${nama}* (${qty}x) - Rp ${subtotal.toLocaleString('id-ID')}\n`;
            count++;
        });

        text += `\n*Total Belanja:* Rp ${total.toLocaleString('id-ID')}\n\n`;
        text += `Mohon info ketersediaan dan ongkirnya. Terima kasih!`;

        const encoded = encodeURIComponent(text);
        // Nomor WA diambil dari config
        document.getElementById('waCheckoutBtn').href = `https://wa.me/{{ config('toko.whatsapp') }}?text=${encoded}`;
    }

    function showToast(msg, type = 'success') {
        const bg = type === 'success'
            ? 'linear-gradient(135deg, rgba(16,185,129,0.95), rgba(5,150,105,0.95))'
            : 'linear-gradient(135deg, rgba(239,68,68,0.95), rgba(220,38,38,0.95))';
        const t = document.createElement('div');
        t.className = 'fixed top-20 right-4 z-50 text-white px-5 py-3 rounded-2xl shadow-2xl text-sm font-semibold';
        t.style.background = bg;
        t.style.backdropFilter = 'blur(12px)';
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