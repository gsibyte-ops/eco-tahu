@extends('layouts.admin')
@section('title', 'Kelola Edukasi')
@section('page-title', 'Kelola Edukasi')

@section('content')

@if (session('success'))
    <div class="glass-card px-4 py-3 mb-4 text-sm" style="border-color: rgb(var(--success) / 0.3); background: rgb(var(--success-soft) / 0.7) !important;">
        <span style="color: rgb(var(--success));">{{ session('success') }}</span>
    </div>
@endif

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--info));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--info));">Total</p>
        <p class="text-3xl font-extrabold" style="color: rgb(var(--text-primary));">{{ $stats['total'] }}</p>
    </div>
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--success));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--success));">Published</p>
        <p class="text-3xl font-extrabold" style="color: rgb(var(--text-primary));">{{ $stats['publish'] }}</p>
    </div>
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--info));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--info));">Scheduled</p>
        <p class="text-3xl font-extrabold" style="color: rgb(var(--text-primary));">{{ $stats['scheduled'] }}</p>
    </div>
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--warning));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--warning));">Draft</p>
        <p class="text-3xl font-extrabold" style="color: rgb(var(--text-primary));">{{ $stats['draft'] }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight" style="color: rgb(var(--text-primary));">Daftar Artikel Edukasi</h2>
        <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">Kelola artikel edukasi seputar tahu dan lingkungan.</p>
    </div>
    <button type="button" onclick="openCreateModal()" class="btn-primary">
        + Tambah Artikel
    </button>
</div>

{{-- Filter --}}
<div class="glass-card p-4 mb-6" x-data="{ statusOpen: false }">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Cari Artikel</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul artikel..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl focus:outline-none"
                       style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border)); color: rgb(var(--text-primary));">
            </div>
        </div>

        <div style="width: 180px;" class="relative" @click.away="statusOpen = false">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Status</label>
            <button type="button" @click="statusOpen = !statusOpen"
                    class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl transition text-left"
                    style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border)); color: rgb(var(--text-primary));">
                <span class="truncate" id="statusLabel">
                    @php
                        $statusLabel = match(request('status')) {
                            'publish'   => 'Publish',
                            'scheduled' => 'Scheduled',
                            'draft'     => 'Draft',
                            default     => 'Semua Status',
                        };
                    @endphp
                    {{ $statusLabel }}
                </span>
                <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="statusOpen" x-cloak class="absolute left-0 right-0 z-30 mt-2 rounded-xl overflow-hidden" style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                @foreach (['' => 'Semua Status', 'publish' => 'Publish', 'scheduled' => 'Scheduled', 'draft' => 'Draft'] as $val => $label)
                    <button type="button" data-value="{{ $val }}" data-label="{{ $label }}" onclick="selectStatusFilter(this)"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition"
                            style="{{ request('status') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                        <span>{{ $label }}</span>
                        @if (request('status') == $val)
                            <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="status" id="inputStatus" value="{{ request('status') }}">
        </div>

        <button type="submit" class="btn-primary !py-2.5 !px-5 !text-sm">Filter</button>
        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.edukasi.index') }}" class="glass-btn !py-2.5 !px-4 !text-sm">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="glass-card overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
                <th class="px-5 py-3">Thumbnail</th>
                <th class="px-5 py-3">Judul</th>
                <th class="px-5 py-3">Penulis</th>
                <th class="px-5 py-3">Tanggal</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($edukasi as $e)
                <tr class="transition" style="border-bottom: 1px solid rgb(var(--border-soft));">
                    <td class="px-5 py-4">
                        @if ($e->thumbnail)
                            <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                 class="w-16 h-12 rounded-lg object-cover" style="border: 1px solid rgb(var(--border-soft));">
                        @else
                            <div class="w-16 h-12 rounded-lg flex items-center justify-center text-xs" style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-muted));">No img</div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-sm font-semibold" style="color: rgb(var(--text-primary));">{{ $e->judul }}</p>
                        <p class="text-xs line-clamp-1" style="color: rgb(var(--text-secondary));">{{ Str::limit($e->konten, 60) }}</p>
                    </td>
                    <td class="px-5 py-4 text-sm" style="color: rgb(var(--text-secondary));">{{ $e->user->username ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm" style="color: rgb(var(--text-secondary));">
                        @if ($e->status === 'publish' && $e->published_at)
                            {{ $e->published_at->format('d M Y') }}
                        @elseif ($e->status === 'scheduled' && $e->scheduled_at)
                            <span style="color: rgb(var(--info));">{{ $e->scheduled_at->format('d M Y H:i') }}</span>
                        @else
                            <span style="color: rgb(var(--text-muted));">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if ($e->status === 'publish')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">Publish</span>
                        @elseif ($e->status === 'scheduled')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">Scheduled</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Draft</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right space-x-2">
                        <button type="button" onclick="openEditModal({{ $e->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        <form action="{{ route('admin.edukasi.destroy', $e->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus artikel ini?')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                    style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center" style="color: rgb(var(--text-muted));">
                    @if (request('q')) Tidak ada artikel dengan kata kunci "{{ request('q') }}"
                    @else Belum ada artikel @endif
                </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $edukasi->links() }}</div>

{{-- ============================================ --}}
{{-- MODAL: CREATE --}}
{{-- ============================================ --}}
<div id="createModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 900px; max-height: 92vh;" class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Tambah Artikel Edukasi</h3>
            </div>
            <button onclick="closeCreateModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'create' && $errors->any())
            <div class="mx-6 mt-4 rounded-xl px-4 py-3 text-sm" style="background: rgb(var(--danger-soft)); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="createForm" method="POST" action="{{ route('admin.edukasi.store') }}"
              enctype="multipart/form-data" novalidate class="flex-1 overflow-y-auto" x-data="createFormData()" x-init="init()">
            @csrf

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Judul Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                           minlength="5" maxlength="200"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.,:!?\-]/g, '')"
                           class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));"
                           placeholder="Contoh: Manfaat Ampas Tahu untuk Pupuk Organik">
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Minimal 5 karakter. Huruf, angka & tanda baca umum.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">
                        Thumbnail <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Disarankan</span>
                    </label>
                    <input type="file" name="thumbnail" accept="image/*" x-ref="fileInput" @change="handleFileChange($event)" class="hidden">

                    <div x-show="previewUrl" x-cloak class="mb-3 p-3 rounded-xl flex items-start gap-4" style="background: rgb(var(--success-soft) / 0.5); border: 1px solid rgb(var(--success) / 0.3);">
                        <div style="position: relative; flex-shrink: 0;">
                            <div @click="openLightbox(previewUrl)" style="width: 140px; height: 140px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid rgb(var(--surface));">
                                <img :src="previewUrl" style="width: 140px; height: 140px; object-fit: cover; display: block;">
                            </div>
                            <button type="button" @click.stop="cancelFile()"
                                    style="position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; background: rgb(var(--surface)); color: rgb(var(--danger)); border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-md); padding: 0;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="flex-1 min-w-0 pt-1">
                            <p class="text-[11px] font-bold uppercase mb-1" style="color: rgb(var(--success));">Preview</p>
                            <p class="text-sm font-medium break-all" style="color: rgb(var(--text-primary));" x-text="fileName"></p>
                        </div>
                    </div>

                    <div class="glass-input flex items-center gap-3 px-3 py-2">
                        <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 rounded-lg text-xs font-semibold flex-shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                            Choose File
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all" style="color: rgb(var(--text-muted));" x-text="fileName || 'No file chosen'"></span>
                    </div>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">JPG, PNG, WEBP · Max 2MB</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Konten Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="konten" rows="10" minlength="20" maxlength="10000" required
                              class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary)); resize: vertical;"
                              placeholder="Tulis konten artikel di sini...">{{ old('konten') }}</textarea>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Minimal 20 karakter.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Status <span style="color: rgb(var(--danger));">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (['draft' => 'Draft', 'publish' => 'Publish', 'scheduled' => 'Schedule'] as $val => $label)
                            <label style="cursor: pointer;">
                                <input type="radio" name="status" value="{{ $val }}" x-model="status" class="sr-only">
                                <div class="radio-card" :style="status === '{{ $val }}' ? 'border-color: rgb(var(--brand)); background: rgb(var(--brand) / 0.10); box-shadow: 0 0 0 3px rgb(var(--brand) / 0.18);' : ''">
                                    <p class="font-bold text-sm text-center" style="color: rgb(var(--text-primary));">{{ $label }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">
                        <b>Draft</b>: simpan tapi belum tayang · <b>Publish</b>: langsung tayang · <b>Schedule</b>: tayang otomatis sesuai jadwal
                    </p>
                </div>

                <div x-show="status === 'scheduled'" x-cloak>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Jadwal Tayang <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                           class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                    <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Minimal 5 menit dari sekarang · Waktu server: {{ now()->format('d M Y H:i') }}</p>
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 flex-shrink-0" style="border-top: 1px solid rgb(var(--border-soft)); background: rgb(var(--bg-secondary) / 0.5);">
            <button type="button" onclick="closeCreateModal()" class="glass-btn">Batal</button>
            <button type="submit" form="createForm" class="btn-primary">Simpan Artikel</button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: EDIT --}}
{{-- ============================================ --}}
<div id="editModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4" style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 900px; max-height: 92vh;" class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Edit Artikel Edukasi</h3>
            </div>
            <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'edit' && $errors->any())
            <div class="mx-6 mt-4 rounded-xl px-4 py-3 text-sm" style="background: rgb(var(--danger-soft)); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="editForm" method="POST" enctype="multipart/form-data" novalidate class="flex-1 overflow-y-auto" x-data="editFormData()" x-init="init()">
            @csrf @method('PUT')

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Judul Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="text" name="judul" x-model="judul"
                           minlength="5" maxlength="200"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.,:!?\-]/g, '')"
                           class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Thumbnail</label>

                    <input type="file" name="thumbnail" accept="image/*" x-ref="fileInput" @change="handleFileChange($event)" class="hidden">
                    <input type="hidden" name="hapus_thumbnail" :value="hapusThumbnailLama ? 1 : 0">

                    {{-- Existing --}}
                    <div x-show="existingImage && !hapusThumbnailLama && !previewUrl" x-cloak class="mb-3">
                        <div style="position: relative; display: inline-block;">
                            <div @click="openLightbox(existingImage)" style="width: 140px; height: 140px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 1px solid rgb(var(--border));">
                                <img :src="existingImage" style="width: 140px; height: 140px; object-fit: cover; display: block;">
                            </div>
                            <button type="button" @click.stop="hapusThumbnailLama = true"
                                    style="position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; background: rgb(var(--surface)); color: rgb(var(--danger)); border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-md); padding: 0;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <p class="text-xs mt-1" style="color: rgb(var(--text-muted));">Thumbnail saat ini · Klik untuk zoom</p>
                    </div>

                    <div x-show="hapusThumbnailLama" x-cloak class="mb-3 flex items-center gap-3 px-3 py-2 rounded-xl" style="background: rgb(var(--warning-soft)); border: 1px solid rgb(var(--warning) / 0.3);">
                        <span class="flex-1 text-xs" style="color: rgb(var(--warning));">Thumbnail lama akan dihapus saat disimpan.</span>
                        <button type="button" @click="hapusThumbnailLama = false" class="text-xs font-bold underline" style="color: rgb(var(--brand));">Urungkan</button>
                    </div>

                    <div x-show="previewUrl" x-cloak class="mb-3 p-3 rounded-xl flex items-start gap-4" style="background: rgb(var(--success-soft) / 0.5); border: 1px solid rgb(var(--success) / 0.3);">
                        <div style="position: relative; flex-shrink: 0;">
                            <div @click="openLightbox(previewUrl)" style="width: 140px; height: 140px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid rgb(var(--surface));">
                                <img :src="previewUrl" style="width: 140px; height: 140px; object-fit: cover; display: block;">
                            </div>
                            <button type="button" @click.stop="cancelFile()"
                                    style="position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; background: rgb(var(--surface)); color: rgb(var(--danger)); border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-md); padding: 0;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="flex-1 min-w-0 pt-1">
                            <p class="text-[11px] font-bold uppercase mb-1" style="color: rgb(var(--success));">Preview Baru</p>
                            <p class="text-sm font-medium break-all" style="color: rgb(var(--text-primary));" x-text="fileName"></p>
                        </div>
                    </div>

                    <div class="glass-input flex items-center gap-3 px-3 py-2">
                        <button type="button" @click="$refs.fileInput.click()" class="px-3 py-1.5 rounded-lg text-xs font-semibold flex-shrink-0" style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                            <span x-text="existingImage ? 'Ganti File' : 'Choose File'"></span>
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all" style="color: rgb(var(--text-muted));" x-text="fileName || 'No file chosen'"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Konten Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="konten" x-model="konten" rows="10" minlength="20" maxlength="10000" required
                              class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary)); resize: vertical;"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Status <span style="color: rgb(var(--danger));">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach (['draft' => 'Draft', 'publish' => 'Publish', 'scheduled' => 'Schedule'] as $val => $label)
                            <label style="cursor: pointer;">
                                <input type="radio" name="status" value="{{ $val }}" x-model="status" class="sr-only">
                                <div class="radio-card" :style="status === '{{ $val }}' ? 'border-color: rgb(var(--brand)); background: rgb(var(--brand) / 0.10); box-shadow: 0 0 0 3px rgb(var(--brand) / 0.18);' : ''">
                                    <p class="font-bold text-sm text-center" style="color: rgb(var(--text-primary));">{{ $label }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="status === 'scheduled'" x-cloak>
                    <label class="block text-sm font-medium mb-1" style="color: rgb(var(--text-primary));">Jadwal Tayang <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="datetime-local" name="scheduled_at" x-model="scheduledAt"
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                           class="glass-input w-full px-4 py-2.5 text-sm" style="color: rgb(var(--text-primary));">
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 flex-shrink-0" style="border-top: 1px solid rgb(var(--border-soft)); background: rgb(var(--bg-secondary) / 0.5);">
            <button type="button" onclick="closeEditModal()" class="glass-btn">Batal</button>
            <button type="submit" form="editForm" class="btn-primary">Update Artikel</button>
        </div>
    </div>
</div>

{{-- GLOBAL LIGHTBOX --}}
<div id="globalLightbox" class="fixed inset-0 hidden" style="z-index: 99999; background: rgba(0,0,0,0.92); backdrop-filter: blur(12px);">
    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 24px;" onclick="closeGlobalLightbox()">
        <img id="globalLightboxImg" src="" alt="Preview"
             onclick="event.stopPropagation()"
             style="max-width: 100%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
    </div>
    <button type="button" onclick="closeGlobalLightbox()"
            style="position: absolute; top: 20px; right: 20px; width: 44px; height: 44px; background: rgb(var(--danger)); color: white; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 100000; box-shadow: var(--shadow-lg);">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>

@endsection

@push('scripts')
<style>
    .radio-card {
        transition: all 0.2s ease;
        border: 2px solid rgb(var(--border));
        background: rgb(var(--surface));
        border-radius: 0.75rem;
        padding: 0.75rem;
    }
    label:hover .radio-card {
        border-color: rgb(var(--brand) / 0.5);
        background: rgb(var(--brand) / 0.04);
    }
</style>

<script>
    const EDUKASI_DATA = @json($edukasiJson ?? []);

    // ==== GLOBAL LIGHTBOX ====
    function openGlobalLightbox(url) {
        if (!url) return;
        document.getElementById('globalLightboxImg').src = url;
        document.getElementById('globalLightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeGlobalLightbox() {
        document.getElementById('globalLightbox').classList.add('hidden');
        document.getElementById('globalLightboxImg').src = '';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeGlobalLightbox(); });

    // ==== CREATE MODAL ====
    function openCreateModal() {
        const m = document.getElementById('createModal');
        m.classList.remove('hidden'); m.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeCreateModal() {
        const m = document.getElementById('createModal');
        m.classList.add('hidden'); m.classList.remove('flex');
        document.body.style.overflow = '';
        m.querySelectorAll('input[type=file]').forEach(i => i.value = '');
        const form = document.getElementById('createForm');
        if (form && form.__x && form.__x.$data) {
            form.__x.$data.previewUrl = null;
            form.__x.$data.fileName = null;
        }
    }

    // ==== EDIT MODAL ====
    function openEditModal(id) {
        const data = EDUKASI_DATA.find(e => e.id === id);
        if (!data) { alert('Data tidak ditemukan'); return; }

        const m = document.getElementById('editModal');
        m.classList.remove('hidden'); m.classList.add('flex');
        document.body.style.overflow = 'hidden';

        document.getElementById('editForm').action = '/admin/edukasi/' + id;

        const form = document.getElementById('editForm');
        const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
        if (state) {
            state.judul = data.judul || '';
            state.konten = data.konten || '';
            state.status = data.status || 'draft';
            state.scheduledAt = data.scheduled_at || '';
            state.existingImage = data.thumbnail || null;
            state.hapusThumbnailLama = false;
            state.previewUrl = null;
            state.fileName = null;
        }
    }
    function closeEditModal() {
        const m = document.getElementById('editModal');
        m.classList.add('hidden'); m.classList.remove('flex');
        document.body.style.overflow = '';
        m.querySelectorAll('input[type=file]').forEach(i => i.value = '');
    }

    document.addEventListener('click', e => {
        if (e.target.id === 'createModal') closeCreateModal();
        if (e.target.id === 'editModal') closeEditModal();
    });

    // ==== ALPINE DATA ====
    function createFormData() {
        return {
            status: '{{ old('status', 'publish') }}',
            fileName: null, previewUrl: null,
            init() {},
            openLightbox(url) { openGlobalLightbox(url); },
            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) { alert('⚠️ Hanya JPG, PNG, WEBP.'); e.target.value = ''; return; }
                if (file.size > 2 * 1024 * 1024) { alert('⚠️ Max 2MB.'); e.target.value = ''; return; }
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = URL.createObjectURL(file);
                this.fileName = file.name;
            },
            cancelFile() {
                this.$refs.fileInput.value = '';
                this.fileName = null;
                if (this.previewUrl) { URL.revokeObjectURL(this.previewUrl); this.previewUrl = null; }
            }
        };
    }

    function editFormData() {
        return {
            judul: '', konten: '', status: 'draft', scheduledAt: '',
            existingImage: null, hapusThumbnailLama: false,
            fileName: null, previewUrl: null,
            init() {},
            openLightbox(url) { openGlobalLightbox(url); },
            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) { alert('⚠️ Hanya JPG, PNG, WEBP.'); e.target.value = ''; return; }
                if (file.size > 2 * 1024 * 1024) { alert('⚠️ Max 2MB.'); e.target.value = ''; return; }
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = URL.createObjectURL(file);
                this.fileName = file.name;
            },
            cancelFile() {
                this.$refs.fileInput.value = '';
                this.fileName = null;
                if (this.previewUrl) { URL.revokeObjectURL(this.previewUrl); this.previewUrl = null; }
            }
        };
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if (session('open_modal') === 'create')
            openCreateModal();
        @elseif (session('open_modal') === 'edit' && session('edit_id'))
            openEditModal({{ session('edit_id') }});
        @endif
    });

    function selectStatusFilter(el) {
        document.getElementById('inputStatus').value = el.dataset.value;
        document.getElementById('statusLabel').textContent = el.dataset.label;
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.statusOpen = false;
    }
</script>
@endpush