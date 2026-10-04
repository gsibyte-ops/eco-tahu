<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Konfirmasi Password — EcoTahu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- LEFT: BRANDING --}}
    <div class="hidden lg:flex flex-col justify-between p-12 relative overflow-hidden"
         style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -translate-y-48 translate-x-48 backdrop-blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full translate-y-40 -translate-x-40 backdrop-blur-3xl"></div>

        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-white/95 backdrop-blur-md flex items-center justify-center text-emerald-600 font-bold text-lg shadow-xl">E</div>
                <span class="text-xl font-bold text-white tracking-tight">EcoTahu</span>
            </a>
        </div>

        <div class="relative z-10">
            <div class="text-7xl mb-6">🛡️</div>
            <h1 class="text-4xl font-display text-white leading-tight mb-4">
                Area Aman
                <br>Terkonfirmasi.
            </h1>
            <p class="text-emerald-50 text-lg leading-relaxed max-w-md text-pretty">
                Konfirmasi password Anda untuk melanjutkan ke area yang membutuhkan keamanan ekstra.
            </p>
        </div>

        <div class="relative z-10">
            <p class="text-emerald-200/70 text-xs">
                © {{ date('Y') }} EcoTahu Indonesia. Pelopor Tahu Organik Berkelanjutan.
            </p>
        </div>
    </div>

    {{-- RIGHT: FORM --}}
    <div class="flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="lg:hidden mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-emerald-500/30">E</div>
                    <span class="text-xl font-bold text-gray-800">EcoTahu</span>
                </a>
            </div>

            <div class="mb-8">
                <h2 class="text-3xl font-display text-gray-800 mb-2">Konfirmasi Password</h2>
                <p class="text-sm text-gray-500">Ini adalah area aman. Silakan konfirmasi password Anda sebelum melanjutkan.</p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="Masukkan password Anda"
                               class="glass-input w-full pl-10 pr-12 py-3 text-sm text-gray-800 placeholder-gray-400">
                        <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-emerald-600 transition">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="glass-btn-primary w-full py-3">
                    Konfirmasi
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>