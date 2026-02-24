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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            // Polymorphic relationship (Product or Service)
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');

            // Review content
            $table->tinyInteger('rating')->unsigned();
            $table->string('title')->nullable();
            $table->text('comment')->nullable();

            // Verification
            $table->boolean('verified_purchase')->default(false);

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Helpfulness
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);

            // Admin response
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('customer_id');
            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('status');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
