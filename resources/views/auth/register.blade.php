<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar — EcoTahu</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- LEFT: BRANDING --}}
    <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-800 p-12 relative overflow-hidden">

        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-48 translate-x-48"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/5 rounded-full translate-y-40 -translate-x-40"></div>
        <div class="absolute top-1/3 left-1/4 w-40 h-40 bg-amber-300/20 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-emerald-600 font-bold text-lg">E</div>
                <span class="text-xl font-bold text-white">EcoTahu</span>
            </a>
        </div>

        <div class="relative z-10">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                Gabung &
                <br>
                Dapatkan Promo
            </h1>
            <p class="text-emerald-100 text-lg leading-relaxed max-w-md mb-8">
                Daftar sekarang dan dapatkan diskon <strong class="text-amber-300">20%</strong> untuk pembelian pertama Anda.
            </p>

            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 border border-white/20 max-w-md">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0 text-lg">🎁</div>
                    <div>
                        <p class="text-white font-semibold text-sm">Diskon 20% First Order</p>
                        <p class="text-emerald-200 text-xs">Berlaku untuk pembelian pertama</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0 text-lg">🚚</div>
                    <div>
                        <p class="text-white font-semibold text-sm">Ongkir Terjangkau</p>
                        <p class="text-emerald-200 text-xs">Cuma Rp 5.000/km</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0 text-lg">🌿</div>
                    <div>
                        <p class="text-white font-semibold text-sm">Produk 100% Organik</p>
                        <p class="text-emerald-200 text-xs">Tanpa pengawet, tanpa bahan kimia</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-10">
            <p class="text-emerald-300/70 text-xs">
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
                   class="px-8 py-3 bg-transparent text-gray-400 font-medium text-sm rounded-xl hover:text-gray-600 transition">
                    Login
                </a>
                <a href="{{ route('register') }}"
                   class="px-8 py-3 bg-white border-2 border-emerald-600 text-emerald-600 font-bold text-sm rounded-xl shadow-sm">
                    Daftar
                </a>
            </div>

            {{-- Heading --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Buat Akun Baru</h2>
                <p class="text-sm text-gray-500">Isi data di bawah untuk mulai berbelanja di EcoTahu.</p>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                               placeholder="Masukkan nama lengkap"
                               class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50/50">
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               placeholder="nama@email.com"
                               class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50/50">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No. Telepon --}}
                <div>
                    <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50/50">
                    </div>
                    @error('no_telepon')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required
                               placeholder="Minimal 8 karakter"
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

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               placeholder="Ulangi password"
                               class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-gray-50/50">
                    </div>
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2">
                    <input id="terms" type="checkbox" required
                           class="w-4 h-4 mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="terms" class="text-xs text-gray-600 leading-relaxed">
                        Saya setuju dengan
                        <a href="#" class="text-emerald-600 hover:underline font-medium">Syarat & Ketentuan</a>
                        dan
                        <a href="#" class="text-emerald-600 hover:underline font-medium">Kebijakan Privasi</a> EcoTahu.
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition">
                    Daftar Sekarang
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs font-medium text-gray-400">ATAU</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            {{-- Login Link --}}
            <a href="{{ route('login') }}"
               class="block w-full text-center py-3 bg-white border-2 border-emerald-600 hover:bg-emerald-50 text-emerald-600 font-bold rounded-xl transition">
                Sudah Punya Akun? Masuk
            </a>

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