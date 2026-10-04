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

<div class="mt-10 pt-8" style="border-top: 1px solid rgb(var(--border-soft));">

    <h2 class="text-xl font-extrabold mb-4" style="color: rgb(var(--text-primary));">Ulasan Pembeli</h2>

    {{-- Ringkasan Rating --}}
    <div class="flex items-center gap-6 mb-6 rounded-2xl p-5" style="background: rgb(var(--bg-secondary));">
        <div class="text-center">
            <p class="text-4xl font-extrabold" style="color: rgb(var(--accent));">{{ number_format($avgRating, 1) }}</p>
            <p class="text-lg leading-none mt-1" style="color: rgb(var(--accent));">
                @for ($i = 1; $i <= 5; $i++)
                    {{ $i <= round($avgRating) ? '★' : '☆' }}
                @endfor
            </p>
            <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">{{ $totalReview }} ulasan</p>
        </div>
    </div>

    {{-- Form Review --}}
    @if ($pesananBisaDireview->isNotEmpty())
        @php $pesanan = $pesananBisaDireview->first(); @endphp

        <div class="mb-6 rounded-2xl p-5" style="background: rgb(var(--success-soft)); border: 1px solid rgb(var(--success) / 0.3);"
             x-data="{ rating: 0, hoverRating: 0 }">
            <p class="font-semibold mb-3" style="color: rgb(var(--success));">Beri Ulasan Anda</p>

            <form action="{{ route('user.review.store', $pesanan->id) }}" method="POST">
                @csrf
                <input type="hidden" name="reviewable_type" value="{{ $itemType }}">
                <input type="hidden" name="reviewable_id" value="{{ $item->id }}">
                <input type="hidden" name="rating" :value="rating">

                <div class="flex gap-1 mb-3">
                    <template x-for="i in 5" :key="i">
                        <button type="button"
                                @click="rating = i"
                                @mouseenter="hoverRating = i"
                                @mouseleave="hoverRating = 0"
                                class="text-3xl transition leading-none"
                                :class="(hoverRating || rating) >= i ? 'text-amber-400' : ''"
                                :style="(hoverRating || rating) >= i ? '' : 'color: rgb(var(--text-faint));'">
                            ★
                        </button>
                    </template>
                    <span class="ml-3 text-sm self-center" style="color: rgb(var(--text-secondary));"
                          x-text="rating === 0 ? 'Pilih bintang' : rating + ' / 5'"></span>
                </div>

                <textarea name="komentar" rows="3" maxlength="500"
                          placeholder="Ceritakan pengalaman Anda (opsional)..."
                          class="glass-input w-full px-4 py-2.5 text-sm mb-3" style="color: rgb(var(--text-primary));">{{ old('komentar') }}</textarea>

                @error('rating')
                    <p class="text-xs mb-2" style="color: rgb(var(--danger));">{{ $message }}</p>
                @enderror

                <button type="submit" :disabled="rating === 0"
                        class="px-5 py-2.5 text-white font-semibold rounded-xl transition text-sm disabled:cursor-not-allowed"
                        :class="rating === 0 ? 'opacity-50' : 'hover:-translate-y-0.5'"
                        style="background: var(--gradient-brand); box-shadow: var(--shadow-brand);">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    @endif

    {{-- List Review --}}
    @forelse ($reviews as $review)
        <div class="py-4 last:border-0" style="border-bottom: 1px solid rgb(var(--border-soft));">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold flex-shrink-0"
                     style="background: rgb(var(--brand-soft)); color: rgb(var(--brand-strong));">
                    {{ strtoupper(substr($review->user->username ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="font-semibold text-sm" style="color: rgb(var(--text-primary));">{{ $review->user->username ?? 'User' }}</p>
                        <p class="text-sm leading-none" style="color: rgb(var(--accent));">{{ $review->bintang }}</p>
                    </div>
                    <p class="text-xs mb-2" style="color: rgb(var(--text-muted));">{{ $review->created_at->format('d M Y H:i') }}</p>
                    @if ($review->komentar)
                        <p class="text-sm leading-relaxed" style="color: rgb(var(--text-secondary));">{{ $review->komentar }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-sm text-center py-8" style="color: rgb(var(--text-muted));">Belum ada ulasan untuk produk ini.</p>
    @endforelse
</div>