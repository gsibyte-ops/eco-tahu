@extends('layouts.admin')
@section('title', 'Kelola Refund')
@section('page-title', 'Kelola Refund')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Pending</p>
        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Diproses</p>
        <p class="text-2xl font-bold text-blue-600">{{ $stats['diproses'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Selesai</p>
        <p class="text-2xl font-bold text-emerald-600">{{ $stats['selesai'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-xs text-gray-500 mb-1">Total Refund Selesai</p>
        <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Kode pesanan / nama pelanggan..."
                   class="w-full px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="px-4 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            Filter
        </button>

        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.refund.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
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
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">Kode Pesanan</th>
                <th class="px-6 py-3">Pelanggan</th>
                <th class="px-6 py-3">Nominal</th>
                <th class="px-6 py-3">Alasan</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($refund as $r)
                <tr class="border-b border-gray-50 hover:bg-gray-50/60 transition">
                    <td class="px-6 py-4 text-sm text-gray-500">#{{ $r->id }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">#{{ $r->pesanan->kode_pesanan ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-800">{{ $r->pesanan->user->username ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $r->pesanan->user->email ?? '' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-emerald-700">Rp {{ number_format($r->nominal_refund, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Str::limit($r->alasan_batal, 40) }}</td>
                    <td class="px-6 py-4">
                        @php
                            $badge = [
                                'pending' => 'bg-amber-50 text-amber-700',
                                'diproses' => 'bg-blue-50 text-blue-700',
                                'selesai' => 'bg-emerald-50 text-emerald-700',
                                'ditolak' => 'bg-red-50 text-red-700',
                            ][$r->status_refund] ?? 'bg-gray-50 text-gray-700';
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $badge }}">{{ ucfirst($r->status_refund) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.refund.show', $r->id) }}" class="text-emerald-600 hover:underline text-sm font-medium">Proses</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">Belum ada pengajuan refund</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $refund->links() }}</div>

@endsection