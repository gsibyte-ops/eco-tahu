@extends('layouts.user')
@section('title', $resep->judul)

@section('content')
@php
    $publishedDate = ($resep->published_at ?? $resep->created_at)?->format('d M Y');
    $shareUrl = route('user.resep.show', $resep->slug);
    $bahanItems = $resep->bahanList();
    $langkahItems = $resep->langkahList();
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <nav class="glass-card inline-flex items-center px-4 py-2 mb-6 text-sm" style="color: rgb(var(--text-secondary));">
        <a href="{{ route('home') }}" class="hover:text-emerald-500 transition">Beranda</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <a href="{{ route('user.resep.index') }}" class="hover:text-emerald-500 transition">Resep</a>
        <span class="mx-2" style="color: rgb(var(--brand));">/</span>
        <span class="font-medium truncate" style="color: rgb(var(--text-primary));">{{ Str::limit($resep->judul, 30) }}</span>
    </nav>

    <header class="glass-card p-6 sm:p-8 mb-8">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="pill inline-flex items-center gap-1"><x-icon name="utensils" class="w-3.5 h-3.5" /> Resep Olahan Tahu</span>
            @if (($resep->media_type ?? 'image') === 'youtube')
                <span class="pill" style="background: rgb(var(--danger-soft)); color: rgb(var(--danger)); border-color: rgb(var(--danger) / 0.3);">▶ Video YouTube</span>
            @elseif (($resep->media_type ?? 'image') === 'video')
                <span class="pill inline-flex items-center gap-1" style="background: rgb(var(--info-soft)); color: rgb(var(--info)); border-color: rgb(var(--info) / 0.3);"><x-icon name="film" class="w-3.5 h-3.5" /> Video Tutorial</span>
            @endif
        </div>

        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight mb-5 mt-4 text-balance" style="color: rgb(var(--text-primary));">
            {{ $resep->judul }}
        </h1>

        <div class="flex items-center gap-4 flex-wrap mb-6">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shadow-md"
                     style="background: var(--gradient-brand); box-shadow: 0 4px 12px rgb(var(--brand) / 0.3);">
                    {{ strtoupper(substr($resep->user->username ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium" style="color: rgb(var(--text-primary));">{{ $resep->user->username ?? 'Admin EcoTahu' }}</p>
                    <p class="text-xs" style="color: rgb(var(--text-muted));">Koki EcoTahu</p>
                </div>
            </div>
            @if ($publishedDate)
                <span style="color: rgb(var(--text-faint));">|</span>
                <div class="text-sm tabular-nums inline-flex items-center gap-1.5" style="color: rgb(var(--text-secondary));">
                    <x-icon name="calendar" class="w-4 h-4 shrink-0" />
                    <span>{{ $publishedDate }}</span>
                </div>
            @endif
        </div>

        {{-- 3 Quick-Stat Tiles --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="p-3 sm:p-4 rounded-xl text-center" style="background: rgb(var(--bg-secondary) / 0.7); border: 1px solid rgb(var(--border-soft));">
                <p class="text-[11px] uppercase tracking-wider font-bold mb-1 inline-flex items-center justify-center gap-1" style="color: rgb(var(--text-muted));"><x-icon name="clock" class="w-3.5 h-3.5" /> Durasi</p>
                <p class="text-sm sm:text-base font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $resep->durasi_menit }} Menit</p>
            </div>
            <div class="p-3 sm:p-4 rounded-xl text-center" style="background: rgb(var(--bg-secondary) / 0.7); border: 1px solid rgb(var(--border-soft));">
                <p class="text-[11px] uppercase tracking-wider font-bold mb-1 inline-flex items-center justify-center gap-1" style="color: rgb(var(--text-muted));"><x-icon name="utensils" class="w-3.5 h-3.5" /> Porsi</p>
                <p class="text-sm sm:text-base font-extrabold tabular-nums" style="color: rgb(var(--text-primary));">{{ $resep->porsi }} Porsi</p>
            </div>
            <div class="p-3 sm:p-4 rounded-xl text-center" style="background: rgb(var(--bg-secondary) / 0.7); border: 1px solid rgb(var(--border-soft));">
                <p class="text-[11px] uppercase tracking-wider font-bold mb-1 inline-flex items-center justify-center gap-1" style="color: rgb(var(--text-muted));"><x-icon name="fire" class="w-3.5 h-3.5" /> Kesulitan</p>
                <p class="text-sm sm:text-base font-extrabold capitalize" style="color: rgb(var(--brand));">{{ $resep->tingkat_kesulitan }}</p>
            </div>
        </div>
    </header>

    {{-- Media Hero Section: YouTube / Video Manual / Foto --}}
    @if (($resep->media_type ?? 'image') === 'youtube' && $resep->youtube_embed_url)
        <div class="rounded-2xl overflow-hidden mb-8 shadow-xl aspect-video bg-black" style="border: 1px solid rgb(var(--border-soft));">
            <iframe src="{{ $resep->youtube_embed_url }}" class="w-full h-full" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    @elseif (($resep->media_type ?? 'image') === 'video' && $resep->video_url)
        <div class="rounded-2xl overflow-hidden mb-8 shadow-xl bg-black" style="border: 1px solid rgb(var(--border-soft));">
            <video controls preload="metadata" @if ($resep->thumbnail_url) poster="{{ $resep->thumbnail_url }}" @endif class="w-full aspect-video">
                <source src="{{ $resep->video_url }}">
                Browser Anda tidak mendukung pemutaran video.
            </video>
        </div>
    @elseif ($resep->thumbnail_url)
        <div class="rounded-2xl overflow-hidden mb-8 shadow-xl" style="border: 1px solid rgb(var(--border-soft));">
            <img src="{{ $resep->thumbnail_url }}"
                 alt="{{ $resep->judul }}"
                 class="w-full aspect-video object-cover">
        </div>
    @endif

    @if ($resep->deskripsi)
        <div class="glass-card p-6 mb-8">
            <p class="text-base leading-relaxed text-pretty" style="color: rgb(var(--text-secondary));">
                {{ $resep->deskripsi }}
            </p>
        </div>
    @endif

    {{-- Bahan-Bahan Card with Interactive Checklist --}}
    <section class="glass-card p-6 lg:p-8 mb-8" x-data="{ checked: [] }">
        <div class="flex items-center justify-between gap-3 mb-5">
            <h2 class="text-xl font-extrabold tracking-tight flex items-center gap-2" style="color: rgb(var(--text-primary));"><x-icon name="clipboard-list" class="w-5 h-5" style="color: rgb(var(--brand));" /> Bahan-Bahan</h2>
            <span class="text-xs font-semibold tabular-nums" style="color: rgb(var(--text-muted));"
                  x-text="checked.length + ' / {{ count($bahanItems) }} siap'"></span>
        </div>
        <div class="space-y-2.5">
            @foreach ($bahanItems as $idx => $item)
                <label class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition"
                       style="background: rgb(var(--bg-secondary) / 0.55); border: 1px solid rgb(var(--border-soft));">
                    <input type="checkbox" value="{{ $idx }}" x-model="checked"
                           class="mt-0.5 rounded border-emerald-500 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm sm:text-base transition"
                          :class="checked.includes('{{ $idx }}') ? 'line-through opacity-55' : ''"
                          style="color: rgb(var(--text-primary));">{{ $item }}</span>
                </label>
            @endforeach
        </div>
    </section>

    {{-- Langkah Memasak Card --}}
    <section class="glass-card p-6 lg:p-8 mb-8">
        <h2 class="text-xl font-extrabold tracking-tight mb-5 flex items-center gap-2" style="color: rgb(var(--text-primary));"><x-icon name="utensils" class="w-5 h-5" style="color: rgb(var(--brand));" /> Langkah Memasak</h2>
        <ol class="space-y-4">
            @foreach ($langkahItems as $idx => $step)
                <li class="flex items-start gap-4 p-4 rounded-xl" style="background: rgb(var(--bg-secondary) / 0.45); border: 1px solid rgb(var(--border-soft));">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                          style="background: var(--gradient-brand); box-shadow: 0 4px 10px rgb(var(--brand) / 0.3);">
                        {{ $idx + 1 }}
                    </span>
                    <p class="text-sm sm:text-base leading-relaxed pt-1" style="color: rgb(var(--text-primary));">{{ $step }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Interactive Like & Share Card --}}
    <div class="glass-card p-5 mb-8"
         x-data="{
             liked: {{ ($isLiked ?? false) ? 'true' : 'false' }},
             likesCount: {{ (int) ($resep->likes_count ?? 0) }},
             loading: false,
             toastMsg: '',
             async toggleLike() {
                 if (this.loading) return;
                 this.loading = true;
                 try {
                     const res = await fetch('{{ route('user.resep.like', $resep->id) }}', {
                         method: 'POST',
                         headers: {
                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                             'Accept': 'application/json',
                         },
                     });
                     if (res.ok) {
                         const data = await res.json();
                         this.liked = data.liked;
                         this.likesCount = data.likes_count;
                     }
                 } finally {
                     this.loading = false;
                 }
             },
             async shareNative() {
                 const shareData = { title: @js($resep->judul), text: @js('Cobain resep tahu ini: ' . $resep->judul), url: @js($shareUrl) };
                 if (navigator.share) {
                     try { await navigator.share(shareData); return; } catch (e) {}
                 }
                 await this.copyLink();
             },
             async copyLink() {
                 try { await navigator.clipboard.writeText(@js($shareUrl)); } catch (e) {}
                 this.toastMsg = 'Link resep berhasil disalin!';
                 setTimeout(() => this.toastMsg = '', 2500);
             }
         }">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                @auth
                    <button type="button" @click="toggleLike()" :disabled="loading"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm transition"
                            :style="liked
                                ? 'background: rgb(var(--danger-soft)); color: rgb(var(--danger)); border: 1px solid rgb(var(--danger) / 0.4);'
                                : 'background: rgb(var(--surface-raised) / 0.6); color: rgb(var(--text-primary)); border: 1px solid rgb(var(--border-soft));'">
                        <svg class="w-4 h-4 transition" :class="liked ? 'text-rose-500' : 'text-current'" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        <span x-text="liked ? 'Disukai' : 'Sukai'"></span>
                        <span class="px-2 py-0.5 rounded-full text-xs tabular-nums"
                              style="background: rgb(var(--bg-secondary));" x-text="likesCount"></span>
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90"
                       style="background: rgb(var(--surface-raised) / 0.6); color: rgb(var(--text-primary)); border: 1px solid rgb(var(--border-soft));">
                        <x-icon name="heart" class="w-4 h-4" />
                        <span>Sukai ({{ (int) ($resep->likes_count ?? 0) }})</span>
                    </a>
                @endauth
                <p class="text-xs hidden sm:block" style="color: rgb(var(--text-secondary));">Sukai &amp; bagikan resep lezat ini</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="shareNative()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold glass-btn transition hover:opacity-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Bagikan
                </button>
                <a href="https://wa.me/?text={{ urlencode($resep->judul . ' - ' . $shareUrl) }}" target="_blank" title="Bagikan ke WhatsApp"
                   class="w-9 h-9 flex items-center justify-center rounded-lg glass-input text-emerald-500 hover:bg-emerald-500/10 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" title="Bagikan ke Facebook"
                   class="w-9 h-9 flex items-center justify-center rounded-lg glass-input text-blue-500 hover:bg-blue-500/10 transition">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>
                <button type="button" @click="copyLink()" title="Salin Link"
                        class="w-9 h-9 flex items-center justify-center rounded-lg glass-input hover:bg-emerald-500/10 transition" style="color: rgb(var(--text-secondary));">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </button>
            </div>
        </div>
        <p x-show="toastMsg" x-cloak class="mt-3 text-xs font-semibold flex items-center gap-1.5" style="color: rgb(var(--success));"><x-icon name="check-circle" class="w-4 h-4 shrink-0" /><span x-text="toastMsg"></span></p>
    </div>

    @if ($terkait->count() > 0)
        <section>
            <h2 class="text-2xl font-extrabold tracking-tight mb-5" style="color: rgb(var(--text-primary));">Resep Lainnya</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach ($terkait as $t)
                    <a href="{{ route('user.resep.show', $t->slug) }}" class="surface edukasi-card overflow-hidden block">
                        <div class="aspect-video overflow-hidden relative" style="background: rgb(var(--bg-secondary));">
                            @if ($t->display_thumbnail_url)
                                <img src="{{ $t->display_thumbnail_url }}" alt="{{ $t->judul }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center" style="color: rgb(var(--text-muted));"><x-icon name="utensils" class="w-10 h-10 opacity-40" /></div>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs mb-1 tabular-nums flex items-center gap-1" style="color: rgb(var(--text-muted));"><x-icon name="clock" class="w-3.5 h-3.5" /> {{ $t->durasi_menit }} mnt · <x-icon name="heart-solid" class="w-3.5 h-3.5 text-rose-500" /> {{ $t->likes_count ?? 0 }}</p>
                            <h3 class="font-semibold text-sm line-clamp-2 leading-snug" style="color: rgb(var(--text-primary));">{{ $t->judul }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <div class="mt-10 text-center">
        <a href="{{ route('user.resep.index') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Resep
        </a>
    </div>
</div>

@endsection
