<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Get products frequently bought together with the given product.
     * Finds orders containing this product, then returns other products from those orders
     * ranked by co-purchase frequency.
     */
    public function getAlsoBought(Product $product, int $limit = 4): Collection
    {
        // Find order IDs that contain this product (paid orders only)
        $orderIds = OrderItem::where('item_type', Product::class)
            ->where('item_id', $product->id)
            ->whereHas('order', fn($q) => $q->where('payment_status', 'paid'))
            ->pluck('order_id');

        if ($orderIds->isEmpty()) {
            return collect();
        }

        // Find other products in those same orders, ranked by frequency
        return Product::active()
            ->inStock()
            ->with(['activeVariants', 'mockups'])
            ->where('products.id', '!=', $product->id)
            ->whereIn('products.id', function ($query) use ($orderIds, $product) {
                $query->select('item_id')
                    ->from('order_items')
                    ->where('item_type', Product::class)
                    ->where('item_id', '!=', $product->id)
                    ->whereIn('order_id', $orderIds);
            })
            ->withCount(['orderItems as co_purchase_count' => function ($query) use ($orderIds) {
                $query->whereIn('order_id', $orderIds);
            }])
            ->orderByDesc('co_purchase_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products similar to the given product based on shared categories
     * and price proximity.
     */
    public function getSimilarProducts(Product $product, int $limit = 4): Collection
    {
        $categoryIds = $product->categories->pluck('id')->toArray();

        if (empty($categoryIds)) {
            return collect();
        }

        $currentPrice = $product->sale_price ?? $product->price;
        $minPrice = $currentPrice * 0.5;
        $maxPrice = $currentPrice * 2.0;

        return Product::active()
            ->inStock()
            ->with(['activeVariants', 'mockups'])
            ->where('products.id', '!=', $product->id)
            ->whereHas('categories', fn($query) =>
                $query->whereIn('product_categories.id', $categoryIds))
            ->where(function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [$minPrice, $maxPrice])
                  ->orWhere(function ($q2) use ($minPrice, $maxPrice) {
                      $q2->whereNotNull('sale_price')
                         ->whereBetween('sale_price', [$minPrice, $maxPrice]);
                  });
            })
            ->orderByRaw('ABS(COALESCE(sale_price, price) - ?) ASC', [$currentPrice])
            ->limit($limit)
            ->get();
    }
}
