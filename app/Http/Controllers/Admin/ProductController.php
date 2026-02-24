<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Product Controller - Extends CatalogItemController
 *
 * Reduced from 520 lines to ~150 lines by leveraging abstract base class
 * Eliminates ~370 lines of code duplication
 */
class ProductController extends CatalogItemController
{
    /**
     * Get the model class name
     */
    protected function getModelClass(): string
    {
        return Product::class;
    }

    /**
     * Get the category model class name
     */
    protected function getCategoryClass(): string
    {
        return ProductCategory::class;
    }

    /**
     * Get the database table name
     */
    protected function getTableName(): string
    {
        return 'products';
    }

    /**
     * Get the item type in singular form
     */
    protected function getItemTypeSingular(): string
    {
        return 'product';
    }

    /**
     * Get the item type in plural form
     */
    protected function getItemTypePlural(): string
    {
        return 'products';
    }

    /**
     * Get the variable name for a single item
     */
    protected function getItemVariableName(): string
    {
        return 'product';
    }

    /**
     * Get the variable name for multiple items
     */
    protected function getItemsVariableName(): string
    {
        return 'products';
    }

    /**
     * Override edit to load Printful relationships and category selection data.
     */
    public function edit($id)
    {
        $product = Product::with(['categories', 'variants', 'designs', 'mockups'])->find($id);

        if (!$product) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Product not found.');
        }

        $allCategories = ProductCategory::with('childrenRecursive')->ordered()->get();

        // Build category selection arrays for the tree-checkbox component
        $selectedCategoryIds = $product->categories->pluck('id')->toArray();
        $primaryCategory = $product->categories->firstWhere('pivot.is_primary', true);
        $primaryCategoryId = $primaryCategory?->id ?? ($selectedCategoryIds[0] ?? null);

        // Determine display image for header: primary mockup > first mockup > first product image
        $headerImageUrl = null;
        $primaryMockup = $product->mockups->firstWhere('is_primary', true);
        if ($primaryMockup) {
            $headerImageUrl = $primaryMockup->mockup_url;
        } elseif ($product->mockups->isNotEmpty()) {
            $headerImageUrl = $product->mockups->first()->mockup_url;
        } elseif ($product->images && is_array($product->images) && count($product->images) > 0) {
            $headerImageUrl = asset('storage/' . $product->images[0]);
        }

        return view('admin.products.edit', compact(
            'product',
            'allCategories',
            'selectedCategoryIds',
            'primaryCategoryId',
            'headerImageUrl',
        ));
    }

    /**
     * Get validation rules for store/update
     */
    protected function getValidationRules(?int $id = null): array
    {
        $uniqueName = $id ? "unique:products,name,{$id}" : 'unique:products,name';
        $uniqueSlug = $id ? "unique:products,slug,{$id}" : 'unique:products,slug';
        $uniqueSku = $id ? "unique:products,sku,{$id}" : 'unique:products,sku';
        $uniqueBarcode = $id ? "unique:products,barcode,{$id}" : 'unique:products,barcode';

        return [
            'name' => "required|string|max:255|{$uniqueName}",
            'slug' => "nullable|string|{$uniqueSlug}",
            'description' => 'nullable|string|max:1000',
            'long_description' => 'nullable|string|max:5000',
            'sku' => "required|string|{$uniqueSku}",
            'barcode' => "nullable|string|{$uniqueBarcode}",
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:product_categories,id',
            'primary_category_id' => 'required|exists:product_categories,id',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'featured' => 'boolean',
            'status' => 'required|in:active,inactive',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Apply custom filters specific to products (stock status)
     */
    protected function applyCustomFilters($query): void
    {
        if (request('stock_status')) {
            switch (request('stock_status')) {
                case 'in_stock':
                    $query->whereColumn('stock_quantity', '>', 'low_stock_threshold');
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '>', 0)
                          ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }
    }

    /**
     * Apply search filter to query
     */
    protected function applySearchFilter($query, string $search): void
    {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%")
              ->orWhere('barcode', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Calculate statistics for dashboard cards
     */
    protected function calculateStats(): array
    {
        return [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
        ];
    }

    /**
     * Handle custom checkboxes (none for products)
     */
    protected function handleCustomCheckboxes(Request $request, array &$validated): void
    {
        // Products don't have additional checkboxes beyond 'featured'
    }

    /**
     * Apply custom default values (none for products)
     */
    protected function applyCustomDefaults(array &$validated): void
    {
        // Products don't need custom defaults
    }

    /**
     * Get CSV headers for export
     */
    protected function getCsvHeaders(): array
    {
        return [
            'Name',
            'SKU',
            'Barcode',
            'Category',
            'Price',
            'Sale Price',
            'Cost',
            'Stock Quantity',
            'Low Stock Threshold',
            'Status',
            'Featured',
        ];
    }

    /**
     * Get CSV row data for a product
     */
    protected function getCsvRow(Model $item): array
    {
        /** @var Product $item */
        return [
            $item->name,
            $item->sku,
            $item->barcode ?? '',
            $item->category ?? '',
            number_format($item->price, 2),
            $item->sale_price ? number_format($item->sale_price, 2) : '',
            $item->cost ? number_format($item->cost, 2) : '',
            $item->stock_quantity,
            $item->low_stock_threshold ?? '',
            ucfirst($item->status),
            $item->featured ? 'Yes' : 'No',
        ];
    }

    /**
     * Get storage path for product images
     */
    protected function getStoragePath(): string
    {
        return 'products';
    }
}
