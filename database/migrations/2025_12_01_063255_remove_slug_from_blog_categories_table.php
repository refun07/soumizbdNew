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
        if (Schema::hasColumn('blog_categories', 'slug')) {
            Schema::table('blog_categories', function ($table) {
                $table->dropColumn('slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('blog_categories', 'slug')) {
            Schema::table('blog_categories', function ($table) {
                $table->dropColumn('slug');
            });
        }
    }
};
