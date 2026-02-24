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
        Schema::create('sponsor_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->text('perks')->nullable();
            $table->string('logo_size')->default('md');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sponsor_tier_id')->nullable();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->decimal('sponsorship_amount', 10, 2)->default(0);
            $table->date('sponsorship_date')->nullable();
            $table->date('sponsorship_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('sponsor_tier_id')->references('id')->on('sponsor_tiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('sponsor_tiers');
    }
};
