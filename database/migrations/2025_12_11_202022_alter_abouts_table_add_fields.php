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
        Schema::table('abouts', function (Blueprint $table) {
            if (!Schema::hasColumn('abouts', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('abouts', 'credentials')) {
                $table->string('credentials')->nullable();
            }
            if (!Schema::hasColumn('abouts', 'short_bio')) {
                $table->text('short_bio')->nullable();
            }
            if (!Schema::hasColumn('abouts', 'bio')) {
                $table->longText('bio')->nullable();
            }
            if (!Schema::hasColumn('abouts', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('abouts', 'published')) {
                $table->boolean('published')->default(true);
            }
            if (!Schema::hasColumn('abouts', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            //
        });
    }
};
