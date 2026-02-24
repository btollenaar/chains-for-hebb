<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'long_description',
        'sku',
        'barcode',
        'price',
        'sale_price',
        'sale_start',
        'sale_end',
        'cost',
        'base_cost',
        'profit_margin',
        'stock_quantity',
        'low_stock_threshold',
        'category',
        'category_id',
        'subcategory',
        'tags',
        'attributes',
        'featured',
        'status',
        'fulfillment_type',
        'fulfillment_provider',
        'fulfillment_sku',
        'printful_product_id',
        'printful_sync_product_id',
        'printful_synced_at',
        'meta_title',
        'meta_description',
        'images',
        'wholesale_cost',
        'weight_oz',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_start' => 'date',
        'sale_end' => 'date',
        'cost' => 'decimal:2',
        'base_cost' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'wholesale_cost' => 'decimal:2',
        'weight_oz' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'tags' => 'array',
        'attributes' => 'array',
        'images' => 'array',
        'featured' => 'boolean',
        'printful_synced_at' => 'datetime',
    ];

    // Relationships
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Many-to-Many: Product belongs to multiple categories
     */
    public function categories()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_product_category',
            'product_id',
            'product_category_id'
        )
        ->withPivot('is_primary', 'display_order')
        ->withTimestamps()
        ->orderByPivot('display_order');
    }

    /**
     * Get the primary category from pivot relationship
     */
    public function primaryCategory()
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_product_category',
            'product_id',
            'product_category_id'
        )
        ->wherePivot('is_primary', true)
        ->withPivot('is_primary');
    }

    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'item');
    }

    public function cartItems()
    {
        return $this->morphMany(Cart::class, 'item');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function stockNotifications()
    {
        return $this->hasMany(StockNotification::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function designs()
    {
        return $this->hasMany(ProductDesign::class);
    }

    public function mockups()
    {
        return $this->hasMany(ProductMockup::class)->orderBy('sort_order');
    }

    public function primaryMockup()
    {
        return $this->hasOne(ProductMockup::class)->where('is_primary', true);
    }

    // Accessors
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute()
    {
        return !is_null($this->sale_price) && $this->sale_price < $this->price;
    }

    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    public function getImageUrlsAttribute()
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->images);
    }

    public function getFirstImageUrlAttribute()
    {
        $urls = $this->image_urls;
        return !empty($urls) ? $urls[0] : asset('images/placeholder.jpg');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeCategory($query, $category)
    {
        // Handle numeric ID
        if (is_numeric($category)) {
            $categoryModel = ProductCategory::find($category);
            if (!$categoryModel) {
                return $query->whereRaw('1 = 0'); // No results
            }
        } else {
            // Look up category by slug
            $categoryModel = ProductCategory::where('slug', $category)->first();
            if (!$categoryModel) {
                // Fallback: Try legacy 'category' field for backwards compatibility
                return $query->where('category', $category);
            }
        }

        // Get category + all descendants (hierarchical support)
        $categoryIds = collect([$categoryModel->id])
            ->merge($categoryModel->getDescendantIds());

        // Filter by BOTH category_id (legacy) AND pivot table (current)
        return $query->where(function($q) use ($categoryIds) {
            $q->whereIn('category_id', $categoryIds->toArray())
              ->orWhereHas('categories', fn($query) =>
                  $query->whereIn('product_categories.id', $categoryIds->toArray()));
        });
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price');
    }

    // Printful helpers

    public function getIsPrintfulAttribute()
    {
        return !is_null($this->printful_product_id);
    }

    public function getAvailableColorsAttribute()
    {
        return $this->activeVariants()
            ->select('color_name', 'color_hex')
            ->distinct()
            ->whereNotNull('color_name')
            ->pluck('color_hex', 'color_name');
    }

    public function getAvailableSizesAttribute()
    {
        return $this->activeVariants()
            ->select('size')
            ->distinct()
            ->whereNotNull('size')
            ->pluck('size');
    }

    public function getPriceRangeAttribute()
    {
        $variants = $this->activeVariants;
        if ($variants->isEmpty()) {
            return null;
        }

        $min = $variants->min('retail_price');
        $max = $variants->max('retail_price');

        if ($min === $max) {
            return '$' . number_format($min, 2);
        }

        return '$' . number_format($min, 2) . ' - $' . number_format($max, 2);
    }

    public function findVariant(?string $color, ?string $size)
    {
        return $this->activeVariants()
            ->when($color, fn($q) => $q->where('color_name', $color))
            ->when($size, fn($q) => $q->where('size', $size))
            ->first();
    }

    // Helper methods
    public function decrementStock($quantity)
    {
        $this->decrement('stock_quantity', $quantity);
    }

    public function incrementStock($quantity)
    {
        $this->increment('stock_quantity', $quantity);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
