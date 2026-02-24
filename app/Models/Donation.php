<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_name',
        'donor_email',
        'customer_id',
        'amount',
        'donation_type',
        'tier_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'stripe_subscription_id',
        'payment_status',
        'is_anonymous',
        'donor_message',
        'display_name',
        'tax_receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
    ];

    // Relationships

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tier()
    {
        return $this->belongsTo(DonationTier::class, 'tier_id');
    }

    public function recurringDonation()
    {
        return $this->hasOne(RecurringDonation::class);
    }

    // Scopes

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeOneTime($query)
    {
        return $query->where('donation_type', 'one_time');
    }

    public function scopeRecurring($query)
    {
        return $query->where('donation_type', 'recurring');
    }

    // Accessors

    public function getDisplayNameAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->is_anonymous) {
            return 'Anonymous';
        }

        return $this->donor_name;
    }
}
