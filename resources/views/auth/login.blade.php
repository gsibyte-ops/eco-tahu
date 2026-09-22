<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — EcoTahu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- LEFT: BRANDING --}}
    <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 p-12 relative overflow-hidden">

        {{-- Decorative circles --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-48 translate-x-48"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full translate-y-40 -translate-x-40"></div>
        <div class="absolute top-1/3 left-1/4 w-40 h-40 bg-amber-300/20 rounded-full blur-3xl"></div>

        {{-- Logo --}}
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-emerald-600 font-bold text-lg">E</div>
                <span class="text-xl font-bold text-white">EcoTahu</span>
            </a>
        </div>

        {{-- Content --}}
        <div class="relative z-10">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                Tahu Sehat,
                <br>
                Lingkungan Kuat
            </h1>
            <p class="text-emerald-100 text-lg leading-relaxed max-w-md mb-8">
                Nikmati kelezatan protein nabati yang diproses secara higienis dan ramah lingkungan.
            </p>

            <div class="flex items-center gap-4">
                <div class="w-32 h-32 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20">
                    <div class="text-6xl">🥛</div>
                </div>
                <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 mt-8">
                    <div class="text-4xl">🌿</div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10">
            <div class="flex items-center gap-4 text-emerald-200 text-xs">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Keamanan Terjamin
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Produk Halal
                </span>
            </div>
            <p class="text-emerald-300/70 text-xs mt-6">
                © {{ date('Y') }} EcoTahu Indonesia. Pelopor Tahu Organik Berkelanjutan.
            </p>
        </div>
    </div>

    {{-- RIGHT: FORM --}}
    <div class="flex items-center justify-center p-6 lg:p-12 bg-gray-50">
        <div class="w-full max-w-md">

            {{-- Mobile Logo --}}
            <div class="lg:hidden mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-lg">E</div>
                    <span class="text-xl font-bold text-gray-800">EcoTahu</span>
                </a>
            </div>

            {{-- Tabs --}}
            <div class="flex justify-center gap-2 mb-8">
                <a href="{{ route('login') }}"
                   class="px-8 py-3 bg-white border-2 border-emerald-600 text-emerald-600 font-bold text-sm rounded-xl shadow-sm">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="px-8 py-3 bg-transparent text-gray-400 font-medium text-sm rounded-xl hover:text-gray-600 transition">
                    Daftar
                </a>
            </div>

            {{-- Heading --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang!</h2>
                <p class="text-sm text-gray-500">Silakan masuk untuk melanjutkan pesanan Anda.</p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email atau No. HP</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="Masukkan email atau nomor HP"
                               class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50/50">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">
                                Lupa password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required
                               placeholder="Masukkan kata sandi"
                               class="w-full pl-10 pr-12 py-3 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50/50">
                        <button type="button" @click="show = !show"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition">
                    Login
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs font-medium text-gray-400">ATAU</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Register Link --}}
            <a href="{{ route('register') }}"
               class="block w-full text-center py-3 bg-white border-2 border-emerald-600 hover:bg-emerald-50 text-emerald-600 font-bold rounded-xl transition">
                Daftar Akun
            </a>

            {{-- Trust Badges --}}
            <div class="flex items-center justify-center gap-6 mt-8">
                <div class="flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Keamanan Terjamin
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Produk Halal
                </div>
            </div>

            {{-- Footer Links --}}
            <div class="flex items-center justify-center gap-4 mt-6 text-xs text-gray-400">
                <a href="#" class="hover:text-gray-600">Bantuan</a>
                <span>·</span>
                <a href="#" class="hover:text-gray-600">Syarat & Ketentuan</a>
                <span>·</span>
                <a href="#" class="hover:text-gray-600">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>