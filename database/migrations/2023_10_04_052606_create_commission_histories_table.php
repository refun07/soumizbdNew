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
        Schema::create('commission_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id');
            $table->integer('shop_id')->nullable();
            $table->integer('subscription_of_shop_id')->nullable();
            $table->double('admin_commission_percentage')->default(0.00);
            $table->double('amount')->default(0.00);
            $table->double('admin_earning_amount')->default(0.00);
            $table->double('shop_earning_amount')->default(0.00);
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
        Schema::dropIfExists('commission_histories');
    }
};
