<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'printful_variant_id',
        'printful_sync_variant_id',
        'color_name',
        'color_hex',
        'size',
        'sku',
        'printful_cost',
        'retail_price',
        'is_active',
        'stock_status',
        'sort_order',
    ];

    protected $casts = [
        'printful_cost' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function mockups()
    {
        return $this->hasMany(ProductMockup::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors

    public function getProfitAttribute()
    {
        return $this->retail_price - $this->printful_cost;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->retail_price <= 0) {
            return 0;
        }

        return round(($this->profit / $this->retail_price) * 100, 1);
    }

    public function getIsInStockAttribute()
    {
        return $this->stock_status === 'in_stock';
    }

    public function getDisplayNameAttribute()
    {
        $parts = array_filter([$this->color_name, $this->size]);
        return implode(' / ', $parts);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    public function scopeForColor($query, string $color)
    {
        return $query->where('color_name', $color);
    }

    public function scopeForSize($query, string $size)
    {
        return $query->where('size', $size);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('color_name')->orderBy('size');
    }
}
