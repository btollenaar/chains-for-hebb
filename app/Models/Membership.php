<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'membership_tier_id',
        'status',
        'stripe_subscription_id',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'trial_ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    // Relationships

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tier()
    {
        return $this->belongsTo(MembershipTier::class, 'membership_tier_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Accessors

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'expired' || ($this->expires_at && $this->expires_at->isPast());
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->expires_at) {
            return null;
        }
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    // Actions

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    public function activate(?\DateTime $expiresAt = null): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => $this->starts_at ?? now(),
            'expires_at' => $expiresAt,
            'cancelled_at' => null,
        ]);
    }

    public function expire(): void
    {
        $this->update(['status' => 'expired']);
    }
}
