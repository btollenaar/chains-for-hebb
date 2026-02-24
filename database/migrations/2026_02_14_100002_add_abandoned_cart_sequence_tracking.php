<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->timestamp('abandoned_cart_email_2_sent_at')->nullable()->after('abandoned_cart_email_sent_at');
            $table->timestamp('abandoned_cart_email_3_sent_at')->nullable()->after('abandoned_cart_email_2_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn(['abandoned_cart_email_2_sent_at', 'abandoned_cart_email_3_sent_at']);
        });
    }
};
