<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand fulfillment_status to include shipped, delivered, and failed.
     *
     * MySQL: alter the ENUM in place.
     * SQLite: convert to string (SQLite has no enum enforcement).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN fulfillment_status ENUM('pending','processing','shipped','delivered','completed','failed','cancelled') NOT NULL DEFAULT 'pending'");
        } else {
            // SQLite — change column to string to accept all values
            Schema::table('orders', function (Blueprint $table) {
                $table->string('fulfillment_status', 20)->default('pending')->change();
            });
        }
    }

    /**
     * Revert to original enum values (MySQL only — SQLite is a no-op since string already works).
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN fulfillment_status ENUM('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('fulfillment_status', 20)->default('pending')->change();
            });
        }
    }
};
