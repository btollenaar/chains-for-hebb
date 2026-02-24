<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'customer_id',
        'session_id',
        'item_type',
        'item_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public static function isWishlisted($itemType, $itemId, $ownerId, $isCustomer = true): bool
    {
        $query = self::where('item_type', $itemType)->where('item_id', $itemId);

        if ($isCustomer) {
            $query->where('customer_id', $ownerId);
        } else {
            $query->where('session_id', $ownerId);
        }

        return $query->exists();
    }

    public static function toggle($itemType, $itemId, $ownerId, $isCustomer = true): bool
    {
        $query = self::where('item_type', $itemType)->where('item_id', $itemId);

        if ($isCustomer) {
            $query->where('customer_id', $ownerId);
        } else {
            $query->where('session_id', $ownerId);
        }

        $existing = $query->first();

        if ($existing) {
            $existing->delete();
            return false; // removed
        }

        $data = [
            'item_type' => $itemType,
            'item_id' => $itemId,
        ];

        if ($isCustomer) {
            $data['customer_id'] = $ownerId;
        } else {
            $data['session_id'] = $ownerId;
        }

        self::create($data);
        return true; // added
    }
}
