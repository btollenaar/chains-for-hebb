<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class About extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'credentials',
        'short_bio',
        'bio',
        'image',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];
}
