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
        Schema::create('donation_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('suggested_amount', 10, 2);
            $table->text('description')->nullable();
            $table->text('perks')->nullable();
            $table->string('badge_icon')->nullable();
            $table->string('badge_color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donor_name');
            $table->string('donor_email');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('donation_type')->default('one_time');
            $table->unsignedBigInteger('tier_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('payment_status')->default('pending');
            $table->boolean('is_anonymous')->default(false);
            $table->text('donor_message')->nullable();
            $table->string('display_name')->nullable();
            $table->string('tax_receipt_number')->nullable()->unique();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('tier_id')->references('id')->on('donation_tiers')->onDelete('set null');
        });

        Schema::create('recurring_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_id');
            $table->string('stripe_subscription_id');
            $table->decimal('amount', 10, 2);
            $table->string('interval')->default('monthly');
            $table->string('status')->default('active');
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamps();

            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_donations');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donation_tiers');
    }
};
