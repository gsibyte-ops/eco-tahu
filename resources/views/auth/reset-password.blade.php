@extends('layouts.auth')

@section('title', 'Reset Password — EcoTahu')
@section('brand-title', 'Buat Password Baru Anda.')
@section('brand-subtitle', 'Pilih password yang kuat dan mudah Anda ingat untuk mengamankan akun.')

@section('form')
<div class="mb-8">
    <h2 class="text-3xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Password Baru</h2>
    <p class="text-sm auth-hint">Isi password baru Anda di bawah ini.</p>
</div>

<form method="POST" action="{{ route('password.store') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div>
        <label for="email" class="block text-sm font-medium auth-label mb-1.5">Email</label>
        <div class="relative">
            <svg class="w-4 h-4 auth-field-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                   placeholder="nama@email.com"
                   class="glass-input w-full pl-10 pr-4 py-3 text-sm">
        </div>
        @error('email')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
    </div>

    <div x-data="{ show: false }">
        <label for="password" class="block text-sm font-medium auth-label mb-1.5">Password Baru</label>
        <div class="relative">
            <svg class="w-4 h-4 auth-field-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                   placeholder="Minimal 8 karakter"
                   class="glass-input w-full pl-10 pr-12 py-3 text-sm">
            <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 auth-field-icon transition hover:opacity-80">
                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
        </div>
        @error('password')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium auth-label mb-1.5">Konfirmasi Password</label>
        <div class="relative">
            <svg class="w-4 h-4 auth-field-icon absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="Ulangi password"
                   class="glass-input w-full pl-10 pr-4 py-3 text-sm">
        </div>
        @error('password_confirmation')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="btn-primary w-full py-3">Reset Password</button>
</form>
@endsection