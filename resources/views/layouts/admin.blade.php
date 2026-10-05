<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EcoTahu Admin</title>

    <script>
        (function() {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = stored || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:200,300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/admin.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased admin-shell">

<div class="flex min-h-screen relative">

    {{-- SIDEBAR --}}
    <aside id="adminSidebar"
           class="admin-sidebar w-64 flex flex-col fixed h-[calc(100vh-2rem)] top-4 left-4 z-30 rounded-3xl transition-transform duration-300 -translate-x-[120%] lg:translate-x-0">

        <div class="h-16 flex items-center px-5 flex-shrink-0" style="border-bottom: 1px solid rgb(var(--border-soft));">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-extrabold text-sm"
                     style="background: var(--gradient-brand); box-shadow: 0 4px 14px rgb(var(--brand) / 0.5);">E</div>
                <div class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight leading-none" style="color: rgb(var(--text-primary));">EcoTahu</span>
                    <span class="text-[10px] font-semibold tracking-widest uppercase" style="color: rgb(var(--text-muted));">Admin Panel</span>
                </div>
            </a>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="pt-4 pb-2 px-4 text-[10px] font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted));">Manajemen</div>

            <a href="{{ route('admin.pesanan.index') }}" class="admin-nav-item {{ request()->routeIs('admin.pesanan.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span class="flex-1 text-left">Pesanan</span>
                @if (($globalPesananPending ?? 0) > 0)
                    <span class="text-[10px] font-bold px-1.5 py-0.5 bg-red-500 text-white rounded-full">{{ $globalPesananPending > 9 ? '9+' : $globalPesananPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.refund.index') }}" class="admin-nav-item {{ request()->routeIs('admin.refund.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                <span class="flex-1 text-left">Refund</span>
                @if (($globalRefundPending ?? 0) > 0)
                    <span class="text-[10px] font-bold px-1.5 py-0.5 bg-red-500 text-white rounded-full">{{ $globalRefundPending > 9 ? '9+' : $globalRefundPending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.produk-tahu.index') }}" class="admin-nav-item {{ request()->routeIs('admin.produk-tahu.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Produk Tahu
            </a>

            <a href="{{ route('admin.limbah.index') }}" class="admin-nav-item {{ request()->routeIs('admin.limbah.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z M9 9h6v6H9z"/></svg>
                Produk Limbah
            </a>

            <a href="{{ route('admin.kategori.index') }}" class="admin-nav-item {{ request()->routeIs('admin.kategori.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori
            </a>

            <a href="{{ route('admin.pelanggan.index') }}" class="admin-nav-item {{ request()->routeIs('admin.pelanggan.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pelanggan
            </a>

            <a href="{{ route('admin.edukasi.index') }}" class="admin-nav-item {{ request()->routeIs('admin.edukasi.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Edukasi
            </a>

            <a href="{{ route('admin.laporan.index') }}" class="admin-nav-item {{ request()->routeIs('admin.laporan.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan
            </a>
        </nav>

        <div class="p-3 space-y-1" style="border-top: 1px solid rgb(var(--border-soft));">
            <a href="{{ route('home') }}" target="_blank" class="admin-nav-item">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Toko
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="admin-nav-item w-full" style="color: rgb(var(--danger));">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div id="sidebarOverlay" class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- MAIN --}}
    <div class="flex-1 lg:ml-[17rem] flex flex-col min-w-0 relative z-10">

        {{-- WRAPPER HEADER — ditambah relative z-40 biar dropdown bisa keluar --}}
        <div class="px-4 lg:px-6 pt-4 relative z-40">
            <header class="admin-topbar h-16 flex items-center justify-between px-4 lg:px-6 rounded-2xl relative">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl flex items-center justify-center transition" style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-base lg:text-lg font-extrabold tracking-tight truncate" style="color: rgb(var(--text-primary));">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center gap-2">
                    <form method="GET" action="{{ route('admin.pesanan.index') }}" class="relative hidden md:block">
                        <input type="text" name="q" placeholder="Cari pesanan..." maxlength="50"
                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s#\-\.]/g, '')"
                               class="w-56 lg:w-64 pl-9 pr-4 py-2 text-sm rounded-xl focus:outline-none transition"
                               style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border)); color: rgb(var(--text-primary));">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </form>

                    {{-- THEME TOGGLE --}}
                    <button type="button" onclick="toggleTheme()" class="w-10 h-10 rounded-xl flex items-center justify-center transition" style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));" title="Ganti Tema">
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    </button>

                    {{-- NOTIFIKASI DROPDOWN --}}
                    <div class="relative" x-data="{ notifOpen: false }" @click.away="notifOpen = false">
                        <button type="button" @click="notifOpen = !notifOpen"
                                class="w-10 h-10 rounded-xl flex items-center justify-center transition relative"
                                style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));"
                                title="Notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if (($globalNotifTotal ?? 0) > 0)
                                <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"
                                      style="box-shadow: 0 0 0 2px rgb(var(--surface));">
                                    {{ $globalNotifTotal > 9 ? '9+' : $globalNotifTotal }}
                                </span>
                            @endif
                        </button>

                        {{-- DROPDOWN — naikin z-index ke 9999 biar ga ketutupan --}}
                        <div x-show="notifOpen" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute right-0 top-full mt-2 w-80 max-w-[calc(100vw-2rem)] rounded-2xl overflow-hidden"
                             style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-xl); z-index: 9999;">

                            {{-- Header dropdown --}}
                            <div class="px-4 py-3 flex items-center justify-between"
                                 style="border-bottom: 1px solid rgb(var(--border-soft));">
                                <div>
                                    <p class="font-bold text-sm" style="color: rgb(var(--text-primary));">Notifikasi</p>
                                    <p class="text-xs" style="color: rgb(var(--text-muted));">
                                        @if (($globalNotifTotal ?? 0) > 0)
                                            {{ $globalNotifTotal }} menunggu tindakan
                                        @else
                                            Semua sudah beres
                                        @endif
                                    </p>
                                </div>
                                @if (($globalNotifTotal ?? 0) > 0)
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-red-500 text-white rounded-full">
                                        {{ $globalNotifTotal }}
                                    </span>
                                @endif
                            </div>

                            {{-- List notif --}}
                            <div class="max-h-80 overflow-y-auto">
                                @forelse (($globalNotifs ?? []) as $n)
                                    <a href="{{ $n['url'] }}"
                                       class="flex items-start gap-3 px-4 py-3 transition hover:bg-emerald-500/5"
                                       style="border-bottom: 1px solid rgb(var(--border-soft));">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                                             style="background: rgb(var(--{{ $n['icon'] === 'cart' ? 'info' : 'warning' }}-soft)); color: rgb(var(--{{ $n['icon'] === 'cart' ? 'info' : 'warning' }}));">
                                            @if ($n['icon'] === 'cart')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold" style="color: rgb(var(--text-primary));">{{ $n['title'] }}</p>
                                            <p class="text-xs truncate" style="color: rgb(var(--text-secondary));">{{ $n['subtitle'] }}</p>
                                            <div class="flex justify-between items-center mt-1 gap-2">
                                                <span class="text-xs font-bold" style="color: rgb(var(--brand));">{{ $n['amount'] }}</span>
                                                <span class="text-[10px] flex-shrink-0" style="color: rgb(var(--text-muted));">{{ $n['time'] }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-10 text-center">
                                        <svg class="w-10 h-10 mx-auto mb-2" style="color: rgb(var(--text-faint));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                        <p class="text-sm" style="color: rgb(var(--text-muted));">Tidak ada notifikasi baru 🎉</p>
                                    </div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                    {{-- PROFIL --}}
                    <div class="flex items-center gap-3 pl-3 ml-1" style="border-left: 1px solid rgb(var(--border-soft));">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold leading-tight" style="color: rgb(var(--text-primary));">{{ auth()->user()->username ?? 'Admin' }}</p>
                            <p class="text-xs leading-tight" style="color: rgb(var(--text-muted));">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background: var(--gradient-brand); box-shadow: 0 4px 14px rgb(var(--brand) / 0.5);">
                            {{ strtoupper(substr(auth()->user()->username ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>
        </div>

        {{-- MAIN CONTENT — ditambah relative z-0 biar ga ganggu dropdown --}}
        <main class="flex-1 p-4 lg:p-6 relative z-0">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Auto-init flatpickr untuk semua input .datepicker
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        today.setHours(23, 59, 59, 999);
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        document.querySelectorAll('input.datepicker').forEach(input => {
            if (input._flatpickr) return;
            flatpickr(input, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                allowInput: false,
                monthSelectorType: 'static',
                maxDate: today,
                onReady: function(_, __, fp) {
                    fp.altInput.classList.add('glass-input');
                    fp.altInput.style.color = 'rgb(var(--text-primary))';
                }
            });
        });

        // Flatpickr untuk input datetime schedule (edukasi)
        document.querySelectorAll('input[name="scheduled_at"]').forEach(input => {
            if (input._flatpickr) return;
            if (input.type === 'datetime-local') {
                input.type = 'text';
            }
            flatpickr(input, {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'd M Y · H:i',
                minDate: new Date(Date.now() + 5 * 60 * 1000),
                time_24hr: true,
                monthSelectorType: 'static',
                onReady: function(_, __, fp) {
                    fp.altInput.classList.add('glass-input');
                    fp.altInput.style.color = 'rgb(var(--text-primary))';
                }
            });
        });

        // Re-init saat modal edukasi dibuka
        const observer = new MutationObserver(() => {
            document.querySelectorAll('input[name="scheduled_at"]').forEach(input => {
                if (input._flatpickr) return;
                flatpickr(input, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'd M Y · H:i',
                    minDate: new Date(Date.now() + 5 * 60 * 1000),
                    time_24hr: true,
                    monthSelectorType: 'static',
                    onReady: function(_, __, fp) {
                        fp.altInput.classList.add('glass-input');
                        fp.altInput.style.color = 'rgb(var(--text-primary))';
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>

@stack('scripts')

</body>
</html>