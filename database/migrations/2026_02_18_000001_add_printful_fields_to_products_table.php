<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add Printful-specific fields to the products table.
     * Repurposes existing fulfillment_provider as fulfillment_type enum.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Printful identifiers
            $table->unsignedInteger('printful_product_id')->nullable()->after('id')
                ->comment('Printful catalog product ID (e.g., 71 = Bella+Canvas 3001)');
            $table->unsignedInteger('printful_sync_product_id')->nullable()->after('printful_product_id')
                ->comment('Printful sync product ID (our store\'s instance)');

            // Pricing
            $table->decimal('base_cost', 8, 2)->nullable()->after('cost')
                ->comment('Printful\'s cost to produce this item');
            $table->decimal('profit_margin', 5, 2)->nullable()->after('base_cost')
                ->comment('Profit margin percentage');

            // Fulfillment type (printful or manual)
            $table->string('fulfillment_type')->default('printful')->after('status')
                ->comment('Fulfillment method: printful or manual');

            // Printful sync status
            $table->timestamp('printful_synced_at')->nullable()->after('fulfillment_type')
                ->comment('Last time this product was synced with Printful');

            // Indexes
            $table->index('printful_product_id');
            $table->index('printful_sync_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['printful_product_id']);
            $table->dropIndex(['printful_sync_product_id']);
            $table->dropColumn([
                'printful_product_id',
                'printful_sync_product_id',
                'base_cost',
                'profit_margin',
                'fulfillment_type',
                'printful_synced_at',
            ]);
        });
    }
};
