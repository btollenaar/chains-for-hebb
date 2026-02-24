<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundraisingMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'icon',
        'is_reached',
        'reached_at',
        'sort_order',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'is_reached' => 'boolean',
        'reached_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeReached($query)
    {
        return $query->where('is_reached', true);
    }

    public function scopeUnreached($query)
    {
        return $query->where('is_reached', false);
    }
}
