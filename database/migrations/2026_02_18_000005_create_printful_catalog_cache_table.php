<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cache of Printful's product catalog, refreshed daily.
     * Admin browses this to pick products to add to the store.
     */
    public function up(): void
    {
        Schema::create('printful_catalog_cache', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('printful_product_id')->unique()
                ->comment('Printful catalog product ID');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('image_url')->nullable();

            // Variant summary data
            $table->unsignedInteger('variant_count')->default(0);
            $table->decimal('min_price', 8, 2)->nullable()
                ->comment('Lowest variant cost from Printful');
            $table->decimal('max_price', 8, 2)->nullable()
                ->comment('Highest variant cost from Printful');

            // Cached option data
            $table->json('colors_json')->nullable()
                ->comment('Available color options');
            $table->json('sizes_json')->nullable()
                ->comment('Available size options');
            $table->json('print_areas_json')->nullable()
                ->comment('Available print placements');

            $table->timestamp('cached_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('category');
            $table->index('cached_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printful_catalog_cache');
    }
};
