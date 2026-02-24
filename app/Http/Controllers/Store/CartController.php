<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $subtotal = $this->getSubtotal();
        $tax = $subtotal * config('business.payments.tax_rate');
        $total = $subtotal + $tax;

        return view('cart.index', compact('cartItems', 'subtotal', 'tax', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:product',
            'item_id' => 'required|integer',
            'quantity' => 'integer|min:1',
            'product_variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $item = Product::findOrFail($request->item_id);
        $quantity = $request->quantity ?? 1;
        $variant = null;

        // For Printful products, a variant is required
        if ($item->isPrintful && $request->product_variant_id) {
            $variant = ProductVariant::where('id', $request->product_variant_id)
                ->where('product_id', $item->id)
                ->where('is_active', true)
                ->firstOrFail();

            if ($variant->stock_status !== 'in_stock') {
                $message = 'Sorry, this variant is out of stock.';
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }
        } else {
            // Standard product stock validation
            if ($item->stock_quantity <= 0) {
                $message = 'Sorry, this product is out of stock.';
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }

            if ($quantity > $item->stock_quantity) {
                $message = "Only {$item->stock_quantity} units available in stock.";
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 400);
                }
                return redirect()->back()->with('error', $message);
            }
        }

        // Check for existing cart item with same variant
        $existingQuery = Cart::query();
        if (Auth::check()) {
            $existingQuery->where('customer_id', $this->getCartIdentifier());
        } else {
            $existingQuery->where('session_id', $this->getCartIdentifier());
        }
        $existingQuery->where('item_type', get_class($item))
            ->where('item_id', $item->id);

        if ($variant) {
            $existingQuery->where('product_variant_id', $variant->id);
        } else {
            $existingQuery->whereNull('product_variant_id');
        }

        $existing = $existingQuery->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            $data = [
                'item_type' => get_class($item),
                'item_id' => $item->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $quantity,
                'attributes' => $request->attributes ?? [],
            ];

            if (Auth::check()) {
                $data['customer_id'] = $this->getCartIdentifier();
            } else {
                $data['session_id'] = $this->getCartIdentifier();
            }

            Cart::create($data);
        }

        $cartCount = $this->getCartCount();
        $message = 'Item added to cart!';

        if ($request->expectsJson()) {
            $itemPrice = $variant ? (float) $variant->retail_price : (float) $item->currentPrice;

            return response()->json([
                'message' => $message,
                'cartCount' => $cartCount,
                'success' => true,
                'item' => [
                    'id' => $variant ? $variant->sku : ($item->sku ?? $item->id),
                    'name' => $variant ? $item->name . ' - ' . $variant->display_name : $item->name,
                    'price' => $itemPrice,
                    'quantity' => $quantity,
                ],
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::findForOwner($id, $this->getCartIdentifier(), Auth::check());
        if (! $cartItem) {
            abort(404);
        }

        // Stock validation for non-variant products
        if (!$cartItem->product_variant_id && $cartItem->item) {
            if ($cartItem->item->stock_quantity <= 0) {
                return redirect()->back()->with('error', 'Sorry, this product is out of stock.');
            }

            if ($request->quantity > $cartItem->item->stock_quantity) {
                return redirect()->back()->with('error', "Only {$cartItem->item->stock_quantity} units available in stock.");
            }
        }

        Cart::updateQuantity(
            $id,
            $request->quantity,
            $this->getCartIdentifier(),
            Auth::check()
        );

        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove($id)
    {
        $cartItem = Cart::findForOwner($id, $this->getCartIdentifier(), Auth::check());
        if (! $cartItem) {
            abort(404);
        }
        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed from cart!');
    }

    public function clear()
    {
        Cart::clearCart($this->getCartIdentifier(), Auth::check());

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    protected function getCartIdentifier()
    {
        return Auth::check() ? Auth::id() : session()->getId();
    }

    protected function getCartItems()
    {
        if (Auth::check()) {
            return Cart::forCustomer(Auth::id())->with(['item', 'variant', 'item.mockups'])->get();
        }
        return Cart::forSession(session()->getId())->with(['item', 'variant', 'item.mockups'])->get();
    }

    protected function getSubtotal()
    {
        return Cart::getSubtotal($this->getCartIdentifier(), Auth::check());
    }

    protected function getCartCount()
    {
        if (Auth::check()) {
            return Cart::forCustomer(Auth::id())->sum('quantity');
        }
        return Cart::forSession(session()->getId())->sum('quantity');
    }
}
