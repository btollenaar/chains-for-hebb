<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Generated mockup images from Printful's mockup generator.
     * These are the product photos customers see on the storefront.
     */
    public function up(): void
    {
        Schema::create('product_mockups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()
                ->constrained('product_variants')->onDelete('cascade');

            // Mockup info
            $table->string('mockup_url')->comment('URL to generated mockup image');
            $table->unsignedInteger('template_id')->nullable()
                ->comment('Printful mockup template ID');
            $table->string('placement')->nullable()
                ->comment('Which placement this mockup shows');

            // Display
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index(['product_id', 'is_primary']);
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_mockups');
    }
};
