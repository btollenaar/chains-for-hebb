<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display all reviews with filters
     */
    public function index(Request $request)
    {
        $query = Review::with(['customer', 'reviewable']);

        // Status filter (all/pending/approved/rejected)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Rating filter (1-5 stars)
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Type filter (products only now)
        if ($request->filled('type') && $request->type === 'products') {
            $query->where('reviewable_type', Product::class);
        }

        // Verified purchase filter
        if ($request->filled('verified')) {
            if ($request->verified === 'yes') {
                $query->where('verified_purchase', true);
            } elseif ($request->verified === 'no') {
                $query->where('verified_purchase', false);
            }
        }

        // Search by customer name or review content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Order by most recent
        $query->orderBy('created_at', 'desc');

        $reviews = $query->paginate(20);

        // Stats for dashboard - Optimized: single query
        $statsQuery = Review::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN admin_response IS NOT NULL THEN 1 ELSE 0 END) as with_response,
            AVG(CASE WHEN status = 'approved' THEN rating ELSE NULL END) as average_rating
        ")->first();

        $stats = [
            'total' => $statsQuery->total ?? 0,
            'pending' => $statsQuery->pending ?? 0,
            'approved' => $statsQuery->approved ?? 0,
            'rejected' => $statsQuery->rejected ?? 0,
            'with_response' => $statsQuery->with_response ?? 0,
            'average_rating' => $statsQuery->average_rating ? round($statsQuery->average_rating, 1) : 0,
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Display review details
     */
    public function show(Review $review)
    {
        $review->load(['customer', 'reviewable']);

        // Get related reviews for the same item
        $relatedReviews = Review::where('reviewable_type', $review->reviewable_type)
            ->where('reviewable_id', $review->reviewable_id)
            ->where('id', '!=', $review->id)
            ->approved()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.reviews.show', compact('review', 'relatedReviews'));
    }

    /**
     * Quick approve action
     */
    public function approve(Review $review)
    {
        $review->approve();

        return back()->with('success', 'Review approved successfully.');
    }

    /**
     * Quick reject action
     */
    public function reject(Review $review)
    {
        $review->reject();

        return back()->with('success', 'Review rejected successfully.');
    }

    /**
     * Update review (add admin response)
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string|max:1000',
        ]);

        $review->addAdminResponse($validated['admin_response']);

        return back()->with('success', 'Admin response added successfully.');
    }

    /**
     * Delete review (soft delete)
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
