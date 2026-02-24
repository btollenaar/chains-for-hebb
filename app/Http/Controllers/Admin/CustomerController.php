<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Tag filter
        if ($request->filled('tag')) {
            $query->withTag($request->tag);
        }

        // Order by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate with counts
        $customers = $query->withCount(['orders'])
            ->paginate(20)
            ->appends($request->all());

        $tags = Tag::ordered()->get();

        return view('admin.customers.index', compact('customers', 'tags'));
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Load relationships
        $customer->load([
            'orders' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
        ]);

        // Calculate statistics
        $stats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('payment_status', 'paid')->sum('total_amount'),
        ];

        // Get all tags for the tag management section
        $tags = Tag::ordered()->get();

        // Load customer's current tags
        $customer->load('tags');

        return view('admin.customers.show', compact('customer', 'stats', 'tags'));
    }

    /**
     * Export customers to CSV
     * Uses chunking to handle large datasets without memory issues
     */
    public function export(Request $request)
    {
        // Generate CSV
        $filename = 'customers-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Store filters for use in callback
        $queryFilters = [
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
        ];

        $callback = function () use ($queryFilters) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Name',
                'Email',
                'Phone',
                'Role',
                'Total Orders',
                'Total Spent',
                'Joined Date',
                'Email Verified',
            ]);

            // Rebuild query inside callback for streaming
            $query = Customer::withCount(['orders']);

            if (!empty($queryFilters['search'])) {
                $search = $queryFilters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if (!empty($queryFilters['role'])) {
                $query->where('role', $queryFilters['role']);
            }

            $query->orderBy($queryFilters['sort_by'], $queryFilters['sort_order']);

            // Use chunk to process in batches of 500 (memory efficient for large exports)
            $query->chunk(500, function ($customers) use ($file) {
                foreach ($customers as $customer) {
                    // Calculate total spent inline (avoid loading all orders)
                    $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total');

                    fputcsv($file, [
                        $customer->name,
                        $customer->email,
                        $customer->phone ?? '',
                        ucfirst($customer->role),
                        $customer->orders_count,
                        number_format($totalSpent, 2),
                        $customer->created_at->format('Y-m-d'),
                        $customer->email_verified_at ? 'Yes' : 'No',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
