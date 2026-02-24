<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvImport extends Model
{
    protected $fillable = [
        'type', 'filename', 'original_filename', 'total_rows',
        'processed_rows', 'successful_rows', 'failed_rows',
        'status', 'error_log', 'uploaded_by', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'error_log' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function uploader()
    {
        return $this->belongsTo(Customer::class, 'uploaded_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing', 'started_at' => now()]);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed', 'completed_at' => now()]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed', 'completed_at' => now()]);
    }

    public function addError(int $row, string $message): void
    {
        $errors = $this->error_log ?? [];
        $errors[] = ['row' => $row, 'message' => $message];
        $this->update([
            'error_log' => $errors,
            'failed_rows' => $this->failed_rows + 1,
        ]);
    }

    public function incrementSuccess(): void
    {
        $this->increment('successful_rows');
        $this->increment('processed_rows');
    }

    public function incrementProcessed(): void
    {
        $this->increment('processed_rows');
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_rows === 0) return 0;
        return (int) round(($this->processed_rows / $this->total_rows) * 100);
    }

    public function getIsCompleteAttribute(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }
}
