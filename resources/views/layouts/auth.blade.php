<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EcoTahu')</title>

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

    @vite(['resources/css/app.css', 'resources/js/auth.js'])

    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        body.auth-shell { background-color: rgb(var(--bg-primary)); overflow-x: hidden; }

        /* NAVBAR STYLES */
        .nav-link { position: relative; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 0.75rem; font-size: 0.8125rem; font-weight: 500; border-radius: 0.625rem; color: rgb(var(--text-secondary)); transition: color 0.25s ease, background 0.25s ease; isolation: isolate; z-index: 2; }
        .nav-link:hover { color: rgb(var(--text-primary)); background: rgb(var(--brand) / 0.08); }
        .nav-link.is-active { color: rgb(var(--brand-hover)); font-weight: 600; }
        [data-theme="dark"] .nav-link.is-active { color: rgb(var(--brand)); }
        .nav-link.is-active::before { content: ''; position: absolute; inset: -6px -2px; border-radius: 1rem; background: radial-gradient(ellipse at center, rgb(var(--brand) / 0.25) 0%, rgb(var(--brand) / 0.10) 40%, transparent 75%); filter: blur(8px); z-index: -1; pointer-events: none; }
        [data-theme="dark"] .nav-link.is-active::before { background: radial-gradient(ellipse at center, rgb(var(--brand) / 0.40) 0%, rgb(var(--brand) / 0.18) 40%, transparent 75%); }
        .nav-icon-btn { position: relative; width: 2.25rem; height: 2.25rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.625rem; color: rgb(var(--text-secondary)); background: transparent; border: none; cursor: pointer; transition: color 0.2s ease, background 0.2s ease; z-index: 2; }
        .nav-icon-btn:hover { color: rgb(var(--brand-hover)); background: rgb(var(--brand) / 0.10); }
        .nav-profile-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem 0.25rem 0.25rem; border-radius: 0.625rem; background: transparent; border: none; cursor: pointer; color: rgb(var(--text-secondary)); transition: background 0.2s ease, color 0.2s ease; z-index: 2; }
        .nav-profile-btn:hover { background: rgb(var(--brand) / 0.10); color: rgb(var(--text-primary)); }
        .dropdown-item { display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem 1rem; font-size: 0.8125rem; font-weight: 500; color: rgb(var(--text-primary)); transition: background 0.15s ease; text-decoration: none; }
        .dropdown-item:hover { background: rgb(var(--brand) / 0.08); }
        .dropdown-icon { width: 1.75rem; height: 1.75rem; border-radius: 0.5rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary)); }
        .brand-gradient { background: var(--gradient-brand); box-shadow: 0 2px 8px rgb(var(--brand) / 0.4), inset 0 1px 0 rgb(255 255 255 / 0.25); }
        .btn-daftar-gradient { background: var(--gradient-brand); color: white; font-weight: 600; box-shadow: 0 2px 8px rgb(var(--brand) / 0.35); transition: all 0.2s ease; }
        .btn-daftar-gradient:hover { filter: brightness(1.08); transform: translateY(-1px); box-shadow: 0 6px 16px rgb(var(--brand) / 0.5); }

        /* AMBIENT & EFFECTS */
        body.auth-shell::before { content: ''; position: fixed; inset: 0; z-index: 0; pointer-events: none; background: radial-gradient(ellipse 55% 45% at 0% 0%, rgb(var(--brand) / 0.16), transparent 55%), radial-gradient(ellipse 50% 40% at 100% 100%, rgb(var(--accent) / 0.10), transparent 55%), radial-gradient(ellipse 40% 40% at 100% 0%, rgb(var(--info) / 0.08), transparent 50%); }
        [data-theme="dark"] body.auth-shell::before { background: radial-gradient(ellipse 55% 45% at 0% 0%, rgb(var(--brand) / 0.26), transparent 55%), radial-gradient(ellipse 50% 40% at 100% 100%, rgb(var(--accent) / 0.15), transparent 55%), radial-gradient(ellipse 40% 40% at 100% 0%, rgb(var(--info) / 0.13), transparent 50%); }
        .auth-blob { position: fixed; border-radius: 50%; pointer-events: none; z-index: 1; will-change: transform; transform: translate3d(0, 0, 0); backface-visibility: hidden; }
        .auth-blob-1 { width: 480px; height: 480px; top: -20%; left: -10%; background: radial-gradient(circle, rgb(var(--brand) / 0.5) 0%, transparent 65%); animation: blobMove1 22s ease-in-out infinite; }
        .auth-blob-2 { width: 420px; height: 420px; bottom: -20%; right: -10%; background: radial-gradient(circle, rgb(var(--accent) / 0.4) 0%, transparent 65%); animation: blobMove2 26s ease-in-out infinite; }
        .auth-blob-3 { width: 360px; height: 360px; top: 30%; left: 40%; background: radial-gradient(circle, rgb(var(--info) / 0.3) 0%, transparent 65%); animation: blobMove3 30s ease-in-out infinite; }
        @keyframes blobMove1 { 0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 33% { transform: translate3d(80px, 60px, 0) scale(1.1); } 66% { transform: translate3d(-40px, 100px, 0) scale(0.95); } }
        @keyframes blobMove2 { 0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 50% { transform: translate3d(-100px, -60px, 0) scale(1.15); } }
        @keyframes blobMove3 { 0%, 100% { transform: translate3d(0, 0, 0) scale(1); } 50% { transform: translate3d(60px, 80px, 0) scale(0.9); } }
        .auth-grid { position: fixed; inset: 0; z-index: 1; pointer-events: none; opacity: 0.05; background-image: linear-gradient(rgb(var(--text-primary)) 1px, transparent 1px), linear-gradient(90deg, rgb(var(--text-primary)) 1px, transparent 1px); background-size: 64px 64px; mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%); -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%); }
        [data-theme="dark"] .auth-grid { opacity: 0.07; }
        .auth-noise { position: fixed; inset: 0; z-index: 2; pointer-events: none; opacity: 0.035; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); }
        [data-theme="dark"] .auth-noise { opacity: 0.06; }
        .auth-particles { position: fixed; inset: 0; z-index: 3; pointer-events: none; overflow: hidden; }
        .auth-particles span { position: absolute; bottom: -20px; width: 5px; height: 5px; border-radius: 50%; background: rgb(var(--brand)); box-shadow: 0 0 10px rgb(var(--brand) / 0.8); will-change: transform, opacity; backface-visibility: hidden; animation: particleFloat linear infinite; }
        @keyframes particleFloat { 0% { transform: translate3d(0, 0, 0) scale(0); opacity: 0; } 15% { transform: translate3d(0, -60px, 0) scale(1); opacity: 0.9; } 85% { transform: translate3d(20px, -85vh, 0) scale(0.7); opacity: 0.5; } 100% { transform: translate3d(0, -105vh, 0) scale(0); opacity: 0; } }
        .auth-particles span:nth-child(1)  { left: 5%;  animation-duration: 9s;  animation-delay: 0s; background: rgb(var(--brand)); }
        .auth-particles span:nth-child(2)  { left: 15%; animation-duration: 11s; animation-delay: 1s; background: rgb(var(--accent)); box-shadow: 0 0 10px rgb(var(--accent) / 0.8); }
        .auth-particles span:nth-child(3)  { left: 25%; animation-duration: 13s; animation-delay: 2s; background: rgb(var(--info)); box-shadow: 0 0 10px rgb(var(--info) / 0.7); }
        .auth-particles span:nth-child(4)  { left: 35%; animation-duration: 10s; animation-delay: 3s; }
        .auth-particles span:nth-child(5)  { left: 45%; animation-duration: 12s; animation-delay: 4s; background: rgb(var(--accent)); box-shadow: 0 0 10px rgb(var(--accent) / 0.8); }
        .auth-particles span:nth-child(6)  { left: 55%; animation-duration: 14s; animation-delay: 5s; }
        .auth-particles span:nth-child(7)  { left: 65%; animation-duration: 9s;  animation-delay: 6s; background: rgb(var(--info)); box-shadow: 0 0 10px rgb(var(--info) / 0.7); }
        .auth-particles span:nth-child(8)  { left: 75%; animation-duration: 11s; animation-delay: 7s; }
        .auth-particles span:nth-child(9)  { left: 85%; animation-duration: 13s; animation-delay: 8s; background: rgb(var(--accent)); box-shadow: 0 0 10px rgb(var(--accent) / 0.8); }
        .auth-particles span:nth-child(10) { left: 10%; animation-duration: 12s; animation-delay: 2.5s; }
        .auth-particles span:nth-child(11) { left: 60%; animation-duration: 10s; animation-delay: 5.5s; background: rgb(var(--info)); box-shadow: 0 0 10px rgb(var(--info) / 0.7); }
        .auth-particles span:nth-child(12) { left: 90%; animation-duration: 15s; animation-delay: 8.5s; }

        .auth-cursor-dot, .auth-cursor-ring { position: fixed; top: 0; left: 0; border-radius: 50%; pointer-events: none; will-change: transform; backface-visibility: hidden; transform: translate3d(-100px, -100px, 0); }
        .auth-cursor-dot { width: 6px; height: 6px; z-index: 9999; background: rgb(var(--brand)); box-shadow: 0 0 10px rgb(var(--brand) / 0.7), 0 0 20px rgb(var(--brand) / 0.4); margin-left: -3px; margin-top: -3px; }
        .auth-cursor-ring { width: 28px; height: 28px; z-index: 9998; border: 1.5px solid rgb(var(--brand) / 0.4); margin-left: -14px; margin-top: -14px; transition: width 0.2s ease, height 0.2s ease, margin 0.2s ease, border-color 0.2s ease; }
        body.auth-cursor-hover .auth-cursor-ring { width: 44px; height: 44px; margin-left: -22px; margin-top: -22px; border-color: rgb(var(--accent) / 0.7); }
        body.auth-cursor-hover .auth-cursor-dot { background: rgb(var(--accent)); }
        @media (hover: none), (max-width: 1024px) { .auth-cursor-dot, .auth-cursor-ring { display: none; } }

        .auth-form-glass { position: relative; border-radius: 1.5rem; padding: 2rem; background: rgb(var(--surface) / 0.92); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgb(var(--border-soft)); box-shadow: 0 20px 50px -20px rgb(0 0 0 / 0.15); }
        [data-theme="dark"] .auth-form-glass { background: rgb(var(--surface) / 0.78); border-color: rgb(var(--border)); box-shadow: 0 20px 50px -20px rgb(0 0 0 / 0.5); }

        .auth-input-wrap { position: relative; display: block; }
        .auth-input-wrap > .auth-input-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); pointer-events: none; z-index: 2; color: rgb(var(--text-muted)); transition: color 0.2s ease; }
        .auth-input-wrap:focus-within > .auth-input-icon { color: rgb(var(--brand)); }
        .auth-input-wrap > input { position: relative; z-index: 1; }

        .auth-shell .glass-input { color: rgb(var(--text-primary)); background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); }
        .auth-shell .glass-input::placeholder { color: rgb(var(--text-muted)); }
        .auth-shell .glass-input:focus { border-color: rgb(var(--brand)); box-shadow: 0 0 0 3px rgb(var(--brand) / 0.15); }
        [data-theme="dark"] .auth-shell .glass-input { background: rgb(var(--surface) / 0.9); }

        .text-glow-pulse { animation: textGlowPulse 4s ease-in-out infinite; }
        @keyframes textGlowPulse { 0%, 100% { text-shadow: 0 0 0 transparent; } 50% { text-shadow: 0 0 24px rgb(var(--brand) / 0.32); } }

        .auth-tab-active { background: var(--gradient-brand); color: white; box-shadow: 0 4px 14px rgb(var(--brand) / 0.35); }
        .auth-tab-inactive { color: rgb(var(--text-secondary)); }
        .auth-tab-inactive:hover { color: rgb(var(--text-primary)); background: rgb(var(--surface-hover)); }
        .auth-label { color: rgb(var(--text-primary)); }
        .auth-hint { color: rgb(var(--text-secondary)); }
        .auth-muted { color: rgb(var(--text-muted)); }
        .auth-link { color: rgb(var(--brand)); }
        .auth-link:hover { color: rgb(var(--brand-hover)); }

        @media (prefers-reduced-motion: reduce) {
            .auth-blob, .auth-particles span, .text-glow-pulse { animation: none !important; }
            .auth-blob { display: none; }
        }

        /* FIX LOGO */
        .logo-light, .logo-dark { height: 2.5rem; width: auto; object-fit: contain; transform: scale(1.6); transform-origin: left center; transition: all 0.3s ease; }
        .logo-light { display: block; }
        .logo-dark { display: none; }
        [data-theme="dark"] .logo-light { display: none; }
        [data-theme="dark"] .logo-dark { display: block; }
        @media (min-width: 640px) { .logo-light, .logo-dark { height: 3rem; transform: scale(1.7); } }

        /* FIX FOOTER LOGO */
        .footer-logo-light, .footer-logo-dark { height: 3rem; width: auto; object-fit: contain; transform: scale(2.2); transform-origin: left center; transition: all 0.3s ease; }
        .footer-logo-light { display: block; }
        .footer-logo-dark { display: none; }
        [data-theme="dark"] .footer-logo-light { display: none; }
        [data-theme="dark"] .footer-logo-dark { display: block; }

        /* =========================================
           MOBILE USER SIDEBAR — Persis Admin
           ========================================= */
        .mobile-user-sidebar {
            background: rgb(var(--surface) / 0.72);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgb(var(--border-soft));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.5),
                0 24px 60px -20px rgb(0 0 0 / 0.2),
                0 0 60px -25px rgb(var(--brand) / 0.35);
        }
        [data-theme="dark"] .mobile-user-sidebar {
            background: rgb(var(--surface) / 0.58);
            border-color: rgb(var(--border));
            box-shadow:
                inset 0 1px 0 rgb(255 255 255 / 0.06),
                0 24px 60px -20px rgb(0 0 0 / 0.6),
                0 0 80px -25px rgb(var(--brand) / 0.4);
        }
    </style>
</head>
<body class="font-sans antialiased auth-shell flex flex-col min-h-screen" x-data="{ mobileMenu: false }">

{{-- Effects --}}
<div class="auth-blob auth-blob-1"></div>
<div class="auth-blob auth-blob-2"></div>
<div class="auth-blob auth-blob-3"></div>
<div class="auth-grid"></div>
<div class="auth-noise"></div>
<div class="auth-particles" aria-hidden="true">
    <span></span><span></span><span></span><span></span><span></span><span></span>
    <span></span><span></span><span></span><span></span><span></span><span></span>
</div>
<div class="auth-cursor-dot" id="authCursorDot" aria-hidden="true"></div>
<div class="auth-cursor-ring" id="authCursorRing" aria-hidden="true"></div>

{{-- FLOATING NAVBAR --}}
<div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-1.5rem)] max-w-6xl px-3 sm:px-0">
    <nav class="floating-nav rounded-2xl px-2.5 h-14 flex items-center justify-between">
        
        {{-- LOGO DUAL MODE --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 px-1.5 shrink-0 relative z-10 overflow-visible">
            <img src="{{ asset('images/logo_light.png') }}" alt="EcoTahu Logo" class="logo-light">
            <img src="{{ asset('images/logo_dark.png') }}" alt="EcoTahu Logo" class="logo-dark">
        </a>

        {{-- Desktop Menu --}}
        <div class="hidden md:flex items-center gap-0.5 relative z-10">
            @php
                $navItems = [
                    ['route' => 'home', 'pattern' => 'home', 'label' => 'Beranda', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>'],
                    ['route' => 'user.produk.index', 'pattern' => 'user.produk.*', 'label' => 'Produk Tahu', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>'],
                    ['route' => 'user.limbah.index', 'pattern' => 'user.limbah.*', 'label' => 'Produk Limbah', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.3M7.5 4.205l-.75 1.3M5.106 6.215l-1.15.964M18.894 17.785l-1.15.964M4.543 12.923l-1.41.513M20.868 11.077l-1.41.513"/>'],
                    ['route' => 'user.edukasi.index', 'pattern' => 'user.edukasi.*', 'label' => 'Edukasi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>'],
                ];
            @endphp
            @foreach ($navItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" class="nav-link {{ $active ? 'is-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">{!! $item['icon'] !!}</svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 sm:gap-2 shrink-0 relative z-10">
            <button type="button" onclick="toggleTheme()" class="nav-icon-btn" title="Ganti Tema" aria-label="Ganti Tema">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
            </button>

            @php $cartCount = count(session('cart', [])); @endphp
            <a href="{{ route('user.cart.index') }}" class="nav-icon-btn" title="Keranjang">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                @if ($cartCount > 0)
                    <span data-cart-badge class="absolute top-0.5 right-0.5 min-w-[16px] h-[16px] px-1 text-white text-[9px] font-bold rounded-full flex items-center justify-center tnum" style="background: var(--gradient-brand); box-shadow: 0 0 0 2px rgb(var(--surface)), 0 2px 6px rgb(var(--brand) / 0.5);">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                @endif
            </a>

            <div class="md:hidden">
                <button @click="mobileMenu = true" class="nav-icon-btn" title="Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

            @auth
                <div class="relative hidden sm:block" x-data="{ open: false }" @click.away="open = false" style="z-index: 100;">
                    <button type="button" @click.prevent="open = !open" class="nav-profile-btn" :aria-expanded="open">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white font-bold text-[11px] brand-gradient">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</div>
                        <span class="hidden md:block text-xs font-semibold max-w-[80px] truncate" style="color: rgb(var(--text-primary));">{{ auth()->user()->username }}</span>
                        <svg class="w-3 h-3 opacity-60 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 mt-3 w-60 glass-overlay rounded-2xl overflow-hidden" style="z-index: 200;">
                        <div class="px-4 py-3 flex items-center gap-3" style="border-bottom: 1px solid rgb(var(--border-soft));">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm shrink-0 brand-gradient">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate" style="color: rgb(var(--text-primary));">{{ auth()->user()->username }}</p>
                                <p class="text-xs truncate" style="color: rgb(var(--text-muted));">{{ auth()->user()->email ?? 'Member' }}</p>
                            </div>
                        </div>
                        <div class="py-1.5">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                    <span class="dropdown-icon" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-hover));"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg></span>
                                    <span class="font-semibold" style="color: rgb(var(--brand-hover));">Dashboard Admin</span>
                                </a>
                            @endif
                            <a href="{{ route('user.pesanan.index') }}" class="dropdown-item">
                                <span class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></span>
                                <span>Pesanan Saya</span>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <span class="dropdown-icon"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                                <span>Profil</span>
                            </a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" style="border-top: 1px solid rgb(var(--border-soft));">
                            @csrf
                            <button type="submit" class="dropdown-item w-full text-left" style="color: rgb(var(--danger));">
                                <span class="dropdown-icon" style="background: rgb(var(--danger) / 0.10); color: rgb(var(--danger));"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></span>
                                <span class="font-semibold">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition" style="color: rgb(var(--text-secondary));">Masuk</a>
                <a href="{{ route('register') }}" class="btn-daftar-gradient !py-2 !px-3 sm:!px-4 !text-xs rounded-xl">Daftar</a>
            @endauth
        </div>
    </nav>
</div>

{{-- MOBILE SLIDE-OVER MENU (Floating Card, Right Side) --}}
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-[999] md:hidden" role="dialog" aria-modal="true">
    <div x-show="mobileMenu" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="mobileMenu = false"></div>
    
    <div x-show="mobileMenu" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="translate-x-[120%]" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-[120%]" class="mobile-user-sidebar w-64 flex flex-col fixed h-[calc(100vh-2rem)] top-4 right-4 z-[1000] rounded-3xl">
        
        {{-- Header --}}
        <div class="h-16 flex items-center justify-between px-5 flex-shrink-0" style="border-bottom: 1px solid rgb(var(--border-soft));">
            <span class="font-extrabold text-lg tracking-tight" style="color: rgb(var(--text-primary));">Menu</span>
            <button @click="mobileMenu = false" class="nav-icon-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $mobileNavItems = [
                    ['route' => 'home', 'pattern' => 'home', 'label' => 'Beranda', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>'],
                    ['route' => 'user.produk.index', 'pattern' => 'user.produk.*', 'label' => 'Produk Tahu', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>'],
                    ['route' => 'user.limbah.index', 'pattern' => 'user.limbah.*', 'label' => 'Produk Limbah', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.3M7.5 4.205l-.75 1.3M5.106 6.215l-1.15.964M18.894 17.785l-1.15.964M4.543 12.923l-1.41.513M20.868 11.077l-1.41.513"/>'],
                    ['route' => 'user.edukasi.index', 'pattern' => 'user.edukasi.*', 'label' => 'Edukasi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>'],
                ];
            @endphp
            @foreach ($mobileNavItems as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp
                <a href="{{ route($item['route']) }}" class="admin-nav-item {{ $active ? 'is-active' : '' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">{!! $item['icon'] !!}</svg>
                    <span class="flex-1 text-left">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- Footer --}}
        <div class="p-3 space-y-1" style="border-top: 1px solid rgb(var(--border-soft));">
            <div class="flex gap-2">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-xl font-bold transition" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); font-size: 0.8rem;">Masuk</a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 rounded-xl font-bold btn-daftar-gradient" style="font-size: 0.8rem;">Daftar</a>
            </div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<main class="flex-grow flex flex-col relative w-full" style="z-index: 10;">
    <div class="flex-grow flex items-center justify-center pt-28 pb-16 w-full">
        <div class="w-full max-w-7xl mx-auto px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_450px] gap-12 lg:gap-16 items-center">
                
                {{-- LEFT: BRANDING --}}
                <div class="hidden lg:flex flex-col justify-center items-start relative">
                    <div class="relative z-10">
                        <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-[1.1] tracking-tight mb-6 text-glow-pulse" style="color: rgb(var(--text-primary));">
                            @yield('brand-title')
                        </h1>
                        <p class="text-lg lg:text-xl leading-relaxed max-w-lg text-pretty mb-8" style="color: rgb(var(--text-secondary));">
                            @yield('brand-subtitle')
                        </p>
                        @yield('brand-extra')
                    </div>

                    <div class="relative z-10 flex items-center gap-4 text-xs mt-10" style="color: rgb(var(--text-muted));">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Keamanan Terjamin
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Produk Halal
                        </span>
                    </div>
                </div>

                {{-- RIGHT: FORM --}}
                <div class="flex justify-center lg:justify-end relative w-full">
                    <div class="w-full max-w-[450px]">
                        @yield('form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- FOOTER --}}
<footer class="mt-20 relative overflow-hidden" style="border-top: 1px solid rgb(var(--border-soft));">
    <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, transparent 0%, rgb(var(--brand) / 0.04) 100%);"></div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 mb-10">
            <div class="md:col-span-5">
                <div class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo_light.png') }}" alt="EcoTahu Logo" class="footer-logo-light">
                    <img src="{{ asset('images/logo_dark.png') }}" alt="EcoTahu Logo" class="footer-logo-dark">
                </div>
                <p class="text-sm leading-relaxed mb-5 max-w-md" style="color: rgb(var(--text-secondary));">Produsen tahu organik pertama di Jember yang mengedepankan kualitas nutrisi dan kelestarian lingkungan hidup. Mendukung SDG 12: Responsible Consumption and Production.</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="pill"><span class="w-1.5 h-1.5 rounded-full" style="background: rgb(var(--brand));"></span> Halal & BPOM</span>
                    <span class="pill-accent">🌿 100% Organik</span>
                    <span class="pill" style="background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);">♻️ Zero Waste</span>
                </div>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color: rgb(var(--text-muted));">Halaman</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Beranda</a></li>
                    <li><a href="{{ route('user.produk.index') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Produk Tahu</a></li>
                    <li><a href="{{ route('user.limbah.index') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Produk Limbah</a></li>
                    <li><a href="{{ route('user.edukasi.index') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Edukasi</a></li>
                </ul>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color: rgb(var(--text-muted));">Bantuan</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('halaman.cara-pesan') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Cara Pesan</a></li>
                    <li><a href="{{ route('halaman.pengiriman') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Pengiriman</a></li>
                    <li><a href="{{ route('halaman.refund') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">Kebijakan Refund</a></li>
                    <li><a href="{{ route('halaman.faq') }}" class="transition hover:text-emerald-600" style="color: rgb(var(--text-secondary));">FAQ</a></li>
                </ul>
            </div>
            <div class="md:col-span-3">
                <h4 class="text-xs font-bold uppercase tracking-widest mb-4" style="color: rgb(var(--text-muted));">Kontak</h4>
                <ul class="space-y-3 text-sm" style="color: rgb(var(--text-secondary));">
                    <li class="flex items-start gap-2"><span class="text-base leading-none mt-0.5">📍</span><span>Lingkungan Panji, Tegalgede, Kec. Sumbersari, Jember, Jawa Timur 68124</span></li>
                    <li class="flex items-center gap-2"><span class="text-base leading-none">📞</span><span>(0331) 123-4567</span></li>
                    <li class="flex items-center gap-2"><span class="text-base leading-none">✉️</span><span>hello@ecotahu.id</span></li>
                </ul>
                <div class="flex items-center gap-2 mt-5">
                    @php $socials = [['icon' => 'instagram', 'label' => 'Instagram'], ['icon' => 'whatsapp', 'label' => 'WhatsApp'], ['icon' => 'facebook', 'label' => 'Facebook']]; @endphp
                    @foreach ($socials as $s)
                        <a href="#" class="w-9 h-9 rounded-xl flex items-center justify-center transition" style="background: rgb(var(--surface-hover)); color: rgb(var(--text-secondary)); border: 1px solid rgb(var(--border-soft));" title="{{ $s['label'] }}">
                            @if ($s['icon'] === 'instagram') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            @elseif ($s['icon'] === 'whatsapp') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                            @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="pt-6 flex flex-wrap items-center justify-between gap-3 text-xs" style="border-top: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-muted));">
            <p>© {{ date('Y') }} EcoTahu Indonesia. Semua Hak Dilindungi.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('halaman.snk') }}" class="transition hover:text-emerald-600">Syarat & Ketentuan</a><span>·</span>
                <a href="{{ route('halaman.privasi') }}" class="transition hover:text-emerald-600">Privasi</a><span>·</span>
                <a href="{{ route('halaman.sitemap') }}" class="transition hover:text-emerald-600">Sitemap</a>
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

    (function() {
        'use strict';
        const isTouch = window.matchMedia('(hover: none)').matches;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!isTouch && !reduceMotion) {
            const dot = document.getElementById('authCursorDot');
            const ring = document.getElementById('authCursorRing');
            if (dot && ring) {
                let mx = -100, my = -100, dx = -100, dy = -100, rx = -100, ry = -100, rafId = null;
                document.addEventListener('mousemove', (e) => {
                    mx = e.clientX; my = e.clientY;
                    if (!rafId) rafId = requestAnimationFrame(tick);
                }, { passive: true });
                function tick() {
                    dx += (mx - dx) * 0.35; dy += (my - dy) * 0.35;
                    rx += (mx - rx) * 0.15; ry += (my - ry) * 0.15;
                    dot.style.transform = `translate3d(${dx}px, ${dy}px, 0)`;
                    ring.style.transform = `translate3d(${rx}px, ${ry}px, 0)`;
                    const moving = Math.abs(mx - dx) > 0.3 || Math.abs(my - dy) > 0.3 || Math.abs(mx - rx) > 0.3 || Math.abs(my - ry) > 0.3;
                    rafId = moving ? requestAnimationFrame(tick) : null;
                }
                document.addEventListener('mouseover', (e) => {
                    if (e.target.closest('a, button, input, select, textarea, label')) document.body.classList.add('auth-cursor-hover');
                });
                document.addEventListener('mouseout', (e) => {
                    if (e.target.closest('a, button, input, select, textarea, label')) document.body.classList.remove('auth-cursor-hover');
                });
            }
        }

        if (!isTouch && !reduceMotion) {
            document.querySelectorAll('.magnetic-btn').forEach(btn => {
                let targetX = 0, targetY = 0, currentX = 0, currentY = 0, rafId = null;
                btn.addEventListener('mousemove', (e) => {
                    const r = btn.getBoundingClientRect();
                    targetX = (e.clientX - r.left - r.width / 2) * 0.2;
                    targetY = (e.clientY - r.top - r.height / 2) * 0.2;
                    if (!rafId) rafId = requestAnimationFrame(loop);
                }, { passive: true });
                btn.addEventListener('mouseleave', () => {
                    targetX = 0; targetY = 0;
                    if (!rafId) rafId = requestAnimationFrame(loop);
                });
                function loop() {
                    currentX += (targetX - currentX) * 0.2; currentY += (targetY - currentY) * 0.2;
                    btn.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;
                    const moving = Math.abs(currentX - targetX) > 0.1 || Math.abs(currentY - targetY) > 0.1;
                    rafId = moving ? requestAnimationFrame(loop) : null;
                }
            });
        }
    })();
</script>

@stack('scripts')

</body>
</html>