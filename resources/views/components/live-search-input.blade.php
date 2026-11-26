@props([
    'name' => 'q',
    'value' => '',
    'scope' => 'all',
    'placeholder' => 'Cari...',
    'accent' => 'emerald',
    'inputClass' => 'glass-input w-full pl-9 pr-8 py-1.5 sm:py-2 text-xs sm:text-sm',
])

@php
    $suggestUrl = route('search.suggest');
    $badgePalette = $accent === 'amber'
        ? 'background: rgb(var(--accent-soft)); color: rgb(var(--accent));'
        : 'background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));';
@endphp

<div class="relative w-full"
     x-data="{
        query: @js((string) $value),
        scope: @js($scope),
        suggestUrl: @js($suggestUrl),
        open: false,
        loading: false,
        searched: false,
        items: [],
        reqSeq: 0,
        async fetchSuggest() {
            const q = (this.query || '').trim();
            if (q.length < 1) {
                this.items = [];
                this.searched = false;
                this.open = false;
                this.loading = false;
                return;
            }
            const seq = ++this.reqSeq;
            this.loading = true;
            this.open = true;
            try {
                const url = this.suggestUrl + '?q=' + encodeURIComponent(q) + '&scope=' + encodeURIComponent(this.scope);
                const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                if (seq !== this.reqSeq) return;
                this.items = Array.isArray(data.items) ? data.items : [];
                this.searched = true;
            } catch (e) {
                if (seq !== this.reqSeq) return;
                this.items = [];
                this.searched = true;
            } finally {
                if (seq === this.reqSeq) this.loading = false;
            }
        },
        clearQuery() {
            this.query = '';
            this.items = [];
            this.searched = false;
            this.open = false;
            this.$nextTick(() => this.$refs.searchField?.focus());
        }
     }"
     @click.away="open = false"
     @keydown.escape.window="open = false">

    <div class="relative flex items-center">
        <span class="pointer-events-none absolute left-3 flex items-center" style="color: rgb(var(--text-muted));">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105.65 5.65a7.5 7.5 0 0010.6 10.6z"/>
            </svg>
        </span>

        <input x-ref="searchField"
               type="text"
               name="{{ $name }}"
               x-model="query"
               @input.debounce.200ms="fetchSuggest()"
               @focus="if ((query || '').trim().length > 0) { if (!searched) { fetchSuggest(); } else { open = true; } }"
               autocomplete="off"
               placeholder="{{ $placeholder }}"
               class="{{ $inputClass }}"
               style="color: rgb(var(--text-primary));">

        <button type="button"
                x-show="(query || '').length > 0"
                x-cloak
                @click="clearQuery()"
                class="absolute right-2.5 p-1 rounded-full transition hover:opacity-80"
                style="color: rgb(var(--text-muted));"
                aria-label="Hapus pencarian">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Google-style floating recommendation dropdown (never alters page content below) --}}
    <div x-show="open && (query || '').trim().length > 0"
         x-cloak
         x-transition.origin.top
         class="absolute left-0 right-0 mt-1.5 rounded-2xl shadow-2xl overflow-hidden"
         style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border)); z-index: 90;">
        <div class="px-3.5 py-2 flex items-center justify-between text-[10px] font-bold uppercase tracking-wider"
             style="background: rgb(var(--bg-secondary) / 0.65); border-bottom: 1px solid rgb(var(--border-soft)); color: rgb(var(--text-muted));">
            <span>Rekomendasi Pencarian Nama</span>
            <span x-show="loading" class="animate-pulse" style="color: rgb(var(--brand));">Mencari...</span>
        </div>

        <div class="max-h-72 overflow-y-auto overscroll-contain">
            <template x-if="!loading && items.length > 0">
                <div>
                    <template x-for="item in items" :key="item.url">
                        <a :href="item.url"
                           class="flex items-center gap-3 px-3.5 py-2.5 transition hover:bg-emerald-500/5"
                           style="border-bottom: 1px solid rgb(var(--border-soft));">
                            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 flex items-center justify-center"
                                 style="background: rgb(var(--bg-secondary));">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.image">
                                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 105.65 5.65a7.5 7.5 0 0010.6 10.6z"/></svg>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs sm:text-sm font-bold truncate" style="color: rgb(var(--text-primary));" x-text="item.name"></p>
                                </div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0" style="{{ $badgePalette }}" x-text="item.badge"></span>
                                    <span class="text-[11px] truncate" style="color: rgb(var(--text-secondary));" x-text="item.meta"></span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 shrink-0 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="!loading && searched && items.length === 0">
                <div class="px-4 py-6 text-center">
                    <p class="text-xs sm:text-sm font-bold" style="color: rgb(var(--text-primary));">
                        Tidak ditemukan untuk "<span x-text="query.trim()" style="color: rgb(var(--brand));"></span>"
                    </p>
                    <p class="text-[11px] mt-1" style="color: rgb(var(--text-muted));">
                        Coba ketik kata kunci nama produk/judul lain.
                    </p>
                </div>
            </template>
        </div>
    </div>
</div>
