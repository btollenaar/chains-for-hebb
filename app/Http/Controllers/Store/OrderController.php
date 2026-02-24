<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display customer's orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['items.item'])
            ->where('customer_id', Auth::id());

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'LIKE', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display single order details
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.item', 'customer']);

        return view('orders.show', compact('order'));
    }

    /**
     * Download invoice PDF for a paid order
     */
    public function downloadInvoice(Order $order, InvoiceService $invoiceService)
    {
        $this->authorize('view', $order);

        if ($order->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Invoice is only available for paid orders.');
        }

        return $invoiceService->downloadInvoice($order);
    }

    /**
     * Reorder: Add items from a previous order back to cart
     */
    public function reorder(Order $order)
    {
        $this->authorize('view', $order);

        $addedCount = 0;
        $unavailableItems = [];
        $priceChanges = [];

        foreach ($order->items as $item) {
            // Check if the item still exists
            if (!$item->item) {
                $unavailableItems[] = $item->name;
                continue;
            }

            // For products, check stock availability
            if ($item->item_type === Product::class) {
                if ($item->item->stock_quantity < 1) {
                    $unavailableItems[] = $item->name . ' (out of stock)';
                    continue;
                }

                // Check if there's enough stock for the quantity
                if ($item->item->stock_quantity < $item->quantity) {
                    $unavailableItems[] = $item->name . ' (only ' . $item->item->stock_quantity . ' available)';
                    continue;
                }
            }

            // Check for price changes
            $currentPrice = $item->item->current_price ?? $item->item->base_price ?? $item->item->price ?? 0;
            if ($currentPrice != $item->unit_price) {
                $priceChanges[] = $item->name . ' (was $' . number_format($item->unit_price, 2) . ', now $' . number_format($currentPrice, 2) . ')';
            }

            // Check if item already exists in cart
            $existingCartItem = Cart::where('customer_id', Auth::id())
                ->where('item_type', $item->item_type)
                ->where('item_id', $item->item_id)
                ->first();

            if ($existingCartItem) {
                // Update quantity
                $existingCartItem->quantity += $item->quantity;
                $existingCartItem->save();
            } else {
                // Add new cart item
                Cart::create([
                    'customer_id' => Auth::id(),
                    'item_type' => $item->item_type,
                    'item_id' => $item->item_id,
                    'quantity' => $item->quantity,
                    'attributes' => $item->attributes ?? [],
                ]);
            }

            $addedCount++;
        }

        // Build success message
        $message = $addedCount . ' item(s) added to your cart.';

        // Add warnings for unavailable items
        if (!empty($unavailableItems)) {
            $message .= ' Some items are no longer available: ' . implode(', ', $unavailableItems) . '.';
        }

        // Add info about price changes
        if (!empty($priceChanges)) {
            $message .= ' Note: Prices have changed for: ' . implode(', ', $priceChanges) . '.';
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    /**
     * Dedicated tracking page for an order
     */
    public function tracking(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.item', 'customer']);

        return view('orders.tracking', compact('order'));
    }

    /**
     * Public tracking lookup form
     */
    public function trackForm()
    {
        return view('orders.track');
    }

    /**
     * Public tracking lookup
     */
    public function trackLookup(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::where('order_number', $validated['order_number'])
            ->whereHas('customer', fn($q) => $q->where('email', $validated['email']))
            ->with(['items.item', 'customer'])
            ->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'No order found with that order number and email.'])->withInput();
        }

        return view('orders.tracking', compact('order'));
    }
}
