<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds unique constraint to prevent duplicate newsletter sends
     * and performance indexes for analytics queries.
     */
    public function up(): void
    {
        Schema::table('newsletter_sends', function (Blueprint $table) {
            // Prevent duplicate sends: same newsletter to same subscriber
            $table->unique(
                ['newsletter_id', 'newsletter_subscription_id'],
                'newsletter_sends_unique_recipient'
            );

            // Performance indexes for campaign analytics
            $table->index(['newsletter_id', 'opened_at'], 'newsletter_sends_opened_idx');
            $table->index(['newsletter_id', 'clicked_at'], 'newsletter_sends_clicked_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('newsletter_sends', function (Blueprint $table) {
            $table->dropUnique('newsletter_sends_unique_recipient');
            $table->dropIndex('newsletter_sends_opened_idx');
            $table->dropIndex('newsletter_sends_clicked_idx');
        });
    }
};
