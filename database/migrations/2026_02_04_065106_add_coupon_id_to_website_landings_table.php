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
        Schema::table('website_landings', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('page_configs')->constrained('coupons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_landings', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn('coupon_id');
        });
    }
};
