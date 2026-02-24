<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Product variants represent size/color combinations synced from Printful.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Printful identifiers
            $table->unsignedInteger('printful_variant_id')
                ->comment('Printful catalog variant ID');
            $table->unsignedInteger('printful_sync_variant_id')->nullable()
                ->comment('Printful sync variant ID (our store\'s instance)');

            // Variant attributes
            $table->string('color_name')->nullable();
            $table->string('color_hex', 7)->nullable()->comment('e.g., #FF0000');
            $table->string('size')->nullable()->comment('e.g., S, M, L, XL, 2XL');
            $table->string('sku')->nullable();

            // Pricing
            $table->decimal('printful_cost', 8, 2)->comment('What Printful charges us');
            $table->decimal('retail_price', 8, 2)->comment('What customer pays');

            // Status
            $table->boolean('is_active')->default(true);
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');

            // Display
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('printful_variant_id');
            $table->index('printful_sync_variant_id');
            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'color_name', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
