<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintfulCatalogCache;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDesign;
use App\Models\ProductVariant;
use App\Services\PrintfulService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrintfulCatalogController extends Controller
{
    public function __construct(
        private PrintfulService $printful,
    ) {}

    /**
     * Browse the cached Printful product catalog.
     */
    public function index(Request $request)
    {
        $query = PrintfulCatalogCache::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        $products = $query->orderBy('name')->paginate(24);

        // Get distinct categories for filter dropdown
        $categories = PrintfulCatalogCache::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Check which catalog products are already in our store
        $existingPrintfulIds = Product::whereNotNull('printful_product_id')
            ->pluck('printful_product_id')
            ->toArray();

        $lastSyncTime = PrintfulCatalogCache::max('cached_at');

        return view('admin.printful.catalog', compact('products', 'categories', 'existingPrintfulIds', 'lastSyncTime'));
    }

    /**
     * Show the setup form for adding a Printful catalog product to our store.
     */
    public function setup(int $printfulProductId)
    {
        // Check if already added
        $existing = Product::where('printful_product_id', $printfulProductId)->first();
        if ($existing) {
            return redirect()->route('admin.products.edit', $existing)
                ->with('info', 'This Printful product is already in your store.');
        }

        // Fetch full product details from Printful API
        $catalogProduct = $this->printful->getCatalogProduct($printfulProductId);

        if (empty($catalogProduct)) {
            return redirect()->route('admin.printful.catalog')
                ->with('error', 'Could not fetch product details from Printful.');
        }

        $product = $catalogProduct['product'] ?? [];
        $variants = $catalogProduct['variants'] ?? [];

        // Group variants by color for easier selection
        $variantsByColor = collect($variants)->groupBy('color');

        // Get available sizes
        $sizes = collect($variants)->pluck('size')->unique()->filter()->values();

        // Get product categories for assignment
        $allCategories = ProductCategory::with('childrenRecursive')->ordered()->get();

        // Get print area info
        $printAreas = $product['files'] ?? [];

        return view('admin.printful.setup', compact(
            'product',
            'variants',
            'variantsByColor',
            'sizes',
            'allCategories',
            'printAreas',
            'printfulProductId',
        ));
    }

    /**
     * Store a new product from Printful catalog setup.
     */
    public function store(Request $request)
    {
        // Filter out empty/unselected entries from sparse variant array
        $variants = collect($request->input('variants', []))
            ->filter(fn ($v) => !empty($v['printful_variant_id']))
            ->values()
            ->toArray();

        $request->merge(['variants' => $variants]);

        $validated = $request->validate([
            'printful_product_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string|max:5000',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:product_categories,id',
            'primary_category_id' => 'required|exists:product_categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.printful_variant_id' => 'required|integer',
            'variants.*.color_name' => 'nullable|string|max:100',
            'variants.*.color_hex' => 'nullable|string|max:7',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.printful_cost' => 'required|numeric|min:0',
            'variants.*.retail_price' => 'required|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
            'featured' => 'nullable|boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure primary category is in selected categories
        if (!in_array($validated['primary_category_id'], $validated['category_ids'])) {
            return back()->withErrors(['primary_category_id' => 'Primary category must be one of the selected categories.'])
                ->withInput();
        }

        // Calculate base retail price from variant prices
        $variantPrices = collect($validated['variants'])->pluck('retail_price');
        $basePrice = $variantPrices->min();
        $baseCost = collect($validated['variants'])->pluck('printful_cost')->min();

        // Create the product
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? '',
            'price' => $basePrice,
            'base_cost' => $baseCost,
            'profit_margin' => $validated['profit_margin'],
            'printful_product_id' => $validated['printful_product_id'],
            'fulfillment_type' => 'printful',
            'status' => $validated['status'],
            'featured' => $request->boolean('featured'),
            'category_id' => $validated['primary_category_id'],
            'sku' => 'PF-' . $validated['printful_product_id'],
            'stock_quantity' => 999, // POD = always in stock
        ]);

        // Sync categories
        $pivotData = [];
        foreach ($validated['category_ids'] as $index => $categoryId) {
            $pivotData[$categoryId] = [
                'is_primary' => ($categoryId == $validated['primary_category_id']),
                'display_order' => $index + 1,
            ];
        }
        $product->categories()->sync($pivotData);

        // Create variants
        foreach ($validated['variants'] as $index => $variantData) {
            ProductVariant::create([
                'product_id' => $product->id,
                'printful_variant_id' => $variantData['printful_variant_id'],
                'printful_product_id' => $validated['printful_product_id'],
                'color_name' => $variantData['color_name'] ?? null,
                'color_hex' => $variantData['color_hex'] ?? null,
                'size' => $variantData['size'] ?? null,
                'sku' => 'PF-' . $variantData['printful_variant_id'],
                'printful_cost' => $variantData['printful_cost'],
                'retail_price' => $variantData['retail_price'],
                'is_active' => true,
                'stock_status' => 'in_stock',
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product created from Printful catalog. You can now upload designs and generate mockups.');
    }

    /**
     * Refresh the local catalog cache from Printful API.
     */
    public function syncCatalog()
    {
        try {
            $count = $this->printful->syncCatalogToCache();
            return redirect()->route('admin.printful.catalog')
                ->with('success', "Catalog synced successfully. {$count} products updated.");
        } catch (\Exception $e) {
            return redirect()->route('admin.printful.catalog')
                ->with('error', 'Catalog sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload a design file for a product.
     */
    public function uploadDesign(Request $request, Product $product)
    {
        $validated = $request->validate([
            'design_file' => 'required|file|mimes:png,jpg,jpeg,svg,pdf|max:20480',
            'placement' => 'required|string|max:50',
        ]);

        $file = $request->file('design_file');
        $path = $file->store('designs/' . $product->id, 'public');

        // Upload to Printful
        try {
            $printfulFile = $this->printful->uploadFile(storage_path('app/public/' . $path));
            $printfulFileId = $printfulFile['id'] ?? null;
        } catch (\Exception $e) {
            $printfulFileId = null;
        }

        ProductDesign::create([
            'product_id' => $product->id,
            'placement' => $validated['placement'],
            'file_url' => $path,
            'printful_file_id' => $printfulFileId,
            'width' => null,
            'height' => null,
        ]);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Design uploaded successfully.');
    }

    /**
     * Generate mockups for a product via Printful API.
     */
    public function generateMockups(Request $request, Product $product)
    {
        if (!$product->printful_product_id) {
            return back()->with('error', 'Product is not linked to Printful catalog.');
        }

        $designs = $product->designs;
        if ($designs->isEmpty()) {
            return back()->with('error', 'Upload at least one design file before generating mockups.');
        }

        // Build files array for Printful mockup API
        $files = [];
        foreach ($designs as $design) {
            if ($design->printful_file_id) {
                // Resolve the actual URL from Printful's file API
                try {
                    $fileInfo = $this->printful->getFile($design->printful_file_id);
                    $fileUrl = $fileInfo['preview_url'] ?? $fileInfo['url'] ?? null;
                } catch (\Exception $e) {
                    $fileUrl = null;
                }

                // Fall back to local storage URL if Printful lookup fails
                if (!$fileUrl && $design->file_url) {
                    $fileUrl = asset('storage/' . $design->file_url);
                }

                if ($fileUrl) {
                    $files[] = [
                        'placement' => $design->placement,
                        'image_url' => $fileUrl,
                    ];
                }
            } elseif ($design->file_url) {
                $files[] = [
                    'placement' => $design->placement,
                    'image_url' => asset('storage/' . $design->file_url),
                ];
            }
        }

        if (empty($files)) {
            return back()->with('error', 'No valid design files found for mockup generation.');
        }

        $variantIds = $product->activeVariants()
            ->whereNotNull('printful_variant_id')
            ->pluck('printful_variant_id')
            ->toArray();

        try {
            $mockups = $this->printful->generateAndWait(
                $product->printful_product_id,
                $files,
                $variantIds
            );

            // Store mockup URLs
            foreach ($mockups as $index => $mockup) {
                $product->mockups()->create([
                    'mockup_url' => $mockup['mockup_url'] ?? $mockup['url'] ?? '',
                    'placement' => $mockup['placement'] ?? 'front',
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }

            return back()->with('success', count($mockups) . ' mockups generated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Mockup generation failed: ' . $e->getMessage());
        }
    }
}
