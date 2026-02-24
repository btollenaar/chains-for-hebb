<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

/**
 * API Cart Controller
 *
 * Provides JSON endpoints for cart operations
 */
class CartController extends Controller
{
    /**
     * Get current cart count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $count = $this->getCartCount();

        return response()->json([
            'count' => $count,
            'success' => true
        ]);
    }

    /**
     * Get the total quantity of items in cart
     *
     * @return int
     */
    protected function getCartCount(): int
    {
        if (Auth::check()) {
            return Cart::forCustomer(Auth::id())->sum('quantity');
        }

        return Cart::forSession(session()->getId())->sum('quantity');
    }
}
