<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataExport extends Model
{
    protected $fillable = [
        'customer_id', 'status', 'file_path', 'expires_at',
        'requested_at', 'completed_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
