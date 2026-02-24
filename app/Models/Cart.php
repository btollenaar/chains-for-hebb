<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = [
        'customer_id',
        'session_id',
        'item_type',
        'item_id',
        'product_variant_id',
        'quantity',
        'attributes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'attributes' => 'array',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Scopes
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    // Helper methods
    public static function getSubtotal($customerIdOrSessionId, $isCustomer = true)
    {
        $query = self::with(['item', 'variant']);

        if ($isCustomer) {
            $query->where('customer_id', $customerIdOrSessionId);
        } else {
            $query->where('session_id', $customerIdOrSessionId);
        }

        $items = $query->get();
        $subtotal = 0;

        foreach ($items as $cartItem) {
            if ($cartItem->variant) {
                $price = (float) $cartItem->variant->retail_price;
            } elseif ($cartItem->item) {
                $price = $cartItem->item->current_price ?? $cartItem->item->base_price ?? $cartItem->item->price ?? 0;
            } else {
                $price = 0;
            }
            $subtotal += $price * $cartItem->quantity;
        }

        return $subtotal;
    }

    public static function clearCart($customerIdOrSessionId, $isCustomer = true)
    {
        $query = self::query();

        if ($isCustomer) {
            $query->where('customer_id', $customerIdOrSessionId);
        } else {
            $query->where('session_id', $customerIdOrSessionId);
        }

        $query->delete();
    }

    public static function addItem($item, $quantity = 1, $attributes = [], $customerIdOrSessionId = null, $isCustomer = true)
    {
        $data = [
            'item_type' => get_class($item),
            'item_id' => $item->id,
            'quantity' => $quantity,
            'attributes' => $attributes,
        ];

        if ($isCustomer) {
            $data['customer_id'] = $customerIdOrSessionId;
        } else {
            $data['session_id'] = $customerIdOrSessionId;
        }

        return self::create($data);
    }

    public static function findForOwner($cartId, $ownerId, $isCustomer = true)
    {
        $query = self::where('id', $cartId);

        if ($isCustomer) {
            $query->where('customer_id', $ownerId);
        } else {
            $query->where('session_id', $ownerId);
        }

        return $query->first();
    }

    public static function updateQuantity($cartId, $quantity, $ownerId, $isCustomer = true)
    {
        $cartItem = self::findForOwner($cartId, $ownerId, $isCustomer);
        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        }
        return $cartItem;
    }
}
