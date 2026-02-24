<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'featured_image',
        'event_type',
        'location_name',
        'starts_at',
        'ends_at',
        'max_attendees',
        'rsvp_deadline',
        'what_to_bring',
        'is_published',
        'is_featured',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'rsvp_deadline' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'max_attendees' => 'integer',
    ];

    // Route key

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships

    public function rsvps()
    {
        return $this->hasMany(EventRsvp::class);
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())
            ->whereNull('cancelled_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    // Accessors

    public function getIsCancelledAttribute()
    {
        return !is_null($this->cancelled_at);
    }

    public function getSpotsRemainingAttribute()
    {
        if (is_null($this->max_attendees)) {
            return null;
        }

        $confirmedCount = $this->rsvps()->confirmed()->sum('party_size');

        return max(0, $this->max_attendees - $confirmedCount);
    }

    public function getIsFullAttribute()
    {
        return $this->spots_remaining === 0;
    }
}
