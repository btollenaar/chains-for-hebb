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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100); // 'profile', 'contact', 'social', etc.
            $table->string('key', 255); // 'business_name', 'phone', 'facebook_url'
            $table->text('value')->nullable(); // Actual value
            $table->string('type', 50)->default('string'); // 'string', 'boolean', 'json', 'image', 'url'
            $table->text('description')->nullable(); // Help text for admins
            $table->integer('order')->default(0); // Display order within category
            $table->timestamps();

            // Ensure unique key per category
            $table->unique(['category', 'key']);

            // Index for faster category queries
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
