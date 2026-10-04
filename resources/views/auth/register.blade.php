@extends('layouts.auth')

@section('title', 'Daftar — EcoTahu')
@section('brand-title', 'Gabung & Dapatkan Promo')
@section('brand-subtitle', 'Daftar sekarang dan nikmati berbagai keuntungan jadi member EcoTahu.')

@push('styles')
<style>
    .brand-stats { animation: brandIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.3s both; }
    @keyframes brandIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .register-card { animation: cardIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) both; }
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .perk-item { animation: perkIn 0.5s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .perk-item:nth-child(1) { animation-delay: 0.5s; }
    .perk-item:nth-child(2) { animation-delay: 0.6s; }
    .perk-item:nth-child(3) { animation-delay: 0.7s; }
    @keyframes perkIn {
        from { opacity: 0; transform: translateX(-12px); }
        to   { opacity: 1; transform: translateX(0); }
    }
</style>
@endpush

@section('brand-extra')
<div class="brand-stats rounded-2xl p-5 max-w-md" style="background: rgb(var(--surface) / 0.6); backdrop-filter: blur(16px); border: 1px solid rgb(var(--border-soft)); box-shadow: 0 12px 32px -12px rgb(0 0 0 / 0.15);">
    <div class="perk-item flex items-start gap-3 mb-3">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg text-white" style="background: var(--gradient-brand);">🎁</div>
        <div>
            <p class="font-semibold text-sm" style="color: rgb(var(--text-primary));">Diskon 20% First Order</p>
            <p class="text-xs auth-muted">Berlaku untuk pembelian pertama</p>
        </div>
    </div>
    <div class="perk-item flex items-start gap-3 mb-3">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg text-white" style="background: var(--gradient-brand);">🚚</div>
        <div>
            <p class="font-semibold text-sm" style="color: rgb(var(--text-primary));">Ongkir Terjangkau</p>
            <p class="text-xs auth-muted">Cuma Rp 2.500/km</p>
        </div>
    </div>
    <div class="perk-item flex items-start gap-3">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-lg text-white" style="background: var(--gradient-brand);">🌿</div>
        <div>
            <p class="font-semibold text-sm" style="color: rgb(var(--text-primary));">Produk 100% Organik</p>
            <p class="text-xs auth-muted">Tanpa pengawet, tanpa bahan kimia</p>
        </div>
    </div>
</div>
@endsection

@section('form')
<div class="register-card auth-form-glass">

    <div class="flex gap-2 p-1.5 mb-8 rounded-xl" style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border-soft));">
        <a href="{{ route('login') }}" class="flex-1 text-center px-6 py-2.5 text-sm font-medium rounded-lg transition auth-tab-inactive">Login</a>
        <a href="{{ route('register') }}" class="flex-1 text-center px-6 py-2.5 text-sm font-semibold rounded-lg auth-tab-active">Daftar</a>
    </div>

    <div class="mb-6">
        <h2 class="text-3xl font-extrabold tracking-tight mb-2" style="color: rgb(var(--text-primary));">Buat Akun Baru ✨</h2>
        <p class="text-sm auth-hint">Isi data di bawah untuk mulai berbelanja.</p>
    </div>

    <a href="{{ route('auth.google') }}"
       class="flex items-center justify-center gap-3 w-full py-3 rounded-xl mb-5 transition hover:-translate-y-0.5"
       style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); color: rgb(var(--text-primary));">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span class="font-semibold text-sm">Daftar dengan Google</span>
    </a>

    <div class="flex items-center gap-3 mb-5">
        <div class="flex-1 h-px" style="background: linear-gradient(to right, transparent, rgb(var(--border)), transparent);"></div>
        <span class="text-xs font-medium auth-muted tracking-wider">ATAU</span>
        <div class="flex-1 h-px" style="background: linear-gradient(to right, transparent, rgb(var(--border)), transparent);"></div>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium auth-label mb-1.5">Nama Lengkap</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                       placeholder="Masukkan nama lengkap"
                       class="glass-input w-full pl-10 pr-4 py-3 text-sm">
            </div>
            @error('name')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium auth-label mb-1.5">Email</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       placeholder="nama@email.com"
                       class="glass-input w-full pl-10 pr-4 py-3 text-sm">
            </div>
            @error('email')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="no_telepon" class="block text-sm font-medium auth-label mb-1.5">No. Telepon</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                       placeholder="08xxxxxxxxxx"
                       class="glass-input w-full pl-10 pr-4 py-3 text-sm">
            </div>
            @error('no_telepon')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="block text-sm font-medium auth-label mb-1.5">Password</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                       placeholder="Minimal 8 karakter"
                       class="glass-input w-full pl-10 pr-12 py-3 text-sm">
                <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 z-10" style="color: rgb(var(--text-muted));">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            @error('password')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium auth-label mb-1.5">Konfirmasi Password</label>
            <div class="auth-input-wrap">
                <svg class="auth-input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       placeholder="Ulangi password"
                       class="glass-input w-full pl-10 pr-4 py-3 text-sm">
            </div>
        </div>

        <div class="flex items-start gap-2">
            <input id="terms" type="checkbox" required
                   class="w-4 h-4 mt-0.5 rounded text-emerald-600 focus:ring-emerald-500/50"
                   style="border-color: rgb(var(--border)); background: rgb(var(--surface));">
            <label for="terms" class="text-xs auth-hint leading-relaxed">
                Setuju dengan <a href="#" class="auth-link font-medium hover:underline">S&K</a> dan <a href="#" class="auth-link font-medium hover:underline">Privasi</a> EcoTahu.
            </label>
        </div>

        <button type="submit" class="btn-primary w-full py-3 magnetic-btn">
            Daftar Sekarang
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </button>
    </form>

    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px" style="background: linear-gradient(to right, transparent, rgb(var(--border)), transparent);"></div>
        <span class="text-xs font-medium auth-muted tracking-wider">ATAU</span>
        <div class="flex-1 h-px" style="background: linear-gradient(to right, transparent, rgb(var(--border)), transparent);"></div>
    </div>

    <a href="{{ route('login') }}" class="glass-btn block w-full text-center py-3">Sudah Punya Akun? Masuk</a>
</div>
@endsection