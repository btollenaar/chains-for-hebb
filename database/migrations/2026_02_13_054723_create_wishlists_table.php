<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->timestamps();

            $table->index(['item_type', 'item_id']);
            $table->unique(['customer_id', 'item_type', 'item_id'], 'wishlists_customer_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
