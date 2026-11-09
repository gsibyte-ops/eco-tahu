@extends('layouts.auth')

@section('title', 'Syarat & Ketentuan — EcoTahu')
@section('brand-title', 'Syarat & Ketentuan')
@section('brand-subtitle', 'Ketentuan penggunaan layanan EcoTahu.')

@section('form')
<div class="login-card auth-form-glass">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Syarat & Ketentuan</h2>
    </div>
    
    <div class="space-y-3 text-sm auth-hint max-h-96 overflow-y-auto pr-2">
        <p>1. Dengan menggunakan layanan EcoTahu, Anda setuju untuk mematuhi semua peraturan yang berlaku.</p>
        <p>2. Pesanan yang sudah dibayar tidak dapat dibatalkan secara sepihak, kecuali sesuai kebijakan refund.</p>
        <p>3. Produk yang dikirim adalah produk segar, pastikan Anda menerima dengan baik.</p>
        <p>4. Penyalahgunaan akun akan dikenakan sanksi pemblokiran.</p>
    </div>

    <div class="mt-8">
        <a href="{{ route('login') }}" class="btn-primary w-full py-3 flex justify-center items-center">
            Saya Mengerti
        </a>
    </div>
</div>
@endsection