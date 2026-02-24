<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintfulCatalogCache extends Model
{
    protected $table = 'printful_catalog_cache';

    protected $fillable = [
        'printful_product_id',
        'name',
        'description',
        'category',
        'image_url',
        'variant_count',
        'min_price',
        'max_price',
        'colors_json',
        'sizes_json',
        'print_areas_json',
        'cached_at',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'colors_json' => 'array',
        'sizes_json' => 'array',
        'print_areas_json' => 'array',
        'cached_at' => 'datetime',
    ];

    // Scopes

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%");
    }

    public function scopeStale($query, int $hours = 24)
    {
        return $query->where('cached_at', '<', now()->subHours($hours));
    }

    // Accessors

    public function getPriceRangeAttribute()
    {
        if ($this->min_price === $this->max_price) {
            return '$' . number_format($this->min_price, 2);
        }

        return '$' . number_format($this->min_price, 2) . ' - $' . number_format($this->max_price, 2);
    }
}
