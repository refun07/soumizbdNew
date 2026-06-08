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
        Schema::create('purchase_return_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_id');
            $table->integer('shop_id');
            $table->integer('warehouse_id');
            $table->integer('supplier_id');
            $table->bigInteger('reference_code')->nullable();
            $table->text('date')->nullable();
            $table->string('status')->default('pending')->comment('pending/completed');
            $table->double('sub_total')->default(0.00);
            $table->double('tax_percentage')->default(0.00);
            $table->double('tax_value')->default(0.00);
            $table->double('shipping')->default(0.00);
            $table->double('discount')->default(0.00);
            $table->double('grand_total')->default(0.00);
            $table->double('paid')->default(0.00);
            $table->double('due')->default(0.00);
            $table->string('payment_status')->default('unpaid')->comment('paid/unpaid');
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('purchase_return_orders');
    }
};
