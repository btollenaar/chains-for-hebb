<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\PrintfulService;
use Illuminate\Console\Command;

class SyncPrintfulPrices extends Command
{
    protected $signature = 'printful:sync-prices';
    protected $description = 'Sync Printful costs for all active variants';

    public function handle(PrintfulService $printful): int
    {
        $this->info('Syncing Printful variant pricing...');

        $products = Product::where('fulfillment_type', 'printful')
            ->whereNotNull('printful_product_id')
            ->with('variants')
            ->get();

        if ($products->isEmpty()) {
            $this->info('No Printful products found.');
            return self::SUCCESS;
        }

        $updated = 0;
        $failed = 0;

        foreach ($products as $product) {
            try {
                $catalogProduct = $printful->getCatalogProduct($product->printful_product_id);
                $catalogVariants = collect($catalogProduct['variants'] ?? [])
                    ->keyBy('id');

                foreach ($product->variants as $variant) {
                    if (!$variant->printful_variant_id) {
                        continue;
                    }

                    $catalogVariant = $catalogVariants->get($variant->printful_variant_id);
                    if ($catalogVariant && isset($catalogVariant['price'])) {
                        $newCost = (float) $catalogVariant['price'];
                        if ($variant->printful_cost != $newCost) {
                            $variant->update(['printful_cost' => $newCost]);
                            $updated++;
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->warn("Failed to sync prices for product #{$product->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->info("Price sync complete. Updated: {$updated} variants. Failed: {$failed} products.");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
