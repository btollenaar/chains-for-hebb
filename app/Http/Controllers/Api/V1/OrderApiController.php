<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('items.item')
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order)
    {
        if ($order->customer_id !== $request->user()->id) {
            abort(403);
        }

        $order->load('items.item');
        return new OrderResource($order);
    }
}
