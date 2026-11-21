@extends('layouts.user')
@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <a href="{{ route('user.cart.index') }}" class="hover:text-emerald-500 transition">Keranjang</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">Checkout</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Checkout</h1>
        <p class="mt-2" style="color: rgb(var(--text-secondary));">Isi data pengiriman dan pilih metode pembayaran yang paling nyaman.</p>
    </div>

    @if (session('error'))
        <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="glass rounded-xl px-4 py-4 mb-5 text-sm" style="color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / 0.3); background: rgb(var(--danger-soft));">
            <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('user.checkout.store') }}"
          x-data="checkoutForm()"
          novalidate
          data-subtotal="{{ $subtotal }}"
          data-ongkir-per-km="{{ $toko['ongkir_per_km'] }}"
          data-ongkir-minimal="{{ $toko['ongkir_minimal'] }}"
          data-radius-maks="{{ $toko['radius_maks_km'] }}"
          data-lat-toko="{{ $toko['lat'] }}"
          data-lng-toko="{{ $toko['lng'] }}"
          data-kecamatan='@json($kecamatanList)'
          data-payment="{{ old('payment_method', 'COD') }}"
          data-delivery="{{ old('delivery_type', 'delivery') }}"
          data-kecamatan-old="{{ old('kecamatan') }}"
          data-minimal-delivery="{{ $minimalDelivery }}"
          data-phone-old="{{ old('no_telepon', auth()->user()->no_telepon) }}"
          data-promo='@json($promo)'
          data-cek-promo-url="{{ route('user.checkout.cekPromo') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-5">

                {{-- 1. Data Penerima --}}
                <div class="glass-card p-5">
                    <h3 class="font-bold mb-4 flex items-center gap-2" style="color: rgb(var(--text-primary));">
                        <span class="w-7 h-7 text-white rounded-full flex items-center justify-center text-xs font-bold" style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.3);">1</span>
                        Data Penerima
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Nama Penerima <span style="color: rgb(var(--danger));">*</span></label>
                            <input type="text" name="nama_penerima" required value="{{ old('nama_penerima', auth()->user()->username) }}" class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">No. Telepon <span style="color: rgb(var(--danger));">*</span></label>
                            <input type="text" name="no_telepon" required x-model="noTelepon" @input.debounce.450ms="cekPromo()" class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Alamat Lengkap <span style="color: rgb(var(--danger));">*</span></label>
                            <textarea name="alamat_pengiriman" required rows="3" class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));" placeholder="Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Patokan... (min. 10 karakter)">{{ old('alamat_pengiriman', auth()->user()->alamat) }}</textarea>
                            <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Minimal 10 karakter. Detail alamat (patokan) membantu kurir menemukan lokasi.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));" placeholder="Contoh: Tahu dipisah dari ampas, dsb">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. Metode Pembayaran --}}
                <div class="glass-card p-5">
                    <h3 class="font-bold mb-4 flex items-center gap-2" style="color: rgb(var(--text-primary));">
                        <span class="w-7 h-7 text-white rounded-full flex items-center justify-center text-xs font-bold" style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.3);">2</span>
                        Metode Pembayaran
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="radio-label">
                            <input type="radio" name="payment_method" value="COD" x-model="payment" class="sr-only">
                            <div class="radio-card">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));"><x-icon name="banknotes" class="w-5 h-5" /></div>
                                    <div>
                                        <p class="font-bold" style="color: rgb(var(--text-primary));">COD</p>
                                        <p class="text-xs" style="color: rgb(var(--text-secondary));">Bayar saat barang datang</p>
                                    </div>
                                </div>
                                <p class="text-xs" style="color: rgb(var(--text-secondary));">Bayar tunai ke kurir saat barang tiba di alamat.</p>
                            </div>
                        </label>

                        <label class="radio-label">
                            <input type="radio" name="payment_method" value="Transfer" x-model="payment" class="sr-only">
                            <div class="radio-card">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--info-soft)); color: rgb(var(--info));"><x-icon name="bank" class="w-5 h-5" /></div>
                                    <div>
                                        <p class="font-bold" style="color: rgb(var(--text-primary));">Transfer Bank</p>
                                        <p class="text-xs" style="color: rgb(var(--text-secondary));">Dapat nomor Virtual Account</p>
                                    </div>
                                </div>
                                <p class="text-xs" style="color: rgb(var(--text-secondary));">Pilih bank, transfer ke VA yang tersedia.</p>
                            </div>
                        </label>
                    </div>

                    <div class="mt-5 pt-5" style="border-top: 1px solid rgb(var(--border-soft));">
                        <p class="text-sm font-semibold mb-3" style="color: rgb(var(--text-primary));">Pilih Tipe Pengiriman:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="radio-label">
                                <input type="radio" name="delivery_type" value="pickup" x-model="delivery" class="sr-only">
                                <div class="radio-card">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand));"><x-icon name="store" class="w-5 h-5" /></div>
                                        <div>
                                            <p class="font-bold text-sm" style="color: rgb(var(--text-primary));">Ambil di Tempat</p>
                                            <p class="text-xs font-semibold" style="color: rgb(var(--brand));">GRATIS ONGKIR</p>
                                        </div>
                                    </div>
                                    <p class="text-xs mt-2" style="color: rgb(var(--text-secondary));">Ambil langsung di pabrik EcoTahu, tanpa biaya ongkir.</p>
                                </div>
                            </label>

                            <label class="radio-label">
                                <input type="radio" name="delivery_type" value="delivery" x-model="delivery" class="sr-only">
                                <div class="radio-card">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: rgb(var(--accent-soft)); color: rgb(var(--accent));"><x-icon name="truck" class="w-5 h-5" /></div>
                                        <div>
                                            <p class="font-bold text-sm" style="color: rgb(var(--text-primary));">Diantar</p>
                                            <p class="text-xs font-semibold" style="color: rgb(var(--accent));">Ongkir otomatis</p>
                                        </div>
                                    </div>
                                    <p class="text-xs mt-2" style="color: rgb(var(--text-secondary));">Min. belanja Rp 15.000. Pilih kecamatan / pin lokasi.</p>
                                    
                                    <!-- TAMBAHAN INFO ONGKIR DI CARD DIANTAR -->
                                    <p class="text-[11px] mt-1 leading-tight font-medium" style="color: rgb(var(--brand));">
                                        Tarif: 0-2 km = Rp 5.000. >2 km = Rp 2.500/km.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div x-show="payment === 'Transfer'" x-cloak class="mt-5 pt-5" style="border-top: 1px solid rgb(var(--border-soft));">
                        <p class="text-sm font-semibold mb-3" style="color: rgb(var(--text-primary));">Pilih Bank Tujuan:</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @foreach (['BCA', 'BRI', 'Mandiri', 'BNI'] as $bank)
                                <label class="bank-label">
                                    <input type="radio" name="bank_tujuan" value="{{ $bank }}" x-model="bank" class="sr-only">
                                    <div class="bank-card">
                                        <p class="font-bold text-sm" style="color: rgb(var(--text-primary));">{{ $bank }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 glass rounded-xl p-3 text-xs flex items-center gap-2" style="color: rgb(var(--info)); background: rgb(var(--info-soft)); border: 1px solid rgb(var(--info) / 0.3);">
                            <x-icon name="clock" class="w-4 h-4 shrink-0" />
                            <span>Selesaikan pembayaran dalam <strong>24 jam</strong>, atau pesanan otomatis dibatalkan.</span>
                        </div>
                    </div>
                </div>

                {{-- 3. Lokasi Pengiriman --}}
                <div x-show="butuhOngkir" x-cloak class="glass-card p-5">
                    <h3 class="font-bold mb-4 flex items-center gap-2" style="color: rgb(var(--text-primary));">
                        <span class="w-7 h-7 text-white rounded-full flex items-center justify-center text-xs font-bold" style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.3);">3</span>
                        Lokasi Pengiriman
                    </h3>

                    <!-- TAMBAHAN INFO ONGKIR DI BOX LOKASI -->
                    <div class="glass-amber rounded-xl p-3 mb-4 text-sm" style="color: rgb(var(--accent));">
                        <p class="font-bold mb-1">Info Tarif Ongkir:</p>
                        <ul class="list-disc pl-4 space-y-0.5 text-xs">
                            <li>Jarak 0 - 2 km: Tarif tetap <b>Rp 5.000</b>.</li>
                            <li>Jarak di atas 2 km: <b>Rp 2.500/km</b>.</li>
                        </ul>
                        <p class="mt-2 text-xs">Wajib pilih kecamatan. Kalau mau lebih presisi, boleh pin lokasi di peta (opsional) kecamatan otomatis ikut berubah.</p>
                    </div>

                    <div x-show="butuhOngkir && subtotal < minimalDelivery" x-cloak
                         class="glass rounded-xl p-3 mb-4 text-sm flex items-start gap-2"
                         style="color: rgb(var(--danger)); background: rgb(var(--danger-soft)); border: 1px solid rgb(var(--danger) / 0.3);">
                        <x-icon name="alert-triangle" class="w-4 h-4 mt-0.5 shrink-0" />
                        <span>
                        Minimum pembelian untuk pengiriman adalah
                        <strong class="tabular-nums">Rp <span x-text="formatNumber(minimalDelivery)"></span></strong>.
                        Belanja Anda kurang <strong class="tabular-nums">Rp <span x-text="formatNumber(minimalDelivery - subtotal)"></span></strong>.
                        Silakan pilih <strong>"Ambil di Tempat"</strong> atau tambah item.
                        </span>
                    </div>

                    {{-- Custom Dropdown Kecamatan --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">
                            Kecamatan <span style="color: rgb(var(--danger));">*</span>
                        </label>

                        {{-- z-index 9999 biar di atas map --}}
                        <div class="relative" style="z-index: 9999;" x-data="{ open: false, search: '' }" @click.away="open = false">
                            <button type="button"
                                    @click="open = !open"
                                    class="custom-select-trigger"
                                    :class="open ? 'is-open' : ''">
                                <span x-text="kecamatan || 'Pilih Kecamatan'"
                                      :style="kecamatan ? 'color: rgb(var(--text-primary)); font-weight: 500;' : 'color: rgb(var(--text-muted));'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="custom-select-menu">
                                <div class="custom-select-menu-inner">
                                    <div class="custom-select-search">
                                        {{-- ← CHANGED: filter cuma huruf & spasi --}}
                                        <input type="text"
                                               x-model="search"
                                               @input="search = search.replace(/[^a-zA-Z\s]/g, '')"
                                               placeholder="Cari kecamatan"
                                               @click.stop>
                                    </div>
                                    <div class="custom-select-list">
                                        <template x-for="nama in Object.keys(kecamatanList).filter(n => n.toLowerCase().includes(search.toLowerCase()))" :key="nama">
                                            <button type="button"
                                                    @click="kecamatan = nama; hitungDariKecamatan(); open = false; search = ''"
                                                    class="custom-select-option"
                                                    :class="kecamatan === nama ? 'is-selected' : ''">
                                                <span x-text="nama"></span>
                                                <svg x-show="kecamatan === nama" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            </button>
                                        </template>
                                        <div x-show="Object.keys(kecamatanList).filter(n => n.toLowerCase().includes(search.toLowerCase())).length === 0"
                                             class="px-4 py-6 text-center text-xs" style="color: rgb(var(--text-muted));">
                                            Kecamatan tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="kecamatan" :value="kecamatan">
                        </div>

                        <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Ongkir dihitung otomatis dari titik tengah kecamatan.</p>
                    </div>

                    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                        <div>
                            <p class="text-sm font-semibold" style="color: rgb(var(--text-primary));">Pin Lokasi di Peta (Opsional)</p>
                            <p class="text-xs" style="color: rgb(var(--text-muted));">Kalau di-pin, kecamatan otomatis ikut berubah & ongkir lebih presisi.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="pakaiLokasiSaya()"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                    style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong)); border: 1px solid rgb(var(--brand) / 0.3);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pakai Lokasi Saya
                            </button>
                            <button type="button" @click="resetPeta()"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium transition"
                                    style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset Pin
                            </button>
                        </div>
                    </div>

                    {{-- isolation + z-index 1 supaya stacking context terpisah dari dropdown --}}
                    <div id="map"
                         style="width: 100%; height: 400px; border-radius: 1rem; overflow: hidden; background: rgb(var(--bg-secondary)); position: relative; z-index: 1; isolation: isolate;"
                         x-init="$nextTick(() => { setTimeout(() => initMap(), 300) })"></div>

                    <input type="hidden" name="lat_pin" :value="latPin">
                    <input type="hidden" name="lng_pin" :value="lngPin">

                    <div x-show="jarak > 0" x-cloak class="grid grid-cols-3 gap-3 mt-3">
                        <div class="glass rounded-xl p-3">
                            <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Sumber</p>
                            <p class="text-sm font-bold" style="color: rgb(var(--text-primary));" x-text="sumber"></p>
                        </div>
                        <div class="glass rounded-xl p-3">
                            <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Jarak</p>
                            <p class="text-sm font-bold tabular-nums" style="color: rgb(var(--text-primary));" x-text="jarak.toFixed(2) + ' km'"></p>
                        </div>
                        <div class="glass rounded-xl p-3">
                            <p class="text-xs mb-1" style="color: rgb(var(--text-muted));">Ongkir</p>
                            <p class="text-sm font-bold tabular-nums" style="color: rgb(var(--brand));" x-text="'Rp ' + formatNumber(ongkir)"></p>
                        </div>
                    </div>

                    <p x-show="jarak > 0 && diLuarRadius" class="text-xs font-semibold mt-2 flex items-center gap-1.5" style="color: rgb(var(--danger));">
                        <x-icon name="alert-triangle" class="w-3.5 h-3.5 shrink-0" />
                        <span>Lokasi di luar jangkauan (maks. {{ $toko['radius_maks_km'] }} km). Silakan pilih kecamatan lain.</span>
                    </p>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="lg:col-span-1">
                <div class="glass-card p-5 sticky top-24">
                    <h3 class="font-bold mb-4" style="color: rgb(var(--text-primary));">Ringkasan Pesanan</h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto mb-4 pb-4" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        @foreach ($cart as $item)
                            <div class="flex gap-3">
                                <div class="w-12 h-12 rounded-lg flex-shrink-0 overflow-hidden" style="background: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border-soft));">
                                    @if ($item['gambar'])
                                        <img src="{{ asset('storage/' . $item['gambar']) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center"><x-icon :name="$item['type'] === 'produk' ? 'cube' : 'recycle'" class="w-5 h-5 opacity-50" style="color: rgb(var(--{{ $item['type'] === 'produk' ? 'brand' : 'accent' }}));" /></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate" style="color: rgb(var(--text-primary));">{{ $item['nama'] }}</p>
                                    <p class="text-xs tabular-nums" style="color: rgb(var(--text-secondary));">{{ $item['qty'] }} × Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Status Promo Pembelian Pertama --}}
                    <template x-if="promo.applied">
                        <div class="rounded-xl px-3.5 py-2.5 mb-4 text-xs flex items-start gap-2" style="background: rgb(var(--success-soft)); border: 1px solid rgb(var(--success) / 0.3); color: rgb(var(--success));">
                            <x-icon name="gift" class="w-4 h-4 shrink-0 mt-0.5" />
                            <span>
                                <strong x-text="promo.label + ' aktif!'"></strong>
                                Hemat <strong class="tabular-nums">Rp <span x-text="formatNumber(diskon)"></span></strong> untuk pesanan pertama ini.
                            </span>
                        </div>
                    </template>
                    <template x-if="!promo.applied && promo.eligible && promo.kurang_minimal > 0">
                        <div class="rounded-xl px-3.5 py-2.5 mb-4 text-xs flex items-start gap-2" style="background: rgb(var(--info-soft)); border: 1px solid rgb(var(--info) / 0.3); color: rgb(var(--info));">
                            <x-icon name="gift" class="w-4 h-4 shrink-0 mt-0.5" />
                            <span>
                                Tambah belanja <strong class="tabular-nums">Rp <span x-text="formatNumber(promo.kurang_minimal)"></span></strong> lagi untuk dapat
                                <strong x-text="promo.label"></strong> (maks. Rp <span x-text="formatNumber(promo.max_diskon)"></span>).
                            </span>
                        </div>
                    </template>
                    <template x-if="!promo.eligible && promo.blocked_by_phone">
                        <div class="rounded-xl px-3.5 py-2.5 mb-4 text-xs flex items-start gap-2" style="background: rgb(var(--warning-soft)); border: 1px solid rgb(var(--warning) / 0.3); color: rgb(var(--warning));">
                            <x-icon name="alert-triangle" class="w-4 h-4 shrink-0 mt-0.5" />
                            <span x-text="promo.reason"></span>
                        </div>
                    </template>

                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span style="color: rgb(var(--text-secondary));">Subtotal</span>
                            <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div x-show="diskon > 0" x-cloak class="flex justify-between text-sm">
                            <span class="font-medium" style="color: rgb(var(--brand));" x-text="'Diskon Order Pertama (' + (promo.persen || 10) + '%)'"></span>
                            <span class="font-bold tabular-nums" style="color: rgb(var(--brand));">-Rp <span x-text="formatNumber(diskon)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color: rgb(var(--text-secondary));">
                                Ongkir
                                <span x-show="!butuhOngkir" class="text-xs" style="color: rgb(var(--brand));">(Ambil di Tempat)</span>
                                <span x-show="butuhOngkir && jarak > 0" class="text-xs">(<span x-text="jarak.toFixed(2)"></span> km)</span>
                            </span>
                            <span class="font-semibold tabular-nums" style="color: rgb(var(--text-primary));">Rp <span x-text="formatNumber(ongkir)"></span></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 mb-5" style="border-top: 2px solid rgb(var(--border));">
                        <span class="font-bold" style="color: rgb(var(--text-primary));">Total</span>
                        <span class="text-xl price text-gradient-green">Rp <span x-text="formatNumber(total)"></span></span>
                    </div>

                    <button type="submit"
                            :disabled="butuhOngkir && (!kecamatan || diLuarRadius || subtotal < minimalDelivery)"
                            class="btn-primary w-full py-3 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <span x-show="!butuhOngkir">Buat Pesanan</span>
                        <span x-show="butuhOngkir && subtotal < minimalDelivery">Belanja Kurang</span>
                        <span x-show="butuhOngkir && subtotal >= minimalDelivery && !kecamatan">Pilih Kecamatan Dulu</span>
                        <span x-show="butuhOngkir && subtotal >= minimalDelivery && kecamatan && diLuarRadius">Di Luar Jangkauan</span>
                        <span x-show="butuhOngkir && subtotal >= minimalDelivery && kecamatan && !diLuarRadius">Buat Pesanan</span>
                    </button>

                    <p class="text-xs text-center mt-3" style="color: rgb(var(--text-muted));">Dengan klik "Buat Pesanan", Anda setuju dengan syarat & ketentuan kami.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* ============================================================
       RADIO CARDS
       ============================================================ */
    .radio-label { cursor: pointer; display: block; }
    .radio-label .radio-card {
        transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        border: 2px solid rgb(var(--border));
        background: rgb(var(--surface));
        border-radius: 1rem;
        padding: 1rem;
    }
    .radio-label:hover .radio-card {
        border-color: rgb(var(--brand) / 0.55);
        background: rgb(var(--brand) / 0.04);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -8px rgb(var(--brand) / 0.3);
    }
    .radio-label input:checked ~ .radio-card {
        border-color: rgb(var(--brand));
        background: rgb(var(--brand) / 0.10);
        box-shadow: 0 0 0 3px rgb(var(--brand) / 0.18), 0 12px 28px -10px rgb(var(--brand) / 0.45);
        transform: translateY(-2px);
    }

    /* ============================================================
       BANK CARDS
       ============================================================ */
    .bank-label { cursor: pointer; display: block; }
    .bank-label .bank-card {
        transition: all 0.2s ease;
        border: 2px solid rgb(var(--border));
        background: rgb(var(--surface));
        border-radius: 0.75rem;
        padding: 0.75rem;
        text-align: center;
    }
    .bank-label:hover .bank-card {
        border-color: rgb(var(--brand) / 0.55);
        background: rgb(var(--brand) / 0.06);
        transform: translateY(-1px);
    }
    .bank-label input:checked ~ .bank-card {
        border-color: rgb(var(--brand));
        background: rgb(var(--brand) / 0.12);
        box-shadow: 0 0 0 3px rgb(var(--brand) / 0.15);
        transform: translateY(-1px);
    }

    /* ============================================================
       CUSTOM SELECT
       ============================================================ */
    .custom-select-trigger {
        transition: all 0.2s ease;
        background: rgb(var(--surface));
        border: 2px solid rgb(var(--border));
        border-radius: 0.75rem;
        padding: 0.7rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        width: 100%;
        text-align: left;
        font-size: 0.875rem;
    }
    .custom-select-trigger:hover {
        border-color: rgb(var(--brand) / 0.55);
        background: rgb(var(--brand) / 0.03);
    }
    .custom-select-trigger.is-open {
        border-color: rgb(var(--brand));
        box-shadow: 0 0 0 3px rgb(var(--brand) / 0.18);
    }

    /* Menu wrapper — z-index raksasa biar di atas Leaflet */
    .custom-select-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        right: 0;
        z-index: 10000 !important;
        max-height: 22rem;
        background: rgb(var(--surface));
        border: 1px solid rgb(var(--border));
        border-radius: 0.75rem;
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.35), 0 8px 16px -4px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    .custom-select-menu-inner {
        display: flex;
        flex-direction: column;
        max-height: 22rem;
        border-radius: inherit;
        overflow: hidden;
    }
    .custom-select-search {
        padding: 0.6rem 0.9rem;
        border-bottom: 1px solid rgb(var(--border-soft));
        background: rgb(var(--bg-secondary));
        flex-shrink: 0;
    }
    .custom-select-search input {
        width: 100%;
        background: transparent;
        border: none;
        outline: none;
        font-size: 0.8125rem;
        color: rgb(var(--text-primary));
        padding: 0.2rem 0;
        font-family: inherit;
    }
    .custom-select-search input::placeholder {
        color: rgb(var(--text-muted));
    }
    .custom-select-list {
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }
    .custom-select-option {
        padding: 0.55rem 0.9rem;
        font-size: 0.8125rem;
        color: rgb(var(--text-secondary));
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
        text-align: left;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: none;
        background: transparent;
        font-family: inherit;
    }
    .custom-select-option:hover {
        background: rgb(var(--brand) / 0.08);
        color: rgb(var(--text-primary));
    }
    .custom-select-option.is-selected {
        background: rgb(var(--brand) / 0.14);
        color: rgb(var(--brand-strong));
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function checkoutForm() {
        const form = document.querySelector('form[x-data]');
        return {
            payment: form.dataset.payment || 'COD',
            delivery: form.dataset.delivery || 'delivery',
            bank: null,
            subtotal: parseFloat(form.dataset.subtotal) || 0,
            ongkirPerKm: parseFloat(form.dataset.ongkirPerKm) || 2500,
            ongkirMinimal: parseFloat(form.dataset.ongkirMinimal) || 5000,
            radiusMaks: parseFloat(form.dataset.radiusMaks) || 10,
            latToko: parseFloat(form.dataset.latToko),
            lngToko: parseFloat(form.dataset.lngToko),
            kecamatanList: JSON.parse(form.dataset.kecamatan),
            minimalDelivery: parseFloat(form.dataset.minimalDelivery) || 15000,
            noTelepon: form.dataset.phoneOld || '',
            cekPromoUrl: form.dataset.cekPromoUrl || '',
            promo: form.dataset.promo ? JSON.parse(form.dataset.promo) : { eligible: false, applied: false, diskon: 0, kurang_minimal: 0, blocked_by_phone: false, persen: 10, max_diskon: 5000, label: 'Diskon 10% Order Pertama' },

            kecamatan: form.dataset.kecamatanOld || '',
            latPin: null,
            lngPin: null,
            jarak: 0,
            sumber: '',
            mapInitialized: false,

            map: null,
            marker: null,
            tokoMarker: null,
            circle: null,

            get butuhOngkir() { return this.delivery === 'delivery'; },
            get diskon() { return this.promo && this.promo.applied ? (Number(this.promo.diskon) || 0) : 0; },
            get ongkir() {
                if (!this.butuhOngkir) return 0;
                if (this.jarak <= 0) return 0;
                const hitung = Math.ceil(this.jarak) * this.ongkirPerKm;
                return Math.max(hitung, this.ongkirMinimal);
            },
            get total() { return Math.max(0, this.subtotal - this.diskon) + this.ongkir; },
            get diLuarRadius() { return this.jarak > this.radiusMaks; },

            async cekPromo() {
                if (!this.cekPromoUrl) return;
                try {
                    const url = new URL(this.cekPromoUrl, window.location.origin);
                    url.searchParams.set('no_telepon', this.noTelepon || '');
                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        this.promo = await res.json();
                    }
                } catch (_) {}
            },

            formatNumber(num) {
                return Number(Math.round(num)).toLocaleString('id-ID');
            },

            haversine(lat1, lng1, lat2, lng2) {
                const R = 6371;
                const toRad = d => d * Math.PI / 180;
                const dLat = toRad(lat2 - lat1);
                const dLng = toRad(lng2 - lng1);
                const a = Math.sin(dLat / 2) ** 2
                        + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            },

            findNearestKecamatan(lat, lng) {
                let nearest = null;
                let minDist = Infinity;
                for (const [nama, coord] of Object.entries(this.kecamatanList)) {
                    if (!coord || coord.lat == null || coord.lng == null) continue;
                    const d = this.haversine(lat, lng, coord.lat, coord.lng);
                    if (d < minDist) {
                        minDist = d;
                        nearest = nama;
                    }
                }
                return nearest;
            },

            init() {
                if (this.kecamatan) this.hitungDariKecamatan();
            },

            initMap() {
                if (this.mapInitialized) return;
                const mapEl = document.getElementById('map');
                if (!mapEl) return;

                if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
                    setTimeout(() => this.initMap(), 300);
                    return;
                }

                this.map = L.map('map', {
                    zoomControl: true,
                    scrollWheelZoom: true,
                }).setView([this.latToko, this.lngToko], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxZoom: 19,
                }).addTo(this.map);

                const tokoIcon = L.divIcon({
                    className: 'toko-marker',
                    html: '<div style="background:#10b981; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(16,185,129,0.5); border: 3px solid white;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg></div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                });

                this.tokoMarker = L.marker([this.latToko, this.lngToko], { icon: tokoIcon })
                    .addTo(this.map)
                    .bindPopup('<strong>{{ $toko["nama"] }}</strong><br><span style="font-size:11px;">{{ $toko["alamat"] }}</span>');

                this.circle = L.circle([this.latToko, this.lngToko], {
                    radius: this.radiusMaks * 1000,
                    color: '#10b981',
                    fillColor: '#10b981',
                    fillOpacity: 0.08,
                    weight: 1.5,
                    dashArray: '5, 5',
                }).addTo(this.map);

                this.map.on('click', (e) => {
                    this.setPin(e.latlng.lat, e.latlng.lng);
                });

                setTimeout(() => {
                    if (this.map) this.map.invalidateSize({ animate: false });
                }, 200);

                this.mapInitialized = true;
            },

            setPin(lat, lng) {
                this.latPin = lat;
                this.lngPin = lng;
                this.sumber = 'Pin peta';
                this.jarak = this.haversine(this.latToko, this.lngToko, lat, lng);

                const nearest = this.findNearestKecamatan(lat, lng);
                if (nearest) this.kecamatan = nearest;

                if (this.marker) {
                    this.marker.setLatLng([lat, lng]);
                } else {
                    const userIcon = L.divIcon({
                        className: 'user-marker',
                        html: '<div style="background:#f59e0b; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(245,158,11,0.5); border: 3px solid white;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-6-4.35-6-10a6 6 0 1112 0c0 5.65-6 10-6 10z"/><circle cx="12" cy="11" r="2.2"/></svg></div>',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16],
                    });
                    this.marker = L.marker([lat, lng], { icon: userIcon, draggable: true }).addTo(this.map);
                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.setPin(pos.lat, pos.lng);
                    });
                }

                const bounds = L.latLngBounds([[this.latToko, this.lngToko], [lat, lng]]);
                this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 16 });
            },

            resetPeta() {
                if (this.marker) {
                    this.map.removeLayer(this.marker);
                    this.marker = null;
                }
                this.latPin = null;
                this.lngPin = null;

                if (this.kecamatan) {
                    this.hitungDariKecamatan();
                } else {
                    this.jarak = 0;
                    this.sumber = '';
                }

                if (this.map) this.map.setView([this.latToko, this.lngToko], 13);
            },

            hitungDariKecamatan() {
                // Clear pin karena user explicitly pilih kecamatan
                if (this.marker && this.map) {
                    this.map.removeLayer(this.marker);
                    this.marker = null;
                }
                this.latPin = null;
                this.lngPin = null;

                if (!this.kecamatan) {
                    this.jarak = 0;
                    this.sumber = '';
                    return;
                }

                const data = this.kecamatanList[this.kecamatan];
                if (!data) return;

                this.jarak = this.haversine(this.latToko, this.lngToko, data.lat, data.lng);
                this.sumber = 'Kec. ' + this.kecamatan;
            },

            pakaiLokasiSaya() {
                if (!navigator.geolocation) {
                    alert('Browser Anda tidak mendukung GPS. Silakan klik manual di peta.');
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (position) => { this.setPin(position.coords.latitude, position.coords.longitude); },
                    (error) => {
                        const msg = {
                            1: 'Izin lokasi ditolak. Silakan klik manual di peta.',
                            2: 'Lokasi tidak tersedia. Silakan klik manual di peta.',
                            3: 'Timeout. Silakan klik manual di peta.',
                        }[error.code] || 'Gagal mendapatkan lokasi. Silakan klik manual di peta.';
                        alert(msg);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        }
    }
</script>
@endpush