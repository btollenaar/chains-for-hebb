<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryAlbum extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'album_date',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'album_date' => 'date',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Boot

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($album) {
            if (empty($album->slug)) {
                $album->slug = Str::slug($album->title);
            }
        });
    }

    // Route key

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships

    public function photos()
    {
        return $this->hasMany(GalleryPhoto::class);
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
