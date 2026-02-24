<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HtmlPurifierService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Abstract base controller for catalog items (Products and Services)
 *
 * Eliminates 700+ lines of duplication by providing shared functionality
 * for listing, creating, updating, and exporting catalog items.
 *
 * Child classes must implement abstract methods to customize behavior.
 */
abstract class CatalogItemController extends Controller
{
    protected HtmlPurifierService $purifier;

    public function __construct(HtmlPurifierService $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * Display a listing of items with filtering
     */
    public function index()
    {
        $query = $this->getModelClass()::query();

        // Apply category filter (includes descendants)
        if (request('category')) {
            $category = $this->getCategoryClass()::where('slug', request('category'))->first();
            if ($category) {
                // Include category + all descendants in filter
                $categoryIds = collect([$category->id])->merge($category->getDescendantIds());
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Apply status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Apply custom filters (stock status, etc.)
        $this->applyCustomFilters($query);

        // Apply featured filter
        if (request('featured') !== null && request('featured') !== '') {
            $query->where('featured', request('featured') === '1');
        }

        // Apply search filter
        if (request('search')) {
            $this->applySearchFilter($query, request('search'));
        }

        $items = $query->with(['categories', 'mockups'])->latest()->paginate(20);

        // Get all categories with hierarchical structure for filter dropdown
        $allCategories = $this->getCategoryClass()::active()->ordered()->get();
        $categories = $this->buildCategoryFilterOptions($allCategories);

        // Calculate stats for dashboard cards
        $stats = $this->calculateStats();

        return view($this->getViewPath('index'), [
            $this->getItemsVariableName() => $items,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new item
     */
    public function create()
    {
        // Load all categories with recursive children for tree component
        $allCategories = $this->getCategoryClass()::with('childrenRecursive')->ordered()->get();
        return view($this->getViewPath('create'), compact('allCategories'));
    }

    /**
     * Store a newly created item in storage
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate($this->getValidationRules());

        // Ensure primary category is in selected categories
        if (!in_array($validated['primary_category_id'], $validated['category_ids'])) {
            return back()->withErrors(['primary_category_id' => 'Primary category must be one of the selected categories.'])
                ->withInput();
        }

        // Sanitize HTML content to prevent XSS
        $this->sanitizeHtmlFields($validated);

        // Convert tags string to array
        if (isset($validated['tags']) && is_string($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $validated['tags'] = array_filter($tags); // Remove empty values
        }

        // Handle checkboxes (if unchecked, they won't be in request)
        $validated['featured'] = $request->has('featured');
        $this->handleCustomCheckboxes($request, $validated);

        // Set category_id for backward compatibility (primary category)
        $validated['category_id'] = $validated['primary_category_id'];

        // Apply custom defaults
        $this->applyCustomDefaults($validated);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store($this->getStoragePath(), 'public');
                $imagePaths[] = $path;
            }
        }

        // Store category data for syncing
        $categoryIds = $validated['category_ids'];
        $primaryCategoryId = $validated['primary_category_id'];

        // Remove category fields from validated data (will be synced via pivot)
        unset($validated['category_ids'], $validated['primary_category_id'], $validated['images']);

        // Assign images to validated data
        $validated['images'] = $imagePaths;

        // Create the item
        $item = $this->getModelClass()::create($validated);

        // Sync categories with pivot data
        $pivotData = [];
        foreach ($categoryIds as $index => $categoryId) {
            $pivotData[$categoryId] = [
                'is_primary' => ($categoryId == $primaryCategoryId),
                'display_order' => $index + 1,
            ];
        }
        $item->categories()->sync($pivotData);

        // Clear navigation cache
        $this->clearNavigationCache();

        return redirect()->route($this->getRouteName('index'))
            ->with('success', ucfirst($this->getItemTypeSingular()) . ' created successfully');
    }

    /**
     * Display the specified item
     */
    public function show($id)
    {
        $item = $this->getModelClass()::with('categories')->find($id);

        if (!$item) {
            return redirect()->route($this->getRouteName('index'))
                ->with('error', ucfirst($this->getItemTypeSingular()) . ' not found.');
        }

        return view($this->getViewPath('show'), [
            $this->getItemVariableName() => $item,
        ]);
    }

    /**
     * Show the form for editing the specified item
     */
    public function edit($id)
    {
        $item = $this->getModelClass()::with('categories')->find($id);

        if (!$item) {
            return redirect()->route($this->getRouteName('index'))
                ->with('error', ucfirst($this->getItemTypeSingular()) . ' not found.');
        }

        $allCategories = $this->getCategoryClass()::with('childrenRecursive')->ordered()->get();

        return view($this->getViewPath('edit'), [
            $this->getItemVariableName() => $item,
            'allCategories' => $allCategories,
        ]);
    }

    /**
     * Update the specified item in storage
     */
    public function update(Request $request, $id)
    {
        $item = $this->getModelClass()::find($id);

        if (!$item) {
            return redirect()->route($this->getRouteName('index'))
                ->with('error', ucfirst($this->getItemTypeSingular()) . ' not found.');
        }

        // Validation rules (allow current item for unique checks)
        $rules = $this->getValidationRules($id);
        $validated = $request->validate($rules);

        // Ensure primary category is in selected categories
        if (!in_array($validated['primary_category_id'], $validated['category_ids'])) {
            return back()->withErrors(['primary_category_id' => 'Primary category must be one of the selected categories.'])
                ->withInput();
        }

        // Sanitize HTML content
        $this->sanitizeHtmlFields($validated);

        // Convert tags string to array
        if (isset($validated['tags']) && is_string($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $validated['tags'] = array_filter($tags);
        }

        // Handle checkboxes
        $validated['featured'] = $request->has('featured');
        $this->handleCustomCheckboxes($request, $validated);

        // Set category_id for backward compatibility
        $validated['category_id'] = $validated['primary_category_id'];

        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($item->images && is_array($item->images)) {
                foreach ($item->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store($this->getStoragePath(), 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        // Store category data for syncing
        $categoryIds = $validated['category_ids'];
        $primaryCategoryId = $validated['primary_category_id'];

        // Remove category fields from validated data
        unset($validated['category_ids'], $validated['primary_category_id']);

        // Update the item
        $item->update($validated);

        // Sync categories with pivot data
        $pivotData = [];
        foreach ($categoryIds as $index => $categoryId) {
            $pivotData[$categoryId] = [
                'is_primary' => ($categoryId == $primaryCategoryId),
                'display_order' => $index + 1,
            ];
        }
        $item->categories()->sync($pivotData);

        // Clear navigation cache
        $this->clearNavigationCache();

        return redirect()->route($this->getRouteName('edit'), $item)
            ->with('success', ucfirst($this->getItemTypeSingular()) . ' updated successfully.');
    }

    /**
     * Remove the specified item from storage (soft delete)
     */
    public function destroy($id)
    {
        $item = $this->getModelClass()::find($id);

        if (!$item) {
            return redirect()->route($this->getRouteName('index'))
                ->with('error', ucfirst($this->getItemTypeSingular()) . ' not found.');
        }
        $item->delete();

        // Clear navigation cache
        $this->clearNavigationCache();

        return redirect()->route($this->getRouteName('index'))
            ->with('success', ucfirst($this->getItemTypeSingular()) . ' deleted successfully');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,publish,unpublish',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:' . $this->getTableName() . ',id',
        ]);

        $count = count($validated['ids']);
        $action = $validated['action'];

        switch ($action) {
            case 'delete':
                $this->getModelClass()::whereIn('id', $validated['ids'])->delete();
                $message = "{$count} " . $this->getItemTypeSingular() . "(s) deleted successfully.";
                break;

            case 'publish':
                $this->getModelClass()::whereIn('id', $validated['ids'])->update(['status' => 'active']);
                $message = "{$count} " . $this->getItemTypeSingular() . "(s) published successfully.";
                break;

            case 'unpublish':
                $this->getModelClass()::whereIn('id', $validated['ids'])->update(['status' => 'inactive']);
                $message = "{$count} " . $this->getItemTypeSingular() . "(s) unpublished successfully.";
                break;

            default:
                return back()->with('error', 'Invalid action selected.');
        }

        // Clear navigation cache
        $this->clearNavigationCache();

        return back()->with('success', $message);
    }

    /**
     * Export items to CSV
     * Uses chunking to handle large datasets without memory issues
     */
    public function export()
    {
        // Generate CSV filename
        $filename = $this->getItemTypePlural() . '_export_' . now()->format('Y-m-d_His') . '.csv';
        $csvHeaders = $this->getCsvHeaders();

        // Store filters and class references for use in callback
        $modelClass = $this->getModelClass();
        $categoryClass = $this->getCategoryClass();
        $queryFilters = [
            'category' => request('category'),
            'status' => request('status'),
            'featured' => request('featured'),
            'search' => request('search'),
        ];

        // Reference $this for getCsvRow method
        $controller = $this;

        $callback = function() use ($modelClass, $categoryClass, $queryFilters, $csvHeaders, $controller) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeaders);

            // Rebuild query inside callback for streaming
            $query = $modelClass::with('categories');

            if (!empty($queryFilters['category'])) {
                $category = $categoryClass::where('slug', $queryFilters['category'])->first();
                if ($category) {
                    $categoryIds = collect([$category->id])->merge($category->getDescendantIds());
                    $query->whereIn('category_id', $categoryIds);
                }
            }

            if (!empty($queryFilters['status'])) {
                $query->where('status', $queryFilters['status']);
            }

            if ($queryFilters['featured'] !== null && $queryFilters['featured'] !== '') {
                $query->where('featured', $queryFilters['featured'] === '1');
            }

            if (!empty($queryFilters['search'])) {
                $controller->applySearchFilter($query, $queryFilters['search']);
            }

            $controller->applyCustomFilters($query);

            // Use chunk to process in batches of 500 (memory efficient for large exports)
            $query->chunk(500, function ($items) use ($file, $controller) {
                foreach ($items as $item) {
                    fputcsv($file, $controller->getCsvRow($item));
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build hierarchical category filter options
     */
    protected function buildCategoryFilterOptions($categories, $parentId = null, $depth = 0)
    {
        $options = collect();

        foreach ($categories->where('parent_id', $parentId) as $category) {
            $options->put(
                $category->slug,
                str_repeat('— ', $depth) . $category->name
            );

            // Recursively add children
            $children = $categories->where('parent_id', $category->id);
            if ($children->isNotEmpty()) {
                $options = $options->merge(
                    $this->buildCategoryFilterOptions($categories, $category->id, $depth + 1)
                );
            }
        }

        return $options;
    }

    /**
     * Sanitize HTML fields to prevent XSS
     */
    protected function sanitizeHtmlFields(array &$validated): void
    {
        $htmlFields = ['description', 'long_description', 'meta_description'];

        foreach ($htmlFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = $this->purifier->clean($validated[$field]);
            }
        }
    }

    /**
     * Clear navigation cache after item changes
     */
    protected function clearNavigationCache(): void
    {
        Cache::forget('navigation.' . $this->getItemTypePlural() . '_categories');
    }

    // ============================================================
    // ABSTRACT METHODS - Must be implemented by child classes
    // ============================================================

    /**
     * Get the model class name (e.g., Product::class, Service::class)
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the category model class name
     */
    abstract protected function getCategoryClass(): string;

    /**
     * Get the database table name
     */
    abstract protected function getTableName(): string;

    /**
     * Get the item type in singular form (e.g., 'product', 'service')
     */
    abstract protected function getItemTypeSingular(): string;

    /**
     * Get the item type in plural form (e.g., 'products', 'services')
     */
    abstract protected function getItemTypePlural(): string;

    /**
     * Get the variable name for a single item (e.g., 'product', 'service')
     */
    abstract protected function getItemVariableName(): string;

    /**
     * Get the variable name for multiple items (e.g., 'products', 'services')
     */
    abstract protected function getItemsVariableName(): string;

    /**
     * Get validation rules for store/update
     *
     * @param int|null $id Current item ID for update (for unique validation)
     */
    abstract protected function getValidationRules(?int $id = null): array;

    /**
     * Apply custom filters specific to the item type
     * (e.g., stock_status for products)
     */
    abstract protected function applyCustomFilters($query): void;

    /**
     * Apply search filter to query
     */
    abstract protected function applySearchFilter($query, string $search): void;

    /**
     * Calculate statistics for dashboard cards
     */
    abstract protected function calculateStats(): array;

    /**
     * Handle custom checkboxes specific to the item type
     */
    abstract protected function handleCustomCheckboxes(Request $request, array &$validated): void;

    /**
     * Apply custom default values
     */
    abstract protected function applyCustomDefaults(array &$validated): void;

    /**
     * Get CSV headers for export
     */
    abstract protected function getCsvHeaders(): array;

    /**
     * Get CSV row data for an item
     */
    abstract protected function getCsvRow(Model $item): array;

    /**
     * Get storage path for uploaded images
     */
    abstract protected function getStoragePath(): string;

    /**
     * Get view path for a given view name
     */
    protected function getViewPath(string $view): string
    {
        return 'admin.' . $this->getItemTypePlural() . '.' . $view;
    }

    /**
     * Get route name for a given action
     */
    protected function getRouteName(string $action): string
    {
        return 'admin.' . $this->getItemTypePlural() . '.' . $action;
    }
}
