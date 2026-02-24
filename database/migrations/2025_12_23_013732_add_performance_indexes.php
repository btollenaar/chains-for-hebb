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
        // Orders - customer + payment status filtering
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['customer_id', 'payment_status'], 'idx_orders_customer_payment_status');
        });

        // Products - category + status filtering
        Schema::table('products', function (Blueprint $table) {
            $table->index(['category_id', 'status'], 'idx_products_category_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_customer_payment_status');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_category_status');
        });
    }
};
