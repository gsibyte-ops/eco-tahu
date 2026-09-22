@extends('layouts.admin')
@section('title', 'Edit Limbah')
@section('page-title', 'Edit Limbah')

@section('content')

<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Form Edit Limbah</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.limbah.update', $limbah->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Limbah <span class="text-red-500">*</span></label>
                <input type="text" name="nama_limbah" value="{{ old('nama_limbah', $limbah->nama_limbah) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id', $limbah->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="harga" value="{{ old('harga', $limbah->harga) }}" required min="0"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
                <input type="number" name="stok" value="{{ old('stok', $limbah->stok) }}" required min="0"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                <input type="text" name="satuan" value="{{ old('satuan', $limbah->satuan) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="aktif" {{ old('status', $limbah->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $limbah->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi', $limbah->deskripsi) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Limbah</label>

            @if ($limbah->gambar)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $limbah->gambar) }}" alt="{{ $limbah->nama_limbah }}"
                         class="w-24 h-24 rounded-xl object-cover border border-gray-100">
                    <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                </div>
            @endif

            <input type="file" name="gambar" accept="image/*"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm
                          file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                          file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin ganti gambar.</p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.limbah.index') }}"
               class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition">
                Update Limbah
            </button>
        </div>
    </form>
</div>

@endsection