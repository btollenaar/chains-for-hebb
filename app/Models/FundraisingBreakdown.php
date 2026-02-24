<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundraisingBreakdown extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'amount',
        'description',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
