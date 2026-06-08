<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('pre_order')->nullable()->after('warranty_info');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->json('pre_order')->nullable()->after('discount_type');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('pre_order_info')->nullable()->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pre_order');
        });

        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn('pre_order');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('pre_order_info');
        });
    }
};
