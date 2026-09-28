@extends('layouts.admin')
@section('title', 'Kelola Edukasi')
@section('page-title', 'Kelola Edukasi')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Statistik --}}
<div class="grid grid-cols-3 gap-4 mb-6">

    {{-- Total Artikel (Putih + Accent Sky) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1" style="background: linear-gradient(180deg, #38bdf8 0%, #0284c7 100%);"></div>
        <div class="pl-3">
            <p class="text-sm font-semibold text-gray-500 mb-1">Total Artikel</p>
            <p class="text-3xl font-bold" style="color: #0284c7;">{{ $stats['total'] }}</p>
        </div>
    </div>

    {{-- Published (Gradient Emerald) --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #34d399 0%, #10b981 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Published</p>
        <p class="text-3xl font-bold">{{ $stats['publish'] }}</p>
    </div>

    {{-- Draft (Gradient Amber) --}}
    <div class="rounded-2xl p-4 shadow-md text-white"
         style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
        <p class="text-sm font-semibold opacity-95 mb-1">Draft</p>
        <p class="text-3xl font-bold">{{ $stats['draft'] }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Daftar Artikel Edukasi</h2>
        <p class="text-sm text-gray-500">Kelola artikel edukasi seputar tahu dan lingkungan.</p>
    </div>
    <a href="{{ route('admin.edukasi.create') }}"
       class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
        + Tambah Artikel
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6" x-data="{ statusOpen: false }">
    <form method="GET" class="flex flex-wrap gap-3 items-end">

        {{-- Search --}}
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Artikel</label>
            <div class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari judul artikel..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        {{-- Dropdown Status --}}
        <div style="width: 180px;" class="relative" @click.away="statusOpen = false">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
            <button type="button" @click="statusOpen = !statusOpen"
                    class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                <span class="text-gray-700 whitespace-nowrap" id="statusLabel">
                    @php
                        $statusLabel = match(request('status')) {
                            'publish' => 'Publish',
                            'draft' => 'Draft',
                            default => 'Semua Status',
                        };
                    @endphp
                    {{ $statusLabel }}
                </span>
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="statusOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border border-gray-100 shadow-lg overflow-hidden">
                @foreach (['' => 'Semua Status', 'publish' => 'Publish', 'draft' => 'Draft'] as $val => $label)
                    <button type="button"
                            data-value="{{ $val }}"
                            data-label="{{ $label }}"
                            onclick="selectStatusFilter(this)"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-50 transition
                                   {{ request('status') == $val ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                        <span>{{ $label }}</span>
                        @if (request('status') == $val)
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
        </div>

        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition whitespace-nowrap">
            Filter
        </button>

        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.edukasi.index') }}"
               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60 border-b border-gray-100">
                <th class="px-6 py-3">Thumbnail</th>
                <th class="px-6 py-3">Judul</th>
                <th class="px-6 py-3">Penulis</th>
                <th class="px-6 py-3">Tanggal</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($edukasi as $e)
                <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                    <td class="px-6 py-4">
                        @if ($e->thumbnail)
                            <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                 class="w-16 h-12 rounded-lg object-cover border border-gray-100">
                        @else
                            <div class="w-16 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No img</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-800">{{ $e->judul }}</p>
                        <p class="text-xs text-gray-500">{{ Str::limit($e->konten, 60) }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $e->user->username ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $e->tanggal_mengunggah->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        @if ($e->status === 'publish')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#d1fae5; color:#047857;">Publish</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background:#fef3c7; color:#b45309;">Draft</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.edukasi.edit', $e->id) }}" class="text-blue-600 hover:underline text-sm font-medium">Edit</a>
                        <form action="{{ route('admin.edukasi.destroy', $e->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus artikel ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-sm font-medium">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                    @if (request('q'))
                        Tidak ada artikel dengan kata kunci "<span class="font-semibold text-gray-600">{{ request('q') }}</span>"
                    @else
                        Belum ada artikel
                    @endif
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $edukasi->links() }}</div>

@endsection

@push('scripts')
<script>
    function selectStatusFilter(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        document.querySelector('[x-data]').__x.$data.statusOpen = false;
    }
</script>
@endpush