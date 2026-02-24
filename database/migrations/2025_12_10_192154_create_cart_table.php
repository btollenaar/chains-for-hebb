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
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('session_id')->nullable();

            // Polymorphic relationship (Product or Service)
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');

            // Cart details
            $table->integer('quantity')->default(1);
            $table->json('attributes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('customer_id');
            $table->index('session_id');
            $table->index(['item_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
