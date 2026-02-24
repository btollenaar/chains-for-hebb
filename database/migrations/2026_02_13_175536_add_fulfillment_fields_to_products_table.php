<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('fulfillment_provider')->nullable()->after('status');
            $table->string('fulfillment_sku')->nullable()->after('fulfillment_provider');
            $table->decimal('wholesale_cost', 8, 2)->nullable()->after('cost');
            $table->decimal('weight_oz', 6, 2)->nullable()->after('wholesale_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['fulfillment_provider', 'fulfillment_sku', 'wholesale_cost', 'weight_oz']);
        });
    }
};
