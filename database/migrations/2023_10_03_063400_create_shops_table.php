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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->boolean('is_approved')->default(0);
            $table->boolean('is_verified_by_admin')->default(0);
            $table->boolean('is_published')->default(0);
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('tagline')->nullable();
            $table->string('name');
            $table->text('slug');
            $table->longText('info')->nullable();
            $table->double('rating')->default(0.00);
            $table->longText('address')->nullable();
            $table->double('min_order_amount')->default(0.00);
            $table->double('admin_commission_percentage')->default(0.00);
            $table->double('current_balance')->default(0.00);
            $table->double('default_shipping_charge')->default(0.00);
            $table->string('manage_stock_by')->default('default')->comment('default/inventory');
            $table->double('monthly_goal_amount')->default(0.00);
            $table->boolean('is_cash_payout')->default(0);
            $table->boolean('is_bank_payout')->default(0);
            $table->string('bank_name')->nullable();
            $table->string('bank_acc_name')->nullable();
            $table->string('bank_acc_no')->nullable();
            $table->string('bank_routing_no')->nullable();
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
        Schema::dropIfExists('shops');
    }
};
