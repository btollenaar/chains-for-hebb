<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filtering
     */
    public function index()
    {
        $query = Order::with(['customer', 'items.item']);

        // Apply payment status filter
        if (request('payment_status')) {
            $query->where('payment_status', request('payment_status'));
        }

        // Apply fulfillment status filter
        if (request('fulfillment_status')) {
            $query->where('fulfillment_status', request('fulfillment_status'));
        }

        // Apply search filter (order number or customer name/email)
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(20);

        // Calculate stats for dashboard cards - Optimized: single query
        $statsQuery = Order::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
            SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN payment_status = 'refunded' THEN 1 ELSE 0 END) as refunded
        ")->first();

        $stats = [
            'total' => $statsQuery->total ?? 0,
            'pending' => $statsQuery->pending ?? 0,
            'paid' => $statsQuery->paid ?? 0,
            'failed' => $statsQuery->failed ?? 0,
            'refunded' => $statsQuery->refunded ?? 0,
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        // Load relationships
        $order->load(['customer', 'items.item']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     */
    public function edit(Order $order)
    {
        // Load relationships
        $order->load(['customer', 'items.item']);

        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'fulfillment_status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_carrier' => 'nullable|string|in:usps,ups,fedex,dhl,other',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $oldFulfillmentStatus = $order->fulfillment_status;
        $newFulfillmentStatus = $validated['fulfillment_status'];

        // Auto-set timestamps based on status transitions
        if ($oldFulfillmentStatus !== 'shipped' && $newFulfillmentStatus === 'shipped') {
            $validated['shipped_at'] = now();
        }
        if ($oldFulfillmentStatus !== 'delivered' && $newFulfillmentStatus === 'delivered') {
            $validated['delivered_at'] = now();
            if (!$order->shipped_at) {
                $validated['shipped_at'] = now();
            }
        }

        $order->update($validated);

        // Send status email on fulfillment status change
        if ($oldFulfillmentStatus !== $newFulfillmentStatus && in_array($newFulfillmentStatus, ['shipped', 'delivered'])) {
            try {
                $order->load('customer');
                Mail::to($order->customer->email)->send(new OrderStatusUpdateMail($order, $newFulfillmentStatus));
            } catch (\Exception $e) {
                Log::error('Order status email failed', [
                    'order_id' => $order->id,
                    'status' => $newFulfillmentStatus,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Download invoice PDF for an order
     */
    public function downloadInvoice(Order $order, InvoiceService $invoiceService)
    {
        return $invoiceService->downloadInvoice($order);
    }

    /**
     * Export orders to CSV
     * Uses chunking to handle large datasets without memory issues
     */
    public function export(Request $request)
    {
        // Build base query with filters (no eager loading yet - will be done in chunks)
        $baseQuery = Order::query();

        // Apply same filters as index
        if ($request->filled('payment_status')) {
            $baseQuery->where('payment_status', $request->payment_status);
        }

        if ($request->filled('fulfillment_status')) {
            $baseQuery->where('fulfillment_status', $request->fulfillment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Generate CSV
        $filename = 'orders-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Clone the query for use in callback (streaming requires fresh query)
        $queryFilters = [
            'payment_status' => $request->input('payment_status'),
            'fulfillment_status' => $request->input('fulfillment_status'),
            'search' => $request->input('search'),
        ];

        $callback = function () use ($queryFilters) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Order Date',
                'Subtotal',
                'Tax',
                'Total',
                'Payment Status',
                'Fulfillment Status',
                'Items Count',
                'Stripe Payment ID',
            ]);

            // Rebuild query inside callback for streaming
            $query = Order::with(['customer', 'items']);

            if (!empty($queryFilters['payment_status'])) {
                $query->where('payment_status', $queryFilters['payment_status']);
            }

            if (!empty($queryFilters['fulfillment_status'])) {
                $query->where('fulfillment_status', $queryFilters['fulfillment_status']);
            }

            if (!empty($queryFilters['search'])) {
                $search = $queryFilters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('order_number', 'LIKE', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Use chunk to process in batches of 500 (memory efficient for large exports)
            $query->latest()->chunk(500, function ($orders) use ($file) {
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->customer->name ?? '',
                        $order->customer->email ?? '',
                        $order->created_at->format('Y-m-d H:i:s'),
                        number_format($order->subtotal, 2),
                        number_format($order->tax_amount, 2),
                        number_format($order->total_amount, 2),
                        ucfirst($order->payment_status),
                        ucfirst($order->fulfillment_status),
                        $order->items->count(),
                        $order->stripe_payment_intent_id ?? '',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
