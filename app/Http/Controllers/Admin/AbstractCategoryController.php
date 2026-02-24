<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class AbstractCategoryController extends Controller
{
    /**
     * Get the model class name (e.g., ProductCategory::class)
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the item type name (e.g., 'product', 'service')
     */
    abstract protected function getItemType(): string;

    /**
     * Get the relationship name for items (e.g., 'products', 'services')
     */
    abstract protected function getItemsRelationship(): string;

    /**
     * Get the table name for unique slug validation (e.g., 'product_categories')
     */
    abstract protected function getTableName(): string;

    /**
     * Get the storage path for images (e.g., 'categories/products')
     */
    abstract protected function getImageStoragePath(): string;

    /**
     * Get the navigation cache key (e.g., 'navigation.product_categories')
     */
    abstract protected function getCacheKey(): string;

    /**
     * Get the view path prefix (e.g., 'admin.products.categories')
     */
    abstract protected function getViewPath(): string;

    /**
     * Get the route name prefix (e.g., 'admin.products.categories')
     */
    abstract protected function getRouteName(): string;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modelClass = $this->getModelClass();
        $itemsRelationship = $this->getItemsRelationship();

        // Load top-level categories with recursive children
        $topLevelCategories = $modelClass::topLevel()
            ->with(["childrenRecursive.{$itemsRelationship}"])
            ->withCount($itemsRelationship)
            ->ordered()
            ->get();

        // Flatten hierarchy for table display with depth information
        $flattenedCategories = $this->flattenHierarchy($topLevelCategories);

        // Paginate the flattened collection
        $currentPage = request()->get('page', 1);
        $categories = new LengthAwarePaginator(
            $flattenedCategories->forPage($currentPage, 20),
            $flattenedCategories->count(),
            20,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view("{$this->getViewPath()}.index", compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $modelClass = $this->getModelClass();
        $categories = $modelClass::ordered()->get();
        $categoryOptions = $this->buildCategoryOptions($categories);

        return view("{$this->getViewPath()}.create", compact('categoryOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|unique:{$this->getTableName()},slug",
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'parent_id' => "nullable|exists:{$this->getTableName()},id",
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store($this->getImageStoragePath(), 'public');
            // Remove UploadedFile object from validated array before assigning processed path
            unset($validated['image']);
            $validated['image'] = $path;
        }

        $modelClass = $this->getModelClass();
        $modelClass::create($validated);

        // Clear navigation cache
        Cache::forget($this->getCacheKey());

        $itemType = ucfirst($this->getItemType());
        return redirect()->route("{$this->getRouteName()}.index")
            ->with('success', "{$itemType} category created successfully.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($category)
    {
        $modelClass = $this->getModelClass();

        // Exclude self and descendants from parent options to prevent circular references
        $excludeIds = collect([$category->id])->merge($category->getDescendantIds());
        $categories = $modelClass::whereNotIn('id', $excludeIds)->ordered()->get();
        $categoryOptions = $this->buildCategoryOptions($categories);

        return view("{$this->getViewPath()}.edit", compact('category', 'categoryOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "nullable|string|max:255|unique:{$this->getTableName()},slug,{$category->id}",
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'parent_id' => "nullable|exists:{$this->getTableName()},id",
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store($this->getImageStoragePath(), 'public');
            // Remove UploadedFile object from validated array before assigning processed path
            unset($validated['image']);
            $validated['image'] = $path;
        }

        $category->update($validated);

        // Clear navigation cache
        Cache::forget($this->getCacheKey());

        $itemType = ucfirst($this->getItemType());
        return redirect()->route("{$this->getRouteName()}.index")
            ->with('success', "{$itemType} category updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($category)
    {
        $itemsRelationship = $this->getItemsRelationship();

        // Check if category has items
        if ($category->{$itemsRelationship}()->count() > 0) {
            $itemType = $this->getItemType();
            return redirect()->back()
                ->with('error', "Cannot delete category with existing {$itemType}s. Please reassign or delete {$itemType}s first.");
        }

        // Check if category has children (CASCADE will delete them, but warn user)
        if ($category->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with subcategories. Please delete or move subcategories first.');
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        // Clear navigation cache
        Cache::forget($this->getCacheKey());

        $itemType = ucfirst($this->getItemType());
        return redirect()->route("{$this->getRouteName()}.index")
            ->with('success', "{$itemType} category deleted successfully.");
    }

    /**
     * Flatten hierarchical categories for table display
     */
    protected function flattenHierarchy($categories, $depth = 0)
    {
        $result = collect();

        foreach ($categories as $category) {
            $category->depth = $depth;
            $result->push($category);

            if ($category->childrenRecursive && $category->childrenRecursive->isNotEmpty()) {
                $result = $result->merge(
                    $this->flattenHierarchy($category->childrenRecursive, $depth + 1)
                );
            }
        }

        return $result;
    }

    /**
     * Build indented category options for dropdown
     */
    protected function buildCategoryOptions($categories, $parentId = null, $depth = 0)
    {
        $options = collect();

        foreach ($categories->where('parent_id', $parentId) as $category) {
            $options->push([
                'id' => $category->id,
                'name' => str_repeat('— ', $depth) . $category->name,
                'depth' => $depth
            ]);

            // Recursively add children
            $children = $categories->where('parent_id', $category->id);
            if ($children->isNotEmpty()) {
                $options = $options->merge(
                    $this->buildCategoryOptions($categories, $category->id, $depth + 1)
                );
            }
        }

        return $options;
    }
}
