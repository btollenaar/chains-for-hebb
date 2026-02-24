<?php

namespace App\Services;

use App\Models\Product;

class GoogleShoppingFeedService
{
    /**
     * Generate XML product feed for Google Merchant Center
     */
    public function generateFeed(): string
    {
        $products = Product::active()
            ->where('price', '>', 0)
            ->where('stock_quantity', '>', 0)
            ->with('primaryCategory')
            ->get();

        $appName = htmlspecialchars(config('app.name'));
        $appUrl = htmlspecialchars(config('app.url'));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= "<title>{$appName}</title>\n";
        $xml .= "<link>{$appUrl}</link>\n";
        $xml .= "<description>Products from {$appName}</description>\n";

        foreach ($products as $product) {
            $xml .= $this->buildItemXml($product);
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    private function buildItemXml(Product $product): string
    {
        $xml = "<item>\n";
        $xml .= '  <g:id>' . $product->id . "</g:id>\n";
        $xml .= '  <g:title>' . htmlspecialchars($product->name) . "</g:title>\n";
        $xml .= '  <g:description>' . htmlspecialchars(strip_tags($product->description)) . "</g:description>\n";
        $xml .= '  <g:link>' . route('products.show', $product->slug) . "</g:link>\n";
        $xml .= '  <g:image_link>' . $this->getProductImageUrl($product) . "</g:image_link>\n";
        $xml .= '  <g:availability>' . ($product->stock_quantity > 0 ? 'in_stock' : 'out_of_stock') . "</g:availability>\n";
        $xml .= '  <g:price>' . number_format($product->price, 2) . " USD</g:price>\n";

        if ($product->sale_price && $product->sale_price < $product->price) {
            $xml .= '  <g:sale_price>' . number_format($product->sale_price, 2) . " USD</g:sale_price>\n";

            if ($product->sale_start && $product->sale_end) {
                $start = $product->sale_start->format('Y-m-d\TH:i:sO');
                $end = $product->sale_end->format('Y-m-d\TH:i:sO');
                $xml .= "  <g:sale_price_effective_date>{$start}/{$end}</g:sale_price_effective_date>\n";
            }
        }

        $xml .= '  <g:brand>' . htmlspecialchars(config('app.name')) . "</g:brand>\n";
        $xml .= '  <g:condition>new</g:condition>' . "\n";

        if ($product->primaryCategory) {
            $xml .= '  <g:product_type>' . htmlspecialchars($this->buildCategoryPath($product)) . "</g:product_type>\n";
        }

        if ($product->sku) {
            $xml .= '  <g:mpn>' . htmlspecialchars($product->sku) . "</g:mpn>\n";
        }

        if ($product->barcode) {
            $xml .= '  <g:gtin>' . htmlspecialchars($product->barcode) . "</g:gtin>\n";
        } else {
            $xml .= "  <g:identifier_exists>false</g:identifier_exists>\n";
        }

        if ($product->weight_oz) {
            $weight_lb = round($product->weight_oz / 16, 2);
            $xml .= '  <g:shipping_weight>' . $weight_lb . " lb</g:shipping_weight>\n";
        }

        // Additional images (up to 10 per Google spec)
        $images = $product->images;
        if (!empty($images) && is_array($images) && count($images) > 1) {
            foreach (array_slice($images, 1, 9) as $image) {
                $xml .= '  <g:additional_image_link>' . url('storage/' . $image) . "</g:additional_image_link>\n";
            }
        }

        $xml .= "</item>\n";

        return $xml;
    }

    private function buildCategoryPath(Product $product): string
    {
        $category = $product->primaryCategory->first() ?? $product->productCategory;
        if (!$category) {
            return '';
        }

        $path = collect();
        $current = $category;
        while ($current) {
            $path->prepend($current->name);
            $current = $current->parent;
        }

        return $path->implode(' > ');
    }

    private function getProductImageUrl(Product $product): string
    {
        $images = $product->images;
        if (!empty($images) && is_array($images)) {
            return url('storage/' . $images[0]);
        }
        return url('images/placeholder.jpg');
    }
}
