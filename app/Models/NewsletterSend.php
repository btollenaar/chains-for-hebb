<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NewsletterSend extends Model
{
    use HasFactory;

    protected $fillable = [
        'newsletter_id',
        'newsletter_subscription_id',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'error_message',
        'tracking_token',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(NewsletterSubscription::class, 'newsletter_subscription_id');
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->newsletter->increment('sent_count');
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);

        $this->newsletter->increment('failed_count');
    }

    public function trackOpen(): void
    {
        if ($this->opened_at === null) {
            $this->update(['opened_at' => now()]);
            $this->newsletter->increment('open_count');
        }
    }

    public function trackClick(): void
    {
        if ($this->clicked_at === null) {
            $this->update(['clicked_at' => now()]);
            $this->newsletter->increment('click_count');

            if ($this->opened_at === null) {
                $this->update(['opened_at' => now()]);
                $this->newsletter->increment('open_count');
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($send) {
            if (empty($send->tracking_token)) {
                $send->tracking_token = Str::random(64);
            }
        });
    }
}
