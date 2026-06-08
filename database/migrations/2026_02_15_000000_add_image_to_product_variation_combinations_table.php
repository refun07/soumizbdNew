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
        Schema::table('product_variation_combinations', function (Blueprint $table) {
            $table->string('image')->nullable()->after('variation_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variation_combinations', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
