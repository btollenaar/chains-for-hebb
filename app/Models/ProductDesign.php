<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDesign extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'placement',
        'file_url',
        'printful_file_id',
        'width',
        'height',
        'dpi',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'dpi' => 'integer',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
