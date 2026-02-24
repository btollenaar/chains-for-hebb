<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    public function generate(): string
    {
        return Cache::remember('sitemap_xml', 3600, function () {
            return $this->buildXml();
        });
    }

    private function buildXml(): string
    {
        $urls = [];

        // Static pages
        $urls[] = $this->url(route('home'), now()->format('Y-m-d'), 'daily', '1.0');
        $urls[] = $this->url(route('products.index'), now()->format('Y-m-d'), 'daily', '0.9');
        $urls[] = $this->url(route('blog.index'), now()->format('Y-m-d'), 'weekly', '0.7');
        $urls[] = $this->url(route('about'), now()->format('Y-m-d'), 'monthly', '0.5');
        $urls[] = $this->url(route('legal.privacy-policy'), now()->format('Y-m-d'), 'monthly', '0.3');
        $urls[] = $this->url(route('legal.terms-of-service'), now()->format('Y-m-d'), 'monthly', '0.3');
        $urls[] = $this->url(route('legal.return-policy'), now()->format('Y-m-d'), 'monthly', '0.3');
        $urls[] = $this->url(route('legal.shipping-policy'), now()->format('Y-m-d'), 'monthly', '0.3');

        // Products
        Product::active()->select('slug', 'updated_at')->each(function ($product) use (&$urls) {
            $urls[] = $this->url(
                route('products.show', $product->slug),
                $product->updated_at->format('Y-m-d'),
                'weekly',
                '0.8'
            );
        });

        // Product categories
        ProductCategory::active()->select('slug', 'updated_at')->each(function ($category) use (&$urls) {
            $urls[] = $this->url(
                route('products.category', $category->slug),
                $category->updated_at->format('Y-m-d'),
                'weekly',
                '0.7'
            );
        });

        // Blog posts
        BlogPost::where('published', true)->select('slug', 'updated_at')->each(function ($post) use (&$urls) {
            $urls[] = $this->url(
                route('blog.show', $post->slug),
                $post->updated_at->format('Y-m-d'),
                'monthly',
                '0.6'
            );
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $xml .= implode("\n", $urls);
        $xml .= "\n" . '</urlset>';

        return $xml;
    }

    private function url(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n" .
            "    <loc>" . htmlspecialchars($loc) . "</loc>\n" .
            "    <lastmod>{$lastmod}</lastmod>\n" .
            "    <changefreq>{$changefreq}</changefreq>\n" .
            "    <priority>{$priority}</priority>\n" .
            "  </url>";
    }
}
