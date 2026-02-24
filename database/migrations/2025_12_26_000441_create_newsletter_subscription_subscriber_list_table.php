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
        Schema::create('newsletter_subscription_subscriber_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('newsletter_subscription_id');
            $table->unsignedBigInteger('subscriber_list_id');
            $table->timestamps();

            // Foreign keys with custom short names
            $table->foreign('newsletter_subscription_id', 'nsub_list_subscription_fk')
                ->references('id')
                ->on('newsletter_subscriptions')
                ->onDelete('cascade');

            $table->foreign('subscriber_list_id', 'nsub_list_list_fk')
                ->references('id')
                ->on('subscriber_lists')
                ->onDelete('cascade');

            $table->unique(['newsletter_subscription_id', 'subscriber_list_id'], 'subscription_list_unique');
            $table->index('subscriber_list_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscription_subscriber_list');
    }
};
