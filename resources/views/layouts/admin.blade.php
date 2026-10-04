<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EcoTahu Admin</title>

    {{-- THEME INIT — anti-FOUC --}}
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }

        /* Custom Flatpickr — match theme */
        .flatpickr-calendar {
            background: rgb(var(--surface));
            border-radius: var(--radius-lg);
            border: 1px solid rgb(var(--border));
            box-shadow: var(--shadow-lg);
            font-family: inherit;
            color: rgb(var(--text-primary));
        }
        .flatpickr-day { color: rgb(var(--text-primary)); border-radius: 0.5rem; }
        .flatpickr-day:hover {
            background: rgb(var(--brand) / 0.15);
            border-color: transparent;
            color: rgb(var(--brand-hover));
        }
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange {
            background: rgb(var(--brand));
            border-color: rgb(var(--brand));
            color: white;
        }
        .flatpickr-day.today {
            border-color: rgb(var(--brand));
        }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: rgb(var(--text-faint));
        }
        .flatpickr-months .flatpickr-month,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 600;
            color: rgb(var(--text-primary));
            background: transparent;
        }
        .flatpickr-current-month .numInputWrapper span.arrowUp:after { border-bottom-color: rgb(var(--text-secondary)); }
        .flatpickr-current-month .numInputWrapper span.arrowDown:after { border-top-color: rgb(var(--text-secondary)); }
        .flatpickr-weekday {
            color: rgb(var(--text-muted));
            font-weight: 600;
            background: transparent;
        }
        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            fill: rgb(var(--text-secondary));
        }
        .flatpickr-months .flatpickr-prev-month:hover svg,
        .flatpickr-months .flatpickr-next-month:hover svg {
            fill: rgb(var(--brand));
        }
        .flatpickr-time input { color: rgb(var(--text-primary)); }

        /* Admin sidebar item animation */
        .admin-nav-item svg {
            transition: transform 0.2s ease;
        }
        .admin-nav-item:hover svg {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex min-h-screen">

    {{-- ============================================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================================ --}}
    <aside id="adminSidebar"
           class="w-64 flex flex-col fixed h-full z-30 transition-transform duration-300 -translate-x-full lg:translate-x-0"
           style="background: rgb(var(--surface)); border-right: 1px solid rgb(var(--border-soft));">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 flex-shrink-0"
             style="border-bottom: 1px solid rgb(var(--border-soft));">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-extrabold text-sm shadow-md"
                     style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.35);">
                    E
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-extrabold tracking-tight leading-none" style="color: rgb(var(--text-primary));">EcoTahu</span>
                    <span class="text-[10px] font-semibold tracking-widest uppercase" style="color: rgb(var(--text-muted));">Admin Panel</span>
                </div>
            </a>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <div class="pt-4 pb-2 px-4 text-[10px] font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted));">
                Manajemen
            </div>

            {{-- Pesanan --}}
            <a href="{{ route('admin.pesanan.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.pesanan.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Pesanan
            </a>

            {{-- Refund --}}
            <a href="{{ route('admin.refund.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.refund.*') ? 'is-active' : '' }} relative">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                <span class="flex-1 text-left">Refund</span>
                @if (($globalRefundPending ?? 0) > 0)
                    <span class="text-[10px] font-bold px-1.5 py-0.5 bg-red-500 text-white rounded-full">
                        {{ $globalRefundPending > 9 ? '9+' : $globalRefundPending }}
                    </span>
                @endif
            </a>

            {{-- Produk Tahu --}}
            <a href="{{ route('admin.produk-tahu.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.produk-tahu.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Produk Tahu
            </a>

            {{-- Produk Limbah --}}
            <a href="{{ route('admin.limbah.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.limbah.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z M9 9h6v6H9z"/>
                </svg>
                Produk Limbah
            </a>

            {{-- Kategori --}}
            <a href="{{ route('admin.kategori.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.kategori.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kategori
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('admin.pelanggan.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.pelanggan.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pelanggan
            </a>

            {{-- Edukasi --}}
            <a href="{{ route('admin.edukasi.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.edukasi.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Edukasi
            </a>

            {{-- Laporan --}}
            <a href="{{ route('admin.laporan.index') }}"
               class="admin-nav-item {{ request()->routeIs('admin.laporan.*') ? 'is-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan
            </a>
        </nav>

        {{-- Bottom Actions --}}
        <div class="p-3 space-y-1" style="border-top: 1px solid rgb(var(--border-soft));">
            <a href="{{ route('home') }}" target="_blank"
               class="admin-nav-item">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Toko
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="admin-nav-item w-full"
                        style="color: rgb(var(--danger));">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MOBILE OVERLAY --}}
    <div id="sidebarOverlay"
         class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm hidden lg:hidden"
         onclick="toggleSidebar()"></div>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT --}}
    {{-- ============================================================ --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">

        {{-- TOPBAR --}}
        <header class="h-16 sticky top-0 z-20 flex items-center justify-between px-4 lg:px-8"
                style="background: rgb(var(--surface) / 0.85); backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%); border-bottom: 1px solid rgb(var(--border-soft));">

            <div class="flex items-center gap-3 min-w-0">
                {{-- Mobile Menu Button --}}
                <button type="button" onclick="toggleSidebar()"
                        class="lg:hidden w-10 h-10 rounded-xl flex items-center justify-center transition"
                        style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="text-base lg:text-lg font-bold truncate" style="color: rgb(var(--text-primary));">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-2">
                {{-- Search --}}
                <form method="GET" action="{{ route('admin.pesanan.index') }}" class="relative hidden md:block">
                    <input type="text" name="q" placeholder="Cari pesanan..."
                           maxlength="50"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s#\-\.]/g, '')"
                           class="w-56 lg:w-64 pl-9 pr-4 py-2 text-sm rounded-xl focus:outline-none transition"
                           style="background: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border)); color: rgb(var(--text-primary));">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                         style="color: rgb(var(--text-muted));"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </form>

                {{-- THEME TOGGLE --}}
                <button type="button" onclick="toggleTheme()"
                        class="w-10 h-10 rounded-xl flex items-center justify-center transition"
                        style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));"
                        title="Ganti Tema">
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                </button>

                {{-- Notif Refund --}}
                <a href="{{ route('admin.refund.index') }}"
                   class="w-10 h-10 rounded-xl flex items-center justify-center transition relative"
                   style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary));">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if (($globalRefundPending ?? 0) > 0)
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"
                              style="box-shadow: 0 0 0 2px rgb(var(--surface));">
                            {{ $globalRefundPending > 9 ? '9+' : $globalRefundPending }}
                        </span>
                    @endif
                </a>

                {{-- Profile --}}
                <div class="flex items-center gap-3 pl-3 ml-1" style="border-left: 1px solid rgb(var(--border-soft));">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold leading-tight" style="color: rgb(var(--text-primary));">{{ auth()->user()->username ?? 'Admin' }}</p>
                        <p class="text-xs leading-tight" style="color: rgb(var(--text-muted));">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md"
                         style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.35);">
                        {{ strtoupper(substr(auth()->user()->username ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

<script>
    // ============================================================
    // THEME TOGGLE
    // ============================================================
    window.toggleTheme = function() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) {}
    };

    // ============================================================
    // SIDEBAR TOGGLE (mobile)
    // ============================================================
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    };
</script>

@stack('scripts')

</body>
</html>