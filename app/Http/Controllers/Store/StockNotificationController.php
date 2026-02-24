<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockNotificationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'email' => 'required|email|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock_quantity > 0) {
            return response()->json(['message' => 'This product is currently in stock.'], 422);
        }

        StockNotification::updateOrCreate(
            [
                'email' => $validated['email'],
                'product_id' => $validated['product_id'],
            ],
            [
                'customer_id' => Auth::id(),
                'notified_at' => null,
            ]
        );

        return response()->json(['message' => 'You\'ll be notified when this product is back in stock!']);
    }
}
