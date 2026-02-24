<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->integer('points'); // positive = earned, negative = redeemed
            $table->string('type'); // earned, redeemed, adjusted, expired
            $table->string('source'); // order, review, signup, admin, referral
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description');
            $table->integer('balance_after');
            $table->timestamps();

            $table->index('customer_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
