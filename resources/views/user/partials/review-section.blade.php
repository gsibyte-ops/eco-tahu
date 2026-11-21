@php
    $reviews = $item->reviews()->with('user')->latest()->get();
    $avgRating = $item->averageRating();
    $totalReview = $item->reviewCount();

    $pesananBisaDireview = collect();
    if (auth()->check()) {
        $pesananBisaDireview = \App\Models\Pesanan::where('user_id', auth()->id())
            ->where('order_status', 'selesai')
            ->whereHas('detail', function ($q) use ($item) {
                $q->where('item_type', get_class($item))
                  ->where('item_id', $item->id);
            })
            ->whereDoesntHave('reviews', function ($q) use ($item) {
                $q->where('reviewable_type', get_class($item))
                  ->where('reviewable_id', $item->id);
            })
            ->get();
    }
@endphp

<div class="mt-8 sm:mt-10 pt-6 sm:pt-8" style="border-top: 1px solid rgb(var(--border-soft));"
     x-data="{ lightboxUrl: null }">

    <h2 class="text-lg sm:text-xl font-extrabold mb-4" style="color: rgb(var(--text-primary));">Ulasan Pembeli</h2>

    {{-- Ringkasan Rating --}}
    <div class="flex items-center gap-4 sm:gap-6 mb-5 sm:mb-6 rounded-xl sm:rounded-2xl p-4 sm:p-5" style="background: rgb(var(--bg-secondary));">
        <div class="text-center">
            <p class="text-3xl sm:text-4xl font-extrabold" style="color: rgb(var(--accent));">{{ number_format($avgRating, 1) }}</p>
            <p class="flex items-center justify-center gap-0.5 mt-1.5" style="color: rgb(var(--accent));">
                @for ($i = 1; $i <= 5; $i++)
                    <x-icon name="star-solid" class="w-4 h-4 {{ $i <= round($avgRating) ? 'text-amber-400' : '' }}" style="{{ $i > round($avgRating) ? 'color: rgb(var(--text-faint));' : '' }}" />
                @endfor
            </p>
            <p class="text-[10px] sm:text-xs mt-1" style="color: rgb(var(--text-secondary));">{{ $totalReview }} ulasan</p>
        </div>
    </div>

    {{-- Form Review (Hanya muncul untuk user yang udah beli & login) --}}
    @if ($pesananBisaDireview->isNotEmpty())
        @php $pesanan = $pesananBisaDireview->first(); @endphp

        <div class="mb-6 rounded-xl sm:rounded-2xl p-4 sm:p-5" style="background: rgb(var(--success-soft)); border: 1px solid rgb(var(--success) / 0.3);"
             x-data="{
                 rating: 0,
                 hoverRating: 0,
                 mediaPreview: null,
                 mediaType: null,
                 mediaName: '',
                 mediaError: '',
                 handleReviewMedia(event) {
                     this.mediaError = '';
                     const file = event.target.files[0];
                     if (!file) { this.clearMedia(); return; }
                     const mime = (file.type || '').toLowerCase();
                     const ext = (file.name.split('.').pop() || '').toLowerCase();
                     const imgMimes = ['image/jpeg', 'image/png', 'image/webp'];
                     const imgExts = ['jpg', 'jpeg', 'png', 'webp'];
                     const vidMimes = ['video/mp4', 'video/quicktime', 'video/webm'];
                     const vidExts = ['mp4', 'mov', 'webm'];

                     if (imgMimes.includes(mime) && imgExts.includes(ext)) {
                         if (file.size > 5 * 1024 * 1024) {
                             this.mediaError = 'Ukuran foto maksimal 5MB.';
                             this.clearMedia();
                             return;
                         }
                         this.mediaType = 'image';
                     } else if (vidMimes.includes(mime) && vidExts.includes(ext)) {
                         if (file.size > 20 * 1024 * 1024) {
                             this.mediaError = 'Ukuran video maksimal 20MB.';
                             this.clearMedia();
                             return;
                         }
                         this.mediaType = 'video';
                     } else {
                         this.mediaError = 'Hanya file foto (JPG, PNG, WEBP) atau video (MP4, MOV, WEBM) yang diperbolehkan.';
                         this.clearMedia();
                         return;
                     }

                     if (this.mediaPreview) URL.revokeObjectURL(this.mediaPreview);
                     this.mediaPreview = URL.createObjectURL(file);
                     this.mediaName = file.name;
                 },
                 clearMedia() {
                     if (this.mediaPreview) URL.revokeObjectURL(this.mediaPreview);
                     this.mediaPreview = null;
                     this.mediaType = null;
                     this.mediaName = '';
                     if (this.$refs.reviewMediaInput) this.$refs.reviewMediaInput.value = '';
                 }
             }">
            <p class="font-semibold mb-3 text-xs sm:text-sm" style="color: rgb(var(--success));">Beri Ulasan Anda</p>

            <form action="{{ route('user.review.store', $pesanan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reviewable_type" value="{{ $itemType }}">
                <input type="hidden" name="reviewable_id" value="{{ $item->id }}">
                <input type="hidden" name="rating" :value="rating">

                <div class="flex flex-wrap items-center gap-1 mb-3">
                    <template x-for="i in 5" :key="i">
                        <button type="button"
                                @click="rating = i"
                                @mouseenter="hoverRating = i"
                                @mouseleave="hoverRating = 0"
                                class="transition leading-none p-0.5"
                                :class="(hoverRating || rating) >= i ? 'text-amber-400' : ''"
                                :style="(hoverRating || rating) >= i ? '' : 'color: rgb(var(--text-faint));'">
                            <x-icon name="star-solid" class="w-6 h-6 sm:w-7 sm:h-7" />
                        </button>
                    </template>
                    <span class="ml-2 sm:ml-3 text-[10px] sm:text-sm self-center" style="color: rgb(var(--text-secondary));"
                          x-text="rating === 0 ? 'Pilih bintang' : rating + ' / 5'"></span>
                </div>

                <textarea name="komentar" rows="3" maxlength="500"
                          placeholder="Ceritakan pengalaman Anda (opsional)..."
                          class="glass-input w-full px-3 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm mb-3" style="color: rgb(var(--text-primary));">{{ old('komentar') }}</textarea>

                {{-- Upload Foto / Video Opsional --}}
                <div class="mb-3">
                    <input type="file" name="media" x-ref="reviewMediaInput"
                           accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/webm"
                           @change="handleReviewMedia($event)"
                           class="hidden">

                    <div x-show="!mediaPreview" class="flex flex-wrap items-center gap-2">
                        <button type="button" @click="$refs.reviewMediaInput.click()"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition hover:opacity-90"
                                style="background: rgb(var(--surface)); color: rgb(var(--text-primary)); border: 1px dashed rgb(var(--brand) / 0.5);">
                            <svg class="w-4 h-4" style="color: rgb(var(--brand));" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lampirkan Foto / Video (Opsional)
                        </button>
                        <span class="text-[10px] sm:text-xs" style="color: rgb(var(--text-muted));">
                            Foto maks 5MB (JPG/PNG/WEBP) · Video maks 20MB (MP4/MOV/WEBM)
                        </span>
                    </div>

                    <div x-show="mediaPreview" x-cloak class="mt-2 p-3 rounded-xl inline-flex flex-col gap-2 max-w-xs"
                         style="background: rgb(var(--surface)); border: 1px solid rgb(var(--border-soft));">
                        <template x-if="mediaType === 'image'">
                            <img :src="mediaPreview" alt="Preview media ulasan" class="rounded-lg max-h-40 object-cover">
                        </template>
                        <template x-if="mediaType === 'video'">
                            <video :src="mediaPreview" controls class="rounded-lg max-h-44 w-full bg-black"></video>
                        </template>
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <span class="truncate" style="color: rgb(var(--text-secondary));" x-text="mediaName"></span>
                            <button type="button" @click="clearMedia()" class="font-semibold text-red-500 hover:underline flex-shrink-0">
                                Hapus
                            </button>
                        </div>
                    </div>

                    <p x-show="mediaError" x-cloak class="mt-1.5 text-[10px] sm:text-xs font-medium" style="color: rgb(var(--danger));" x-text="mediaError"></p>
                    @error('media')
                        <p class="mt-1.5 text-[10px] sm:text-xs font-medium" style="color: rgb(var(--danger));">{{ $message }}</p>
                    @enderror
                </div>

                @error('rating')
                    <p class="text-[10px] sm:text-xs mb-2" style="color: rgb(var(--danger));">{{ $message }}</p>
                @enderror

                <button type="submit" :disabled="rating === 0"
                        class="px-4 sm:px-5 py-2 sm:py-2.5 text-white font-semibold rounded-lg sm:rounded-xl transition text-xs sm:text-sm disabled:cursor-not-allowed"
                        :class="rating === 0 ? 'opacity-50' : 'hover:-translate-y-0.5'"
                        style="background: var(--gradient-brand); box-shadow: var(--shadow-brand);">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    @endif

    {{-- Tampilan untuk Guest yang mau ngasih ulasan --}}
    @guest
        <div class="mb-6 rounded-xl sm:rounded-2xl p-4 sm:p-5 text-center" style="background: rgb(var(--bg-secondary)); border: 1px dashed rgb(var(--border-soft));">
            <p class="text-xs sm:text-sm mb-2" style="color: rgb(var(--text-secondary));">Punya pengalaman dengan produk ini?</p>
            <a href="{{ route('login') }}" class="text-[10px] sm:text-sm font-semibold inline-block px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg" style="color: rgb(var(--brand)); background: rgb(var(--brand-soft));">
                Login untuk memberi ulasan
            </a>
        </div>
    @endguest

    {{-- List Review --}}
    @forelse ($reviews as $review)
        <div class="py-3 sm:py-4 last:border-0" style="border-bottom: 1px solid rgb(var(--border-soft));">
            <div class="flex items-start gap-2 sm:gap-3">
                @if ($review->user?->avatar_url)
                    <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->username ?? 'User' }}"
                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover flex-shrink-0"
                         style="border: 1px solid rgb(var(--border-soft));">
                @else
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-base flex-shrink-0"
                         style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                        {{ strtoupper(substr($review->user->username ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="font-semibold text-xs sm:text-sm" style="color: rgb(var(--text-primary));">{{ $review->user->username ?? 'User' }}</p>
                        <p class="text-[10px] sm:text-sm leading-none" style="color: rgb(var(--accent));">{{ $review->bintang }}</p>
                    </div>
                    <p class="text-[9px] sm:text-xs mb-1.5 sm:mb-2" style="color: rgb(var(--text-muted));">{{ $review->created_at->format('d M Y H:i') }}</p>
                    @if ($review->komentar)
                        <p class="text-xs sm:text-sm leading-relaxed" style="color: rgb(var(--text-secondary));">{{ $review->komentar }}</p>
                    @endif
                    @if ($review->media_url)
                        @if ($review->media_type === 'video')
                            <video controls preload="metadata" class="mt-2.5 rounded-xl max-h-64 w-full sm:w-80 bg-black/90"
                                   style="border: 1px solid rgb(var(--border-soft));">
                                <source src="{{ $review->media_url }}">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        @else
                            <button type="button" @click="lightboxUrl = '{{ $review->media_url }}'"
                                    class="mt-2.5 block group relative rounded-xl overflow-hidden focus:outline-none"
                                    style="border: 1px solid rgb(var(--border-soft));">
                                <img src="{{ $review->media_url }}" alt="Foto ulasan"
                                     class="h-32 w-32 sm:h-40 sm:w-40 object-cover group-hover:scale-105 transition duration-200">
                                <span class="absolute bottom-1.5 right-1.5 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-black/60 text-white">
                                    Perbesar
                                </span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-xs sm:text-sm text-center py-6 sm:py-8" style="color: rgb(var(--text-muted));">Belum ada ulasan untuk produk ini.</p>
    @endforelse

    {{-- Lightbox Preview Foto Ulasan --}}
    <div x-show="lightboxUrl" x-cloak @click.self="lightboxUrl = null" @keydown.escape.window="lightboxUrl = null"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4"
         style="background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(6px);">
        <div class="relative max-w-3xl w-full flex flex-col items-center">
            <button type="button" @click="lightboxUrl = null"
                    class="self-end mb-2 px-3 py-1.5 rounded-full text-xs font-bold bg-white/15 text-white hover:bg-white/25 transition inline-flex items-center gap-1">
                <span>Tutup</span> <x-icon name="x-mark" class="w-4 h-4" />
            </button>
            <img :src="lightboxUrl" alt="Preview foto ulasan" class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl">
        </div>
    </div>
</div>