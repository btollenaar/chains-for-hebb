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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('label', 100)->default('Home');
            $table->enum('type', ['shipping', 'billing', 'both'])->default('both');
            $table->boolean('is_default')->default(false);
            $table->string('street');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip', 10);
            $table->string('country', 2)->default('US');
            $table->string('phone', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
