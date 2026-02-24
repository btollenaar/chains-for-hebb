<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_album_id',
        'file_path',
        'thumbnail_path',
        'caption',
        'alt_text',
        'photo_type',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_featured' => 'boolean',
    ];

    // Relationships

    public function album()
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }

    // Scopes

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('photo_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
