<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Design files uploaded for print-on-demand products.
     * Each product can have designs for multiple placements (front, back, sleeve, etc.).
     */
    public function up(): void
    {
        Schema::create('product_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

            // Design placement
            $table->string('placement')->default('front')
                ->comment('front, back, sleeve_left, sleeve_right, etc.');

            // File info
            $table->string('file_url')->comment('URL or path to design file');
            $table->unsignedInteger('printful_file_id')->nullable()
                ->comment('Printful\'s file ID after upload');

            // Dimensions
            $table->unsignedInteger('width')->nullable()->comment('pixels');
            $table->unsignedInteger('height')->nullable()->comment('pixels');
            $table->unsignedSmallInteger('dpi')->nullable()->default(300);

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('printful_file_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_designs');
    }
};
