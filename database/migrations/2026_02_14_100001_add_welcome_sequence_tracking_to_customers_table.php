<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('welcome_email_2_sent_at')->nullable()->after('welcome_email_sent_at');
            $table->timestamp('welcome_email_3_sent_at')->nullable()->after('welcome_email_2_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['welcome_email_2_sent_at', 'welcome_email_3_sent_at']);
        });
    }
};
