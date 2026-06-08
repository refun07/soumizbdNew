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
        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->text('date')->nullable();
            $table->string('payment_method')->default('cash');
            $table->double('paid_amount')->default(0.00);
            $table->double('return_amount')->default(0.00);
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
        Schema::dropIfExists('purchase_order_payments');
    }
};
