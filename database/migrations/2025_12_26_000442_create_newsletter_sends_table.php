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
        Schema::create('newsletter_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->onDelete('cascade');
            $table->foreignId('newsletter_subscription_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('tracking_token', 64)->unique();
            $table->timestamps();

            $table->index(['newsletter_id', 'status']);
            $table->index('tracking_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_sends');
    }
};
