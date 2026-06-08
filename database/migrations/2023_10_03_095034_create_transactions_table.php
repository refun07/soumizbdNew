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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('for')->default('checkout');
            $table->double('amount')->default(0.00);
            $table->double('tax_amount')->default(0.00);
            $table->double('shipping_charge_amount')->default(0.00);
            $table->double('discount_amount')->default(0.00);
            $table->double('coupon_discount_amount')->default(0.00);
            $table->double('total_amount')->default(0.00);
            $table->string('status')->default('unpaid')->comment('paid/unpaid/partial/failed/cancelled');
            $table->string('payment_method')->nullable();
            $table->longText('payment_details')->nullable();
            $table->boolean('is_manual_payment')->default(0);
            $table->longText('manual_payment_details')->nullable();
            $table->longText('manual_payment_document')->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
