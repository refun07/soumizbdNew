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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id');
            $table->text('banner')->nullable();
            $table->string('code');
            $table->longText('info')->nullable();
            $table->string('discount_type')->comment('flat/percentage');
            $table->double('discount_value')->default(0.00);
            $table->boolean('is_free_shipping')->default(0);
            $table->text('start_date')->nullable();
            $table->text('end_date')->nullable();
            $table->double('min_spend')->default(0.00);
            $table->double('max_discount_value')->default(0.00);
            $table->integer('total_usage_limit')->default(1);
            $table->integer('customer_usage_limit')->default(1);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('coupons');
    }
};
