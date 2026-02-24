<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->inStock();

        if ($request->has('category')) {
            $query->category($request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhereHas('activeVariants', fn($vq) =>
                      $vq->where('color_name', 'like', '%' . $search . '%')
                          ->orWhere('size', 'like', '%' . $search . '%'));
            });
        }

        if ($request->has('on_sale')) {
            $query->onSale();
        }

        $this->applyFilters($query, $request);

        $query = match($request->input('sort')) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('name'),
        };

        $products = $query->with(['categories', 'activeVariants', 'mockups'])->paginate(12);

        $categories = $this->loadCategories();
        $filterOptions = $this->getFilterOptions();

        return view('products.index', compact('products', 'categories', 'filterOptions'));
    }

    public function show($slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['reviews', 'categories', 'activeVariants', 'mockups', 'designs'])
            ->firstOrFail();

        // Get related products from ANY shared category (not just primary)
        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = Product::active()
            ->with(['categories', 'activeVariants', 'mockups'])
            ->inStock()
            ->where('id', '!=', $product->id)
            ->where(function($q) use ($categoryIds) {
                $q->whereHas('categories', fn($query) =>
                    $query->whereIn('product_categories.id', $categoryIds));
            })
            ->limit(4)
            ->get();

        // Get product recommendations
        $recommendationService = new RecommendationService();
        $alsoBought = $recommendationService->getAlsoBought($product);
        $similarProducts = $recommendationService->getSimilarProducts($product);

        return view('products.show', compact('product', 'relatedProducts', 'alsoBought', 'similarProducts'));
    }

    public function category($category, Request $request)
    {
        $categoryModel = ProductCategory::active()
            ->where('slug', $category)
            ->with([
                'children' => fn($q) => $q->active()
                    ->ordered()
                    ->withCount(['allProducts as active_products_count' => fn($q) => $q->where('status', 'active')])
                    ->withCount(['products as direct_products_count' => fn($q) => $q->where('status', 'active')])
            ])
            ->firstOrFail();

        if ($categoryModel->children && $categoryModel->children->isNotEmpty()) {
            $filteredChildren = $categoryModel->children->filter(function($child) {
                $hasProductsViaPivot = isset($child->active_products_count)
                    ? $child->active_products_count > 0
                    : $child->allProducts()->where('status', 'active')->exists();

                $hasProductsViaCategoryId = isset($child->direct_products_count)
                    ? $child->direct_products_count > 0
                    : $child->products()->where('status', 'active')->exists();

                return $hasProductsViaPivot || $hasProductsViaCategoryId;
            });
            $categoryModel->setRelation('children', $filteredChildren);
        }

        $categoryIds = collect([$categoryModel->id])
            ->merge($categoryModel->getDescendantIds());

        $productsQuery = Product::active()
            ->inStock()
            ->where(function($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                  ->orWhereHas('categories', fn($query) =>
                      $query->whereIn('product_categories.id', $categoryIds->toArray()));
            });

        $this->applyFilters($productsQuery, $request);

        $productsQuery = match(request('sort')) {
            'price_asc' => $productsQuery->orderBy('price', 'asc'),
            'price_desc' => $productsQuery->orderBy('price', 'desc'),
            'newest' => $productsQuery->orderBy('created_at', 'desc'),
            default => $productsQuery->orderBy('name'),
        };

        $products = $productsQuery->with(['activeVariants', 'mockups'])->paginate(12);

        $categories = $this->loadCategories();
        $filterOptions = $this->getFilterOptions();

        return view('products.category', compact('products', 'categoryModel', 'categories', 'filterOptions'));
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('colors')) {
            $colors = is_array($request->colors) ? $request->colors : explode(',', $request->colors);
            $query->whereHas('activeVariants', fn($q) => $q->whereIn('color_name', $colors));
        }

        if ($request->filled('sizes')) {
            $sizes = is_array($request->sizes) ? $request->sizes : explode(',', $request->sizes);
            $query->whereHas('activeVariants', fn($q) => $q->whereIn('size', $sizes));
        }

        if ($request->filled('min_price')) {
            $query->whereHas('activeVariants', fn($q) => $q->where('retail_price', '>=', $request->min_price));
        }

        if ($request->filled('max_price')) {
            $query->whereHas('activeVariants', fn($q) => $q->where('retail_price', '<=', $request->max_price));
        }

        if ($request->boolean('in_stock')) {
            $query->whereHas('activeVariants', fn($q) => $q->where('stock_status', 'in_stock'));
        }
    }

    private function getFilterOptions(): array
    {
        $activeVariants = ProductVariant::active()
            ->whereHas('product', fn($q) => $q->where('status', 'active'));

        return [
            'colors' => (clone $activeVariants)
                ->whereNotNull('color_name')
                ->select('color_name', 'color_hex')
                ->distinct()
                ->orderBy('color_name')
                ->get()
                ->unique('color_name'),
            'sizes' => (clone $activeVariants)
                ->whereNotNull('size')
                ->distinct()
                ->orderByRaw("CASE size WHEN 'XS' THEN 1 WHEN 'S' THEN 2 WHEN 'M' THEN 3 WHEN 'L' THEN 4 WHEN 'XL' THEN 5 WHEN '2XL' THEN 6 WHEN '3XL' THEN 7 WHEN '4XL' THEN 8 WHEN '5XL' THEN 9 ELSE 10 END ASC, size ASC")
                ->pluck('size'),
            'price_range' => [
                'min' => (clone $activeVariants)->min('retail_price') ?? 0,
                'max' => (clone $activeVariants)->max('retail_price') ?? 100,
            ],
        ];
    }

    private function loadCategories()
    {
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

        return ProductCategory::filterEmptyCategories($categories);
    }
}
