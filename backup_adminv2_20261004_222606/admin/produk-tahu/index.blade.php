@extends('layouts.admin')
@section('title', 'Kelola Produk Tahu')
@section('page-title', 'Kelola Produk Tahu')

@section('content')

@if (session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 txt-brand-hover px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold txt-primary">Daftar Produk Tahu</h2>
        <p class="text-sm txt-secondary">Kelola semua produk tahu di sini.</p>
    </div>
    <button type="button" onclick="openCreateModal()"
            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
        + Tambah Produk
    </button>
</div>

<div class="glass-card overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-semibold txt-muted uppercase tracking-wider bg-soft border-b bd-soft">
                <th class="px-6 py-3">Gambar</th>
                <th class="px-6 py-3">Nama Produk</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3">Harga</th>
                <th class="px-6 py-3">Stok</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produk as $p)
                <tr class="border-b bd-soft hover:bg-soft transition">
                    <td class="px-6 py-4">
                        @if ($p->gambar)
                            <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->nama_produk }}"
                                 class="w-14 h-14 rounded-xl object-cover border bd-soft">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-amber-50 border border-dashed border-amber-200 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold txt-primary">{{ $p->nama_produk }}</p>
                        <p class="text-xs txt-secondary">{{ Str::limit($p->deskripsi, 50) }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm txt-secondary">{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold txt-primary">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm txt-secondary">{{ $p->stok }}</td>
                    <td class="px-6 py-4">
                        @if ($p->status === 'aktif')
                            <span class="px-2.5 py-1 bg-emerald-50 txt-brand-hover rounded-lg text-xs font-bold">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-50 txt-danger rounded-lg text-xs font-bold">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button type="button" onclick="openEditModal({{ $p->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 txt-info rounded-lg text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        <form action="{{ route('admin.produk-tahu.destroy', $p->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 txt-danger rounded-lg text-xs font-semibold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-6 py-10 text-center txt-muted">Belum ada produk</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $produk->links() }}</div>

{{-- ============================================ --}}
{{-- MODAL: CREATE --}}
{{-- ============================================ --}}
<div id="createModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 720px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Tambah Produk Tahu</h3>
            </div>
            <button onclick="closeCreateModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'create' && $errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="createForm" method="POST" action="{{ route('admin.produk-tahu.store') }}"
              enctype="multipart/form-data" novalidate class="flex-1 overflow-y-auto" x-data="createFormData()" x-init="init()">
            @csrf

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium txt-primary mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_produk" value="{{ old('nama_produk') }}"
                               minlength="3" maxlength="150"
                               oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Contoh: Tahu Putih Premium">
                        <p class="text-xs txt-secondary mt-1">Hanya huruf & spasi. Minimal 3 karakter.</p>
                    </div>

                    <div class="relative" @click.away="kategoriOpen = false">
                        <label class="block text-sm font-medium txt-primary mb-1">Kategori <span class="text-red-500">*</span></label>
                        <button type="button" @click="kategoriOpen = !kategoriOpen"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                            <span class="txt-primary" x-text="kategoriLabel"></span>
                            <svg class="w-4 h-4 txt-muted" :class="kategoriOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="kategoriOpen" x-cloak
                             class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg max-h-60 overflow-y-auto">
                            @foreach ($kategori as $k)
                                <button type="button"
                                        data-value="{{ $k->id }}" data-label="{{ $k->nama_kategori }}"
                                        @click="selectKategori($event.currentTarget)"
                                        class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">
                                    {{ $k->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="kategori_id" :value="kategoriId">
                    </div>

                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="harga" value="{{ old('harga') }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="12500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Stok <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="stok" value="{{ old('stok') }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500"
                               placeholder="Contoh: 100">
                        <p class="text-xs txt-secondary mt-1">Minimal 1.</p>
                    </div>

                    <div class="relative" @click.away="statusOpen = false">
                        <label class="block text-sm font-medium txt-primary mb-1">Status <span class="text-red-500">*</span></label>
                        <button type="button" @click="statusOpen = !statusOpen"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                            <span class="txt-primary" x-text="statusLabel"></span>
                            <svg class="w-4 h-4 txt-muted" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="statusOpen" x-cloak
                             class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                            <button type="button" @click="selectStatus('aktif', 'Aktif')"
                                    class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Aktif</button>
                            <button type="button" @click="selectStatus('nonaktif', 'Nonaktif')"
                                    class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Nonaktif</button>
                        </div>
                        <input type="hidden" name="status" :value="statusValue">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" maxlength="1000"
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                              placeholder="Deskripsi produk...">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- FILE INPUT --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">
                        Gambar Produk
                        <span class="text-xs bg-amber-50 txt-accent px-2 py-0.5 rounded font-medium">Disarankan</span>
                    </label>

                    <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/webp"
                           x-ref="fileInput" @change="handleFileChange($event)" class="hidden">

                    {{-- Undo Banner --}}
                    <div x-show="showUndo" x-cloak
                         class="mb-3 flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-xs text-amber-800 truncate">
                            File <strong x-text="undoFileName"></strong> dibatalkan
                        </span>
                        <button type="button" @click="undoCancel()"
                                class="text-xs txt-brand hover:txt-brand font-bold underline flex-shrink-0">
                            Urungkan
                        </button>
                    </div>

                    {{-- Preview --}}
                    <div x-show="previewUrl" x-cloak
                         style="display: flex; align-items: flex-start; gap: 16px; padding: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; margin-bottom: 12px;">
                        <div style="position: relative; flex-shrink: 0;">
                            <div @click="openLightbox(previewUrl)"
                                 style="position: relative; width: 160px; height: 160px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
                                 onmouseover="this.querySelector('.zoom-overlay').style.opacity='1'"
                                 onmouseout="this.querySelector('.zoom-overlay').style.opacity='0'">
                                <img :src="previewUrl" alt="Preview"
                                     style="width: 160px; height: 160px; object-fit: cover; display: block;">
                                <div class="zoom-overlay"
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
                            <p style="font-size: 11px; color: #059669; font-weight: 700; text-transform: uppercase; margin: 0 0 4px 0;">Preview</p>
                            <p class="break-all" style="font-size: 14px; color: #1f2937; font-weight: 500; margin: 0 0 4px 0; line-height: 1.4;" x-text="fileName"></p>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">Klik gambar untuk memperbesar.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default bg-white focus-within:ring-2 focus-within:ring-emerald-500 transition">
                        <button type="button" @click="$refs.fileInput.click()"
                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 txt-brand-hover rounded-lg text-xs font-semibold transition flex-shrink-0">
                            Choose File
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="fileName || 'No file chosen'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1">Format: JPG, PNG, WEBP. Max 2MB.</p>
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 border-t bd-soft bg-soft flex-shrink-0">
            <button type="button" onclick="closeCreateModal()"
                    class="px-5 py-2.5 bg-soft hover:bg-gray-200 txt-primary text-sm font-medium rounded-xl transition">Batal</button>
            <button type="submit" form="createForm"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Simpan Produk
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: EDIT --}}
{{-- ============================================ --}}
<div id="editModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.6);">

    <div style="width: 100%; max-width: 720px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Edit Produk Tahu</h3>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'edit' && $errors->any())
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 txt-danger px-4 py-3 rounded-xl text-sm">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="editForm" method="POST" enctype="multipart/form-data" novalidate
              class="flex-1 overflow-y-auto" x-data="editFormData()" x-init="init()">
            @csrf @method('PUT')
            <input type="hidden" name="edit_id" id="editIdInput" value="{{ session('open_modal') === 'edit' ? session('edit_id') : '' }}">

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium txt-primary mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_produk"
                               value="{{ session('open_modal') === 'edit' ? old('nama_produk') : '' }}"
                               minlength="3" maxlength="150"
                               oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="relative" @click.away="kategoriOpen = false">
                        <label class="block text-sm font-medium txt-primary mb-1">Kategori <span class="text-red-500">*</span></label>
                        <button type="button" @click="kategoriOpen = !kategoriOpen"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                            <span class="txt-primary" id="editKategoriLabel" x-text="kategoriLabel">-- Pilih Kategori --</span>
                            <svg class="w-4 h-4 txt-muted" :class="kategoriOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="kategoriOpen" x-cloak
                             class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg max-h-60 overflow-y-auto">
                            @foreach ($kategori as $k)
                                <button type="button"
                                        data-value="{{ $k->id }}" data-label="{{ $k->nama_kategori }}"
                                        @click="selectKategori($event.currentTarget)"
                                        class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">
                                    {{ $k->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="kategori_id" id="editKategoriHidden" :value="kategoriId">
                    </div>

                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="harga"
                               value="{{ session('open_modal') === 'edit' ? old('harga') : '' }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Stok <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" name="stok"
                               value="{{ session('open_modal') === 'edit' ? old('stok') : '' }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <p class="text-xs txt-secondary mt-1">Boleh 0 kalau stok habis.</p>
                    </div>

                    <div class="relative" @click.away="statusOpen = false">
                        <label class="block text-sm font-medium txt-primary mb-1">Status <span class="text-red-500">*</span></label>
                        <button type="button" @click="statusOpen = !statusOpen"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default bg-white hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                            <span class="txt-primary" id="editStatusLabel" x-text="statusLabel">Aktif</span>
                            <svg class="w-4 h-4 txt-muted" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="statusOpen" x-cloak
                             class="absolute left-0 right-0 z-30 mt-2 bg-white rounded-xl border bd-soft shadow-lg overflow-hidden">
                            <button type="button" @click="selectStatus('aktif', 'Aktif')"
                                    class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Aktif</button>
                            <button type="button" @click="selectStatus('nonaktif', 'Nonaktif')"
                                    class="w-full px-4 py-2.5 text-sm text-left hover:bg-emerald-50 transition txt-primary">Nonaktif</button>
                        </div>
                        <input type="hidden" name="status" id="editStatusHidden" :value="statusValue">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" maxlength="1000"
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none">{{ session('open_modal') === 'edit' ? old('deskripsi') : '' }}</textarea>
                </div>

                {{-- FILE INPUT --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">
                        Gambar Produk
                        <span class="text-xs bg-amber-50 txt-accent px-2 py-0.5 rounded font-medium">Disarankan</span>
                    </label>

                    <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/webp"
                           x-ref="fileInput" @change="handleFileChange($event)" class="hidden">
                    <input type="hidden" name="hapus_gambar" :value="hapusGambarLama ? 1 : 0">

                    {{-- Existing Image (dengan hover zoom + klik lightbox) --}}
                    <div x-show="existingImage && !hapusGambarLama && !previewUrl" x-cloak class="mb-3">
                        <div style="position: relative; display: inline-block;">
                            <div @click="openLightbox(existingImage)"
                                 style="position: relative; width: 120px; height: 120px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 1px solid #e5e7eb;"
                                 onmouseover="this.querySelector('.zoom-overlay').style.opacity='1'"
                                 onmouseout="this.querySelector('.zoom-overlay').style.opacity='0'">
                                <img :src="existingImage" alt="Existing"
                                     style="width: 120px; height: 120px; object-fit: cover; display: block;">
                                <div class="zoom-overlay"
                                     style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                </div>
                            </div>
                            <button type="button" @click.stop="hapusGambarLama = true"
                                    style="position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; background: rgba(255,255,255,0.95); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 0;"
                                    title="Hapus gambar">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <p style="font-size: 11px; color: #9ca3af; margin-top: 6px;">Gambar saat ini · Klik untuk zoom</p>
                    </div>

                    {{-- Banner hapus gambar lama --}}
                    <div x-show="hapusGambarLama" x-cloak
                         class="mb-3 flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-xs text-amber-800">Gambar lama akan dihapus saat disimpan.</span>
                        <button type="button" @click="hapusGambarLama = false"
                                class="text-xs txt-brand hover:txt-brand font-bold underline">Urungkan</button>
                    </div>

                    {{-- Undo banner --}}
                    <div x-show="showUndo" x-cloak
                         class="mb-3 flex items-center gap-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-xs text-amber-800 truncate">
                            File <strong x-text="undoFileName"></strong> dibatalkan
                        </span>
                        <button type="button" @click="undoCancel()"
                                class="text-xs txt-brand hover:txt-brand font-bold underline flex-shrink-0">Urungkan</button>
                    </div>

                    {{-- Preview gambar baru --}}
                    <div x-show="previewUrl" x-cloak
                         style="display: flex; align-items: flex-start; gap: 16px; padding: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; margin-bottom: 12px;">
                        <div style="position: relative; flex-shrink: 0;">
                            <div @click="openLightbox(previewUrl)"
                                 style="position: relative; width: 120px; height: 120px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid white;"
                                 onmouseover="this.querySelector('.zoom-overlay2').style.opacity='1'"
                                 onmouseout="this.querySelector('.zoom-overlay2').style.opacity='0'">
                                <img :src="previewUrl" alt="Preview"
                                     style="width: 120px; height: 120px; object-fit: cover; display: block;">
                                <div class="zoom-overlay2"
                                     style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                </div>
                            </div>
                            <button type="button" @click.stop="cancelFile()"
                                    style="position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; background: rgba(255,255,255,0.95); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 0;"
                                    title="Batalkan file">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div style="flex: 1; min-width: 0; padding-top: 4px;">
                            <p style="font-size: 11px; color: #059669; font-weight: 700; text-transform: uppercase; margin: 0 0 4px 0;">Preview Baru</p>
                            <p class="break-all" style="font-size: 12px; color: #1f2937; font-weight: 500; margin: 0; line-height: 1.4;" x-text="fileName"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default bg-white focus-within:ring-2 focus-within:ring-emerald-500 transition">
                        <button type="button" @click="$refs.fileInput.click()"
                                class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 txt-brand-hover rounded-lg text-xs font-semibold transition flex-shrink-0">
                            <span x-text="existingImage ? 'Ganti File' : 'Choose File'"></span>
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="fileName || 'No file chosen'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1">Format: JPG, PNG, WEBP. Max 2MB.</p>
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 border-t bd-soft bg-soft flex-shrink-0">
            <button type="button" onclick="closeEditModal()"
                    class="px-5 py-2.5 bg-soft hover:bg-gray-200 txt-primary text-sm font-medium rounded-xl transition">Batal</button>
            <button type="submit" form="editForm"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                Update Produk
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- GLOBAL LIGHTBOX --}}
{{-- ============================================ --}}
<div id="globalLightbox" class="fixed inset-0 hidden"
     style="z-index: 99999; background: rgba(0,0,0,0.9);">
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;">
        <img id="globalLightboxImg" src="" alt="Preview"
             onclick="event.stopPropagation()"
             style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
    </div>
    <button type="button" onclick="closeGlobalLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 48px; height: 48px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
            title="Tutup">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

@endsection

@push('scripts')
<script>
    const PRODUK_DATA = @json($produkJson);

    // ==== GLOBAL LIGHTBOX ====
    function openGlobalLightbox(url) {
        const lb = document.getElementById('globalLightbox');
        document.getElementById('globalLightboxImg').src = url;
        lb.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeGlobalLightbox() {
        const lb = document.getElementById('globalLightbox');
        lb.classList.add('hidden');
        document.getElementById('globalLightboxImg').src = '';
        document.body.style.overflow = '';
    }

    document.getElementById('globalLightbox').addEventListener('click', function(e) {
        if (e.target === this) closeGlobalLightbox();
    });

    // ==== UNIVERSAL FILE STATE ====
    const fileState = {
        currentFile: null,
        currentPreviewUrl: null,
        currentFileName: null,
        reset() {
            if (this.currentPreviewUrl) URL.revokeObjectURL(this.currentPreviewUrl);
            this.currentFile = null;
            this.currentPreviewUrl = null;
            this.currentFileName = null;
        }
    };

    // ==== CREATE MODAL ====
    function openCreateModal() {
        fileState.reset();
        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        const form = document.getElementById('createForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.previewUrl = null;
                state.fileName = null;
                state.showUndo = false;
                state.undoFile = null;
                state.undoFileName = null;
                if (state.undoTimer) clearTimeout(state.undoTimer);
            }
        }
    }

    function closeCreateModal() {
        fileState.reset();
        const modal = document.getElementById('createModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';

        const fileInput = modal.querySelector('input[name="gambar"]');
        if (fileInput) fileInput.value = '';

        const form = document.getElementById('createForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.previewUrl = null;
                state.fileName = null;
                state.showUndo = false;
                state.undoFile = null;
                state.undoFileName = null;
                if (state.undoTimer) clearTimeout(state.undoTimer);
            }
        }
    }

    // ==== EDIT MODAL ====
    function openEditModal(id) {
        const data = PRODUK_DATA.find(p => p.id === id);
        if (!data) return;

        fileState.reset();
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        document.getElementById('editForm').action = '/admin/produk-tahu/' + id;

        const namaInput = modal.querySelector('input[name="nama_produk"]');
        const hargaInput = modal.querySelector('input[name="harga"]');
        const stokInput = modal.querySelector('input[name="stok"]');
        const deskripsiInput = modal.querySelector('textarea[name="deskripsi"]');

        if (!namaInput.value || namaInput.value.trim() === '') namaInput.value = data.nama_produk;
        if (!hargaInput.value || hargaInput.value.trim() === '') hargaInput.value = data.harga;
        if (!stokInput.value || stokInput.value.trim() === '') stokInput.value = data.stok;
        if (!deskripsiInput.value || deskripsiInput.value.trim() === '') deskripsiInput.value = data.deskripsi || '';

        const editIdInput = modal.querySelector('#editIdInput');
        if (editIdInput) editIdInput.value = id;

        const statusLabelText = data.status.charAt(0).toUpperCase() + data.status.slice(1);

        const kategoriLabelSpan = modal.querySelector('#editKategoriLabel');
        const statusLabelSpan = modal.querySelector('#editStatusLabel');
        const kategoriHidden = modal.querySelector('#editKategoriHidden');
        const statusHidden = modal.querySelector('#editStatusHidden');

        if (kategoriLabelSpan) kategoriLabelSpan.textContent = data.kategori_nama;
        if (statusLabelSpan) statusLabelSpan.textContent = statusLabelText;
        if (kategoriHidden) kategoriHidden.value = data.kategori_id;
        if (statusHidden) statusHidden.value = data.status;

        const form = document.getElementById('editForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.currentId = id;
                state.kategoriId = data.kategori_id;
                state.kategoriLabel = data.kategori_nama;
                state.statusValue = data.status;
                state.statusLabel = statusLabelText;
                state.existingImage = data.gambar;
                state.hapusGambarLama = false;
                state.previewUrl = null;
                state.fileName = null;
                state.showUndo = false;
                state.undoFile = null;
                state.undoFileName = null;
                if (state.undoTimer) clearTimeout(state.undoTimer);
            }
        }
    }

    function closeEditModal() {
        fileState.reset();
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';

        const fileInput = modal.querySelector('input[name="gambar"]');
        if (fileInput) fileInput.value = '';

        const form = document.getElementById('editForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.previewUrl = null;
                state.fileName = null;
                state.showUndo = false;
                state.undoFile = null;
                state.undoFileName = null;
                if (state.undoTimer) clearTimeout(state.undoTimer);
            }
        }
    }

    // ==== ALPINE DATA ====
    function createFormData() {
        return {
            kategoriOpen: false,
            statusOpen: false,
            kategoriId: '{{ old('kategori_id') }}',
            kategoriLabel: '-- Pilih Kategori --',
            statusValue: '{{ old('status', 'aktif') }}',
            statusLabel: '{{ old('status') === 'nonaktif' ? 'Nonaktif' : 'Aktif' }}',
            fileName: null,
            previewUrl: null,
            undoFile: null,
            undoFileName: null,
            showUndo: false,
            undoTimer: null,

            init() {
                this.fileName = null;
                this.previewUrl = null;
                this.undoFile = null;
                this.undoFileName = null;
                this.showUndo = false;
            },

            selectKategori(el) {
                this.kategoriId = el.dataset.value;
                this.kategoriLabel = el.dataset.label;
                this.kategoriOpen = false;
            },
            selectStatus(value, label) {
                this.statusValue = value;
                this.statusLabel = label;
                this.statusOpen = false;
            },

            openLightbox(url) {
                openGlobalLightbox(url);
            },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;

                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('⚠️ Hanya file gambar yang diperbolehkan (JPG, PNG, WEBP).');
                    e.target.value = '';
                    this.fileName = null;
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ Ukuran gambar maksimal 2MB.');
                    e.target.value = '';
                    this.fileName = null;
                    return;
                }

                fileState.currentFile = file;
                fileState.currentFileName = file.name;

                if (fileState.currentPreviewUrl) URL.revokeObjectURL(fileState.currentPreviewUrl);
                fileState.currentPreviewUrl = URL.createObjectURL(file);

                this.previewUrl = fileState.currentPreviewUrl;
                this.fileName = file.name;
                this.showUndo = false;
                clearTimeout(this.undoTimer);
            },

            cancelFile() {
                const file = this.$refs.fileInput.files[0];
                if (!file) return;

                this.undoFile = file;
                this.undoFileName = this.fileName;

                this.$refs.fileInput.value = '';
                this.fileName = null;
                this.previewUrl = null;

                this.showUndo = true;
                clearTimeout(this.undoTimer);
                this.undoTimer = setTimeout(() => {
                    this.showUndo = false;
                    this.undoFile = null;
                    this.undoFileName = null;
                }, 10000);
            },

            undoCancel() {
                if (!this.undoFile) {
                    if (fileState.currentFile) {
                        this.undoFile = fileState.currentFile;
                        this.undoFileName = fileState.currentFileName;
                    } else {
                        return;
                    }
                }

                const dt = new DataTransfer();
                dt.items.add(this.undoFile);
                this.$refs.fileInput.files = dt.files;

                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = URL.createObjectURL(this.undoFile);
                this.fileName = this.undoFile.name;

                this.showUndo = false;
                clearTimeout(this.undoTimer);
            }
        };
    }

    function editFormData() {
        return {
            kategoriOpen: false,
            statusOpen: false,
            kategoriId: '',
            kategoriLabel: '-- Pilih Kategori --',
            statusValue: 'aktif',
            statusLabel: 'Aktif',
            currentId: null,
            existingImage: null,
            hapusGambarLama: false,
            fileName: null,
            previewUrl: null,
            undoFile: null,
            undoFileName: null,
            showUndo: false,
            undoTimer: null,

            init() {
                this.fileName = null;
                this.previewUrl = null;
                this.undoFile = null;
                this.undoFileName = null;
                this.showUndo = false;
            },

            selectKategori(el) {
                this.kategoriId = el.dataset.value;
                this.kategoriLabel = el.dataset.label;
                this.kategoriOpen = false;
            },
            selectStatus(value, label) {
                this.statusValue = value;
                this.statusLabel = label;
                this.statusOpen = false;
            },

            openLightbox(url) {
                openGlobalLightbox(url);
            },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;

                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('⚠️ Hanya file gambar yang diperbolehkan (JPG, PNG, WEBP).');
                    e.target.value = '';
                    this.fileName = null;
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ Ukuran gambar maksimal 2MB.');
                    e.target.value = '';
                    this.fileName = null;
                    return;
                }

                fileState.currentFile = file;
                fileState.currentFileName = file.name;

                if (fileState.currentPreviewUrl) URL.revokeObjectURL(fileState.currentPreviewUrl);
                fileState.currentPreviewUrl = URL.createObjectURL(file);

                this.previewUrl = fileState.currentPreviewUrl;
                this.fileName = file.name;
                this.showUndo = false;
                clearTimeout(this.undoTimer);
            },

            cancelFile() {
                const file = this.$refs.fileInput.files[0];
                if (!file) return;

                this.undoFile = file;
                this.undoFileName = this.fileName;

                this.$refs.fileInput.value = '';
                this.fileName = null;
                this.previewUrl = null;

                this.showUndo = true;
                clearTimeout(this.undoTimer);
                this.undoTimer = setTimeout(() => {
                    this.showUndo = false;
                    this.undoFile = null;
                    this.undoFileName = null;
                }, 10000);
            },

            undoCancel() {
                if (!this.undoFile) {
                    if (fileState.currentFile) {
                        this.undoFile = fileState.currentFile;
                        this.undoFileName = fileState.currentFileName;
                    } else {
                        return;
                    }
                }

                const dt = new DataTransfer();
                dt.items.add(this.undoFile);
                this.$refs.fileInput.files = dt.files;

                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = URL.createObjectURL(this.undoFile);
                this.fileName = this.undoFile.name;

                this.showUndo = false;
                clearTimeout(this.undoTimer);
            }
        };
    }

    // ==== AUTO OPEN MODAL ON ERROR ====
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('open_modal') === 'create')
            openCreateModal();
        @elseif (session('open_modal') === 'edit' && session('edit_id'))
            openEditModal({{ session('edit_id') }});
        @endif
    });

    document.getElementById('createModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const lb = document.getElementById('globalLightbox');
            if (!lb.classList.contains('hidden')) {
                closeGlobalLightbox();
                return;
            }
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush