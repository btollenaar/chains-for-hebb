<?php

namespace App\View\Composers;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Cache for 1 hour for performance
        $productCategories = Cache::remember('navigation.product_categories', 3600, function () {
            // Load all active top-level categories with their children
            // IMPORTANT: Eager load product counts to prevent N+1 queries
            $categories = ProductCategory::active()
                ->topLevel()
                ->with([
                    'childrenRecursive' => fn($q) => $q->where('is_active', true)
                        ->ordered()
                        ->withCount(['allProducts as active_products_count' => fn($q) => $q->where('status', 'active')])
                        ->withCount(['products as direct_products_count' => fn($q) => $q->where('status', 'active')])
                ])
                ->withCount(['allProducts as active_products_count' => fn($q) => $q->where('status', 'active')])
                ->withCount(['products as direct_products_count' => fn($q) => $q->where('status', 'active')])
                ->ordered()
                ->get();

            // Recursively filter out empty categories
            return ProductCategory::filterEmptyCategories($categories);
        });

        $view->with([
            'navProductCategories' => $productCategories,
        ]);
    }
}
