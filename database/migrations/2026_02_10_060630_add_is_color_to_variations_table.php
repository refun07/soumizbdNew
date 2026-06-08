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
        Schema::table('variations', function (Blueprint $table) {
            $table->boolean('is_color')->default(false)->after('is_active');
        });

        // Set is_color = true for existing color variation (id = 1)
        \DB::table('variations')->where('id', 1)->update(['is_color' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->dropColumn('is_color');
        });
    }
};
