<?php

namespace App\Services;

use App\Models\Product;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class SearchService
{
    /**
     * Quick search for autocomplete (limited results per type).
     */
    public function search(string $query, int $limit = 4): array
    {
        $query = trim($query);

        if (strlen($query) < 2) {
            return ['products' => [], 'blog' => []];
        }

        return [
            'products' => Product::where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('sku', 'LIKE', "%{$query}%")
                      ->orWhereHas('activeVariants', fn($vq) =>
                          $vq->where('color_name', 'LIKE', "%{$query}%")
                              ->orWhere('size', 'LIKE', "%{$query}%"));
                })
                ->selectRaw("*, (
                    CASE WHEN name = ? THEN 100
                         WHEN name LIKE ? THEN 80
                         WHEN sku LIKE ? THEN 70
                         WHEN description LIKE ? THEN 30
                         ELSE 10 END
                ) as relevance_score", [$query, "%{$query}%", "%{$query}%", "%{$query}%"])
                ->orderByDesc('relevance_score')
                ->limit($limit)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->currentPrice,
                    'image' => $p->first_image_url,
                    'url' => route('products.show', $p->slug),
                    'type' => 'product',
                ]),

            'blog' => BlogPost::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('excerpt', 'LIKE', "%{$query}%");
                })
                ->select('id', 'title', 'slug', 'excerpt', 'featured_image')
                ->limit($limit)
                ->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->title,
                    'slug' => $b->slug,
                    'excerpt' => Str::limit(strip_tags($b->excerpt), 80),
                    'image' => $b->featured_image ? asset('storage/' . $b->featured_image) : null,
                    'url' => route('blog.show', $b->slug),
                    'type' => 'blog',
                ]),
        ];
    }

    /**
     * Full search for the results page (supports type filtering and pagination).
     */
    public function fullSearch(string $query, ?string $type = null, int $perPage = 12)
    {
        $query = trim($query);

        if (strlen($query) < 2) {
            return collect();
        }

        if ($type === 'products') {
            return Product::where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('sku', 'LIKE', "%{$query}%");
                })
                ->paginate($perPage)
                ->appends(['q' => $query, 'type' => $type]);
        }

        if ($type === 'blog') {
            return BlogPost::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('excerpt', 'LIKE', "%{$query}%");
                })
                ->paginate($perPage)
                ->appends(['q' => $query, 'type' => $type]);
        }

        // All results combined (no pagination, grouped by type)
        return [
            'products' => Product::where('status', 'active')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('sku', 'LIKE', "%{$query}%");
                })
                ->get(),

            'blog' => BlogPost::published()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('content', 'LIKE', "%{$query}%")
                      ->orWhere('excerpt', 'LIKE', "%{$query}%");
                })
                ->get(),
        ];
    }
}
