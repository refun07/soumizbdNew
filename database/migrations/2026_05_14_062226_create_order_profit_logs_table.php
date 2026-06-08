<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_profit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique();
            $table->double('revenue')->default(0.00);
            $table->double('cogs')->default(0.00);
            $table->double('admin_commission')->default(0.00);
            $table->double('seller_earning')->default(0.00);
            $table->double('agent_commission')->default(0.00)->nullable();
            $table->double('shipping_cost')->default(0.00);
            $table->double('tax_collected')->default(0.00);
            $table->double('discount_given')->default(0.00);
            $table->double('gross_profit')->default(0.00);
            $table->double('net_profit')->default(0.00);
            $table->double('profit_margin_pct')->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_profit_logs');
    }
};
