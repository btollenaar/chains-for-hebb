<?php

namespace App\Console\Commands;

use App\Services\PrintfulService;
use Illuminate\Console\Command;

class SyncPrintfulCatalog extends Command
{
    protected $signature = 'printful:sync-catalog';
    protected $description = 'Sync Printful product catalog to local cache';

    public function handle(PrintfulService $printful): int
    {
        $this->info('Syncing Printful catalog...');

        try {
            $count = $printful->syncCatalogToCache();
            $this->info("Synced {$count} products from Printful catalog.");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Catalog sync failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
