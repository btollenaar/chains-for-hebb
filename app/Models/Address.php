<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'label', 'type', 'is_default',
        'street', 'city', 'state', 'zip', 'country', 'phone',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeShipping($query)
    {
        return $query->whereIn('type', ['shipping', 'both']);
    }

    public function scopeBilling($query)
    {
        return $query->whereIn('type', ['billing', 'both']);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($address) {
            if ($address->is_default) {
                static::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id ?? 0)
                    ->update(['is_default' => false]);
            }
        });
    }
}
