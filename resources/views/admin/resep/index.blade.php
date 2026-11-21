@extends('layouts.admin')
@section('title', 'Kelola Resep Olahan Tahu')
@section('page-title', 'Kelola Resep Olahan Tahu')

@push('styles')
<style>
    /* CSS KHUSUS BIAR SIDEBAR & HEADER ILANG PAS MODAL MUNCUL */
    body.modal-active #adminSidebar { display: none !important; }
    body.modal-active .sticky.top-0 { display: none !important; }
    body.modal-active .lg\:ml-\[17rem\] { margin-left: 0 !important; }
</style>
@endpush

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
        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: rgb(var(--info));">Total Resep</p>
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

<div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
    <div>
        <h2 class="text-xl font-bold txt-primary">Daftar Resep Olahan Tahu</h2>
        <p class="text-sm txt-secondary">Kelola kreasi resep olahan tahu lengkap dengan foto, video, atau link YouTube resmi.</p>
    </div>
    <button type="button" onclick="openCreateModal()" class="btn-primary">
        + Tambah Resep
    </button>
</div>

{{-- Filter --}}
<div class="glass-card filter-card p-4 mb-6 relative z-30" x-data="{ statusOpen: false, diffOpen: false }">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Cari Resep</label>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" style="color: rgb(var(--text-muted));"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" data-filter="title"
                       placeholder="Cari judul resep..."
                       class="glass-input w-full pl-10 pr-4 py-2.5 text-sm"
                       style="color: rgb(var(--text-primary));">
            </div>
        </div>

        <div style="width: 170px;" class="relative" @click.away="statusOpen = false">
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

        <div style="width: 170px;" class="relative" @click.away="diffOpen = false">
            <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: rgb(var(--text-muted));">Kesulitan</label>
            <button type="button" @click="diffOpen = !diffOpen"
                    class="glass-input w-full flex items-center justify-between gap-2 py-2.5 text-sm text-left"
                    style="color: rgb(var(--text-primary));">
                <span class="truncate" id="filterDiffLabel">
                    @php
                        $filterDiffLabel = match(request('tingkat_kesulitan')) {
                            'mudah'  => 'Mudah',
                            'sedang' => 'Sedang',
                            'sulit'  => 'Sulit',
                            default  => 'Semua Tingkat',
                        };
                    @endphp
                    {{ $filterDiffLabel }}
                </span>
                <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-muted));" :class="diffOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="diffOpen" x-cloak
                 class="absolute left-0 right-0 z-50 mt-2 rounded-xl overflow-hidden"
                 style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow-lg);">
                @foreach (['' => 'Semua Tingkat', 'mudah' => 'Mudah', 'sedang' => 'Sedang', 'sulit' => 'Sulit'] as $val => $label)
                    <button type="button"
                            data-value="{{ $val }}"
                            data-label="{{ $label }}"
                            onclick="selectDiffFilter(this)"
                            class="w-full flex items-center justify-between gap-2 px-4 py-2.5 text-sm text-left whitespace-nowrap hover:bg-emerald-500/10 transition"
                            style="{{ request('tingkat_kesulitan') == $val ? 'color: rgb(var(--brand)); font-weight: 600;' : 'color: rgb(var(--text-secondary));' }}">
                        <span>{{ $label }}</span>
                        @if (request('tingkat_kesulitan') == $val)
                            <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                @endforeach
            </div>

            <input type="hidden" name="tingkat_kesulitan" id="filterInputDiff" value="{{ request('tingkat_kesulitan') }}">
        </div>

        <button type="submit" class="btn-primary whitespace-nowrap">Filter</button>

        @if (request()->hasAny(['q', 'status', 'tingkat_kesulitan']))
            <a href="{{ route('admin.resep.index') }}" class="glass-btn whitespace-nowrap">
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
                    <th class="px-6 py-3">Media</th>
                    <th class="px-6 py-3">Resep & Detail</th>
                    <th class="px-6 py-3">Penulis</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resep as $r)
                    @php
                        $diffBadgeStyle = match($r->tingkat_kesulitan) {
                            'sulit'  => 'background: rgb(var(--danger-soft)); color: rgb(var(--danger));',
                            'sedang' => 'background: rgb(var(--warning-soft)); color: rgb(var(--warning));',
                            default  => 'background: rgb(var(--success-soft)); color: rgb(var(--success));',
                        };
                    @endphp
                    <tr class="transition" style="border-bottom: 1px solid rgb(var(--border-soft));">
                        <td class="px-6 py-4">
                            <div class="relative inline-block">
                                @if ($r->display_thumbnail_url)
                                    <img src="{{ $r->display_thumbnail_url }}" alt="{{ $r->judul }}"
                                         class="w-16 h-12 rounded-lg object-cover" style="border: 1px solid rgb(var(--border-soft));">
                                @else
                                    <div class="w-16 h-12 rounded-lg flex items-center justify-center txt-muted"
                                         style="background: rgb(var(--bg-secondary));"><x-icon name="utensils" class="w-6 h-6 opacity-50" /></div>
                                @endif
                            </div>
                            <div class="mt-1">
                                @if (($r->media_type ?? 'image') === 'youtube')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold"
                                          style="background: rgb(var(--danger-soft)); color: rgb(var(--danger));">▶ YouTube</span>
                                @elseif (($r->media_type ?? 'image') === 'video')
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold"
                                          style="background: rgb(var(--info-soft)); color: rgb(var(--info));"><x-icon name="film" class="w-3 h-3" /> Video</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold"
                                          style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));"><x-icon name="photo" class="w-3 h-3" /> Foto</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold txt-primary">{{ $r->judul }}</p>
                            @if ($r->deskripsi)
                                <p class="text-xs txt-secondary mt-0.5">{{ Str::limit($r->deskripsi, 65) }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold tabular-nums"
                                      style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));"><x-icon name="clock" class="w-3 h-3" /> {{ $r->durasi_menit }} mnt</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold tabular-nums"
                                      style="background: rgb(var(--bg-secondary)); color: rgb(var(--text-secondary));"><x-icon name="utensils" class="w-3 h-3" /> {{ $r->porsi }} porsi</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold capitalize"
                                      style="{{ $diffBadgeStyle }}">{{ $r->tingkat_kesulitan }}</span>
                                <span class="inline-flex items-center gap-1 text-[11px] txt-muted tabular-nums"><x-icon name="heart-solid" class="w-3 h-3 text-rose-500" /> {{ $r->likes_count ?? 0 }} suka</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm txt-secondary">{{ $r->user->username ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm txt-secondary">
                            @if ($r->status === 'publish' && $r->published_at)
                                {{ $r->published_at->format('d M Y') }}
                            @elseif ($r->status === 'scheduled' && $r->scheduled_at)
                                <span style="color: rgb(var(--info));">{{ $r->scheduled_at->format('d M Y H:i') }}</span>
                            @else
                                <span class="txt-muted">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($r->status === 'publish')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--success-soft)); color: rgb(var(--success));">Publish</span>
                            @elseif ($r->status === 'scheduled')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--info-soft)); color: rgb(var(--info));">Scheduled</span>
                                <p class="text-xs txt-secondary mt-1">→ {{ $r->scheduled_at?->format('d M Y H:i') }}</p>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <button type="button" onclick="openEditModal({{ $r->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                    style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>
                            <form action="{{ route('admin.resep.destroy', $r->id) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus resep ini?')">
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
                            Tidak ada resep dengan kata kunci "<span class="font-semibold txt-secondary">{{ request('q') }}</span>"
                        @else
                            Belum ada resep olahan tahu
                        @endif
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $resep->links() }}</div>

{{-- ============================================ --}}
{{-- MODAL: CREATE --}}
{{-- ============================================ --}}
<div id="createModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 820px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Tambah Resep Olahan Tahu</h3>
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
                    @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form id="createForm" method="POST" action="{{ route('admin.resep.store') }}"
              enctype="multipart/form-data" novalidate class="flex-1 overflow-y-auto"
              x-data="createFormData()" x-init="init()">
            @csrf

            <div class="p-6 space-y-5">

                {{-- JUDUL --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Judul Resep <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                           minlength="4" maxlength="200" data-filter="title"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                           style="color: rgb(var(--text-primary));"
                           placeholder="Contoh: Tahu Crispy Saus Asam Manis">
                    <p class="text-xs txt-secondary mt-1">Huruf, angka, spasi & tanda baca umum. Minimal 4 karakter.</p>
                </div>

                {{-- METADATA RESEP --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Durasi (Menit) <span style="color: rgb(var(--danger));">*</span></label>
                        <input type="text" inputmode="numeric" name="durasi_menit" value="{{ old('durasi_menit', 20) }}"
                               maxlength="4" data-filter="number"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input tabular-nums"
                               style="color: rgb(var(--text-primary));"
                               placeholder="20">
                        <p class="text-[11px] txt-muted mt-1">Khusus angka (1 - 1440 menit).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Porsi <span style="color: rgb(var(--danger));">*</span></label>
                        <input type="text" inputmode="numeric" name="porsi" value="{{ old('porsi', 2) }}"
                               maxlength="3" data-filter="number"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input tabular-nums"
                               style="color: rgb(var(--text-primary));"
                               placeholder="2">
                        <p class="text-[11px] txt-muted mt-1">Khusus angka (1 - 100 porsi).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Tingkat Kesulitan <span style="color: rgb(var(--danger));">*</span></label>
                        <select name="tingkat_kesulitan"
                                class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                                style="color: rgb(var(--text-primary)); background: rgb(var(--surface));">
                            <option value="mudah" @selected(old('tingkat_kesulitan', 'mudah') === 'mudah')>Mudah</option>
                            <option value="sedang" @selected(old('tingkat_kesulitan') === 'sedang')>Sedang</option>
                            <option value="sulit" @selected(old('tingkat_kesulitan') === 'sulit')>Sulit</option>
                        </select>
                    </div>
                </div>

                {{-- PILIH MODE MEDIA --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-2">Jenis Media Resep <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="hidden" name="media_type" :value="mediaType">
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="mediaType = 'image'"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold border transition flex items-center justify-center gap-1.5"
                                :style="mediaType === 'image' ? 'background: rgb(var(--brand-soft)); border-color: rgb(var(--brand)); color: rgb(var(--brand-strong));' : 'border-color: rgb(var(--border-soft)); color: rgb(var(--text-secondary));'">
                            <x-icon name="photo" class="w-3.5 h-3.5" /> Foto Manual
                        </button>
                        <button type="button" @click="mediaType = 'video'"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold border transition flex items-center justify-center gap-1.5"
                                :style="mediaType === 'video' ? 'background: rgb(var(--info-soft)); border-color: rgb(var(--info)); color: rgb(var(--info));' : 'border-color: rgb(var(--border-soft)); color: rgb(var(--text-secondary));'">
                            <x-icon name="film" class="w-3.5 h-3.5" /> Upload Video
                        </button>
                        <button type="button" @click="mediaType = 'youtube'"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold border transition flex items-center justify-center gap-1.5"
                                :style="mediaType === 'youtube' ? 'background: rgb(var(--danger-soft)); border-color: rgb(var(--danger)); color: rgb(var(--danger));' : 'border-color: rgb(var(--border-soft)); color: rgb(var(--text-secondary));'">
                            <span>▶</span> Link YouTube
                        </button>
                    </div>
                </div>

                {{-- UPLOAD VIDEO MANUAL --}}
                <div x-show="mediaType === 'video'" x-cloak class="p-4 rounded-xl"
                     style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border-soft));">
                    <label class="block text-sm font-medium txt-primary mb-2">
                        Upload Video Resep <span style="color: rgb(var(--danger));">*</span>
                    </label>
                    <input type="file" name="video_file" accept="video/mp4,video/quicktime,video/webm"
                           data-filter="video-only" x-ref="videoInput" @change="handleVideoChange($event)" class="hidden">

                    <div x-show="videoPreviewUrl" x-cloak class="mb-3 rounded-xl overflow-hidden bg-black/90 p-2"
                         style="border: 1px solid rgb(var(--border-soft));">
                        <video :src="videoPreviewUrl" controls class="w-full max-h-56 rounded-lg"></video>
                        <div class="flex items-center justify-between gap-2 mt-2 px-1">
                            <span class="text-xs text-white/80 truncate" x-text="videoFileName"></span>
                            <button type="button" @click="cancelVideo()" class="text-xs font-bold text-red-400 hover:underline flex-shrink-0">
                                Batalkan Video
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default glass-card">
                        <button type="button" @click="$refs.videoInput.click()"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0"
                                style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                            Pilih Video
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="videoFileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="videoFileName || 'Belum ada video dipilih'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1.5">Hanya video MP4, MOV, atau WEBM. Maksimal 20MB.</p>
                </div>

                {{-- LINK YOUTUBE ONLY --}}
                <div x-show="mediaType === 'youtube'" x-cloak class="p-4 rounded-xl"
                     style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border-soft));">
                    <label class="block text-sm font-medium txt-primary mb-1.5">
                        Link YouTube Resmi <span style="color: rgb(var(--danger));">*</span>
                    </label>
                    <input type="url" name="youtube_url" x-model="youtubeUrl"
                           placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/..."
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input text-sm"
                           style="color: rgb(var(--text-primary));">
                    <p class="text-xs txt-muted mt-1">Hanya menerima link YouTube resmi (`youtube.com` / `youtu.be`). TikTok, IG, atau link lain ditolak.</p>

                    <template x-if="youtubeUrl && !getYoutubeId()">
                        <p class="text-xs font-semibold mt-2 flex items-center gap-1.5" style="color: rgb(var(--danger));">
                            <x-icon name="alert-triangle" class="w-3.5 h-3.5" />
                            <span>Hanya link YouTube resmi (youtube.com atau youtu.be) yang diperbolehkan!</span>
                        </p>
                    </template>
                    <template x-if="getYoutubeId()">
                        <div class="mt-3">
                            <p class="text-xs font-semibold mb-1.5 flex items-center gap-1.5" style="color: rgb(var(--success));">
                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                <span>Link YouTube valid — Preview:</span>
                            </p>
                            <div class="aspect-video rounded-xl overflow-hidden bg-black" style="border: 1px solid rgb(var(--border-soft));">
                                <iframe :src="'https://www.youtube.com/embed/' + getYoutubeId()" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- THUMBNAIL / FOTO --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">
                        <span x-text="mediaType === 'image' ? 'Foto Masakan / Thumbnail' : 'Cover Thumbnail (Opsional)'"></span>
                        <span class="text-xs px-2 py-0.5 rounded font-medium" style="background: rgb(var(--warning-soft)); color: rgb(var(--warning));">Disarankan</span>
                        <span class="txt-muted font-normal">(opsional)</span>
                    </label>

                    <input type="file" name="thumbnail" accept="image/jpeg,image/jpg,image/png,image/webp"
                           data-filter="image-only" x-ref="fileInput" @change="handleFileChange($event)" class="hidden">

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
                            Pilih Foto
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="fileName || 'Belum ada foto dipilih'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1">Hanya file FOTO asli (JPG, PNG, WEBP). Maksimal 2MB.</p>
                </div>

                {{-- DESKRIPSI --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Deskripsi Singkat <span class="txt-muted font-normal">(opsional)</span></label>
                    <textarea name="deskripsi" rows="3" maxlength="1000"
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input resize-none"
                              style="color: rgb(var(--text-primary));"
                              placeholder="Ceritakan singkat kelezatan atau tips utama resep ini...">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- BAHAN --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Bahan-Bahan <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="bahan" rows="6" minlength="5" maxlength="10000" required
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input"
                              style="color: rgb(var(--text-primary));"
                              placeholder="Tulis 1 bahan per baris, contoh:&#10;5 buah tahu putih organik EcoTahu&#10;3 sdm tepung maizena&#10;2 siung bawang putih halus">{{ old('bahan') }}</textarea>
                    <p class="text-xs txt-secondary mt-1">1 baris = 1 bahan (otomatis menjadi checklist untuk pengguna).</p>
                </div>

                {{-- LANGKAH --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Langkah Memasak <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="langkah" rows="7" minlength="10" maxlength="15000" required
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input"
                              style="color: rgb(var(--text-primary));"
                              placeholder="Tulis 1 langkah per baris, contoh:&#10;Potong dadu tahu putih lalu tiriskan hingga agak kering.&#10;Balur tahu dengan campuran tepung dan bumbu.&#10;Goreng dalam minyak panas hingga keemasan.">{{ old('langkah') }}</textarea>
                    <p class="text-xs txt-secondary mt-1">1 baris = 1 langkah bernomor urut secara otomatis.</p>
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
            <button type="submit" form="createForm" class="btn-primary">Simpan Resep</button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL: EDIT --}}
{{-- ============================================ --}}
<div id="editModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4"
     style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">
    <div style="width: 100%; max-width: 820px; max-height: 92vh;"
         class="glass-card rounded-2xl overflow-hidden flex flex-col shadow-2xl">

        <div class="flex items-center justify-between px-6 py-4 text-white flex-shrink-0" style="background: var(--gradient-brand);">
            <div>
                <p class="text-xs opacity-80">Form</p>
                <h3 class="text-base font-bold">Edit Resep Olahan Tahu</h3>
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
                    @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
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
                    <label class="block text-sm font-medium txt-primary mb-1">Judul Resep <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="text" name="judul" id="editJudul"
                           minlength="4" maxlength="200" data-filter="title"
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                           style="color: rgb(var(--text-primary));">
                    <p class="text-xs txt-secondary mt-1">Huruf, angka, spasi & tanda baca umum. Minimal 4 karakter.</p>
                </div>

                {{-- METADATA RESEP --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Durasi (Menit) <span style="color: rgb(var(--danger));">*</span></label>
                        <input type="text" inputmode="numeric" name="durasi_menit" id="editDurasi"
                               maxlength="4" data-filter="number"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input tabular-nums"
                               style="color: rgb(var(--text-primary));">
                        <p class="text-[11px] txt-muted mt-1">Khusus angka (1 - 1440 menit).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Porsi <span style="color: rgb(var(--danger));">*</span></label>
                        <input type="text" inputmode="numeric" name="porsi" id="editPorsi"
                               maxlength="3" data-filter="number"
                               class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input tabular-nums"
                               style="color: rgb(var(--text-primary));">
                        <p class="text-[11px] txt-muted mt-1">Khusus angka (1 - 100 porsi).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium txt-primary mb-1">Tingkat Kesulitan <span style="color: rgb(var(--danger));">*</span></label>
                        <select name="tingkat_kesulitan" id="editTingkatKesulitan"
                                class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input"
                                style="color: rgb(var(--text-primary)); background: rgb(var(--surface));">
                            <option value="mudah">Mudah</option>
                            <option value="sedang">Sedang</option>
                            <option value="sulit">Sulit</option>
                        </select>
                    </div>
                </div>

                {{-- PILIH MODE MEDIA --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-2">Jenis Media Resep <span style="color: rgb(var(--danger));">*</span></label>
                    <input type="hidden" name="media_type" :value="mediaType">
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="mediaType = 'image'"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold border transition flex items-center justify-center gap-1.5"
                                :style="mediaType === 'image' ? 'background: rgb(var(--brand-soft)); border-color: rgb(var(--brand)); color: rgb(var(--brand-strong));' : 'border-color: rgb(var(--border-soft)); color: rgb(var(--text-secondary));'">
                            <x-icon name="photo" class="w-3.5 h-3.5" /> Foto Manual
                        </button>
                        <button type="button" @click="mediaType = 'video'"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold border transition flex items-center justify-center gap-1.5"
                                :style="mediaType === 'video' ? 'background: rgb(var(--info-soft)); border-color: rgb(var(--info)); color: rgb(var(--info));' : 'border-color: rgb(var(--border-soft)); color: rgb(var(--text-secondary));'">
                            <x-icon name="film" class="w-3.5 h-3.5" /> Upload Video
                        </button>
                        <button type="button" @click="mediaType = 'youtube'"
                                class="px-3 py-2.5 rounded-xl text-xs font-bold border transition flex items-center justify-center gap-1.5"
                                :style="mediaType === 'youtube' ? 'background: rgb(var(--danger-soft)); border-color: rgb(var(--danger)); color: rgb(var(--danger));' : 'border-color: rgb(var(--border-soft)); color: rgb(var(--text-secondary));'">
                            <span>▶</span> Link YouTube
                        </button>
                    </div>
                </div>

                {{-- UPLOAD VIDEO MANUAL --}}
                <div x-show="mediaType === 'video'" x-cloak class="p-4 rounded-xl"
                     style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border-soft));">
                    <label class="block text-sm font-medium txt-primary mb-2">Upload Video Resep</label>
                    <input type="file" name="video_file" accept="video/mp4,video/quicktime,video/webm"
                           data-filter="video-only" x-ref="videoInput" @change="handleVideoChange($event)" class="hidden">
                    <input type="hidden" name="hapus_video" :value="hapusVideoLama ? 1 : 0">

                    <div x-show="existingVideoUrl && !hapusVideoLama && !videoPreviewUrl" x-cloak
                         class="mb-3 rounded-xl overflow-hidden bg-black/90 p-2" style="border: 1px solid rgb(var(--border-soft));">
                        <video :src="existingVideoUrl" controls class="w-full max-h-56 rounded-lg"></video>
                        <div class="flex items-center justify-between gap-2 mt-2 px-1">
                            <span class="text-xs text-white/80">Video saat ini</span>
                            <button type="button" @click="hapusVideoLama = true" class="text-xs font-bold text-red-400 hover:underline">
                                Hapus Video Lama
                            </button>
                        </div>
                    </div>

                    <div x-show="hapusVideoLama && !videoPreviewUrl" x-cloak
                         class="mb-3 flex items-center gap-3 px-3 py-2 rounded-xl"
                         style="background: rgb(var(--warning-soft) / 0.5); border: 1px solid rgb(var(--warning) / 0.3);">
                        <span class="flex-1 text-xs" style="color: rgb(var(--warning));">Video lama akan dihapus — upload video baru atau ubah mode.</span>
                        <button type="button" @click="hapusVideoLama = false" class="text-xs font-bold underline" style="color: rgb(var(--brand));">Urungkan</button>
                    </div>

                    <div x-show="videoPreviewUrl" x-cloak class="mb-3 rounded-xl overflow-hidden bg-black/90 p-2"
                         style="border: 1px solid rgb(var(--border-soft));">
                        <video :src="videoPreviewUrl" controls class="w-full max-h-56 rounded-lg"></video>
                        <div class="flex items-center justify-between gap-2 mt-2 px-1">
                            <span class="text-xs text-white/80 truncate" x-text="videoFileName"></span>
                            <button type="button" @click="cancelVideo()" class="text-xs font-bold text-red-400 hover:underline flex-shrink-0">
                                Batalkan Video
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl border bd-default glass-card">
                        <button type="button" @click="$refs.videoInput.click()"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex-shrink-0"
                                style="background: rgb(var(--info-soft)); color: rgb(var(--info));">
                            <span x-text="existingVideoUrl ? 'Ganti Video' : 'Pilih Video'"></span>
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="videoFileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="videoFileName || 'Belum ada video baru dipilih'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1.5">Hanya video MP4, MOV, atau WEBM. Maksimal 20MB.</p>
                </div>

                {{-- LINK YOUTUBE ONLY --}}
                <div x-show="mediaType === 'youtube'" x-cloak class="p-4 rounded-xl"
                     style="background: rgb(var(--bg-secondary) / 0.6); border: 1px solid rgb(var(--border-soft));">
                    <label class="block text-sm font-medium txt-primary mb-1.5">
                        Link YouTube Resmi <span style="color: rgb(var(--danger));">*</span>
                    </label>
                    <input type="url" name="youtube_url" x-model="youtubeUrl"
                           placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/..."
                           class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 glass-input text-sm"
                           style="color: rgb(var(--text-primary));">
                    <p class="text-xs txt-muted mt-1">Hanya menerima link YouTube resmi (`youtube.com` / `youtu.be`). TikTok, IG, atau link lain ditolak.</p>

                    <template x-if="youtubeUrl && !getYoutubeId()">
                        <p class="text-xs font-semibold mt-2 flex items-center gap-1.5" style="color: rgb(var(--danger));">
                            <x-icon name="alert-triangle" class="w-3.5 h-3.5" />
                            <span>Hanya link YouTube resmi (youtube.com atau youtu.be) yang diperbolehkan!</span>
                        </p>
                    </template>
                    <template x-if="getYoutubeId()">
                        <div class="mt-3">
                            <p class="text-xs font-semibold mb-1.5 flex items-center gap-1.5" style="color: rgb(var(--success));">
                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                <span>Link YouTube valid — Preview:</span>
                            </p>
                            <div class="aspect-video rounded-xl overflow-hidden bg-black" style="border: 1px solid rgb(var(--border-soft));">
                                <iframe :src="'https://www.youtube.com/embed/' + getYoutubeId()" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- THUMBNAIL --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">
                        <span x-text="mediaType === 'image' ? 'Foto Masakan / Thumbnail' : 'Cover Thumbnail (Opsional)'"></span>
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
                           data-filter="image-only" x-ref="fileInput" @change="handleFileChange($event)" class="hidden">
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
                            <span x-text="existingImage ? 'Ganti Foto' : 'Pilih Foto'"></span>
                        </button>
                        <span class="flex-1 min-w-0 text-xs break-all"
                              :class="fileName ? 'txt-primary font-medium' : 'txt-muted'"
                              x-text="fileName || 'Belum ada foto dipilih'"></span>
                    </div>
                    <p class="text-xs txt-secondary mt-1">Hanya file FOTO asli (JPG, PNG, WEBP). Maksimal 2MB.</p>
                </div>

                {{-- DESKRIPSI --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Deskripsi Singkat <span class="txt-muted font-normal">(opsional)</span></label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="3" maxlength="1000"
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input resize-none"
                              style="color: rgb(var(--text-primary));"></textarea>
                </div>

                {{-- BAHAN --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Bahan-Bahan <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="bahan" id="editBahan" rows="6" minlength="5" maxlength="10000" required
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input"
                              style="color: rgb(var(--text-primary));"></textarea>
                    <p class="text-xs txt-secondary mt-1">1 baris = 1 bahan.</p>
                </div>

                {{-- LANGKAH --}}
                <div>
                    <label class="block text-sm font-medium txt-primary mb-1">Langkah Memasak <span style="color: rgb(var(--danger));">*</span></label>
                    <textarea name="langkah" id="editLangkah" rows="7" minlength="10" maxlength="15000" required
                              class="w-full px-4 py-2.5 rounded-xl border bd-default focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm glass-input"
                              style="color: rgb(var(--text-primary));"></textarea>
                    <p class="text-xs txt-secondary mt-1">1 baris = 1 langkah memasak.</p>
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
            <button type="submit" form="editForm" class="btn-primary">Update Resep</button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- GLOBAL LIGHTBOX --}}
{{-- ============================================ --}}
<div id="globalLightbox" class="fixed inset-0 hidden" style="z-index: 99999; background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);">
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
    const RESEP_DATA = @json($resepJson);
    const IS_EDIT_ERROR = @json(session('open_modal') === 'edit' && $errors->any());
    const OLD_EDIT_INPUT = {
        judul: @js(old('judul')),
        deskripsi: @js(old('deskripsi')),
        bahan: @js(old('bahan')),
        langkah: @js(old('langkah')),
        durasi_menit: @js(old('durasi_menit')),
        porsi: @js(old('porsi')),
        tingkat_kesulitan: @js(old('tingkat_kesulitan')),
        media_type: @js(old('media_type')),
        youtube_url: @js(old('youtube_url')),
        status: @js(old('status')),
        scheduled_at: @js(old('scheduled_at')),
    };

    // ==== GLOBAL LIGHTBOX ====
    function openGlobalLightbox(url) {
        const lb = document.getElementById('globalLightbox');
        document.getElementById('globalLightboxImg').src = url;
        lb.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-active');
    }
    function closeGlobalLightbox() {
        const lb = document.getElementById('globalLightbox');
        lb.classList.add('hidden');
        document.getElementById('globalLightboxImg').src = '';
        document.body.style.overflow = '';

        const createModal = document.getElementById('createModal');
        const editModal = document.getElementById('editModal');
        const isCreateOpen = createModal && !createModal.classList.contains('hidden');
        const isEditOpen = editModal && !editModal.classList.contains('hidden');
        if (!isCreateOpen && !isEditOpen) {
            document.body.classList.remove('modal-active');
        }
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

    function selectDiffFilter(el) {
        document.getElementById('filterInputDiff').value = el.dataset.value;
        document.getElementById('filterDiffLabel').textContent = el.dataset.label;
        const c = el.closest('[x-data]');
        if (c && c.__x) c.__x.$data.diffOpen = false;
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
        document.body.classList.add('modal-active');

        const form = document.getElementById('createForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state && !@json(session('open_modal') === 'create' && $errors->any())) {
                state.mediaType = 'image';
                state.youtubeUrl = '';
                state.previewUrl = null;
                state.fileName = null;
                if (state.videoPreviewUrl) URL.revokeObjectURL(state.videoPreviewUrl);
                state.videoPreviewUrl = null;
                state.videoFileName = null;
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

        const lb = document.getElementById('globalLightbox');
        const isLbOpen = lb && !lb.classList.contains('hidden');
        if (!isLbOpen) {
            document.body.classList.remove('modal-active');
        }

        const fileInput = modal.querySelector('input[name="thumbnail"]');
        if (fileInput) fileInput.value = '';
        const videoInput = modal.querySelector('input[name="video_file"]');
        if (videoInput) videoInput.value = '';
        const form = document.getElementById('createForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.previewUrl = null;
                state.fileName = null;
                if (state.videoPreviewUrl) URL.revokeObjectURL(state.videoPreviewUrl);
                state.videoPreviewUrl = null;
                state.videoFileName = null;
                state.showUndo = false;
                state.undoFile = null;
                state.undoFileName = null;
            }
        }
    }

    // ==== EDIT MODAL ====
    function openEditModal(id, useOld = false) {
        const data = RESEP_DATA.find(r => r.id === id);
        if (!data) return;

        fileState.reset();
        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-active');

        document.getElementById('editForm').action = '/admin/resep/' + id;

        document.getElementById('editJudul').value = (useOld && OLD_EDIT_INPUT.judul !== null) ? OLD_EDIT_INPUT.judul : data.judul;
        document.getElementById('editDeskripsi').value = (useOld && OLD_EDIT_INPUT.deskripsi !== null) ? OLD_EDIT_INPUT.deskripsi : (data.deskripsi || '');
        document.getElementById('editBahan').value = (useOld && OLD_EDIT_INPUT.bahan !== null) ? OLD_EDIT_INPUT.bahan : data.bahan;
        document.getElementById('editLangkah').value = (useOld && OLD_EDIT_INPUT.langkah !== null) ? OLD_EDIT_INPUT.langkah : data.langkah;
        document.getElementById('editDurasi').value = (useOld && OLD_EDIT_INPUT.durasi_menit !== null) ? OLD_EDIT_INPUT.durasi_menit : data.durasi_menit;
        document.getElementById('editPorsi').value = (useOld && OLD_EDIT_INPUT.porsi !== null) ? OLD_EDIT_INPUT.porsi : data.porsi;
        document.getElementById('editTingkatKesulitan').value = (useOld && OLD_EDIT_INPUT.tingkat_kesulitan) ? OLD_EDIT_INPUT.tingkat_kesulitan : (data.tingkat_kesulitan || 'mudah');
        document.getElementById('editIdInput').value = id;

        const effStatus = (useOld && OLD_EDIT_INPUT.status) ? OLD_EDIT_INPUT.status : data.status;
        const statusLabelText = effStatus === 'scheduled' ? 'Schedule' : (effStatus === 'draft' ? 'Draft' : 'Publish');

        const form = document.getElementById('editForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.currentId = id;
                state.statusValue = effStatus;
                state.statusLabel = statusLabelText;
                state.scheduledAt = (useOld && OLD_EDIT_INPUT.scheduled_at !== null) ? OLD_EDIT_INPUT.scheduled_at : (data.scheduled_at || '');
                state.mediaType = (useOld && OLD_EDIT_INPUT.media_type) ? OLD_EDIT_INPUT.media_type : (data.media_type || 'image');
                state.existingImage = data.thumbnail;
                state.existingVideoUrl = data.video_url || null;
                state.youtubeUrl = (useOld && OLD_EDIT_INPUT.youtube_url !== null) ? OLD_EDIT_INPUT.youtube_url : (data.youtube_url || '');
                state.hapusThumbnailLama = false;
                state.hapusVideoLama = false;
                state.previewUrl = null;
                state.fileName = null;
                if (state.videoPreviewUrl) URL.revokeObjectURL(state.videoPreviewUrl);
                state.videoPreviewUrl = null;
                state.videoFileName = null;
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

        const lb = document.getElementById('globalLightbox');
        const isLbOpen = lb && !lb.classList.contains('hidden');
        if (!isLbOpen) {
            document.body.classList.remove('modal-active');
        }

        const fileInput = modal.querySelector('input[name="thumbnail"]');
        if (fileInput) fileInput.value = '';
        const videoInput = modal.querySelector('input[name="video_file"]');
        if (videoInput) videoInput.value = '';
        const form = document.getElementById('editForm');
        if (form) {
            const state = (form.__x && form.__x.$data) || (form._x_dataStack && form._x_dataStack[0]);
            if (state) {
                state.previewUrl = null;
                state.fileName = null;
                if (state.videoPreviewUrl) URL.revokeObjectURL(state.videoPreviewUrl);
                state.videoPreviewUrl = null;
                state.videoFileName = null;
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
            statusValue: @js(old('status', 'publish')),
            statusLabel: @js(old('status') === 'draft' ? 'Draft' : (old('status') === 'scheduled' ? 'Schedule' : 'Publish')),
            scheduledAt: @js(old('scheduled_at', '')),
            mediaType: @js(old('media_type', 'image')),
            youtubeUrl: @js(old('youtube_url', '')),
            videoFileName: null,
            videoPreviewUrl: null,
            fileName: null,
            previewUrl: null,
            undoFile: null,
            undoFileName: null,
            showUndo: false,
            undoTimer: null,

            init() { this.fileName = null; this.previewUrl = null; this.showUndo = false; },

            getYoutubeId() {
                return window.extractYoutubeId ? window.extractYoutubeId(this.youtubeUrl) : null;
            },

            selectStatus(value, label) {
                this.statusValue = value;
                this.statusLabel = label;
                this.statusOpen = false;
            },

            openLightbox(url) { openGlobalLightbox(url); },

            handleVideoChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['video/mp4', 'video/quicktime', 'video/webm'];
                if (!allowed.includes(file.type)) {
                    alert('Hanya file VIDEO yang diperbolehkan (MP4, MOV, WEBM).');
                    e.target.value = ''; this.videoFileName = null; return;
                }
                if (file.size > 20 * 1024 * 1024) {
                    alert('Ukuran video maksimal 20MB.');
                    e.target.value = ''; this.videoFileName = null; return;
                }
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = URL.createObjectURL(file);
                this.videoFileName = file.name;
            },

            cancelVideo() {
                if (this.$refs.videoInput) this.$refs.videoInput.value = '';
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = null;
                this.videoFileName = null;
            },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('Hanya file gambar yang diperbolehkan (JPG, PNG, WEBP).');
                    e.target.value = ''; this.fileName = null; return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran gambar maksimal 2MB.');
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
            mediaType: 'image',
            existingVideoUrl: null,
            youtubeUrl: '',
            hapusVideoLama: false,
            videoFileName: null,
            videoPreviewUrl: null,
            existingImage: null,
            hapusThumbnailLama: false,
            fileName: null,
            previewUrl: null,
            undoFile: null,
            undoFileName: null,
            showUndo: false,
            undoTimer: null,

            init() { this.fileName = null; this.previewUrl = null; this.showUndo = false; },

            getYoutubeId() {
                return window.extractYoutubeId ? window.extractYoutubeId(this.youtubeUrl) : null;
            },

            selectStatus(value, label) {
                this.statusValue = value;
                this.statusLabel = label;
                this.statusOpen = false;
            },

            openLightbox(url) { openGlobalLightbox(url); },

            handleVideoChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['video/mp4', 'video/quicktime', 'video/webm'];
                if (!allowed.includes(file.type)) {
                    alert('Hanya file VIDEO yang diperbolehkan (MP4, MOV, WEBM).');
                    e.target.value = ''; this.videoFileName = null; return;
                }
                if (file.size > 20 * 1024 * 1024) {
                    alert('Ukuran video maksimal 20MB.');
                    e.target.value = ''; this.videoFileName = null; return;
                }
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = URL.createObjectURL(file);
                this.videoFileName = file.name;
                this.hapusVideoLama = false;
            },

            cancelVideo() {
                if (this.$refs.videoInput) this.$refs.videoInput.value = '';
                if (this.videoPreviewUrl) URL.revokeObjectURL(this.videoPreviewUrl);
                this.videoPreviewUrl = null;
                this.videoFileName = null;
            },

            handleFileChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('Hanya file gambar yang diperbolehkan (JPG, PNG, WEBP).');
                    e.target.value = ''; this.fileName = null; return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran gambar maksimal 2MB.');
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
            openEditModal({{ (int) session('edit_id') }}, IS_EDIT_ERROR);
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
