<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistItems = Wishlist::with('item')
            ->forCustomer(Auth::id())
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:product',
            'item_id' => 'required|integer',
        ]);

        $itemType = Product::class;
        $itemId = $request->item_id;

        $item = Product::find($itemId);
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $added = Wishlist::toggle($itemType, $itemId, Auth::id());

        $count = Wishlist::forCustomer(Auth::id())->count();

        return response()->json([
            'success' => true,
            'added' => $added,
            'message' => $added ? 'Added to wishlist' : 'Removed from wishlist',
            'wishlistCount' => $count,
        ]);
    }

    public function moveToCart(Wishlist $wishlist)
    {
        if ($wishlist->customer_id !== Auth::id()) {
            abort(403);
        }

        $existingCart = Cart::where('customer_id', Auth::id())
            ->where('item_type', $wishlist->item_type)
            ->where('item_id', $wishlist->item_id)
            ->first();

        if ($existingCart) {
            $existingCart->increment('quantity');
        } else {
            Cart::create([
                'customer_id' => Auth::id(),
                'item_type' => $wishlist->item_type,
                'item_id' => $wishlist->item_id,
                'quantity' => 1,
            ]);
        }

        $wishlist->delete();

        return redirect()->back()
            ->with('success', 'Item moved to cart');
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->customer_id !== Auth::id()) {
            abort(403);
        }

        $wishlist->delete();

        return redirect()->back()
            ->with('success', 'Item removed from wishlist');
    }

    public function share()
    {
        $customer = Auth::user();

        if (!$customer->wishlist_share_token) {
            $customer->update([
                'wishlist_share_token' => Str::random(64),
            ]);
        }

        $shareUrl = route('wishlist.shared', $customer->wishlist_share_token);

        return response()->json([
            'success' => true,
            'url' => $shareUrl,
        ]);
    }

    public function shared(string $token)
    {
        $customer = Customer::where('wishlist_share_token', $token)->firstOrFail();

        $wishlistItems = Wishlist::with('item')
            ->forCustomer($customer->id)
            ->latest()
            ->get();

        $ownerName = $customer->name;

        return view('wishlist.shared', compact('wishlistItems', 'ownerName'));
    }
}
