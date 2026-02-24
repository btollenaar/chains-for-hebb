<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add variant reference to cart items for POD products.
     */
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('item_id')
                ->constrained('product_variants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
