<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriberList extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'is_system',
        'subscriber_count',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(NewsletterSubscription::class, 'newsletter_subscription_subscriber_list');
    }

    public function newsletters(): BelongsToMany
    {
        return $this->belongsToMany(Newsletter::class, 'newsletter_subscriber_list');
    }

    public function updateSubscriberCount(): void
    {
        $this->subscriber_count = $this->subscribers()->count();
        $this->save();
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($list) {
            if ($list->is_system) {
                throw new \Exception('Cannot delete system lists.');
            }
        });
    }
}
