<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SoftDeletes, Notifiable, MustVerifyEmailTrait, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'billing_street',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'email_verified_at',
        'welcome_email_sent_at',
        'welcome_email_2_sent_at',
        'welcome_email_3_sent_at',
        'win_back_email_sent_at',
        'win_back_email_2_sent_at',
        'loyalty_points_balance',
        'wishlist_share_token',
    ];

    /**
     * The attributes that are not mass assignable (protected from privilege escalation).
     */
    protected $guarded = [
        'role',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'welcome_email_sent_at' => 'datetime',
        'welcome_email_2_sent_at' => 'datetime',
        'welcome_email_3_sent_at' => 'datetime',
        'win_back_email_sent_at' => 'datetime',
        'win_back_email_2_sent_at' => 'datetime',
        'is_admin' => 'boolean',
        'role' => 'string',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultShippingAddress()
    {
        return $this->addresses()->shipping()->default()->first();
    }

    public function defaultBillingAddress()
    {
        return $this->addresses()->billing()->default()->first();
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class)->where('status', 'active');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'customer_tag')
            ->withPivot('assigned_by', 'created_at');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getBillingAddressAttribute()
    {
        return trim("{$this->billing_street}, {$this->billing_city}, {$this->billing_state} {$this->billing_zip}");
    }

    public function getShippingAddressAttribute()
    {
        return trim("{$this->shipping_street}, {$this->shipping_city}, {$this->shipping_state} {$this->shipping_zip}");
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        if ($value === null) {
            $this->attributes['password'] = null;
            return;
        }

        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeWithOrders($query)
    {
        return $query->with('orders');
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeWithTag($query, string $slug)
    {
        return $query->whereHas('tags', fn($q) => $q->where('slug', $slug));
    }

    public function scopeWithAnyTags($query, array $ids)
    {
        return $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $ids));
    }

    // Role Helper Methods

    /**
     * Check if customer has admin privileges
     */
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->role === 'admin';
    }

    /**
     * Check if customer has staff privileges (admin or front_desk)
     */
    public function isStaff(): bool
    {
        return $this->is_admin || in_array($this->role, ['admin', 'front_desk']);
    }

    /**
     * Get the role display name
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'customer' => 'Customer',
            'admin' => 'Administrator',
            default => 'Customer',
        };
    }
}
