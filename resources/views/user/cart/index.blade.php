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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ITEM LIST --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach ($cart as $key => $item)
                    <div class="glass-card p-4 flex gap-4" id="item-{{ $key }}"
                         data-harga="{{ $item['harga'] }}"
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

                    <a href="{{ route('user.checkout.index') }}"
                       class="block w-full text-center py-3 btn-primary">
                        Lanjut ke Checkout
                    </a>

                    <p class="text-xs text-center mt-3" style="color: rgb(var(--text-muted));">
                        Ongkir & minimum pembelian dicek saat checkout.
                    </p>
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

    // ← CHANGED: format number helper
    function fmtRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    // ← CHANGED: update qty tanpa reload
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

        // Optimistic update — update UI dulu, baru kirim request
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
                // Rollback
                el.textContent = oldQty;
                document.getElementById('subtotal-' + key).textContent = fmtRp(harga * oldQty);
                recalcTotals();
                showToast(d.message || 'Gagal', 'error');
            }
            // Kalau success, UI udah bener
        })
        .catch(() => {
            // Rollback
            el.textContent = oldQty;
            document.getElementById('subtotal-' + key).textContent = fmtRp(harga * oldQty);
            recalcTotals();
            showToast('Error', 'error');
        });
    }

    // ← CHANGED: remove item tanpa reload
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
                    // Animasi fade out + slide
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
                        // Kalau keranjang kosong, reload biar tampil empty state
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

    // ← CHANGED: recalc total item + subtotal dari DOM
    function recalcTotals() {
        let totalQty = 0;
        let totalPrice = 0;

        document.querySelectorAll('[id^="item-"]').forEach(el => {
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