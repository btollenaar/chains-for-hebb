<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    /**
     * Show the return request form for an order
     */
    public function create(Order $order)
    {
        $this->authorize('view', $order);

        // Only allow returns for delivered or completed orders
        if (!in_array($order->fulfillment_status, ['delivered', 'completed'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Returns can only be requested for delivered orders.');
        }

        // Check if there's already an active return request
        $existingReturn = $order->returnRequests()
            ->whereIn('status', ['requested', 'approved'])
            ->first();

        if ($existingReturn) {
            return redirect()->route('returns.show', $existingReturn)
                ->with('info', 'A return request already exists for this order.');
        }

        $order->load('items.item');

        return view('returns.create', [
            'order' => $order,
            'reasons' => ReturnRequest::reasonOptions(),
        ]);
    }

    /**
     * Store a new return request
     */
    public function store(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        if (!in_array($order->fulfillment_status, ['delivered', 'completed'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Returns can only be requested for delivered orders.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:' . implode(',', array_keys(ReturnRequest::reasonOptions()))],
            'details' => ['nullable', 'string', 'max:2000'],
            'items' => ['nullable', 'array'],
            'items.*' => ['exists:order_items,id'],
        ]);

        // Build items array from selected order items
        $returnItems = null;
        if (!empty($validated['items'])) {
            $returnItems = collect($validated['items'])->map(function ($itemId) use ($order) {
                $orderItem = $order->items()->find($itemId);
                return $orderItem ? [
                    'order_item_id' => $orderItem->id,
                    'name' => $orderItem->name,
                    'quantity' => $orderItem->quantity,
                ] : null;
            })->filter()->values()->toArray();
        }

        $returnRequest = ReturnRequest::create([
            'order_id' => $order->id,
            'customer_id' => Auth::id(),
            'reason' => $validated['reason'],
            'details' => $validated['details'],
            'items' => $returnItems,
        ]);

        // Notify admins (fail silently)
        try {
            \App\Services\AdminNotificationService::notifyNewReturnRequest($returnRequest);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin notification failed for return request', ['return_id' => $returnRequest->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('returns.show', $returnRequest)
            ->with('success', 'Your return request has been submitted. We\'ll review it shortly.');
    }

    /**
     * Show return request details
     */
    public function show(ReturnRequest $return)
    {
        if ($return->customer_id !== Auth::id()) {
            abort(403);
        }

        $return->load(['order.items.item']);

        return view('returns.show', compact('return'));
    }
}
