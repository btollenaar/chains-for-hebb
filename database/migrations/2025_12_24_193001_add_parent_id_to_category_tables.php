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
        // Add parent_id to product_categories
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')
                  ->constrained('product_categories')
                  ->onDelete('cascade');

            // Composite index for efficient tree queries
            $table->index(['parent_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'display_order']);
            $table->dropColumn('parent_id');
        });
    }
};
