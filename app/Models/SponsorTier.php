<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'min_amount',
        'perks',
        'logo_size',
        'sort_order',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Relationships

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class, 'sponsor_tier_id');
    }

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
