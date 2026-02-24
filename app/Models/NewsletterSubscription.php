<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'customer_id',
        'source',
        'is_active',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lists()
    {
        return $this->belongsToMany(SubscriberList::class, 'newsletter_subscription_subscriber_list');
    }

    public function sends()
    {
        return $this->hasMany(NewsletterSend::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeInList($query, $listId)
    {
        return $query->whereHas('lists', function ($q) use ($listId) {
            $q->where('subscriber_list_id', $listId);
        });
    }
}
