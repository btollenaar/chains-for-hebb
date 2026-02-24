<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewVote extends Model
{
    protected $fillable = [
        'review_id',
        'customer_id',
        'vote_type',
    ];

    /**
     * Get the review that was voted on
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the customer who voted
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
