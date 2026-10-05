@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')

<div class="max-w-2xl mx-auto glass-card rounded-2xl p-6"
     x-data="{ tipeOpen: false }">

    <h2 class="text-xl font-bold txt-primary mb-6">Form Edit Kategori</h2>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm"
             style="background: rgb(var(--danger-soft) / 0.6); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
            <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.kategori.update', $kategori->id) }}" method="POST" novalidate class="space-y-5">
        @csrf @method('PUT')

        {{-- NAMA KATEGORI --}}
        <div>
            <label class="block text-sm font-medium txt-primary mb-1">
                Nama Kategori <span style="color: rgb(var(--danger));">*</span>
            </label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                   minlength="3" maxlength="100"
                   oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                   class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                   style="color: rgb(var(--text-primary));">
            @error('nama_kategori')
                <p class="text-xs mt-1" style="color: rgb(var(--danger));">{{ $message }}</p>
            @else
                <p class="text-xs txt-secondary mt-1">Hanya huruf & spasi. Minimal 3 karakter.</p>
            @enderror
        </div>

        {{-- TIPE (CUSTOM DROPDOWN) --}}
        <div class="relative" @click.away="tipeOpen = false">
            <label class="block text-sm font-medium txt-primary mb-1">
                Tipe <span style="color: rgb(var(--danger));">*</span>
            </label>
            <button type="button" @click="tipeOpen = !tipeOpen"
                    class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default glass-card hover:bd-strong focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                <span class="txt-primary" id="tipeLabel">
                    @php
                        $selectedTipe = old('tipe', $kategori->tipe);
                        $tipeLabel = match($selectedTipe) {
                            'produk_tahu' => 'Produk Tahu',
                            'limbah' => 'Produk Limbah',
                            default => '-- Pilih Tipe --',
                        };
                    @endphp
                    {{ $tipeLabel }}
                </span>
                <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="tipeOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="tipeOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 glass-card rounded-xl border bd-soft shadow-lg overflow-hidden">
                <button type="button" data-value="produk_tahu" data-label="Produk Tahu"
                        onclick="selectTipe(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition
                               {{ $selectedTipe == 'produk_tahu' ? 'bg-emerald-500/10 txt-brand font-semibold' : 'txt-primary' }}">
                    <span>Produk Tahu</span>
                    @if ($selectedTipe == 'produk_tahu')
                        <svg class="w-4 h-4 txt-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
                <button type="button" data-value="limbah" data-label="Produk Limbah"
                        onclick="selectTipe(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition
                               {{ $selectedTipe == 'limbah' ? 'bg-emerald-500/10 txt-brand font-semibold' : 'txt-primary' }}">
                    <span>Produk Limbah</span>
                    @if ($selectedTipe == 'limbah')
                        <svg class="w-4 h-4 txt-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @endif
                </button>
            </div>

            <input type="hidden" name="tipe" id="inputTipe" value="{{ $selectedTipe }}">

            @error('tipe')
                <p class="text-xs mt-1" style="color: rgb(var(--danger));">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.kategori.index') }}"
               class="glass-btn !py-2.5 !px-5">Batal</a>
            <button type="submit"
                    class="btn-primary">
                Update Kategori
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