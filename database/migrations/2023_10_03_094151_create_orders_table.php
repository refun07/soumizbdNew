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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('order_group_id');
            $table->bigInteger('order_code');
            $table->integer('shop_id');
            $table->integer('warehouse_id')->nullable();
            $table->double('amount')->default(0.00);
            $table->double('tax_amount')->default(0.00);
            $table->double('shipping_charge_amount')->default(0.00);
            $table->double('discount_amount')->default(0.00);
            $table->double('coupon_discount_amount')->default(0.00);
            $table->double('total_amount')->default(0.00);
            $table->integer('coupon_id')->nullable();
            $table->string('pickup_or_delivery')->default('delivery');
            $table->string('delivery_status')->default('order_placed');
            $table->string('payment_status')->default('unpaid');
            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('tracking_url')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
