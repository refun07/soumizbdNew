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
        if (Schema::hasColumn('blogs', 'name')) {
            Schema::table('blogs', function ($table) {
                $table->dropColumn('name');
            });
        }

        if (Schema::hasColumn('blogs', 'is_active') && !Schema::hasColumn('blogs', 'is_published')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->renameColumn('is_active', 'is_published');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            //
        });
    }
};
