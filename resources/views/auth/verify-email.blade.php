<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email — EcoTahu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <div class="text-7xl mb-6">✉️</div>
            <h1 class="text-4xl font-display text-white leading-tight mb-4">
                Verifikasi
                <br>Email Anda.
            </h1>
            <p class="text-emerald-50 text-lg leading-relaxed max-w-md text-pretty">
                Kami sudah mengirimkan link verifikasi ke email Anda. Klik link tersebut untuk mengaktifkan akun.
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

            <div class="glass-card p-8">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-500/40">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h2 class="text-2xl font-display text-gray-800 mb-2">Cek Email Anda</h2>
                    <p class="text-sm text-gray-500 text-pretty">
                        Terima kasih sudah mendaftar! Sebelum memulai, silakan verifikasi email Anda dengan mengklik link yang baru saja kami kirim.
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="glass rounded-xl px-4 py-3 mb-4 text-sm text-emerald-700 border-emerald-300/50">
                        ✓ Link verifikasi baru sudah dikirim ke email Anda.
                    </div>
                @endif

                <div class="space-y-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="glass-btn-primary w-full py-3">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="glass-btn w-full py-3">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>