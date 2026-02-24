<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsor_tier_id',
        'name',
        'logo',
        'website_url',
        'sponsorship_amount',
        'sponsorship_date',
        'sponsorship_expires_at',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'sponsorship_amount' => 'decimal:2',
        'sponsorship_date' => 'date',
        'sponsorship_expires_at' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships

    public function sponsorTier()
    {
        return $this->belongsTo(SponsorTier::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('sponsorship_expires_at')
                    ->orWhere('sponsorship_expires_at', '>', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
