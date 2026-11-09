@extends('layouts.auth')

@section('title', 'Kebijakan Privasi — EcoTahu')
@section('brand-title', 'Kebijakan Privasi')
@section('brand-subtitle', 'Bagaimana kami melindungi data Anda.')

@section('form')
<div class="login-card auth-form-glass">
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Kebijakan Privasi</h2>
    </div>
    
    <div class="space-y-3 text-sm auth-hint max-h-96 overflow-y-auto pr-2">
        <p>1. Kami hanya mengumpulkan data yang diperlukan untuk proses pemesanan (nama, alamat, no HP).</p>
        <p>2. Data Anda tidak akan dijual atau dibagikan kepada pihak ketiga tanpa izin.</p>
        <p>3. Pembayaran diproses melalui payment gateway yang aman dan terenkripsi.</p>
        <p>4. Anda berhak menghapus data akun Anda kapan saja melalui pengaturan profil.</p>
    </div>

    <div class="mt-8">
        <a href="{{ route('login') }}" class="btn-primary w-full py-3 flex justify-center items-center">
            Kembali ke Login
        </a>
    </div>
</div>
@endsection