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
        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'banner_image')) {
                $table->text('banner_image')->nullable()->after('thumbnail_image');
            }
        });

        Schema::table('brand_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('brand_translations', 'banner_image')) {
                $table->text('banner_image')->nullable()->after('thumbnail_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_translations', function (Blueprint $table) {
            if (Schema::hasColumn('brand_translations', 'banner_image')) {
                $table->dropColumn('banner_image');
            }
        });

        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'banner_image')) {
                $table->dropColumn('banner_image');
            }
        });
    }
};
