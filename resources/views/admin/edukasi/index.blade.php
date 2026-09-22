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
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Artikel</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Published</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['publish'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Draft</p>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['draft'] }}</p>
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
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Judul</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari artikel..."
                   class="w-full px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua</option>
                <option value="publish" {{ request('status') == 'publish' ? 'selected' : '' }}>Publish</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            Filter
        </button>

        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.edukasi.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
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
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold">Publish</span>
                        @else
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold">Draft</span>
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
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada artikel</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $edukasi->links() }}</div>

@endsection