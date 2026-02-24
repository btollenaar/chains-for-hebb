<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Product-Category Pivot Table
        Schema::create('product_product_category', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_category_id')->constrained('product_categories')->onDelete('cascade');

            // Pivot Data
            $table->boolean('is_primary')->default(false);
            $table->integer('display_order')->default(0);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'product_category_id']);
            $table->index('is_primary');

            // Unique constraint - prevent duplicate assignments
            $table->unique(['product_id', 'product_category_id']);
        });

        // Migrate existing data - Products
        $products = DB::table('products')->whereNotNull('category_id')->get();
        foreach ($products as $product) {
            DB::table('product_product_category')->insert([
                'product_id' => $product->id,
                'product_category_id' => $product->category_id,
                'is_primary' => true,
                'display_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product_category');
    }
};
