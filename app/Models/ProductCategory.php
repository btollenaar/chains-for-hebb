<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\HasHierarchicalCategories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory, HasHierarchicalCategories, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'display_order',
        'is_active',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    // ============================================================
    // RELATIONSHIPS (Product-specific)
    // ============================================================

    /**
     * One-to-Many: Category has many products (legacy)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Many-to-Many: Category has many products (via pivot)
     */
    public function allProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_product_category',
            'product_category_id',
            'product_id'
        )
        ->withPivot('is_primary', 'display_order')
        ->withTimestamps();
    }

    /**
     * Parent category relationship
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Child categories relationship
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->ordered();
    }

    // ============================================================
    // SCOPES (Product-specific)
    // ============================================================

    /**
     * Scope: Filter categories that have at least one active product
     * Only checks direct products (use filterEmptyCategories for recursive filtering)
     */
    public function scopeHasProducts($query)
    {
        return $query->whereHas('allProducts', function ($productQuery) {
            $productQuery->where('is_active', true);
        });
    }

    // ============================================================
    // STATIC METHODS
    // ============================================================

    /**
     * Recursively filter out categories with no products
     * Removes categories that have no products AND no children with products
     *
     * @param \Illuminate\Support\Collection $categories
     * @return \Illuminate\Support\Collection
     */
    public static function filterEmptyCategories($categories)
    {
        return $categories->filter(function ($category) {
            // First, recursively filter immediate children
            if ($category->childrenRecursive && $category->childrenRecursive->isNotEmpty()) {
                $filteredChildren = static::filterEmptyCategories($category->childrenRecursive);
                $category->setRelation('childrenRecursive', $filteredChildren);
            }

            // Check if category has products (check BOTH relationships)
            // allProducts = many-to-many pivot table
            // products = hasMany via category_id
            // Use eager-loaded counts if available (prevents N+1 queries), otherwise fallback to exists()
            $hasProductsViaPivot = isset($category->active_products_count)
                ? $category->active_products_count > 0
                : $category->allProducts()->where('status', 'active')->exists();

            $hasProductsViaCategoryId = isset($category->direct_products_count)
                ? $category->direct_products_count > 0
                : $category->products()->where('status', 'active')->exists();

            // Keep if has products through either relationship
            if ($hasProductsViaPivot || $hasProductsViaCategoryId) {
                return true;
            }

            // Keep if has any children left after filtering
            if ($category->childrenRecursive && $category->childrenRecursive->isNotEmpty()) {
                return true;
            }

            // No products and no children - filter out
            return false;
        });
    }

    // ============================================================
    // ACCESSORS (Product-specific)
    // ============================================================

    /**
     * Get count of direct products (legacy relationship)
     */
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get count of active products in this category AND all descendant categories
     * Uses the many-to-many pivot relationship
     */
    public function getActiveProductCountAttribute()
    {
        // Get this category ID + all descendant IDs
        $categoryIds = collect([$this->id])->merge($this->getDescendantIds());

        // Count distinct products assigned to ANY of these categories via pivot table
        return Product::active()
            ->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('product_categories.id', $categoryIds);
            })
            ->distinct()
            ->count();
    }
}
