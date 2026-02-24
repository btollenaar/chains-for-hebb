<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add variant reference to order items for POD fulfillment.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('item_id')
                ->constrained('product_variants')->onDelete('set null');
            $table->unsignedInteger('printful_variant_id')->nullable()->after('product_variant_id')
                ->comment('Printful variant ID snapshot for fulfillment');

            // Variant snapshot (size/color at time of order)
            $table->json('variant_snapshot')->nullable()->after('attributes')
                ->comment('{"color": "Black", "size": "L"} snapshot at order time');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_variant_id', 'printful_variant_id', 'variant_snapshot']);
        });
    }
};
