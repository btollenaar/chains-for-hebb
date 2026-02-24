<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Display newsletter subscribers with filters
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscription::query();

        // Status filter (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Search by email or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Order by most recent
        $query->orderBy('created_at', 'desc');

        $subscriptions = $query->paginate(20);

        // Stats for dashboard
        $stats = [
            'total' => NewsletterSubscription::count(),
            'active' => NewsletterSubscription::where('is_active', true)->count(),
            'inactive' => NewsletterSubscription::where('is_active', false)->count(),
            'this_month' => NewsletterSubscription::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        // Calculate active percentage
        $stats['active_percentage'] = $stats['total'] > 0
            ? round(($stats['active'] / $stats['total']) * 100, 1)
            : 0;

        // Get unique sources for filter dropdown
        $sources = NewsletterSubscription::distinct()->pluck('source')->filter();

        return view('admin.newsletter.index', compact('subscriptions', 'stats', 'sources'));
    }

    /**
     * Export subscribers to CSV
     * Uses chunking to handle large datasets without memory issues
     */
    public function export(Request $request)
    {
        // Generate CSV
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Store filters for use in callback
        $queryFilters = [
            'status' => $request->input('status'),
            'source' => $request->input('source'),
            'search' => $request->input('search'),
        ];

        $callback = function () use ($queryFilters) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, ['Email', 'Name', 'Source', 'Status', 'Subscribed Date', 'Unsubscribed Date']);

            // Rebuild query inside callback for streaming
            $query = NewsletterSubscription::query();

            if (!empty($queryFilters['status'])) {
                if ($queryFilters['status'] === 'active') {
                    $query->where('is_active', true);
                } elseif ($queryFilters['status'] === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            if (!empty($queryFilters['source'])) {
                $query->where('source', $queryFilters['source']);
            }

            if (!empty($queryFilters['search'])) {
                $search = $queryFilters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
                });
            }

            // Use chunk to process in batches of 500 (memory efficient for large exports)
            $query->orderBy('created_at', 'desc')->chunk(500, function ($subscriptions) use ($file) {
                foreach ($subscriptions as $subscription) {
                    fputcsv($file, [
                        $subscription->email,
                        $subscription->name ?? '',
                        $subscription->source ?? '',
                        $subscription->is_active ? 'Active' : 'Inactive',
                        $subscription->subscribed_at ? $subscription->subscribed_at->format('Y-m-d H:i:s') : '',
                        $subscription->unsubscribed_at ? $subscription->unsubscribed_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Activate a subscription (resubscribe)
     */
    public function activate(NewsletterSubscription $subscription)
    {
        $subscription->update([
            'is_active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        return back()->with('success', 'Subscriber reactivated successfully.');
    }

    /**
     * Deactivate a subscription (unsubscribe)
     */
    public function deactivate(NewsletterSubscription $subscription)
    {
        $subscription->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return back()->with('success', 'Subscriber unsubscribed successfully.');
    }

    /**
     * Delete a subscription permanently
     */
    public function destroy(NewsletterSubscription $subscription)
    {
        $subscription->delete();

        return back()->with('success', 'Subscriber deleted successfully.');
    }
}
