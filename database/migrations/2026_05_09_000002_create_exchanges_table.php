<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_order_id');
            $table->unsignedBigInteger('exchange_order_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('status')->default('pending');
            $table->double('total_original_price')->default(0);
            $table->double('total_exchange_price')->default(0);
            $table->double('price_difference')->default(0);
            $table->double('refund_amount')->nullable();
            $table->double('shipping_charge')->default(0);
            $table->integer('requested_by')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('original_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreign('exchange_order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
