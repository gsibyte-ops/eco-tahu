@extends('layouts.admin')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
     x-data="{ tipeOpen: false }">

    <h2 class="text-xl font-bold text-gray-800 mb-6">Form Tambah Kategori</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kategori.store') }}" method="POST" novalidate class="space-y-5">
        @csrf

        {{-- NAMA KATEGORI --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Kategori <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                   minlength="3" maxlength="100"
                   oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                   class="w-full px-4 py-2.5 rounded-xl border @error('nama_kategori') border-red-300 bg-red-50 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500"
                   placeholder="Contoh: Tahu Putih">
            @error('nama_kategori')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @else
                <p class="text-xs text-gray-500 mt-1">Hanya huruf & spasi. Minimal 3 karakter.</p>
            @enderror
        </div>

        {{-- TIPE (CUSTOM DROPDOWN) --}}
        <div class="relative" @click.away="tipeOpen = false">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tipe <span class="text-red-500">*</span>
            </label>
            <button type="button" @click="tipeOpen = !tipeOpen"
                    class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border @error('tipe') border-red-300 bg-red-50 @else border-gray-200 bg-white @enderror hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                <span class="text-gray-700" id="tipeLabel">
                    @php
                        $tipeLabel = match(old('tipe')) {
                            'produk_tahu' => 'Produk Tahu',
                            'limbah' => 'Produk Limbah',
                            default => '-- Pilih Tipe --',
                        };
                    @endphp
                    {{ $tipeLabel }}
                </span>
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" :class="tipeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="tipeOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border border-gray-100 shadow-lg overflow-hidden">
                <button type="button" data-value="produk_tahu" data-label="Produk Tahu"
                        onclick="selectTipe(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                               {{ old('tipe') == 'produk_tahu' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                    <span>Produk Tahu</span>
                    @if (old('tipe') == 'produk_tahu')
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
                <button type="button" data-value="limbah" data-label="Produk Limbah"
                        onclick="selectTipe(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                               {{ old('tipe') == 'limbah' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                    <span>Produk Limbah</span>
                    @if (old('tipe') == 'limbah')
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
            </div>

            <input type="hidden" name="tipe" id="inputTipe" value="{{ old('tipe') }}">

            @error('tipe')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
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

@push('scripts')
<script>
    function selectTipe(el) {
        document.getElementById('inputTipe').value = el.dataset.value;
        document.getElementById('tipeLabel').textContent = el.dataset.label;
        document.querySelector('[x-data]').__x.$data.tipeOpen = false;
    }
</script>
@endpush