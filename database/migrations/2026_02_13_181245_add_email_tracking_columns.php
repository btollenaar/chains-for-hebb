<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->timestamp('abandoned_cart_email_sent_at')->nullable()->after('attributes');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('welcome_email_sent_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn('abandoned_cart_email_sent_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('welcome_email_sent_at');
        });
    }
};
