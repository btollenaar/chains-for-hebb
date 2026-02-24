<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MembershipTier extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_interval',
        'discount_percentage',
        'features',
        'priority_booking',
        'free_shipping',
        'is_active',
        'display_order',
        'stripe_product_id',
        'stripe_price_id',
        'badge_color',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'features' => 'array',
        'priority_booking' => 'boolean',
        'free_shipping' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tier) {
            if (empty($tier->slug)) {
                $tier->slug = Str::slug($tier->name);
            }
        });
    }

    // Relationships

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function activeMembers()
    {
        return $this->hasMany(Membership::class)->where('status', 'active');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('price');
    }

    // Accessors

    public function getFormattedPriceAttribute(): string
    {
        $interval = $this->billing_interval === 'yearly' ? '/year' : '/month';
        return '$' . number_format($this->price, 2) . $interval;
    }

    public function getMonthlyEquivalentAttribute(): float
    {
        if ($this->billing_interval === 'yearly') {
            return round($this->price / 12, 2);
        }
        return $this->price;
    }

    public function getActiveMemberCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }
}
