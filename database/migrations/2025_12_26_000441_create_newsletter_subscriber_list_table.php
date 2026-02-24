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
        Schema::create('newsletter_subscriber_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscriber_list_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['newsletter_id', 'subscriber_list_id'], 'newsletter_list_unique');
            $table->index('subscriber_list_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriber_list');
    }
};
