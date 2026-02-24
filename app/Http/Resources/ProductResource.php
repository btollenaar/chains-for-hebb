<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'in_stock' => $this->stock_quantity > 0,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'images' => $this->images ? collect($this->images)->map(fn($img) => asset('storage/' . $img)) : [],
            'weight_oz' => $this->weight_oz,
            'category' => $this->whenLoaded('productCategory', function () {
                return [
                    'id' => $this->productCategory->id,
                    'name' => $this->productCategory->name,
                    'slug' => $this->productCategory->slug,
                ];
            }),
            'average_rating' => $this->reviews()->where('status', 'approved')->avg('rating'),
            'review_count' => $this->reviews()->where('status', 'approved')->count(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
