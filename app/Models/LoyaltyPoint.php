<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'customer_id',
        'points',
        'type',
        'source',
        'source_id',
        'description',
        'balance_after',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeemed');
    }

    public function scopeForCustomer($query, int $id)
    {
        return $query->where('customer_id', $id);
    }
}
