<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'stripe_subscription_id',
        'amount',
        'interval',
        'status',
        'current_period_start',
        'current_period_end',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
    ];

    // Relationships

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
