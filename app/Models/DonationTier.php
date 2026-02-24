<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'suggested_amount',
        'description',
        'perks',
        'badge_icon',
        'badge_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'suggested_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships

    public function donations()
    {
        return $this->hasMany(Donation::class, 'tier_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
