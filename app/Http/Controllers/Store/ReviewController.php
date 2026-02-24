<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a product review
     */
    public function storeProductReview(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('customer_id', Auth::id())
            ->where('reviewable_type', Product::class)
            ->where('reviewable_id', $product->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Check if this is a verified purchase
        $verifiedPurchase = Order::where('customer_id', Auth::id())
            ->whereHas('items', function ($query) use ($product) {
                $query->where('item_type', Product::class)
                      ->where('item_id', $product->id);
            })
            ->where('payment_status', 'paid')
            ->exists();

        $review = Review::create([
            'customer_id' => Auth::id(),
            'reviewable_type' => Product::class,
            'reviewable_id' => $product->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'verified_purchase' => $verifiedPurchase,
            'status' => 'pending', // Requires admin approval
        ]);

        // Notify admins of new review (fail silently)
        try {
            \App\Services\AdminNotificationService::notifyNewReview($review);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin notification failed for new review', ['review_id' => $review->id, 'error' => $e->getMessage()]);
        }

        return back()->with('success', 'Thank you for your review! It will be published after moderation.');
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Review $review)
    {
        // Prevent voting on own review
        if ($review->customer_id === Auth::id()) {
            return back()->with('error', 'You cannot vote on your own review.');
        }

        // Only allow voting on approved reviews
        if ($review->status !== 'approved') {
            return back()->with('error', 'This review is not available for voting.');
        }

        // Check for existing vote
        $existingVote = ReviewVote::where('review_id', $review->id)
            ->where('customer_id', Auth::id())
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'helpful') {
                return back()->with('info', 'You have already marked this review as helpful.');
            }
            // User previously voted "not helpful", change their vote
            $existingVote->update(['vote_type' => 'helpful']);
            $review->decrement('not_helpful_count');
            $review->increment('helpful_count');
            return back()->with('success', 'Your vote has been updated.');
        }

        // Record new vote
        ReviewVote::create([
            'review_id' => $review->id,
            'customer_id' => Auth::id(),
            'vote_type' => 'helpful',
        ]);

        $review->markHelpful();

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Mark review as not helpful
     */
    public function markNotHelpful(Review $review)
    {
        // Prevent voting on own review
        if ($review->customer_id === Auth::id()) {
            return back()->with('error', 'You cannot vote on your own review.');
        }

        // Only allow voting on approved reviews
        if ($review->status !== 'approved') {
            return back()->with('error', 'This review is not available for voting.');
        }

        // Check for existing vote
        $existingVote = ReviewVote::where('review_id', $review->id)
            ->where('customer_id', Auth::id())
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === 'not_helpful') {
                return back()->with('info', 'You have already marked this review as not helpful.');
            }
            // User previously voted "helpful", change their vote
            $existingVote->update(['vote_type' => 'not_helpful']);
            $review->decrement('helpful_count');
            $review->increment('not_helpful_count');
            return back()->with('success', 'Your vote has been updated.');
        }

        // Record new vote
        ReviewVote::create([
            'review_id' => $review->id,
            'customer_id' => Auth::id(),
            'vote_type' => 'not_helpful',
        ]);

        $review->markNotHelpful();

        return back()->with('success', 'Thank you for your feedback!');
    }
}
