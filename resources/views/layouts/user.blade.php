<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EcoTahu') — Tahu Sehat, Lingkungan Kuat</title>

    {{-- Font: Inter + fitur OpenType biar tipografi setajam Raycast --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased text-gray-800">

{{-- NAVBAR (Glass) --}}
<nav class="sticky top-0 z-40 glass-nav">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/30">E</div>
                <span class="text-lg font-bold text-gray-800">EcoTahu</span>
            </a>

            {{-- Menu Desktop --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}"
                   class="px-4 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('home') ? 'bg-emerald-500/15 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-white/50 hover:text-emerald-600' }}">
                    Beranda
                </a>
                <a href="{{ route('user.produk.index') }}"
                   class="px-4 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('user.produk.*') ? 'bg-emerald-500/15 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-white/50 hover:text-emerald-600' }}">
                    Produk
                </a>
                <a href="{{ route('user.limbah.index') }}"
                   class="px-4 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('user.limbah.*') ? 'bg-amber-500/15 text-amber-700 font-semibold' : 'text-gray-600 hover:bg-white/50 hover:text-amber-600' }}">
                    Limbah
                </a>
                <a href="{{ route('user.edukasi.index') }}"
                   class="px-4 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('user.edukasi.*') ? 'bg-emerald-500/15 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-white/50 hover:text-emerald-600' }}">
                    Edukasi
                </a>

                @auth
                    @if (!auth()->user()->isAdmin())
                        <a href="{{ route('user.pesanan.index') }}"
                           class="px-4 py-2 text-sm font-medium rounded-xl transition {{ request()->routeIs('user.pesanan.*') ? 'bg-emerald-500/15 text-emerald-700 font-semibold' : 'text-gray-600 hover:bg-white/50 hover:text-emerald-600' }}">
                            Pesanan Saya
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">

                {{-- Cart --}}
                @php
                    $cartCount = count(session('cart', []));
                @endphp
                <a href="{{ route('user.cart.index') }}"
                   class="relative w-10 h-10 flex items-center justify-center rounded-xl glass-input hover:bg-white/70 transition">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-gradient-to-br from-emerald-500 to-emerald-700 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-md shadow-emerald-500/40 tabular-nums">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    @endif
                </a>

                @auth
                    {{-- User Menu --}}
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-xl glass-input hover:bg-white/70 transition">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-emerald-500/30">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->username }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-56 glass-dropdown overflow-hidden">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                   class="block px-4 py-2.5 text-sm text-emerald-700 hover:bg-emerald-50/70 font-semibold border-b border-white/40">
                                    🎛️ Dashboard Admin
                                </a>
                            @endif
                            <a href="{{ route('user.pesanan.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-white/60">Pesanan Saya</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-white/60">Profil</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-white/40">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50/70">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-gray-600 hover:text-emerald-600 px-4 py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="glass-btn-primary text-sm">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Global Alert --}}
@if (session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="glass rounded-xl px-4 py-3 text-sm text-emerald-700 flex items-center gap-2 border-emerald-300/50">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    </div>
@endif

@if (session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="glass rounded-xl px-4 py-3 text-sm text-red-700 flex items-center gap-2 border-red-300/50">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            {{ session('error') }}
        </div>
    </div>
@endif

{{-- CONTENT --}}
<main>
    @yield('content')
</main>

{{-- FOOTER (Glass) --}}
<footer class="glass-nav border-t border-white/50 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/30">E</div>
                    <span class="text-lg font-bold text-gray-800">EcoTahu</span>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed max-w-md">
                    Produsen tahu organik pertama di Indonesia yang mengedepankan kualitas nutrisi dan kelestarian lingkungan hidup. Mendukung SDG 12.
                </p>
            </div>

            <div>
                <h4 class="font-semibold text-gray-800 mb-3">Halaman</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Beranda</a></li>
                    <li><a href="{{ route('user.produk.index') }}" class="hover:text-emerald-600 transition">Produk</a></li>
                    <li><a href="{{ route('user.limbah.index') }}" class="hover:text-emerald-600 transition">Limbah</a></li>
                    <li><a href="{{ route('user.edukasi.index') }}" class="hover:text-emerald-600 transition">Edukasi</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-gray-800 mb-3">Kontak</h4>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li>📍 Jl. Hijau Lestari No. 88, Bandung</li>
                    <li>📞 (022) 1234-5678</li>
                    <li>✉️ hello@ecotahu.id</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/50 mt-8 pt-6 text-center text-xs text-gray-500">
            © {{ date('Y') }} EcoTahu Indonesia. Semua Hak Dilindungi.
        </div>
    </div>
</footer>

@stack('scripts')

</body>
</html>