<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exchange_id');
            $table->unsignedBigInteger('original_product_variation_id');
            $table->unsignedBigInteger('exchange_product_variation_id');
            $table->unsignedBigInteger('original_order_item_id');
            $table->unsignedBigInteger('exchange_order_item_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->double('original_unit_price')->default(0);
            $table->double('exchange_unit_price')->default(0);
            $table->double('price_difference')->default(0);
            $table->string('reason')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->default('pending');
            $table->string('media_path')->nullable();
            $table->timestamps();

            $table->foreign('exchange_id')->references('id')->on('exchanges')->cascadeOnDelete();
            $table->foreign('original_product_variation_id')->references('id')->on('product_variations')->cascadeOnDelete();
            $table->foreign('exchange_product_variation_id')->references('id')->on('product_variations')->cascadeOnDelete();
            $table->foreign('original_order_item_id')->references('id')->on('order_items')->cascadeOnDelete();
            $table->foreign('exchange_order_item_id')->references('id')->on('order_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_items');
    }
};
