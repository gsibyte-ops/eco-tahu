<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EcoTahu') — Tahu Sehat, Lingkungan Kuat</title>

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

    {{-- MODEL-VIEWER --}}
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.5.0/model-viewer.min.js"></script>

    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }

        /* ============================================================
           NAVBAR LINK
           ============================================================ */
        .nav-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.8125rem;
            font-weight: 500;
            border-radius: 0.625rem;
            color: rgb(var(--text-secondary));
            transition: color 0.25s ease, background 0.25s ease;
            isolation: isolate;
            z-index: 2;
        }
        .nav-link:hover {
            color: rgb(var(--text-primary));
            background: rgb(var(--brand) / 0.08);
        }
        .nav-link.is-active {
            color: rgb(var(--brand-hover));
            font-weight: 600;
        }
        [data-theme="dark"] .nav-link.is-active {
            color: rgb(var(--brand));
        }
        .nav-link.is-active::before {
            content: '';
            position: absolute;
            inset: -6px -2px;
            border-radius: 1rem;
            background: radial-gradient(
                ellipse at center,
                rgb(var(--brand) / 0.25) 0%,
                rgb(var(--brand) / 0.10) 40%,
                transparent 75%
            );
            filter: blur(8px);
            z-index: -1;
            pointer-events: none;
        }
        [data-theme="dark"] .nav-link.is-active::before {
            background: radial-gradient(
                ellipse at center,
                rgb(var(--brand) / 0.40) 0%,
                rgb(var(--brand) / 0.18) 40%,
                transparent 75%
            );
        }

        .nav-icon-btn {
            position: relative;
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.625rem;
            color: rgb(var(--text-secondary));
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease, background 0.2s ease;
            z-index: 2;
        }
        .nav-icon-btn:hover {
            color: rgb(var(--brand-hover));
            background: rgb(var(--brand) / 0.10);
        }

        .nav-profile-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.5rem 0.25rem 0.25rem;
            border-radius: 0.625rem;
            background: transparent;
            border: none;
            cursor: pointer;
            color: rgb(var(--text-secondary));
            transition: background 0.2s ease, color 0.2s ease;
            z-index: 2;
        }
        .nav-profile-btn:hover {
            background: rgb(var(--brand) / 0.10);
            color: rgb(var(--text-primary));
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 1rem;
            font-size: 0.8125rem;
            font-weight: 500;
            color: rgb(var(--text-primary));
            transition: background 0.15s ease;
            text-decoration: none;
        }
        .dropdown-item:hover {
            background: rgb(var(--brand) / 0.08);
        }

        .dropdown-icon {
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgb(var(--bg-secondary));
            color: rgb(var(--text-secondary));
        }

        .brand-gradient {
            background: var(--gradient-brand);
            box-shadow: 0 2px 8px rgb(var(--brand) / 0.4), inset 0 1px 0 rgb(255 255 255 / 0.25);
        }

        .btn-daftar-gradient {
            background: var(--gradient-brand);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgb(var(--brand) / 0.35);
            transition: all 0.2s ease;
        }
        .btn-daftar-gradient:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgb(var(--brand) / 0.5);
        }

        /* Animasi pop cart badge */
        @keyframes cart-badge-pop {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.5); }
            100% { transform: scale(1); }
        }
        .cart-badge-pop {
            animation: cart-badge-pop 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }
    </style>
</head>
<body class="font-sans antialiased">

{{-- FLOATING NAVBAR --}}
<div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-1.5rem)] max-w-6xl px-3 sm:px-0">
    <nav class="floating-nav rounded-2xl px-2.5 h-14 flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 px-1.5 shrink-0 relative z-10">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-extrabold text-sm brand-gradient">
                E
            </div>
            <span class="text-sm font-extrabold tracking-tight hidden sm:inline" style="color: rgb(var(--text-primary));">EcoTahu</span>
        </a>

        {{-- Menu Desktop --}}
        <div class="hidden md:flex items-center gap-0.5 relative z-10">
            @php
                $navItems = [
                    ['route' => 'home', 'pattern' => 'home', 'label' => 'Beranda',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>'],
                    ['route' => 'user.produk.index', 'pattern' => 'user.produk.*', 'label' => 'Produk Tahu',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>'],
                    ['route' => 'user.limbah.index', 'pattern' => 'user.limbah.*', 'label' => 'Produk Limbah',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.3M7.5 4.205l-.75 1.3M5.106 6.215l-1.15.964M18.894 17.785l-1.15.964M4.543 12.923l-1.41.513M20.868 11.077l-1.41.513"/>'],
                    ['route' => 'user.edukasi.index', 'pattern' => 'user.edukasi.*', 'label' => 'Edukasi',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>'],
                ];
                $isAuth = auth()->check() && !auth()->user()->isAdmin();
            @endphp

            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" class="nav-link {{ $active ? 'is-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        {!! $item['icon'] !!}
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            @if ($isAuth)
                @php $activePesanan = request()->routeIs('user.pesanan.*'); @endphp
                <a href="{{ route('user.pesanan.index') }}" class="nav-link {{ $activePesanan ? 'is-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                    </svg>
                    <span>Pesanan</span>
                </a>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-0.5 shrink-0 relative z-10">

            {{-- Theme Toggle --}}
            <button type="button" onclick="toggleTheme()" class="nav-icon-btn" title="Ganti Tema" aria-label="Ganti Tema">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                </svg>
            </button>

            {{-- Cart --}}
            @php $cartCount = count(session('cart', [])); @endphp
            <a href="{{ route('user.cart.index') }}" class="nav-icon-btn" title="Keranjang">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                </svg>
                @if ($cartCount > 0)
                    <span data-cart-badge
                          class="absolute top-0.5 right-0.5 min-w-[16px] h-[16px] px-1 text-white text-[9px] font-bold rounded-full flex items-center justify-center tnum"
                          style="background: var(--gradient-brand); box-shadow: 0 0 0 2px rgb(var(--surface)), 0 2px 6px rgb(var(--brand) / 0.5);">
                        {{ $cartCount > 9 ? '9+' : $cartCount }}
                    </span>
                @endif
            </a>

            @auth
                <div class="relative" x-data="{ open: false }" @click.away="open = false" style="z-index: 100;">
                    <button type="button" @click.prevent="open = !open" class="nav-profile-btn" :aria-expanded="open">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white font-bold text-[11px] brand-gradient">
                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-xs font-semibold max-w-[80px] truncate" style="color: rgb(var(--text-primary));">
                            {{ auth()->user()->username }}
                        </span>
                        <svg class="w-3 h-3 opacity-60 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-60 glass-overlay rounded-2xl overflow-hidden"
                         style="z-index: 200;">
                        <div class="px-4 py-3 flex items-center gap-3" style="border-bottom: 1px solid rgb(var(--border-soft));">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0 brand-gradient">
                                {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate" style="color: rgb(var(--text-primary));">{{ auth()->user()->username }}</p>
                                <p class="text-xs truncate" style="color: rgb(var(--text-muted));">{{ auth()->user()->email ?? 'Member EcoTahu' }}</p>
                            </div>
                        </div>

                        <div class="py-1.5">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                    <span class="dropdown-icon" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-hover));">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                    </span>
                                    <span class="font-semibold" style="color: rgb(var(--brand-hover));">Dashboard Admin</span>
                                </a>
                            @endif

                            <a href="{{ route('user.pesanan.index') }}" class="dropdown-item">
                                <span class="dropdown-icon">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </span>
                                <span>Pesanan Saya</span>
                            </a>

                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <span class="dropdown-icon">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </span>
                                <span>Profil</span>
                            </a>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" style="border-top: 1px solid rgb(var(--border-soft));">
                            @csrf
                            <button type="submit" class="dropdown-item w-full text-left" style="color: rgb(var(--danger));">
                                <span class="dropdown-icon" style="background: rgb(var(--danger) / 0.10); color: rgb(var(--danger));">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </span>
                                <span class="font-semibold">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition"
                   style="color: rgb(var(--text-secondary));">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="btn-daftar-gradient !py-2 !px-4 !text-xs rounded-xl">
                    Daftar
                </a>
            @endauth
        </div>
    </nav>
</div>

<div class="h-20"></div>

{{-- FLASH MESSAGES --}}
@if (session('success'))
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-2">
        <div class="flex items-center gap-2.5 px-4 py-3 text-sm rounded-xl"
             style="background: rgb(var(--success-soft)); border: 1px solid rgb(var(--success) / 0.3); color: rgb(var(--success));">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-2">
        <div class="flex items-center gap-2.5 px-4 py-3 text-sm rounded-xl"
             style="background: rgb(var(--danger-soft)); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    </div>
@endif

<main>
    @yield('content')
</main>

{{-- ============================================================ --}}
{{-- FOOTER — lebih kaya konten --}}
{{-- ============================================================ --}}
<footer class="mt-20 relative overflow-hidden" style="border-top: 1px solid rgb(var(--border-soft));">
    {{-- Ambient gradient --}}
    <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, transparent 0%, rgb(var(--brand) / 0.04) 100%);"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        {{-- TOP ROW --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 mb-10">

            {{-- Brand --}}
            <div class="md:col-span-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-extrabold brand-gradient">
                        E
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-extrabold tracking-tight leading-none" style="color: rgb(var(--text-primary));">EcoTahu</span>
                        <span class="text-[10px] font-semibold tracking-widest uppercase" style="color: rgb(var(--text-muted));">Since 2024</span>
                    </div>
                </div>
                <p class="text-sm leading-relaxed mb-5 max-w-md" style="color: rgb(var(--text-secondary));">
                    Produsen tahu organik pertama di Jember yang mengedepankan kualitas nutrisi dan kelestarian lingkungan hidup. Mendukung SDG 12: Responsible Consumption and Production.
                </p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="pill">
                        <span class="w-1.5 h-1.5 rounded-full" style="background: rgb(var(--brand));"></span>
                        Halal & BPOM
                    </span>
                    <span class="pill-accent">🌿 100% Organik</span>
                    <span class="pill" style="background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);">
                        ♻️ Zero Waste
                    </span>
                </div>
            </div>

            {{-- Halaman --}}
            <div class="md:col-span-2">
                <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color: rgb(var(--text-muted));">Halaman</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="transition" style="color: rgb(var(--text-secondary));">Beranda</a></li>
                    <li><a href="{{ route('user.produk.index') }}" class="transition" style="color: rgb(var(--text-secondary));">Produk Tahu</a></li>
                    <li><a href="{{ route('user.limbah.index') }}" class="transition" style="color: rgb(var(--text-secondary));">Produk Limbah</a></li>
                    <li><a href="{{ route('user.edukasi.index') }}" class="transition" style="color: rgb(var(--text-secondary));">Edukasi</a></li>
                </ul>
            </div>

            {{-- Bantuan --}}
            <div class="md:col-span-2">
                <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color: rgb(var(--text-muted));">Bantuan</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="transition" style="color: rgb(var(--text-secondary));">Cara Pesan</a></li>
                    <li><a href="#" class="transition" style="color: rgb(var(--text-secondary));">Pengiriman</a></li>
                    <li><a href="#" class="transition" style="color: rgb(var(--text-secondary));">Kebijakan Refund</a></li>
                    <li><a href="#" class="transition" style="color: rgb(var(--text-secondary));">FAQ</a></li>
                </ul>
            </div>

            {{-- Kontak --}}
            <div class="md:col-span-3">
                <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color: rgb(var(--text-muted));">Kontak</h4>
                <ul class="space-y-3 text-sm" style="color: rgb(var(--text-secondary));">
                    <li class="flex items-start gap-2">
                        <span class="text-base leading-none mt-0.5">📍</span>
                        <span>Lingkungan Panji, Tegalgede, Kec. Sumbersari, Jember, Jawa Timur 68124</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-base leading-none">📞</span>
                        <span>(0331) 123-4567</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-base leading-none">✉️</span>
                        <span>hello@ecotahu.id</span>
                    </li>
                </ul>

                {{-- Social --}}
                <div class="flex items-center gap-2 mt-5">
                    @php
                        $socials = [
                            ['icon' => 'instagram', 'label' => 'Instagram'],
                            ['icon' => 'whatsapp', 'label' => 'WhatsApp'],
                            ['icon' => 'facebook', 'label' => 'Facebook'],
                        ];
                    @endphp
                    @foreach ($socials as $s)
                        <a href="#" class="w-9 h-9 rounded-xl flex items-center justify-center transition"
                           style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary)); border: 1px solid rgb(var(--border-soft));"
                           title="{{ $s['label'] }}">
                            @if ($s['icon'] === 'instagram')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            @elseif ($s['icon'] === 'whatsapp')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- BOTTOM ROW --}}
        <div class="pt-6 flex flex-wrap items-center justify-between gap-3 text-xs"
             style="border-top: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-muted));">
            <p>© {{ date('Y') }} EcoTahu Indonesia. Semua Hak Dilindungi.</p>
            <div class="flex items-center gap-4">
                <a href="#" class="transition">Syarat & Ketentuan</a>
                <span>·</span>
                <a href="#" class="transition">Privasi</a>
                <span>·</span>
                <a href="#" class="transition">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<script>
    window.toggleTheme = function() {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        try { localStorage.setItem('theme', next); } catch (e) {}
    };

    // Footer links: hover color
    document.querySelectorAll('footer a[href="#"], footer a[href^="{{ url("/") }}"]').forEach(a => {
        a.addEventListener('mouseenter', () => a.style.color = 'rgb(var(--brand))');
        a.addEventListener('mouseleave', () => a.style.color = '');
    });

    /* ============================================================
       GLOBAL CART BADGE
       ============================================================ */
    window.setCartBadge = function(count) {
        const cartLink = document.querySelector('a[title="Keranjang"]');
        if (!cartLink) return;

        let badge = cartLink.querySelector('[data-cart-badge]');

        if (count <= 0) {
            if (badge) badge.remove();
            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.setAttribute('data-cart-badge', '');
            badge.className = 'absolute top-0.5 right-0.5 min-w-[16px] h-[16px] px-1 text-white text-[9px] font-bold rounded-full flex items-center justify-center tnum';
            badge.style.cssText = 'background: var(--gradient-brand); box-shadow: 0 0 0 2px rgb(var(--surface)), 0 2px 6px rgb(var(--brand) / 0.5);';
            cartLink.appendChild(badge);
        }

        badge.textContent = count > 9 ? '9+' : count;

        badge.classList.remove('cart-badge-pop');
        void badge.offsetWidth;
        badge.classList.add('cart-badge-pop');
    };

    window.incrementCartBadge = function() {
        const cartLink = document.querySelector('a[title="Keranjang"]');
        if (!cartLink) return;

        const badge = cartLink.querySelector('[data-cart-badge]');
        const current = badge ? parseInt(badge.textContent.replace('+', '')) || 0 : 0;
        window.setCartBadge(current + 1);
    };
</script>

@stack('scripts')

</body>
</html>