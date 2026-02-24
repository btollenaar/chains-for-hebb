<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'order_id',
        'customer_id',
        'return_number',
        'status',
        'reason',
        'details',
        'items',
        'refund_amount',
        'refund_method',
        'admin_notes',
        'approved_at',
        'rejected_at',
        'completed_at',
        'processed_by',
    ];

    protected $casts = [
        'items' => 'array',
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(Customer::class, 'processed_by');
    }

    // Scopes

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeRequested($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Accessors

    public function getIsRequestedAttribute(): bool
    {
        return $this->status === 'requested';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    // Actions

    public function approve(float $refundAmount, string $refundMethod, ?string $adminNotes, int $processedBy): void
    {
        $this->update([
            'status' => 'approved',
            'refund_amount' => $refundAmount,
            'refund_method' => $refundMethod,
            'admin_notes' => $adminNotes,
            'approved_at' => now(),
            'processed_by' => $processedBy,
        ]);
    }

    public function reject(?string $adminNotes, int $processedBy): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $adminNotes,
            'rejected_at' => now(),
            'processed_by' => $processedBy,
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    // Return reason options
    public static function reasonOptions(): array
    {
        return [
            'defective' => 'Product is defective or damaged',
            'wrong_item' => 'Wrong item received',
            'not_as_described' => 'Item not as described',
            'no_longer_needed' => 'No longer needed',
            'better_price' => 'Found a better price',
            'other' => 'Other reason',
        ];
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $return->return_number = 'RET-' . strtoupper(uniqid());
            }
        });
    }
}
