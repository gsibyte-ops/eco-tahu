@extends('layouts.admin')
@section('title', 'Edit Produk Tahu')
@section('page-title', 'Edit Produk Tahu')

@section('content')

<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
     x-data="{
         kategoriOpen: false,
         statusOpen: false,
         hapusGambarLama: false,
         fileName: null,
         previewUrl: null,
         undoFile: null,
         undoFileName: '',
         showUndo: false,
         undoTimer: null,
         showLightbox: false,
         lightboxUrl: '',

         openLightbox(url) {
             this.lightboxUrl = url;
             this.showLightbox = true;
             document.body.style.overflow = 'hidden';
         },

         closeLightbox() {
             this.showLightbox = false;
             document.body.style.overflow = '';
         },

         handleFileChange(e) {
             const file = e.target.files[0];
             if (!file) return;
             if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
             this.previewUrl = URL.createObjectURL(file);
             this.fileName = file.name;
             this.showUndo = false;
             this.undoFile = null;
             clearTimeout(this.undoTimer);
         },

         cancelFile() {
             const file = this.$refs.fileInput.files[0];
             if (!file) return;
             this.undoFile = file;
             this.undoFileName = this.fileName;
             this.$refs.fileInput.value = '';
             this.fileName = null;
             if (this.previewUrl) {
                 URL.revokeObjectURL(this.previewUrl);
                 this.previewUrl = null;
             }
             this.showUndo = true;
             clearTimeout(this.undoTimer);
             this.undoTimer = setTimeout(() => {
                 this.showUndo = false;
                 this.undoFile = null;
             }, 5000);
         },

         undoCancel() {
             if (!this.undoFile) return;
             const dt = new DataTransfer();
             dt.items.add(this.undoFile);
             this.$refs.fileInput.files = dt.files;
             this.previewUrl = URL.createObjectURL(this.undoFile);
             this.fileName = this.undoFileName;
             this.showUndo = false;
             this.undoFile = null;
             clearTimeout(this.undoTimer);
         }
     }"
     @keydown.escape.window="closeLightbox()">

    <h2 class="text-xl font-bold text-gray-800 mb-6">Form Edit Produk Tahu</h2>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.produk-tahu.update', $produkTahu->id) }}" method="POST" enctype="multipart/form-data"
          novalidate class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- NAMA PRODUK --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Produk <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_produk" value="{{ old('nama_produk', $produkTahu->nama_produk) }}"
                       minlength="3" maxlength="150"
                       oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                       class="w-full px-4 py-2.5 rounded-xl border @error('nama_produk') border-red-300 bg-red-50 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('nama_produk')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 mt-1">Hanya huruf & spasi. Minimal 3 karakter.</p>
                @enderror
            </div>

            {{-- KATEGORI --}}
            <div class="relative" @click.away="kategoriOpen = false">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <button type="button" @click="kategoriOpen = !kategoriOpen"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border @error('kategori_id') border-red-300 bg-red-50 @else border-gray-200 bg-white @enderror hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                    <span class="text-gray-700" id="kategoriLabel">
                        @php
                            $selectedKategori = old('kategori_id', $produkTahu->kategori_id);
                            $selectedKategoriName = $kategori->firstWhere('id', $selectedKategori)?->nama_kategori ?? '-- Pilih Kategori --';
                        @endphp
                        {{ $selectedKategoriName }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" :class="kategoriOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="kategoriOpen" x-cloak
                     class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border border-gray-100 shadow-lg max-h-64 overflow-y-auto">
                    @foreach ($kategori as $k)
                        <button type="button" data-value="{{ $k->id }}" data-label="{{ $k->nama_kategori }}"
                                onclick="selectKategori(this)"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                                       {{ $selectedKategori == $k->id ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                            <span>{{ $k->nama_kategori }}</span>
                            @if ($selectedKategori == $k->id)
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                    @endforeach
                </div>

                <input type="hidden" name="kategori_id" id="inputKategori" value="{{ $selectedKategori }}">
                @error('kategori_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- HARGA --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Harga (Rp) <span class="text-red-500">*</span>
                </label>
                <input type="text" inputmode="numeric" name="harga" value="{{ old('harga', $produkTahu->harga) }}"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="w-full px-4 py-2.5 rounded-xl border @error('harga') border-red-300 bg-red-50 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('harga')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 mt-1">Hanya angka.</p>
                @enderror
            </div>

            {{-- STOK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Stok <span class="text-red-500">*</span>
                </label>
                <input type="text" inputmode="numeric" name="stok" value="{{ old('stok', $produkTahu->stok) }}"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="w-full px-4 py-2.5 rounded-xl border @error('stok') border-red-300 bg-red-50 @else border-gray-200 @enderror focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('stok')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @else
                    <p class="text-xs text-gray-500 mt-1">Boleh 0 kalau stok habis.</p>
                @enderror
            </div>

            {{-- STATUS --}}
            <div class="relative" @click.away="statusOpen = false">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <button type="button" @click="statusOpen = !statusOpen"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                    <span class="text-gray-700" id="statusLabel">
                        {{ old('status', $produkTahu->status) === 'nonaktif' ? 'Nonaktif' : 'Aktif' }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="statusOpen" x-cloak
                     class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border border-gray-100 shadow-lg overflow-hidden">
                    <button type="button" data-value="aktif" data-label="Aktif" onclick="selectStatus(this)"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                                   {{ old('status', $produkTahu->status) == 'aktif' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                        <span>Aktif</span>
                        <span class="text-xs text-gray-400">— dijual di katalog</span>
                    </button>
                    <button type="button" data-value="nonaktif" data-label="Nonaktif" onclick="selectStatus(this)"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition
                                   {{ old('status', $produkTahu->status) == 'nonaktif' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700' }}">
                        <span>Nonaktif</span>
                        <span class="text-xs text-gray-400">— disembunyikan</span>
                    </button>
                </div>

                <input type="hidden" name="status" id="inputStatus" value="{{ old('status', $produkTahu->status) }}">
            </div>
        </div>

        {{-- DESKRIPSI --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4" maxlength="1000"
                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi', $produkTahu->deskripsi) }}</textarea>
        </div>

        {{-- GAMBAR --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Gambar Produk
                <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded font-medium">Disarankan</span>
                <span class="text-gray-400 font-normal">(opsional)</span>
            </label>

            {{-- GAMBAR EXISTING — KIRI --}}
            @if ($produkTahu->gambar)
                <div class="mb-3">
                    <div x-show="!hapusGambarLama" class="inline-block">
                        <div style="position: relative; display: inline-block;">
                            <div @click="openLightbox('{{ asset('storage/' . $produkTahu->gambar) }}')"
                                 style="position: relative; width: 160px; height: 160px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 1px solid #e5e7eb; background: #f9fafb;"
                                 onmouseover="this.querySelector('.zoom-overlay').style.opacity='1'"
                                 onmouseout="this.querySelector('.zoom-overlay').style.opacity='0'">
                                <img src="{{ asset('storage/' . $produkTahu->gambar) }}"
                                     alt="{{ $produkTahu->nama_produk }}"
                                     style="width: 160px; height: 160px; object-fit: cover; display: block;">
                                <div class="zoom-overlay"
                                     style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                    <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                </div>
                            </div>
                            <button type="button" @click.stop="hapusGambarLama = true"
                                    style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(255,255,255,0.95); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 0; transition: all 0.15s;"
                                    onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.transform='scale(1.1)'"
                                    onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.color='#ef4444'; this.style.transform='scale(1)'"
                                    title="Hapus gambar">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <p style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Gambar saat ini · Klik untuk zoom</p>
                    </div>

                    {{-- Banner hapus --}}
                    <div x-show="hapusGambarLama" x-cloak
                         class="flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-xs text-amber-800">
                            Gambar lama akan dihapus saat Anda klik <strong>Update Produk</strong>
                        </span>
                        <button type="button" @click="hapusGambarLama = false"
                                class="text-xs text-emerald-600 hover:text-emerald-700 font-bold underline flex-shrink-0">
                            Urungkan
                        </button>
                    </div>
                </div>
            @endif

            <input type="hidden" name="hapus_gambar" :value="hapusGambarLama ? 1 : 0">

            <input type="file" name="gambar" accept="image/*" x-ref="fileInput"
                   @change="handleFileChange($event)"
                   class="hidden">

            {{-- Preview gambar baru --}}
            <div x-show="previewUrl" x-cloak
                 style="display: flex; align-items: flex-start; gap: 16px; padding: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; margin-bottom: 12px;">
                <div style="position: relative; flex-shrink: 0;">
                    <div @click="openLightbox(previewUrl)"
                         style="position: relative; width: 160px; height: 160px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
                         onmouseover="this.querySelector('.zoom-overlay2').style.opacity='1'"
                         onmouseout="this.querySelector('.zoom-overlay2').style.opacity='0'">
                        <img :src="previewUrl" alt="Preview"
                             style="width: 160px; height: 160px; object-fit: cover; display: block;">
                        <div class="zoom-overlay2"
                             style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                            <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </div>
                    </div>
                    <button type="button" @click.stop="cancelFile()"
                            style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(255,255,255,0.95); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 0; transition: all 0.15s;"
                            onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.transform='scale(1.1)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.95)'; this.style.color='#ef4444'; this.style.transform='scale(1)'"
                            title="Batalkan file">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div style="flex: 1; min-width: 0; padding-top: 8px;">
                    <p style="font-size: 11px; color: #059669; font-weight: 700; text-transform: uppercase; margin: 0 0 4px 0;">Preview Gambar Baru</p>
                    <p style="font-size: 14px; color: #1f2937; font-weight: 500; margin: 0 0 4px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="fileName"></p>
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">Klik gambar untuk memperbesar.</p>
                </div>
            </div>

            {{-- File Input --}}
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl border border-gray-200 bg-white focus-within:ring-2 focus-within:ring-emerald-500 transition">
                <button type="button" @click="$refs.fileInput.click()"
                        class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold transition flex-shrink-0">
                    {{ $produkTahu->gambar ? 'Ganti File' : 'Choose File' }}
                </button>
                <span class="flex-1 min-w-0 text-sm truncate"
                      :class="fileName ? 'text-gray-800 font-medium' : 'text-gray-400'"
                      x-text="fileName || 'No file chosen'"></span>
            </div>

            {{-- Undo --}}
            <div x-show="showUndo" x-cloak
                 class="mt-2 flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="flex-1 text-xs text-amber-800 truncate">
                    File <strong x-text="undoFileName"></strong> dibatalkan
                </span>
                <button type="button" @click="undoCancel()"
                        class="text-xs text-emerald-600 hover:text-emerald-700 font-bold underline flex-shrink-0">
                    Urungkan
                </button>
            </div>

            <p class="text-xs text-gray-500 mt-2">
                Kosongkan jika tidak ingin ganti gambar.
                @if ($produkTahu->gambar)
                    Klik tombol <strong>X</strong> di gambar untuk menghapus.
                @endif
            </p>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.produk-tahu.index') }}"
               class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition">
                Update Produk
            </button>
        </div>
    </form>

    {{-- LIGHTBOX --}}
    <div x-show="showLightbox" x-cloak
         @click="closeLightbox()"
         class="fixed inset-0"
         style="z-index: 99999; background: rgba(0,0,0,0.9);">
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;">
            <img :src="lightboxUrl" alt="Preview"
                 @click.stop
                 style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
        </div>
        <button @click.stop="closeLightbox()"
                style="position: absolute; top: 20px; right: 20px; width: 48px; height: 48px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
                onmouseover="this.style.background='#dc2626'"
                onmouseout="this.style.background='#ef4444'"
                title="Tutup">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function selectKategori(el) {
        document.getElementById('inputKategori').value = el.dataset.value;
        document.getElementById('kategoriLabel').textContent = el.dataset.label;
        document.querySelector('[x-data]').__x.$data.kategoriOpen = false;
    }

    function selectStatus(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        document.querySelector('[x-data]').__x.$data.statusOpen = false;
    }
</script>
@endpush