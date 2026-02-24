<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'max_uses',
        'used_count',
        'max_uses_per_customer',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'max_uses_per_customer' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coupon) {
            $coupon->code = strtoupper($coupon->code);
        });

        static::updating(function ($coupon) {
            $coupon->code = strtoupper($coupon->code);
        });
    }

    // Relationships

    public function usage()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    // Validation

    public function validate(float $subtotal, ?int $customerId = null): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'error' => 'This coupon is no longer active.'];
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return ['valid' => false, 'error' => 'This coupon is not yet available.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'error' => 'This coupon has expired.'];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return ['valid' => false, 'error' => 'This coupon has reached its usage limit.'];
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return ['valid' => false, 'error' => 'Minimum order amount of $' . number_format($this->min_order_amount, 2) . ' required.'];
        }

        if ($customerId !== null && $this->max_uses_per_customer !== null) {
            $customerUsage = $this->usage()->where('customer_id', $customerId)->count();
            if ($customerUsage >= $this->max_uses_per_customer) {
                return ['valid' => false, 'error' => 'You have already used this coupon the maximum number of times.'];
            }
        }

        return ['valid' => true, 'error' => null];
    }

    // Discount Calculation

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount_amount !== null) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
        } else {
            $discount = (float) $this->value;
        }

        return min($discount, $subtotal);
    }

    // Record Usage

    public function recordUsage(int $customerId, int $orderId, float $discount): void
    {
        $this->usage()->create([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'discount_amount' => $discount,
            'used_at' => now(),
        ]);

        $this->increment('used_count');
    }

    // Accessors

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getIsMaxedOutAttribute(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return rtrim(rtrim(number_format($this->value, 2), '0'), '.') . '%';
        }

        return '$' . number_format($this->value, 2);
    }

    public function getRemainingUsesAttribute(): ?int
    {
        if ($this->max_uses === null) {
            return null;
        }

        return max(0, $this->max_uses - $this->used_count);
    }
}
