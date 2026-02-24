<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

/**
 * HasHierarchicalCategories Trait
 *
 * Provides hierarchical category functionality for ProductCategory models.
 * Extracts common methods for parent-child category relationships.
 *
 * Models using this trait must implement:
 * - parent() relationship
 * - children() relationship
 */
trait HasHierarchicalCategories
{
    /**
     * Boot the trait and register model event listeners
     */
    protected static function bootHasHierarchicalCategories()
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::saving(function ($category) {
            if ($category->parent_id) {
                // Can't be own parent
                if ($category->parent_id === $category->id) {
                    throw new \Exception('A category cannot be its own parent.');
                }

                // Can't have descendant as parent
                if ($category->exists) {
                    $parent = static::find($category->parent_id);
                    if ($parent && $parent->isDescendantOf($category)) {
                        throw new \Exception('Cannot create circular reference.');
                    }
                }
            }
        });
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Recursive children relationship
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope: Only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Order by display_order then name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Scope: Only top-level categories (no parent)
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Only categories that have children
     */
    public function scopeHasChildren($query)
    {
        return $query->whereHas('children');
    }

    /**
     * Scope: Only leaf categories (no children)
     */
    public function scopeLeaf($query)
    {
        return $query->whereDoesntHave('children');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get image URL accessor
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // ============================================================
    // UTILITY METHODS
    // ============================================================

    /**
     * Get full category path (e.g., "Parent > Child > This Category")
     *
     * @param string $separator
     * @return string
     */
    public function getFullPath($separator = ' > ')
    {
        $path = $this->ancestors()->pluck('name')->toArray();
        $path[] = $this->name;
        return implode($separator, $path);
    }

    /**
     * Get all ancestor categories (parents, grandparents, etc.)
     *
     * @return \Illuminate\Support\Collection
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }
        return $ancestors;
    }

    /**
     * Get all descendant categories (children, grandchildren, etc.)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        return $descendants;
    }

    /**
     * Get IDs of all descendant categories
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDescendantIds()
    {
        return $this->getAllDescendants()->pluck('id');
    }

    /**
     * Get depth of category in hierarchy (0 = top level)
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->ancestors()->count();
    }

    /**
     * Check if this category is a descendant of another category
     *
     * @param self $category
     * @return bool
     */
    public function isDescendantOf($category)
    {
        $parent = $this->parent;
        while ($parent) {
            if ($parent->id === $category->id) {
                return true;
            }
            $parent = $parent->parent;
        }
        return false;
    }
}
