@extends('layouts.admin')
@section('title', 'Edit Artikel Edukasi')
@section('page-title', 'Edit Artikel Edukasi')

@section('content')

<div class="max-w-4xl mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Form Edit Artikel</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.edukasi.update', $edukasi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Artikel <span class="text-red-500">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $edukasi->judul) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>

            @if ($edukasi->thumbnail)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $edukasi->thumbnail) }}" alt="{{ $edukasi->judul }}"
                         class="w-32 h-24 rounded-xl object-cover border border-gray-100">
                    <p class="text-xs text-gray-500 mt-1">Thumbnail saat ini</p>
                </div>
            @endif

            <input type="file" name="thumbnail" accept="image/*"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm
                          file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                          file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin ganti thumbnail.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konten Artikel <span class="text-red-500">*</span></label>
            <textarea name="konten" rows="12" required
                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono text-sm">{{ old('konten', $edukasi->konten) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select name="status" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="draft" {{ old('status', $edukasi->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="publish" {{ old('status', $edukasi->status) == 'publish' ? 'selected' : '' }}>Publish</option>
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.edukasi.index') }}"
               class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm transition">
                Update Artikel
            </button>
        </div>
    </form>
</div>

@endsection