@extends('layouts.auth')

@section('title', 'Bantuan — EcoTahu')
@section('brand-title', 'Pusat Bantuan')
@section('brand-subtitle', 'Temukan jawaban atas pertanyaanmu seputar EcoTahu.')

@section('form')
<div class="login-card auth-form-glass">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Butuh Bantuan?</h2>
        <p class="text-sm auth-hint">Hubungi kami atau baca FAQ di bawah ini.</p>
    </div>
    
    <div class="space-y-4 text-sm auth-hint">
        <p><strong>1. Bagaimana cara memesan tahu?</strong><br>Pilih produk, masukkan ke keranjang, dan lakukan checkout.</p>
        <p><strong>2. Bagaimana cara melacak pesanan?</strong><br>Masuk ke akun Anda dan buka menu "Pesanan".</p>
        <p><strong>Butuh bantuan lain?</strong><br>Hubungi WhatsApp kami di <a href="https://wa.me/628123456789" class="auth-link">0812-3456-789</a></p>
    </div>

    <div class="mt-8">
        <a href="{{ route('login') }}" class="btn-primary w-full py-3 flex justify-center items-center">
            Kembali ke Login
        </a>
    </div>
</div>
@endsection