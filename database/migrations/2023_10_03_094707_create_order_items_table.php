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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('product_variation_id');
            $table->integer('qty')->default(1);
            $table->double('unit_price')->default(0.00);
            $table->double('total_tax')->default(0.00);
            $table->double('total_discount')->default(0.00);
            $table->double('total_price')->default(0.00);
            $table->bigInteger('reward_points')->default(0);
            $table->boolean('is_refunded')->default(0);
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
        Schema::dropIfExists('order_items');
    }
};
