<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Newsletter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'preview_text',
        'content',
        'plain_text_content',
        'status',
        'scheduled_at',
        'sent_at',
        'started_sending_at',
        'finished_sending_at',
        'recipient_count',
        'sent_count',
        'failed_count',
        'open_count',
        'click_count',
        'created_by',
        'from_name',
        'from_email',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'started_sending_at' => 'datetime',
        'finished_sending_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'created_by');
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(SubscriberList::class, 'newsletter_subscriber_list');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePendingSend($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    public function getOpenRateAttribute()
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->open_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute()
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->click_count / $this->sent_count) * 100, 2);
    }

    public function duplicate(): Newsletter
    {
        $copy = $this->replicate();
        $copy->subject = $this->subject . ' (Copy)';
        $copy->status = 'draft';
        $copy->scheduled_at = null;
        $copy->sent_at = null;
        $copy->started_sending_at = null;
        $copy->finished_sending_at = null;
        $copy->recipient_count = 0;
        $copy->sent_count = 0;
        $copy->failed_count = 0;
        $copy->open_count = 0;
        $copy->click_count = 0;
        $copy->save();

        $copy->lists()->sync($this->lists->pluck('id'));

        return $copy;
    }

    public function generatePlainText(): string
    {
        $text = strip_tags($this->content);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);

        return trim($text);
    }
}
