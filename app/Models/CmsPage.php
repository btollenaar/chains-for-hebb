<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'template',
        'is_published',
        'sort_order',
        'show_in_nav',
        'parent_id',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_nav' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Boot

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    // Route key

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships

    public function parent()
    {
        return $this->belongsTo(CmsPage::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CmsPage::class, 'parent_id');
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeNavPages($query)
    {
        return $query->where('is_published', true)
            ->where('show_in_nav', true)
            ->orderBy('sort_order');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
