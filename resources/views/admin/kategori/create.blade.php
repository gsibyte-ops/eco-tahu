@extends('layouts.admin')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Form Tambah Kategori</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kategori.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                   placeholder="Contoh: Tahu Putih">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
            <select name="tipe" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">-- Pilih Tipe --</option>
                <option value="produk_tahu" {{ old('tipe') == 'produk_tahu' ? 'selected' : '' }}>Produk Tahu</option>
                <option value="limbah" {{ old('tipe') == 'limbah' ? 'selected' : '' }}>Limbah (Ampas)</option>
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.kategori.index') }}"
               class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition">
                Simpan Kategori
            </button>
        </div>
    </form>
</div>

@endsection