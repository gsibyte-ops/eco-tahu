@extends('layouts.user')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium" style="color: rgb(var(--text-primary));">Profil Saya</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Profil Saya</h1>
        <p class="mt-2" style="color: rgb(var(--text-secondary));">Kelola informasi akun, password, dan preferensi Anda.</p>
    </div>

    {{-- Profile Header Card --}}
    <div class="glass-card p-6 mb-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-24" style="background: var(--gradient-brand); opacity: 0.5;"></div>
        <div class="relative flex flex-col sm:flex-row items-center sm:items-end gap-5 pt-8">
            <div class="w-24 h-24 rounded-2xl flex items-center justify-center text-white font-extrabold text-4xl flex-shrink-0"
                 style="background: var(--gradient-brand); box-shadow: 0 12px 32px -8px rgb(var(--brand) / 0.6); border: 3px solid rgb(var(--surface));">
                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-2xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">{{ auth()->user()->username }}</h2>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">{{ auth()->user()->email }}</p>
                <div class="flex flex-wrap gap-2 mt-3 justify-center sm:justify-start">
                    <span class="pill">
                        <span class="w-1.5 h-1.5 rounded-full" style="background: rgb(var(--brand));"></span>
                        Member EcoTahu
                    </span>
                    @if (auth()->user()->email_verified_at)
                        <span class="pill" style="background: rgb(var(--success-soft)); color: rgb(var(--success)); border-color: rgb(var(--success) / 0.3);">
                            ✓ Terverifikasi
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Profile Info --}}
    <div class="glass-card p-6 mb-6">
        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold" style="color: rgb(var(--text-primary));">Informasi Profil</h3>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Update nama, email, dan nomor telepon Anda.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="username" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">Nama Lengkap</label>
                <div class="auth-input-wrap" style="position: relative;">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <input id="username" type="text" name="username" value="{{ old('username', auth()->user()->username) }}" required
                           class="glass-input w-full pl-10 pr-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
                </div>
                @error('username')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">Email</label>
                <div style="position: relative;">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="glass-input w-full pl-10 pr-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
                </div>
                @error('email')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="no_telepon" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">No. Telepon</label>
                <div style="position: relative;">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon', auth()->user()->no_telepon) }}"
                           class="glass-input w-full pl-10 pr-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
                </div>
                @error('no_telepon')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="alamat" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="glass-input w-full px-4 py-3 text-sm" style="color: rgb(var(--text-primary)); resize: vertical;">{{ old('alamat', auth()->user()->alamat) }}</textarea>
                @error('alamat')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan Perubahan
                </button>
                @if (session('status') === 'profile-updated')
                    <span class="text-sm" style="color: rgb(var(--success));">✓ Tersimpan</span>
                @endif
            </div>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="glass-card p-6 mb-6">
        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold" style="color: rgb(var(--text-primary));">Update Password</h3>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Pastikan akun Anda tetap aman dengan password yang kuat.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div x-data="{ show: false }">
                <label for="current_password" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">Password Saat Ini</label>
                <div style="position: relative;">
                    <input id="current_password" :type="show ? 'text' : 'password'" name="current_password" required autocomplete="current-password"
                           class="glass-input w-full px-4 py-3 pr-12 text-sm" style="color: rgb(var(--text-primary));">
                    <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2" style="color: rgb(var(--text-muted));">
                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('current_password', 'updatePassword')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">Password Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="glass-input w-full px-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
                @error('password', 'updatePassword')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium mb-1.5" style="color: rgb(var(--text-primary));">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="glass-input w-full px-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Update Password
                </button>
                @if (session('status') === 'password-updated')
                    <span class="text-sm" style="color: rgb(var(--success));">✓ Tersimpan</span>
                @endif
            </div>
        </form>
    </div>

    {{-- Delete Account --}}
    <div class="glass-card p-6" style="border-color: rgb(var(--danger) / 0.3) !important;">
        <div class="flex items-start gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold" style="color: rgb(var(--danger));">Hapus Akun</h3>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Setelah akun dihapus, semua data akan hilang permanen.</p>
            </div>
        </div>

        <button type="button" onclick="document.getElementById('deleteAccountModal').classList.remove('hidden'); document.getElementById('deleteAccountModal').classList.add('flex');"
                class="glass-btn" style="color: rgb(var(--danger)); border-color: rgb(var(--danger) / 0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus Akun Saya
        </button>
    </div>
</div>

{{-- Delete Account Modal --}}
<div id="deleteAccountModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);">
    <div class="glass-card p-6" style="max-width: 480px; width: 100%;" onclick="event.stopPropagation()">
        <h3 class="text-xl font-extrabold mb-2" style="color: rgb(var(--text-primary));">Hapus Akun?</h3>
        <p class="text-sm mb-5" style="color: rgb(var(--text-secondary));">Masukkan password Anda untuk konfirmasi. Tindakan ini tidak dapat dibatalkan.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <input type="password" name="password" required placeholder="Masukkan password"
                       class="glass-input w-full px-4 py-3 text-sm" style="color: rgb(var(--text-primary));">
                @error('password', 'userDeletion')<p class="mt-1.5 text-xs" style="color: rgb(var(--danger));">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('deleteAccountModal').classList.add('hidden'); document.getElementById('deleteAccountModal').classList.remove('flex');"
                        class="glass-btn">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-semibold text-white transition hover:opacity-90"
                        style="background: rgb(var(--danger));">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection