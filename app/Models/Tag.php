<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // Relationships

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_tag')
            ->withPivot('assigned_by', 'created_at');
    }

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    // Accessors

    public function getCustomerCountAttribute()
    {
        return $this->customers()->count();
    }
}
