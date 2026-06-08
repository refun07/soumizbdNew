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
        Schema::create('purchase_return_order_product_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_return_order_id');
            $table->integer('product_id'); // to get product name directly
            $table->integer('purchase_order_id');
            $table->integer('product_variation_id');
            $table->double('base_unit_price')->default(0.00); // price which is set in product variation
            $table->double('unit_price')->default(0.00);
            $table->bigInteger('qty')->default(0);
            $table->bigInteger('returned_qty')->default(0);
            $table->double('sub_total')->default(0.00);
            $table->double('discount')->default(0.00);
            $table->double('tax')->default(0.00);
            $table->double('grand_total')->default(0.00);
            $table->bigInteger('prev_stock')->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_order_product_variations');
    }
};
