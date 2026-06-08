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
        Schema::create('campaign_products', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id');
            $table->integer('campaign_id');
            $table->integer('product_id');
            $table->integer('product_variation_id');
            $table->string('discount_type')->default('flat')->comment('flat/percentage');
            $table->double('discount_value')->default(0.00);
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
        Schema::dropIfExists('campaign_products');
    }
};
