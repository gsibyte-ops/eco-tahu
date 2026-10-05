@extends('layouts.admin')
@section('title', 'Tambah Artikel Edukasi')
@section('page-title', 'Tambah Artikel Edukasi')

@section('content')

<div class="max-w-4xl mx-auto glass-card p-6"
     x-data="{
         statusOpen: false,
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

    <h2 class="text-xl font-bold txt-primary mb-6">Form Tambah Artikel</h2>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm"
             style="background: rgb(var(--danger-soft) / 0.6); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
            <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.edukasi.store') }}" method="POST" enctype="multipart/form-data"
          novalidate class="space-y-5">
        @csrf

        {{-- JUDUL --}}
        <div>
            <label class="block text-sm font-medium txt-primary mb-1">
                Judul Artikel <span style="color: rgb(var(--danger));">*</span>
            </label>
            <input type="text" name="judul" value="{{ old('judul') }}"
                   minlength="5" maxlength="200"
                   oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.,:!?\-]/g, '')"
                   class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                   style="color: rgb(var(--text-primary));"
                   placeholder="Contoh: Manfaat Ampas Tahu untuk Pupuk Organik">
            @error('judul')
                <p class="text-xs mt-1" style="color: rgb(var(--danger));">{{ $message }}</p>
            @else
                <p class="text-xs txt-secondary mt-1">Huruf, angka, spasi & tanda baca umum. Minimal 5 karakter.</p>
            @enderror
        </div>

        {{-- THUMBNAIL --}}
        <div>
            <label class="block text-sm font-medium txt-primary mb-1">
                Thumbnail
                <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Disarankan</span>
                <span class="txt-muted font-normal">(opsional)</span>
            </label>

            <input type="file" name="thumbnail" accept="image/jpeg,image/jpg,image/png,image/webp" x-ref="fileInput"
                   @change="handleFileChange($event)"
                   class="hidden">

            {{-- UNDO BANNER --}}
            <div x-show="showUndo" x-cloak
                 class="mb-3 flex items-center gap-3 px-3 py-2 rounded-xl"
                 style="background: rgb(var(--warning-soft) / 0.5); border: 1px solid rgb(var(--warning) / 0.3);">
                <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="flex-1 text-xs truncate" style="color: rgb(var(--warning));">
                    File <strong x-text="undoFileName"></strong> dibatalkan
                </span>
                <button type="button" @click="undoCancel()"
                        class="text-xs font-bold underline flex-shrink-0" style="color: rgb(var(--brand));">
                    Urungkan
                </button>
            </div>

            {{-- PREVIEW --}}
            <div x-show="previewUrl" x-cloak
                 style="display: flex; align-items: flex-start; gap: 16px; padding: 16px; background: rgb(var(--brand-soft) / 0.3); border: 1px solid rgb(var(--brand) / 0.3); border-radius: 12px; margin-bottom: 12px;">
                <div style="position: relative; flex-shrink: 0;">
                    <div @click="openLightbox(previewUrl)"
                         style="position: relative; width: 160px; height: 160px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid rgb(var(--border)); box-shadow: 0 2px 8px rgba(0,0,0,0.08);"
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
                            style="position: absolute; top: 8px; right: 8px; width: 28px; height: 28px; background: rgba(255,255,255,0.95); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 0;"
                            title="Batalkan file">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div style="flex: 1; min-width: 0; padding-top: 8px;">
                    <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; margin: 0 0 4px 0; color: rgb(var(--brand));">Preview</p>
                    <p class="break-all" style="font-size: 14px; font-weight: 500; margin: 0 0 4px 0; line-height: 1.4; color: rgb(var(--text-primary));" x-text="fileName"></p>
                    <p style="font-size: 12px; margin: 0; color: rgb(var(--text-muted));">Klik gambar untuk memperbesar.</p>
                </div>
            </div>

            {{-- FILE INPUT --}}
            <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default glass-card focus-within:ring-2 focus-within:ring-emerald-500 transition">
                <button type="button" @click="$refs.fileInput.click()"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0"
                        style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                    Choose File
                </button>
                <span class="flex-1 min-w-0 text-sm truncate"
                      :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                      x-text="fileName || 'No file chosen'"></span>
            </div>

            <p class="text-xs txt-secondary mt-2">Format: JPG, PNG, WEBP. Max 2MB.</p>
        </div>

        {{-- KONTEN --}}
        <div>
            <label class="block text-sm font-medium txt-primary mb-1">
                Konten Artikel <span style="color: rgb(var(--danger));">*</span>
            </label>
            <textarea name="konten" rows="12" minlength="20" maxlength="10000" required
                      class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input"
                      style="color: rgb(var(--text-primary));"
                      placeholder="Tulis konten artikel di sini...">{{ old('konten') }}</textarea>
            @error('konten')
                <p class="text-xs mt-1" style="color: rgb(var(--danger));">{{ $message }}</p>
            @else
                <p class="text-xs txt-secondary mt-1">Minimal 20 karakter.</p>
            @enderror
        </div>

        {{-- STATUS --}}
        <div class="relative" @click.away="statusOpen = false">
            <label class="block text-sm font-medium txt-primary mb-1">
                Status <span style="color: rgb(var(--danger));">*</span>
            </label>
            <button type="button" @click="statusOpen = !statusOpen"
                    class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default glass-card hover:bd-strong focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                <span class="txt-primary" id="statusLabel">
                    @php
                        $st = old('status', 'publish');
                        echo match($st) {
                            'draft' => 'Draft',
                            'scheduled' => 'Schedule',
                            default => 'Publish',
                        };
                    @endphp
                </span>
                <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="statusOpen" x-cloak
                 class="absolute left-0 right-0 z-30 mt-2 glass-card rounded-xl border bd-soft shadow-lg overflow-hidden">
                <button type="button" data-value="draft" data-label="Draft"
                        onclick="selectStatusForm(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition
                               {{ old('status') == 'draft' ? 'bg-emerald-500/10 txt-brand font-semibold' : 'txt-primary' }}">
                    <span>Draft</span>
                    <span class="text-xs txt-muted">— simpan tapi belum tampil</span>
                </button>
                <button type="button" data-value="publish" data-label="Publish"
                        onclick="selectStatusForm(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition
                               {{ old('status', 'publish') == 'publish' ? 'bg-emerald-500/10 txt-brand font-semibold' : 'txt-primary' }}">
                    <span>Publish</span>
                    <span class="text-xs txt-muted">— langsung tayang</span>
                </button>
                <button type="button" data-value="scheduled" data-label="Schedule"
                        onclick="selectStatusForm(this)"
                        class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition
                               {{ old('status') == 'scheduled' ? 'bg-emerald-500/10 txt-brand font-semibold' : 'txt-primary' }}">
                    <span>Schedule</span>
                    <span class="text-xs txt-muted">— tayang otomatis</span>
                </button>
            </div>

            <input type="hidden" name="status" id="inputStatus" value="{{ old('status', 'publish') }}">
        </div>

        {{-- SCHEDULED_AT --}}
        <div id="scheduleWrap" style="{{ old('status') === 'scheduled' ? '' : 'display:none' }}">
            <label class="block text-sm font-medium txt-primary mb-1">
                Jadwal Tayang <span style="color: rgb(var(--danger));">*</span>
            </label>
            <input type="datetime-local" name="scheduled_at" id="scheduledAtInput"
                   value="{{ old('scheduled_at') }}"
                   min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                   class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                   style="color: rgb(var(--text-primary));">
            @error('scheduled_at')
                <p class="text-xs mt-1" style="color: rgb(var(--danger));">{{ $message }}</p>
            @else
                <p class="text-xs txt-secondary mt-1">Minimal 5 menit dari sekarang. Waktu server: {{ now()->format('d M Y H:i') }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.edukasi.index') }}"
               class="glass-btn">Batal</a>
            <button type="submit"
                    class="btn-primary">
                Simpan Artikel
            </button>
        </div>
    </form>

    {{-- LIGHTBOX --}}
    <div x-show="showLightbox" x-cloak @click="closeLightbox()" class="fixed inset-0"
         style="z-index: 99999; background: rgba(0,0,0,0.9);">
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;">
            <img :src="lightboxUrl" alt="Preview" @click.stop
                 style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
        </div>
        <button @click.stop="closeLightbox()"
                style="position: absolute; top: 20px; right: 20px; width: 48px; height: 48px; background: #ef4444; color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"
                title="Tutup">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function selectStatusForm(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;

        // FIX: pakai closest, bukan querySelector
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.statusOpen = false;

        // Toggle input jadwal
        const wrap = document.getElementById('scheduleWrap');
        if (el.dataset.value === 'scheduled') {
            wrap.style.display = '';
        } else {
            wrap.style.display = 'none';
            document.getElementById('scheduledAtInput').value = '';
        }
    }
</script>
@endpush