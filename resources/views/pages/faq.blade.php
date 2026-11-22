@extends('layouts.user')
@section('title', 'FAQ')

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center py-12 sm:py-16">
    <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card p-6 sm:p-8 md:p-12" x-data="{ open: 0 }">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--brand));">Pusat Informasi</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold" style="color: rgb(var(--text-primary));">Frequently Asked Questions (FAQ)</h1>
                <p class="text-sm mt-2" style="color: rgb(var(--text-secondary));">Klik pertanyaan di bawah ini untuk melihat jawaban selengkapnya.</p>
            </div>

            @php
                $faqs = [
                    [
                        'q' => 'Apakah tahu EcoTahu 100% organik?',
                        'a' => 'Ya, kami menggunakan kedelai non-GMO pilihan tanpa bahan pengawet sintesis.',
                    ],
                    [
                        'q' => 'Apa itu produk limbah?',
                        'a' => 'Produk limbah adalah olahan dari ampas tahu yang kami proses menjadi pakan ternak, pupuk, atau olahan pangan (seperti tempe gembus) untuk mendukung zero waste.',
                    ],
                    [
                        'q' => 'Bisa COD (Bayar di Tempat)?',
                        'a' => 'Sangat bisa. Anda bisa memilih metode pembayaran COD saat checkout.',
                    ],
                    [
                        'q' => 'Bagaimana perhitungan ongkos kirim EcoTahu?',
                        'a' => 'Untuk pengiriman jarak 0 - 2 km dikenakan tarif tetap Rp 5.000. Untuk jarak di atas 2 km dihitung Rp 2.500 per km.',
                    ],
                    [
                        'q' => 'Bagaimana cara melacak status pesanan saya?',
                        'a' => 'Silakan masuk ke akun Anda dan buka menu Pesanan Saya untuk melihat status pemrosesan hingga pengiriman secara langsung.',
                    ],
                ];
            @endphp

            <div class="space-y-3">
                @foreach ($faqs as $idx => $faq)
                    <div class="rounded-2xl transition overflow-hidden"
                         style="background: rgb(var(--surface-hover) / 0.45); border: 1px solid rgb(var(--border-soft));">
                        <button type="button"
                                @click="open = open === {{ $idx }} ? null : {{ $idx }}"
                                :aria-expanded="(open === {{ $idx }}).toString()"
                                aria-controls="faq-panel-{{ $idx }}"
                                class="w-full px-5 py-4 flex items-center justify-between gap-4 text-left font-bold text-base sm:text-lg transition hover:opacity-90"
                                style="color: rgb(var(--text-primary));">
                            <span>{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 shrink-0 transition-transform duration-200"
                                 :class="open === {{ $idx }} && 'rotate-180'"
                                 style="color: rgb(var(--brand));"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div id="faq-panel-{{ $idx }}"
                             x-show="open === {{ $idx }}"
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="px-5 pb-4 text-sm sm:text-base leading-relaxed"
                             style="color: rgb(var(--text-secondary)); border-top: 1px solid rgb(var(--border-soft));">
                            <p class="pt-3">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 flex flex-wrap items-center justify-between gap-3 text-sm"
                 style="border-top: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-secondary));">
                <span>Masih memiliki pertanyaan lain seputar pesanan atau produk?</span>
                <a href="{{ route('halaman.bantuan') }}" class="font-semibold hover:underline" style="color: rgb(var(--brand));">Kunjungi Pusat Bantuan</a>
            </div>
        </div>
    </div>
</div>
@endsection
