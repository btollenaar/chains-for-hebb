@props(['reviewable', 'type'])

@php
    // Get approved reviews for this product/service
    $reviews = $reviewable->reviews()
        ->with('customer')
        ->approved()
        ->orderBy('created_at', 'desc')
        ->get();

    $averageRating = $reviews->avg('rating');
    $totalReviews = $reviews->count();

    // Rating distribution
    $ratingCounts = [
        5 => $reviews->where('rating', 5)->count(),
        4 => $reviews->where('rating', 4)->count(),
        3 => $reviews->where('rating', 3)->count(),
        2 => $reviews->where('rating', 2)->count(),
        1 => $reviews->where('rating', 1)->count(),
    ];

    // Check if current user has already reviewed
    $userReview = null;
    if (Auth::check()) {
        $userReview = $reviewable->reviews()
            ->where('customer_id', Auth::id())
            ->first();
    }
@endphp

<div class="card-glass rounded-2xl p-6 md:p-8">
    <h2 class="text-2xl font-bold mb-6" style="color: var(--on-surface);">Customer Reviews</h2>

    @if($totalReviews > 0)
        <!-- Rating Summary -->
        <div class="pb-6 mb-6" style="border-bottom: 1px solid var(--surface-border);">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <!-- Overall Rating -->
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 mb-2">
                        <span class="text-4xl font-bold" style="color: var(--on-surface);">{{ number_format($averageRating, 1) }}</span>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-yellow-400' : '' }} text-xl" style="{{ $i > round($averageRating) ? 'color: var(--on-surface-muted); opacity: 0.3;' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <p style="color: var(--on-surface-muted);">Based on {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</p>
                </div>

                <!-- Rating Breakdown -->
                <div class="flex-1 max-w-md">
                    @foreach([5, 4, 3, 2, 1] as $rating)
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm w-8" style="color: var(--on-surface-muted);">{{ $rating }} <i class="fas fa-star text-xs text-yellow-400"></i></span>
                            <div class="flex-1 rounded-full h-2" style="background: var(--surface-border, rgba(148,163,184,0.2));">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $totalReviews > 0 ? ($ratingCounts[$rating] / $totalReviews * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm w-8" style="color: var(--on-surface-muted);">{{ $ratingCounts[$rating] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Write a Review Button -->
    @auth
        @if(!$userReview)
            <div class="mb-8">
                <button onclick="document.getElementById('review-form').scrollIntoView({behavior: 'smooth'})"
                        class="btn-gradient">
                    <i class="fas fa-edit mr-2"></i>Write a Review
                </button>
            </div>
        @else
            <div class="rounded-lg p-4 mb-8" style="background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.3);">
                <p style="color: var(--on-surface);">
                    <i class="fas fa-info-circle mr-2 text-blue-400"></i>
                    You have already submitted a review for this {{ $type }}.
                    @if($userReview->status === 'pending')
                        It is currently pending approval.
                    @elseif($userReview->status === 'approved')
                        Your review has been approved and is shown below.
                    @endif
                </p>
            </div>
        @endif
    @else
        <div class="rounded-lg p-4 mb-8" style="background: var(--surface-raised); border: 1px solid var(--surface-border, rgba(148,163,184,0.2));">
            <p style="color: var(--on-surface-muted);">
                <a href="{{ route('login') }}" class="text-earth-primary hover:underline font-semibold">Sign in</a> to write a review.
            </p>
        </div>
    @endauth

    <!-- Reviews List -->
    @if($totalReviews > 0)
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="pb-6 last:border-b-0 last:pb-0" style="border-bottom: 1px solid var(--surface-border, rgba(148,163,184,0.2));">
                    <!-- Review Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-1 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : '' }}" style="{{ $i > $review->rating ? 'color: var(--on-surface-muted); opacity: 0.3;' : '' }}"></i>
                                @endfor
                                @if($review->verified_purchase)
                                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-earth-success/15 text-earth-success">
                                        <i class="fas fa-check-circle"></i> Verified Purchase
                                    </span>
                                @endif
                            </div>
                            @if($review->title)
                                <h3 class="font-semibold" style="color: var(--on-surface);">{{ $review->title }}</h3>
                            @endif
                        </div>
                        <span class="text-sm" style="color: var(--on-surface-muted);">{{ $review->created_at->format('M d, Y') }}</span>
                    </div>

                    <!-- Review Content -->
                    <p class="mb-3" style="color: var(--on-surface-muted);">{{ $review->comment }}</p>

                    <!-- Review Meta -->
                    <div class="flex items-center justify-between">
                        <p class="text-sm" style="color: var(--on-surface-muted);">
                            <i class="fas fa-user mr-1"></i>{{ $review->customer->name }}
                        </p>

                        <!-- Helpful Buttons -->
                        @auth
                            <div class="flex items-center gap-4 text-sm">
                                <span style="color: var(--on-surface-muted);">Was this helpful?</span>
                                <form action="{{ route('reviews.helpful', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="hover:text-earth-success transition-colors" style="color: var(--on-surface-muted);">
                                        <i class="fas fa-thumbs-up"></i>
                                        @if($review->helpful_count > 0)
                                            <span>({{ $review->helpful_count }})</span>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('reviews.not-helpful', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="hover:text-red-400 transition-colors" style="color: var(--on-surface-muted);">
                                        <i class="fas fa-thumbs-down"></i>
                                        @if($review->not_helpful_count > 0)
                                            <span>({{ $review->not_helpful_count }})</span>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>

                    <!-- Admin Response -->
                    @if($review->admin_response)
                        <div class="mt-4 ml-6 p-4 rounded-lg" style="background: var(--surface-raised); border-left: 4px solid var(--glass-border);">
                            <p class="text-sm font-semibold mb-1" style="color: var(--on-surface);">
                                <i class="fas fa-reply mr-1"></i>Response from {{ config('business.profile.name') }}
                            </p>
                            <p class="text-sm" style="color: var(--on-surface-muted);">{{ $review->admin_response }}</p>
                            <p class="text-xs mt-1" style="color: var(--on-surface-muted); opacity: 0.7;">{{ $review->responded_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-star text-6xl mb-4" style="color: var(--on-surface-muted); opacity: 0.3;"></i>
            <p class="text-lg mb-2" style="color: var(--on-surface-muted);">No reviews yet</p>
            <p style="color: var(--on-surface-muted); opacity: 0.7;">Be the first to review this {{ $type }}!</p>
        </div>
    @endif

    <!-- Review Form -->
    @auth
        @if(!$userReview)
            <div id="review-form" class="mt-8 pt-8" style="border-top: 1px solid var(--surface-border, rgba(148,163,184,0.2));">
                <h3 class="text-xl font-bold mb-4" style="color: var(--on-surface);">Write Your Review</h3>

                <form action="{{ route('products.reviews.store', $reviewable) }}" method="POST">
                    @csrf

                    <!-- Rating -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2" style="color: var(--on-surface-muted);">
                            Rating <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-1" x-data="{ rating: 0, hover: 0 }">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        @click="rating = {{ $i }}"
                                        @mouseenter="hover = {{ $i }}"
                                        @mouseleave="hover = 0"
                                        class="text-3xl focus:outline-none">
                                    <i class="fas fa-star transition-colors"
                                       :class="(hover >= {{ $i }} || (hover === 0 && rating >= {{ $i }})) ? 'text-yellow-400' : ''"
                                       :style="(hover >= {{ $i }} || (hover === 0 && rating >= {{ $i }})) ? '' : 'color: var(--on-surface-muted); opacity: 0.3;'"></i>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating" required>
                        </div>
                        @error('rating')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium mb-2" style="color: var(--on-surface-muted);">
                            Review Title <span style="color: var(--on-surface-muted); opacity: 0.7;">(Optional)</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                               class="input-glass block w-full rounded-xl"
                               placeholder="Summarize your experience">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comment -->
                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium mb-2" style="color: var(--on-surface-muted);">
                            Your Review <span class="text-red-500">*</span>
                        </label>
                        <textarea name="comment" id="comment" rows="4" required
                                  class="input-glass block w-full rounded-xl"
                                  placeholder="Share your experience with this {{ $type }}...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-gradient">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Review
                    </button>
                </form>
            </div>
        @endif
    @endauth
</div>
