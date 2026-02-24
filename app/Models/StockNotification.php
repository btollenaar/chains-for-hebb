<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockNotification extends Model
{
    protected $fillable = ['customer_id', 'email', 'product_id', 'notified_at'];

    protected $casts = ['notified_at' => 'datetime'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePending($query)
    {
        return $query->whereNull('notified_at');
    }

    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }
}
