<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'title',
        'comment',
        'verified_purchase',
        'status',
        'helpful_count',
        'not_helpful_count',
        'admin_response',
        'responded_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('reviewable_type', Product::class)
            ->where('reviewable_id', $productId);
    }

    public function scopeForService($query, $serviceId)
    {
        return $query->where('reviewable_type', Service::class)
            ->where('reviewable_id', $serviceId);
    }

    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Accessors
    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    // Helper methods
    public function approve()
    {
        $this->status = 'approved';
        $this->save();
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }

    public function markHelpful()
    {
        $this->increment('helpful_count');
    }

    public function markNotHelpful()
    {
        $this->increment('not_helpful_count');
    }

    public function addAdminResponse($response)
    {
        $this->admin_response = $response;
        $this->responded_at = now();
        $this->save();
    }
}
