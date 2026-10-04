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

<div class="mt-10 border-t border-gray-100 pt-8">

    <h2 class="text-xl font-bold text-gray-800 mb-4">Ulasan Pembeli</h2>

    {{-- Ringkasan Rating --}}
    <div class="flex items-center gap-6 mb-6 bg-gray-50 rounded-2xl p-5">
        <div class="text-center">
            <p class="text-4xl font-bold text-amber-500">{{ number_format($avgRating, 1) }}</p>
            <p class="text-amber-400 text-lg leading-none mt-1">
                @for ($i = 1; $i <= 5; $i++)
                    {{ $i <= round($avgRating) ? '★' : '☆' }}
                @endfor
            </p>
            <p class="text-xs text-gray-500 mt-1">{{ $totalReview }} ulasan</p>
        </div>
    </div>

    {{-- Form Review --}}
    @if ($pesananBisaDireview->isNotEmpty())
        @php $pesanan = $pesananBisaDireview->first(); @endphp

        <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-2xl p-5"
             x-data="{ rating: 0, hoverRating: 0 }">
            <p class="font-semibold text-emerald-700 mb-3">Beri Ulasan Anda</p>

            <form action="{{ route('user.review.store', $pesanan->id) }}" method="POST">
                @csrf
                <input type="hidden" name="reviewable_type" value="{{ $itemType }}">
                <input type="hidden" name="reviewable_id" value="{{ $item->id }}">
                <input type="hidden" name="rating" :value="rating">

                {{-- Pilih Bintang --}}
                <div class="flex gap-1 mb-3">
                    <template x-for="i in 5" :key="i">
                        <button type="button"
                                @click="rating = i"
                                @mouseenter="hoverRating = i"
                                @mouseleave="hoverRating = 0"
                                class="text-3xl transition leading-none"
                                :class="(hoverRating || rating) >= i ? 'text-amber-400' : 'text-gray-300'">
                            ★
                        </button>
                    </template>
                    <span class="ml-3 text-sm text-gray-500 self-center"
                          x-text="rating === 0 ? 'Pilih bintang' : rating + ' / 5'"></span>
                </div>

                {{-- Komentar --}}
                <textarea name="komentar" rows="3" maxlength="500"
                          placeholder="Ceritakan pengalaman Anda (opsional)..."
                          class="w-full px-4 py-2.5 rounded-xl border border-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm mb-3">{{ old('komentar') }}</textarea>

                @error('rating')
                    <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                @enderror

                <button type="submit" :disabled="rating === 0"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition text-sm">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    @endif

    {{-- List Review --}}
    @forelse ($reviews as $review)
        <div class="border-b border-gray-100 py-4 last:border-0">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold flex-shrink-0">
                    {{ strtoupper(substr($review->user->username ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <p class="font-semibold text-gray-800 text-sm">{{ $review->user->username ?? 'User' }}</p>
                        <p class="text-amber-400 text-sm leading-none">{{ $review->bintang }}</p>
                    </div>
                    <p class="text-xs text-gray-400 mb-2">{{ $review->created_at->format('d M Y H:i') }}</p>
                    @if ($review->komentar)
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $review->komentar }}</p>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-400 text-sm text-center py-8">Belum ada ulasan untuk produk ini.</p>
    @endforelse
</div>