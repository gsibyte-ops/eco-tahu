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

        /* ============================================================
           AMBIENT BASE — static
           ============================================================ */
        body.auth-shell::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 55% 45% at 0% 0%, rgb(var(--brand) / 0.16), transparent 55%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgb(var(--accent) / 0.10), transparent 55%),
                radial-gradient(ellipse 40% 40% at 100% 0%, rgb(var(--info) / 0.08), transparent 50%);
        }
        [data-theme="dark"] body.auth-shell::before {
            background:
                radial-gradient(ellipse 55% 45% at 0% 0%, rgb(var(--brand) / 0.26), transparent 55%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgb(var(--accent) / 0.15), transparent 55%),
                radial-gradient(ellipse 40% 40% at 100% 0%, rgb(var(--info) / 0.13), transparent 50%);
        }

        /* ============================================================
           AURORA — 3 blob CSS-only
           ============================================================ */
        .auth-blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            will-change: transform;
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
        }
        .auth-blob-1 {
            width: 480px; height: 480px;
            top: -20%; left: -10%;
            background: radial-gradient(circle, rgb(var(--brand) / 0.5) 0%, transparent 65%);
            animation: blobMove1 22s ease-in-out infinite;
        }
        .auth-blob-2 {
            width: 420px; height: 420px;
            bottom: -20%; right: -10%;
            background: radial-gradient(circle, rgb(var(--accent) / 0.4) 0%, transparent 65%);
            animation: blobMove2 26s ease-in-out infinite;
        }
        .auth-blob-3 {
            width: 360px; height: 360px;
            top: 30%; left: 40%;
            background: radial-gradient(circle, rgb(var(--info) / 0.3) 0%, transparent 65%);
            animation: blobMove3 30s ease-in-out infinite;
        }
        @keyframes blobMove1 {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            33%      { transform: translate3d(80px, 60px, 0) scale(1.1); }
            66%      { transform: translate3d(-40px, 100px, 0) scale(0.95); }
        }
        @keyframes blobMove2 {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50%      { transform: translate3d(-100px, -60px, 0) scale(1.15); }
        }
        @keyframes blobMove3 {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50%      { transform: translate3d(60px, 80px, 0) scale(0.9); }
        }

        /* ============================================================
           GRID + NOISE
           ============================================================ */
        .auth-grid {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            opacity: 0.05;
            background-image:
                linear-gradient(rgb(var(--text-primary)) 1px, transparent 1px),
                linear-gradient(90deg, rgb(var(--text-primary)) 1px, transparent 1px);
            background-size: 64px 64px;
            mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 20%, transparent 75%);
        }
        [data-theme="dark"] .auth-grid { opacity: 0.07; }

        .auth-noise {
            position: fixed;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            opacity: 0.035;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }
        [data-theme="dark"] .auth-noise { opacity: 0.06; }

        /* ============================================================
           PARTICLES — 12 span CSS-only
           ============================================================ */
        .auth-particles {
            position: fixed;
            inset: 0;
            z-index: 3;
            pointer-events: none;
            overflow: hidden;
        }
        .auth-particles span {
            position: absolute;
            bottom: -20px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: rgb(var(--brand));
            box-shadow: 0 0 10px rgb(var(--brand) / 0.8);
            will-change: transform, opacity;
            backface-visibility: hidden;
            animation: particleFloat linear infinite;
        }
        @keyframes particleFloat {
            0%   { transform: translate3d(0, 0, 0) scale(0); opacity: 0; }
            15%  { transform: translate3d(0, -60px, 0) scale(1); opacity: 0.9; }
            85%  { transform: translate3d(20px, -85vh, 0) scale(0.7); opacity: 0.5; }
            100% { transform: translate3d(0, -105vh, 0) scale(0); opacity: 0; }
        }
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

        /* ============================================================
           CURSOR FOLLOWER
           ============================================================ */
        .auth-cursor-dot,
        .auth-cursor-ring {
            position: fixed;
            top: 0;
            left: 0;
            border-radius: 50%;
            pointer-events: none;
            will-change: transform;
            backface-visibility: hidden;
            transform: translate3d(-100px, -100px, 0);
        }
        .auth-cursor-dot {
            width: 6px;
            height: 6px;
            z-index: 9999;
            background: rgb(var(--brand));
            box-shadow: 0 0 10px rgb(var(--brand) / 0.7), 0 0 20px rgb(var(--brand) / 0.4);
            margin-left: -3px;
            margin-top: -3px;
        }
        .auth-cursor-ring {
            width: 28px;
            height: 28px;
            z-index: 9998;
            border: 1.5px solid rgb(var(--brand) / 0.4);
            margin-left: -14px;
            margin-top: -14px;
            transition: width 0.2s ease, height 0.2s ease, margin 0.2s ease, border-color 0.2s ease;
        }
        body.auth-cursor-hover .auth-cursor-ring {
            width: 44px;
            height: 44px;
            margin-left: -22px;
            margin-top: -22px;
            border-color: rgb(var(--accent) / 0.7);
        }
        body.auth-cursor-hover .auth-cursor-dot {
            background: rgb(var(--accent));
        }
        @media (hover: none), (max-width: 1024px) {
            .auth-cursor-dot, .auth-cursor-ring { display: none; }
        }

        /* ============================================================
           FORM GLASS — FIXED (NO PLASTIC)
           ============================================================ */
        .auth-form-glass {
            position: relative;
            border-radius: 1.5rem;
            padding: 2rem;
            background: rgb(var(--surface) / 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgb(var(--border-soft));
            box-shadow: 0 20px 50px -20px rgb(0 0 0 / 0.15);
        }
        [data-theme="dark"] .auth-form-glass {
            background: rgb(var(--surface) / 0.78);
            border-color: rgb(var(--border));
            box-shadow: 0 20px 50px -20px rgb(0 0 0 / 0.5);
        }

        /* ============================================================
           INPUT WRAPPER — icon position FIXED
           ============================================================ */
        .auth-input-wrap {
            position: relative;
            display: block;
        }
        .auth-input-wrap > .auth-input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            z-index: 2;
            color: rgb(var(--text-muted));
            transition: color 0.2s ease;
        }
        .auth-input-wrap:focus-within > .auth-input-icon {
            color: rgb(var(--brand));
        }
        .auth-input-wrap > input {
            position: relative;
            z-index: 1;
        }

        /* ============================================================
           INPUT
           ============================================================ */
        .auth-shell .glass-input {
            color: rgb(var(--text-primary));
            background: rgb(var(--surface));
            border: 1px solid rgb(var(--border));
        }
        .auth-shell .glass-input::placeholder {
            color: rgb(var(--text-muted));
        }
        .auth-shell .glass-input:focus {
            border-color: rgb(var(--brand));
            box-shadow: 0 0 0 3px rgb(var(--brand) / 0.15);
        }
        [data-theme="dark"] .auth-shell .glass-input {
            background: rgb(var(--surface) / 0.9);
        }

        /* ============================================================
           TEXT GLOW PULSE
           ============================================================ */
        .text-glow-pulse {
            animation: textGlowPulse 4s ease-in-out infinite;
        }
        @keyframes textGlowPulse {
            0%, 100% { text-shadow: 0 0 0 transparent; }
            50% { text-shadow: 0 0 24px rgb(var(--brand) / 0.32); }
        }

        /* ============================================================
           TAB BUTTONS
           ============================================================ */
        .auth-tab-active {
            background: var(--gradient-brand);
            color: white;
            box-shadow: 0 4px 14px rgb(var(--brand) / 0.35);
        }
        .auth-tab-inactive { color: rgb(var(--text-secondary)); }
        .auth-tab-inactive:hover {
            color: rgb(var(--text-primary));
            background: rgb(var(--surface-hover));
        }

        .auth-label { color: rgb(var(--text-primary)); }
        .auth-hint { color: rgb(var(--text-secondary)); }
        .auth-muted { color: rgb(var(--text-muted)); }
        .auth-link { color: rgb(var(--brand)); }
        .auth-link:hover { color: rgb(var(--brand-hover)); }

        /* ============================================================
           REDUCED MOTION
           ============================================================ */
        @media (prefers-reduced-motion: reduce) {
            .auth-blob,
            .auth-particles span,
            .text-glow-pulse { animation: none !important; }
            .auth-blob { display: none; }
        }
    </style>
</head>
<body class="font-sans antialiased auth-shell">

{{-- Aurora blobs --}}
<div class="auth-blob auth-blob-1"></div>
<div class="auth-blob auth-blob-2"></div>
<div class="auth-blob auth-blob-3"></div>

{{-- Grid + noise --}}
<div class="auth-grid"></div>
<div class="auth-noise"></div>

{{-- Particles --}}
<div class="auth-particles" aria-hidden="true">
    <span></span><span></span><span></span><span></span>
    <span></span><span></span><span></span><span></span>
    <span></span><span></span><span></span><span></span>
</div>

{{-- Cursor --}}
<div class="auth-cursor-dot" id="authCursorDot" aria-hidden="true"></div>
<div class="auth-cursor-ring" id="authCursorRing" aria-hidden="true"></div>

{{-- Theme toggle --}}
<button type="button" onclick="toggleTheme()"
        class="fixed top-4 right-4 z-50 w-10 h-10 rounded-xl flex items-center justify-center transition"
        style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-secondary)); box-shadow: 0 4px 12px rgb(0 0 0 / 0.08);"
        title="Ganti Tema">
    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
</button>

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 relative" style="z-index: 10;">

    {{-- LEFT: BRANDING --}}
    <div class="hidden lg:flex flex-col justify-between p-12 relative overflow-hidden">
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white font-extrabold text-lg transition group-hover:scale-110"
                     style="background: var(--gradient-brand); box-shadow: 0 8px 24px rgb(var(--brand) / 0.5);">E</div>
                <span class="text-xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">EcoTahu</span>
            </a>
        </div>

        <div class="relative z-10">
            <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight mb-4 text-glow-pulse" style="color: rgb(var(--text-primary));">
                @yield('brand-title')
            </h1>
            <p class="text-lg leading-relaxed max-w-md text-pretty mb-8" style="color: rgb(var(--text-secondary));">
                @yield('brand-subtitle')
            </p>
            @yield('brand-extra')
        </div>

        <div class="relative z-10 flex items-center gap-4 text-xs" style="color: rgb(var(--text-muted));">
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
    <div class="flex items-center justify-center p-6 lg:p-12 relative">
        <div class="w-full max-w-md">
            <div class="lg:hidden mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-extrabold text-lg"
                         style="background: var(--gradient-brand); box-shadow: 0 4px 14px rgb(var(--brand) / 0.5);">E</div>
                    <span class="text-xl font-extrabold" style="color: rgb(var(--text-primary));">EcoTahu</span>
                </a>
            </div>

            @yield('form')
        </div>
    </div>
</div>

<script>
    (function() {
        'use strict';
        const isTouch = window.matchMedia('(hover: none)').matches;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Cursor follower
        if (!isTouch && !reduceMotion) {
            const dot = document.getElementById('authCursorDot');
            const ring = document.getElementById('authCursorRing');

            if (dot && ring) {
                let mx = -100, my = -100;
                let dx = -100, dy = -100;
                let rx = -100, ry = -100;
                let rafId = null;

                document.addEventListener('mousemove', (e) => {
                    mx = e.clientX;
                    my = e.clientY;
                    if (!rafId) rafId = requestAnimationFrame(tick);
                }, { passive: true });

                function tick() {
                    dx += (mx - dx) * 0.35;
                    dy += (my - dy) * 0.35;
                    rx += (mx - rx) * 0.15;
                    ry += (my - ry) * 0.15;

                    dot.style.transform = `translate3d(${dx}px, ${dy}px, 0)`;
                    ring.style.transform = `translate3d(${rx}px, ${ry}px, 0)`;

                    const moving = Math.abs(mx - dx) > 0.3 || Math.abs(my - dy) > 0.3
                                || Math.abs(mx - rx) > 0.3 || Math.abs(my - ry) > 0.3;
                    rafId = moving ? requestAnimationFrame(tick) : null;
                }

                document.addEventListener('mouseover', (e) => {
                    if (e.target.closest('a, button, input, select, textarea, label')) {
                        document.body.classList.add('auth-cursor-hover');
                    }
                });
                document.addEventListener('mouseout', (e) => {
                    if (e.target.closest('a, button, input, select, textarea, label')) {
                        document.body.classList.remove('auth-cursor-hover');
                    }
                });
            }
        }

        // Magnetic button
        if (!isTouch && !reduceMotion) {
            document.querySelectorAll('.magnetic-btn').forEach(btn => {
                let targetX = 0, targetY = 0;
                let currentX = 0, currentY = 0;
                let rafId = null;

                btn.addEventListener('mousemove', (e) => {
                    const r = btn.getBoundingClientRect();
                    targetX = (e.clientX - r.left - r.width / 2) * 0.2;
                    targetY = (e.clientY - r.top - r.height / 2) * 0.2;
                    if (!rafId) rafId = requestAnimationFrame(loop);
                }, { passive: true });

                btn.addEventListener('mouseleave', () => {
                    targetX = 0;
                    targetY = 0;
                    if (!rafId) rafId = requestAnimationFrame(loop);
                });

                function loop() {
                    currentX += (targetX - currentX) * 0.2;
                    currentY += (targetY - currentY) * 0.2;
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