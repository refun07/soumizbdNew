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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id');
            $table->string('name');
            $table->string('slug');
            $table->integer('brand_id')->nullable();
            $table->integer('unit_id')->nullable();
            $table->text('thumbnail_image')->nullable();
            $table->longText('gallery_images')->default(null)->nullable();
            $table->longText('product_tags')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->double('min_price')->default(0.00);
            $table->double('max_price')->default(0.00);
            $table->string('discount_info')->nullable();
            $table->integer('discount_start_date')->nullable();
            $table->integer('discount_end_date')->nullable();
            $table->integer('stock_qty')->default(0)->comment('To check alert qty only, no other usage');
            $table->integer('alert_qty')->default(0);
            $table->boolean('is_published')->default(1);
            $table->integer('min_purchase_qty')->default(1);
            $table->integer('max_purchase_qty')->default(1);
            $table->text('est_delivery_time')->nullable();
            $table->boolean('has_emi')->default(0);
            $table->text('emi_info')->nullable();
            $table->boolean('has_variation')->default(1);
            $table->boolean('has_warranty')->default(1);
            $table->text('warranty_info')->nullable();
            $table->double('total_sale_count')->default(0.00);
            $table->text('size_guides')->nullable();
            $table->integer('reward_points')->default(0);
            $table->mediumText('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->string('meta_image')->nullable();
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
        Schema::dropIfExists('products');
    }
};
