<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'product_variant_id',
        'printful_variant_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'subtotal',
        'tax_amount',
        'total',
        'attributes',
        'variant_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'attributes' => 'array',
        'variant_snapshot' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Accessors
    public function getTotalAttribute($value)
    {
        return $value ?? ($this->subtotal + $this->tax_amount);
    }

    // Helper methods
    public function snapshotItemDetails()
    {
        if ($this->item) {
            $this->name = $this->item->name;
            $this->description = $this->item->description ?? '';
            $this->unit_price = $this->item->current_price ?? $this->item->base_price ?? $this->item->price;
            $this->subtotal = $this->unit_price * $this->quantity;

            $taxRate = config('business.payments.tax_rate', 0.0);
            $this->tax_amount = round($this->subtotal * $taxRate, 2);
            $this->total = $this->subtotal + $this->tax_amount;
        }
    }
}
