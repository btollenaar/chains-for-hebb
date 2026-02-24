<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $validated = $request->validate([
            'retail_price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $variant->update($validated);

        // Update product base price to min active variant retail_price
        $this->syncProductPrice($product);

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'retail_price' => number_format($variant->retail_price, 2, '.', ''),
                'is_active' => $variant->is_active,
                'profit' => number_format($variant->profit, 2, '.', ''),
                'profit_margin' => $variant->profit_margin,
            ],
            'product_price' => number_format($product->fresh()->price, 2, '.', ''),
        ]);
    }

    public function bulkUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'variant_ids' => 'required|array|min:1',
            'variant_ids.*' => 'exists:product_variants,id',
            'action' => 'required|in:markup_percent,flat_price,activate,deactivate',
            'value' => 'required_if:action,markup_percent,flat_price|nullable|numeric|min:0',
        ]);

        $variants = ProductVariant::where('product_id', $product->id)
            ->whereIn('id', $validated['variant_ids'])
            ->get();

        if ($variants->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No matching variants found.'], 422);
        }

        foreach ($variants as $variant) {
            match ($validated['action']) {
                'markup_percent' => $variant->update([
                    'retail_price' => round($variant->printful_cost * (1 + $validated['value'] / 100), 2),
                ]),
                'flat_price' => $variant->update([
                    'retail_price' => $validated['value'],
                ]),
                'activate' => $variant->update(['is_active' => true]),
                'deactivate' => $variant->update(['is_active' => false]),
            };
        }

        $this->syncProductPrice($product);

        // Return updated variant data
        $updatedVariants = ProductVariant::where('product_id', $product->id)
            ->ordered()
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'color_name' => $v->color_name,
                'color_hex' => $v->color_hex,
                'size' => $v->size,
                'printful_cost' => number_format($v->printful_cost, 2, '.', ''),
                'retail_price' => number_format($v->retail_price, 2, '.', ''),
                'profit' => number_format($v->profit, 2, '.', ''),
                'profit_margin' => $v->profit_margin,
                'is_active' => $v->is_active,
            ]);

        return response()->json([
            'success' => true,
            'variants' => $updatedVariants,
            'product_price' => number_format($product->fresh()->price, 2, '.', ''),
            'message' => count($validated['variant_ids']) . ' variant(s) updated.',
        ]);
    }

    private function syncProductPrice(Product $product): void
    {
        $minPrice = $product->activeVariants()->min('retail_price');
        if ($minPrice !== null) {
            $product->update(['price' => $minPrice]);
        }
    }
}
