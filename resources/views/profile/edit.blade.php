@extends('layouts.user')
@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Beranda</a>
        <span class="mx-2 text-emerald-400">/</span>
        <span class="text-gray-800 font-medium">Profil</span>
    </nav>

    <div class="mb-8">
        <h1 class="text-4xl font-display text-gray-800">Profil Saya</h1>
        <p class="text-gray-500 mt-2">Kelola informasi akun, keamanan, dan preferensi Anda.</p>
    </div>

    <div class="space-y-6">
        <div class="glass-card p-6 lg:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="glass-card p-6 lg:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="glass-card p-6 lg:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection