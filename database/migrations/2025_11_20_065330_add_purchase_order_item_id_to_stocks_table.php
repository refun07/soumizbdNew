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
        Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->after('product_variation_id');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_product_variations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_item_id']);
            $table->dropColumn('purchase_order_item_id');
        });
    }
};
