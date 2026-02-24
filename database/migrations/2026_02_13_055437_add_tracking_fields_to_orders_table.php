<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('fulfillment_status');
            $table->string('tracking_carrier')->nullable()->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('tracking_carrier');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'tracking_carrier', 'shipped_at', 'delivered_at']);
        });
    }
};
