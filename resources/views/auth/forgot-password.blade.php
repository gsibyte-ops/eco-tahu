@extends('layouts.auth')

@section('title', 'Lupa Password — EcoTahu')
@section('brand-title', 'Lupa Password? Tidak Masalah.')
@section('brand-subtitle', 'Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password.')

@section('form')
<div class="mb-8">
    <h2 class="text-3xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Reset Password</h2>
    <p class="text-sm auth-hint">Masukkan email terdaftar untuk menerima link reset password.</p>
</div>

@if (session('status'))
    <div class="glass rounded-xl px-4 py-3 mb-4 text-sm" style="color: rgb(var(--success)); background: rgb(var(--success-soft) / 0.6); border: 1px solid rgb(var(--success) / 0.3);">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium auth-label mb-1.5">Email</label>
        <div class="relative">
            <svg class="w-4 h-4 auth-field-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="nama@email.com"
                   class="glass-input w-full pl-10 pr-4 py-3 text-sm">
        </div>
        @error('email')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="btn-primary w-full py-3">Kirim Link Reset Password</button>
</form>

<div class="mt-6 text-center">
    <a href="{{ route('login') }}" class="text-sm font-medium auth-link transition inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Login
    </a>
</div>
@endsection