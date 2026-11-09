@extends('layouts.user')
@section('title', 'FAQ')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="glass-card p-8 md:p-12">
        <h1 class="text-3xl font-extrabold mb-6" style="color: rgb(var(--text-primary));">Frequently Asked Questions (FAQ)</h1>
        <div class="space-y-6 leading-relaxed" style="color: rgb(var(--text-secondary));">
            <div>
                <h3 class="font-bold text-lg mb-1" style="color: rgb(var(--text-primary));">Apakah tahu EcoTahu 100% organik?</h3>
                <p>Ya, kami menggunakan kedelai non-GMO pilihan tanpa bahan pengawet sintesis.</p>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-1" style="color: rgb(var(--text-primary));">Apa itu produk limbah?</h3>
                <p>Produk limbah adalah olahan dari ampas tahu yang kami proses menjadi pakan ternak, pupuk, atau olahan pangan (seperti tempe gembus) untuk mendukung zero waste.</p>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-1" style="color: rgb(var(--text-primary));">Bisa COD (Bayar di Tempat)?</h3>
                <p>Sangat bisa. Anda bisa memilih metode pembayaran COD saat checkout.</p>
            </div>
        </div>
    </div>
</div>
@endsection