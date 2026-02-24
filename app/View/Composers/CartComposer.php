<?php

namespace App\View\Composers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Cart View Composer
 *
 * Provides cart item count to views
 * Registered with header component to show cart badge
 */
class CartComposer
{
    /**
     * Bind data to the view
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $cartCount = $this->getCartCount();
        $view->with('cartCount', $cartCount);
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
