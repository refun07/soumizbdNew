<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->double('unit_cost_price')->default(0.00)->after('total_price');
            $table->double('total_cost')->default(0.00)->after('unit_cost_price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_cost_price', 'total_cost']);
        });
    }
};
