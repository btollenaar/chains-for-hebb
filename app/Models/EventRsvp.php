<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventRsvp extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'customer_id',
        'name',
        'email',
        'party_size',
        'status',
        'notes',
        'token',
        'reminder_sent_at',
    ];

    protected $casts = [
        'party_size' => 'integer',
        'reminder_sent_at' => 'datetime',
    ];

    // Boot

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rsvp) {
            if (empty($rsvp->token)) {
                $rsvp->token = Str::random(64);
            }
        });
    }

    // Relationships

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Scopes

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
