@extends('layouts.admin')
@section('title', 'Kelola Limbah')
@section('page-title', 'Kelola Limbah')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Daftar Limbah (Ampas Tahu)</h2>
        <p class="text-sm text-gray-500">Kelola semua limbah ampas tahu di sini.</p>
    </div>
    <a href="{{ route('admin.limbah.create') }}"
       class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
        + Tambah Limbah
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/60 border-b border-gray-100">
                <th class="px-6 py-3">Gambar</th>
                <th class="px-6 py-3">Nama Limbah</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3">Harga</th>
                <th class="px-6 py-3">Stok</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($limbah as $l)
                <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                    <td class="px-6 py-4">
                        @if ($l->gambar)
                            <img src="{{ asset('storage/' . $l->gambar) }}" alt="{{ $l->nama_limbah }}"
                                 class="w-14 h-14 rounded-xl object-cover border border-gray-100">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-amber-50 border border-dashed border-amber-200 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-gray-800">{{ $l->nama_limbah }}</p>
                        <p class="text-xs text-gray-500">{{ Str::limit($l->deskripsi, 50) }}</p>
                        @if (!$l->gambar)
                            <p class="text-xs text-amber-600 font-medium mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Belum ada foto
                            </p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $l->kategori->nama_kategori ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($l->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $l->stok }} {{ $l->satuan }}</td>
                    <td class="px-6 py-4">
                        @if ($l->status === 'aktif')
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-bold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.limbah.edit', $l->id) }}" class="text-blue-600 hover:underline text-sm font-medium">Edit</a>
                        <form action="{{ route('admin.limbah.destroy', $l->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus limbah ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-sm font-medium">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">Belum ada limbah</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $limbah->links() }}</div>

@endsection