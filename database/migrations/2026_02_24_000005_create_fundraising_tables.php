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
        Schema::create('fundraising_milestones', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 10, 2);
            $table->string('icon')->nullable();
            $table->boolean('is_reached')->default(false);
            $table->timestamp('reached_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('fundraising_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fundraising_breakdowns');
        Schema::dropIfExists('fundraising_milestones');
    }
};
