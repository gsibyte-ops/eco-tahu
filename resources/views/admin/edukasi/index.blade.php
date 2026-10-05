@extends('layouts.admin')
@section('title', 'Kelola Edukasi')
@section('page-title', 'Kelola Edukasi')

@section('content')

@if (session('success'))
    <div class="mb-4 px-4 py-3 rounded-xl text-sm"
         style="background: rgb(var(--success-soft) / 0.6); border: 1px solid rgb(var(--success) / 0.3); color: rgb(var(--success));">
        {{ session('success') }}
    </div>
@endif

{{-- Statistik --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--info));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--info));">Total Artikel</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['total'] }}</p>
    </div>
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--success));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--success));">Published</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['publish'] }}</p>
    </div>
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--brand));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--brand));">Scheduled</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['scheduled'] }}</p>
    </div>
    <div class="glass-card p-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1" style="background: rgb(var(--warning));"></div>
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--warning));">Draft</p>
        <p class="text-3xl font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $stats['draft'] }}</p>
    </div>
</div>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold txt-primary">Daftar Artikel Edukasi</h2>
        <p class="text-sm txt-secondary">Kelola artikel edukasi seputar tahu dan lingkungan.</p>
    </div>
    <button type="button" onclick="openCreateModal()" class="btn-primary">
        + Tambah Artikel
    </button>
</div>

{{-- Filter --}}
<div class="glass-card filter-card p-4 mb-6 relative z-30" x-data="{ statusOpen: false }">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Cari Artikel</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                       placeholder="Cari judul artikel..."
                       class="glass-input w-full pl-10 pr-4 py-2.5 text-sm"
                       style="color: rgb(var(--text-primary));">
            </div>
        </div>

        <div style="width: 180px;" class="relative" @click.away="statusOpen = false">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Status</label>
            <button type="button" @click="statusOpen = !statusOpen"
                    class="glass-input w-full flex items-center justify-between gap-2 py-2.5 text-sm text-left"
                    style="color: rgb(var(--text-primary));">
                <span class="truncate" id="filterStatusLabel">
                    @php
                        $filterStatusLabel = match(request('status')) {
                            'publish'   => 'Publish',
                            'scheduled' => 'Scheduled',
                            'draft'     => 'Draft',
                            default     => 'Semua Status',
                        };
                    @endphp
                    {{ $filterStatusLabel }}
                </span>
                <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="statusOpen" x-cloak
                 class="absolute left-0 right-0 z-50 mt-2 rounded-xl overflow-hidden"
                 style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                @foreach (['' => 'Semua Status', 'publish' => 'Publish', 'scheduled' => 'Scheduled', 'draft' => 'Draft'] as $val => $label)
                    <button type="button"
                            data-value="{{ $val }}"
                            data-label="{{ $label }}"
                            onclick="selectStatusFilter(this)"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-500/10 transition"
                            style="{{ request('status') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                        <span>{{ $label }}</span>
                        @if (request('status') == $val)
                            <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="status" id="filterInputStatus" value="{{ request('status') }}">
        </div>

        <button type="submit" class="btn-primary whitespace-nowrap">Filter</button>

        @if (request()->hasAny(['q', 'status']))
            <a href="{{ route('admin.edukasi.index') }}" class="glass-btn whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Tabel --}}
<div class="glass-card overflow-hidden relative z-0">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-xs font-bold uppercase tracking-widest" style="color: rgb(var(--text-muted)); border-bottom: 1px solid rgb(var(--border-soft));">
                    <th class="px-6 py-3">Thumbnail</th>
                    <th class="px-6 py-3">Judul</th>
                    <th class="px-6 py-3">Penulis</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($edukasi as $e)
                    <tr class="transition" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        <td class="px-6 py-4">
                            @if ($e->thumbnail)
                                <img src="{{ asset('storage/' . $e->thumbnail) }}" alt="{{ $e->judul }}"
                                     class="w-16 h-12 rounded-lg object-cover" style="border: 1px solid rgb(var(--border-soft));">
                            @else
                                <div class="w-16 h-12 rounded-lg flex items-center justify-center txt-muted text-xs"
                                     style="background: rgb(var(--bg-secondary));">No img</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold txt-primary">{{ $e->judul }}</p>
                            <p class="text-xs txt-secondary">{{ Str::limit($e->konten, 60) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm txt-secondary">{{ $e->user->username ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm txt-secondary">
                            @if ($e->status === 'publish' && $e->published_at)
                                {{ $e->published_at->format('d M Y') }}
                            @elseif ($e->status === 'scheduled' && $e->scheduled_at)
                                <span style="color: rgb(var(--info));">{{ $e->scheduled_at->format('d M Y H:i') }}</span>
                            @else
                                <span class="txt-muted">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($e->status === 'publish')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">Publish</span>
                            @elseif ($e->status === 'scheduled')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">Scheduled</span>
                                <p class="text-xs txt-secondary mt-1">→ {{ $e->scheduled_at?->format('d M Y H:i') }}</p>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button type="button" onclick="openEditModal({{ $e->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                    style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            <form action="{{ route('admin.edukasi.destroy', $e->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus artikel ini?')">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                        style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-10 text-center txt-muted">
                        @if (request('q'))
                            Tidak ada artikel dengan kata kunci "<span class="font-semibold txt-secondary">{{ request('q') }}</span>"
                        @else
                            Belum ada artikel
                        @endif
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $edukasi->links() }}</div>

{{-- ============================================ --}}
{{-- MODAL: CREATE --}}
{{-- ============================================ --}}
<div id="createModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 800px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Tambah Artikel Edukasi</h3>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'create' && $errors->any())
            <div class="mx-6 mt-4 px-4 py-3 rounded-xl text-sm"
                 style="background: rgb(var(--danger-soft) / 0.6); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
                <p class="font-bold mb-1">Ada beberapa kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="createForm" method="POST" action="{{ route('admin.edukasi.store') }}"
              enctype="multipart/form-data" novalidate class="flex-1 overflow-y-auto"
              x-data="createFormData()" x-init="init()">
            @csrf

            <div class="p-6 space-y-5">

                {{-- JUDUL --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Judul Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                           minlength="5" maxlength="200"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.,:!?\-]/g, '')"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                           style="color: rgb(var(--text-primary));"
                           placeholder="Contoh: Manfaat Ampas Tahu untuk Pupuk Organik">
                    <p class="text-xs txt-secondary mt-1">Huruf, angka, spasi & tanda baca umum. Minimal 5 karakter.</p>
                </div>

                {{-- THUMBNAIL --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">
                        Thumbnail
                        <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Disarankan</span>
                        <span class="txt-muted font-normal">(opsional)</span>
                    </label>

                    <input type="file" name="thumbnail" accept="image/jpeg,image/jpg,image/png,image/webp"
                           x-ref="fileInput" @change="handleFileChange($event)" class="hidden">

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

                    <div x-show="previewUrl" x-cloak
                         style="display: flex; align-items: flex-start; gap: 16px; padding: 16px; background: rgb(var(--brand-soft) / 0.3); border: 1px solid rgb(var(--brand) / 0.3); border-radius: 12px; margin-bottom: 12px;">
                        <div style="position: relative; flex-shrink: 0;">
                            <div @click="openLightbox(previewUrl)"
                                 style="position: relative; width: 160px; height: 160px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid rgb(var(--border));"
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

                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default glass-card focus-within:ring-2 focus-within:ring-emerald-500 transition">
                        <button type="button" @click="$refs.fileInput.click()"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0"
                                style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                            Choose File
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="fileName || 'No file chosen'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1">Format: JPG, PNG, WEBP. Max 2MB.</p>
                </div>

                {{-- KONTEN --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Konten Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="konten" rows="10" minlength="20" maxlength="10000" required
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input resize-none"
                              style="color: rgb(var(--text-primary));"
                              placeholder="Tulis konten artikel di sini...">{{ old('konten') }}</textarea>
                    <p class="text-xs txt-secondary mt-1">Minimal 20 karakter.</p>
                </div>

                {{-- STATUS --}}
                <div class="relative" @click.away="statusOpen = false">
                    <label class="block text-sm font-medium txt-primary mb-1">Status <span style="color: rgb(var(--danger));">*</span></label>
                    <button type="button" @click="statusOpen = !statusOpen"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default glass-card hover:bd-strong focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                        <span class="txt-primary" x-text="statusLabel"></span>
                        <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="statusOpen" x-cloak
                         class="absolute left-0 right-0 z-30 mt-2 glass-card rounded-xl border bd-soft shadow-lg overflow-hidden">
                        <button type="button" @click="selectStatus('draft', 'Draft')"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition txt-primary">
                            <span>Draft</span>
                            <span class="text-xs txt-muted">— simpan tapi belum tampil</span>
                        </button>
                        <button type="button" @click="selectStatus('publish', 'Publish')"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition txt-primary">
                            <span>Publish</span>
                            <span class="text-xs txt-muted">— langsung tayang</span>
                        </button>
                        <button type="button" @click="selectStatus('scheduled', 'Schedule')"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition txt-primary">
                            <span>Schedule</span>
                            <span class="text-xs txt-muted">— tayang otomatis</span>
                        </button>
                    </div>
                    <input type="hidden" name="status" :value="statusValue">
                </div>

                {{-- SCHEDULED_AT --}}
                <div x-show="statusValue === 'scheduled'" x-cloak>
                    <label class="block text-sm font-medium txt-primary mb-1">Jadwal Tayang <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="datetime-local" name="scheduled_at" :value="scheduledAt"
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                           style="color: rgb(var(--text-primary));">
                    <p class="text-xs txt-secondary mt-1">Minimal 5 menit dari sekarang. Waktu server: {{ now()->format('d M Y H:i') }}</p>
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
<div id="editModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 800px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Edit Artikel Edukasi</h3>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        @if (session('open_modal') === 'edit' && $errors->any())
            <div class="mx-6 mt-4 px-4 py-3 rounded-xl text-sm"
                 style="background: rgb(var(--danger-soft) / 0.6); border: 1px solid rgb(var(--danger) / 0.3); color: rgb(var(--danger));">
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

            <div class="p-6 space-y-5">

                {{-- JUDUL --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Judul Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="text" name="judul" id="editJudul"
                           minlength="5" maxlength="200"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.,:!?\-]/g, '')"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                           style="color: rgb(var(--text-primary));">
                    <p class="text-xs txt-secondary mt-1">Huruf, angka, spasi & tanda baca umum. Minimal 5 karakter.</p>
                </div>

                {{-- THUMBNAIL --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">
                        Thumbnail
                        <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Disarankan</span>
                    </label>

                    {{-- Existing --}}
                    <div x-show="existingImage && !hapusThumbnailLama && !previewUrl" x-cloak class="mb-3">
                        <div style="position: relative; display: inline-block;">
                            <div @click="openLightbox(existingImage)"
                                 style="position: relative; width: 120px; height: 120px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 1px solid rgb(var(--border));"
                                 onmouseover="this.querySelector('.zoom-overlay').style.opacity='1'"
                                 onmouseout="this.querySelector('.zoom-overlay').style.opacity='0'">
                                <img :src="existingImage" alt="Existing"
                                     style="width: 120px; height: 120px; object-fit: cover; display: block;">
                                <div class="zoom-overlay"
                                     style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;">
                                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                </div>
                            </div>
                            <button type="button" @click.stop="hapusThumbnailLama = true"
                                    style="position: absolute; top: 6px; right: 6px; width: 26px; height: 26px; background: rgba(255,255,255,0.95); color: #ef4444; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); padding: 0;"
                                    title="Hapus thumbnail">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <p style="font-size: 11px; margin-top: 6px; color: rgb(var(--text-muted));">Thumbnail saat ini · Klik untuk zoom</p>
                    </div>

                    {{-- Hapus banner --}}
                    <div x-show="hapusThumbnailLama" x-cloak
                         class="mb-3 flex items-center gap-3 px-3 py-2 rounded-xl"
                         style="background: rgb(var(--warning-soft) / 0.5); border: 1px solid rgb(var(--warning) / 0.3);">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-xs" style="color: rgb(var(--warning));">Thumbnail lama akan dihapus saat disimpan.</span>
                        <button type="button" @click="hapusThumbnailLama = false"
                                class="text-xs font-bold underline" style="color: rgb(var(--brand));">Urungkan</button>
                    </div>

                    <input type="file" name="thumbnail" accept="image/jpeg,image/jpg,image/png,image/webp"
                           x-ref="fileInput" @change="handleFileChange($event)" class="hidden">
                    <input type="hidden" name="hapus_thumbnail" :value="hapusThumbnailLama ? 1 : 0">

                    {{-- Undo banner --}}
                    <div x-show="showUndo" x-cloak
                         class="mb-3 flex items-center gap-3 px-3 py-2 rounded-xl"
                         style="background: rgb(var(--warning-soft) / 0.5); border: 1px solid rgb(var(--warning) / 0.3);">
                        <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--warning));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="flex-1 text-xs truncate" style="color: rgb(var(--warning));">
                            File <strong x-text="undoFileName"></strong> dibatalkan
                        </span>
                        <button type="button" @click="undoCancel()"
                                class="text-xs font-bold underline flex-shrink-0" style="color: rgb(var(--brand));">Urungkan</button>
                    </div>

                    {{-- Preview baru --}}
                    <div x-show="previewUrl" x-cloak
                         style="display: flex; align-items: flex-start; gap: 16px; padding: 12px; background: rgb(var(--brand-soft) / 0.3); border: 1px solid rgb(var(--brand) / 0.3); border-radius: 12px; margin-bottom: 12px;">
                        <div style="position: relative; flex-shrink: 0;">
                            <div @click="openLightbox(previewUrl)"
                                 style="position: relative; width: 120px; height: 120px; border-radius: 12px; overflow: hidden; cursor: zoom-in; border: 2px solid rgb(var(--border));"
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
                            <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; margin: 0 0 4px 0; color: rgb(var(--brand));">Preview Baru</p>
                            <p class="break-all" style="font-size: 12px; font-weight: 500; margin: 0; line-height: 1.4; color: rgb(var(--text-primary));" x-text="fileName"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default glass-card focus-within:ring-2 focus-within:ring-emerald-500 transition">
                        <button type="button" @click="$refs.fileInput.click()"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0"
                                style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                            <span x-text="existingImage ? 'Ganti File' : 'Choose File'"></span>
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="fileName || 'No file chosen'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1">Format: JPG, PNG, WEBP. Max 2MB.</p>
                </div>

                {{-- KONTEN --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Konten Artikel <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="konten" id="editKonten" rows="10" minlength="20" maxlength="10000" required
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input resize-none"
                              style="color: rgb(var(--text-primary));"></textarea>
                    <p class="text-xs txt-secondary mt-1">Minimal 20 karakter.</p>
                </div>

                {{-- STATUS --}}
                <div class="relative" @click.away="statusOpen = false">
                    <label class="block text-sm font-medium txt-primary mb-1">Status <span style="color: rgb(var(--danger));">*</span></label>
                    <button type="button" @click="statusOpen = !statusOpen"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm rounded-xl border bd-default glass-card hover:bd-strong focus:outline-none focus:ring-2 focus:ring-emerald-500 transition text-left">
                        <span class="txt-primary" x-text="statusLabel"></span>
                        <svg class="w-4 h-4 txt-muted flex-shrink-0" :class="statusOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="statusOpen" x-cloak
                         class="absolute left-0 right-0 z-30 mt-2 glass-card rounded-xl border bd-soft shadow-lg overflow-hidden">
                        <button type="button" @click="selectStatus('draft', 'Draft')"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition txt-primary">
                            <span>Draft</span>
                            <span class="text-xs txt-muted">— simpan tapi belum tampil</span>
                        </button>
                        <button type="button" @click="selectStatus('publish', 'Publish')"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition txt-primary">
                            <span>Publish</span>
                            <span class="text-xs txt-muted">— langsung tayang</span>
                        </button>
                        <button type="button" @click="selectStatus('scheduled', 'Schedule')"
                                class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left hover:bg-emerald-500/10 transition txt-primary">
                            <span>Schedule</span>
                            <span class="text-xs txt-muted">— tayang otomatis</span>
                        </button>
                    </div>
                    <input type="hidden" name="status" id="editStatusHidden" :value="statusValue">
                </div>

                {{-- SCHEDULED_AT --}}
                <div x-show="statusValue === 'scheduled'" x-cloak>
                    <label class="block text-sm font-medium txt-primary mb-1">Jadwal Tayang <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="datetime-local" name="scheduled_at" id="editScheduledAt" :value="scheduledAt"
                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                           style="color: rgb(var(--text-primary));">
                    <p class="text-xs txt-secondary mt-1">Minimal 5 menit dari sekarang. Waktu server: {{ now()->format('d M Y H:i') }}</p>
                </div>
            </div>
        </form>

        <div class="flex justify-end gap-2 px-6 py-4 flex-shrink-0" style="border-top: 1px solid rgb(var(--border-soft)); background: rgb(var(--bg-secondary) / 0.5);">
            <button type="button" onclick="closeEditModal()" class="glass-btn">Batal</button>
            <button type="submit" form="editForm" class="btn-primary">Update Artikel</button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- GLOBAL LIGHTBOX --}}
{{-- ============================================ --}}
<div id="globalLightbox" class="fixed inset-0 hidden" style="z-index: 99999; background: rgba(0,0,0,0.9);">
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
    const EDUKASI_DATA = @json($edukasiJson);

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

    // ==== FILTER HANDLER ====
    function selectStatusFilter(el) {
        document.getElementById('filterInputStatus').value = el.dataset.value;
        document.getElementById('filterStatusLabel').textContent = el.dataset.label;
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.statusOpen = false;
    }

    // ==== FILE STATE ====
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
        const fileInput = modal.querySelector('input[name="thumbnail"]');
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
            }
        }
    }

    // ==== EDIT MODAL ====
    function openEditModal(id) {
        const data = EDUKASI_DATA.find(e => e.id === id);
        if (!data) return;

        fileState.reset();
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        document.getElementById('editForm').action = '/admin/edukasi/' + id;

        document.getElementById('editJudul').value = data.judul;
        document.getElementById('editKonten').value = data.konten;
        document.getElementById('editIdInput').value = id;

        const statusLabelText = data.status === 'scheduled' ? 'Schedule' : (data.status === 'draft' ? 'Draft' : 'Publish');

        const form = document.getElementById('editForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.currentId = id;
                state.statusValue = data.status;
                state.statusLabel = statusLabelText;
                state.scheduledAt = data.scheduled_at || '';
                state.existingImage = data.thumbnail;
                state.hapusThumbnailLama = false;
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
        const fileInput = modal.querySelector('input[name="thumbnail"]');
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
            }
        }
    }

    // ==== ALPINE DATA ====
    function createFormData() {
        return {
            statusOpen: false,
            statusValue: '{{ old('status', 'publish') }}',
            statusLabel: '{{ old('status') === 'draft' ? 'Draft' : (old('status') === 'scheduled' ? 'Schedule' : 'Publish') }}',
            scheduledAt: '{{ old('scheduled_at') }}',
            fileName: null,
            previewUrl: null,
            undoFile: null,
            undoFileName: null,
            showUndo: false,
            undoTimer: null,

            init() { this.fileName = null; this.previewUrl = null; this.showUndo = false; },

            selectStatus(value, label) {
                this.statusValue = value;
                this.statusLabel = label;
                this.statusOpen = false;
            },

            openLightbox(url) { openGlobalLightbox(url); },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('⚠️ Hanya file gambar yang diperbolehkan (JPG, PNG, WEBP).');
                    e.target.value = ''; this.fileName = null; return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ Ukuran gambar maksimal 2MB.');
                    e.target.value = ''; this.fileName = null; return;
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
                    this.showUndo = false; this.undoFile = null; this.undoFileName = null;
                }, 10000);
            },

            undoCancel() {
                if (!this.undoFile) return;
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
            statusOpen: false,
            statusValue: 'publish',
            statusLabel: 'Publish',
            scheduledAt: '',
            currentId: null,
            existingImage: null,
            hapusThumbnailLama: false,
            fileName: null,
            previewUrl: null,
            undoFile: null,
            undoFileName: null,
            showUndo: false,
            undoTimer: null,

            init() { this.fileName = null; this.previewUrl = null; this.showUndo = false; },

            selectStatus(value, label) {
                this.statusValue = value;
                this.statusLabel = label;
                this.statusOpen = false;
            },

            openLightbox(url) { openGlobalLightbox(url); },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('⚠️ Hanya file gambar yang diperbolehkan (JPG, PNG, WEBP).');
                    e.target.value = ''; this.fileName = null; return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ Ukuran gambar maksimal 2MB.');
                    e.target.value = ''; this.fileName = null; return;
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
                    this.showUndo = false; this.undoFile = null; this.undoFileName = null;
                }, 10000);
            },

            undoCancel() {
                if (!this.undoFile) return;
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
            if (!lb.classList.contains('hidden')) { closeGlobalLightbox(); return; }
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush