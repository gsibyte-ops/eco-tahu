@extends('layouts.admin')
@section('title', 'Kelola Pelanggan')
@section('page-title', 'Kelola Pelanggan')

@section('content')

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Pelanggan</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_pelanggan'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Aktif (30 hari)</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['pelanggan_aktif'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_transaksi'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Rata-rata Belanja</p>
        <p class="text-lg font-bold text-purple-600">Rp {{ number_format($stats['rata_rata_belanja'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Pelanggan</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama, email, atau no. telepon..."
                   class="w-full px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            Cari
        </button>

        @if (request()->has('q'))
            <a href="{{ route('admin.pelanggan.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
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
                <th class="px-6 py-3">Pelanggan</th>
                <th class="px-6 py-3">Kontak</th>
                <th class="px-6 py-3">Total Pesanan</th>
                <th class="px-6 py-3">Total Belanja</th>
                <th class="px-6 py-3">Terdaftar</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelanggan as $p)
                <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                {{ strtoupper(substr($p->username, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $p->username }}</p>
                                <p class="text-xs text-gray-500">{{ $p->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $p->no_telepon ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold">
                            {{ $p->pesanan_count }} pesanan
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-emerald-700">
                        Rp {{ number_format($p->total_belanja ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.pelanggan.show', $p->id) }}"
                           class="text-emerald-600 hover:underline text-sm font-medium">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada pelanggan</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $pelanggan->links() }}</div>

@endsection