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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->integer('shop_id')->default(1);
            $table->integer('parent_id')->nullable();
            $table->integer('level')->comment('level of the category');
            $table->integer('sorting_order_level')->default(0);
            $table->text('thumbnail_image')->nullable();
            $table->text('icon')->nullable();
            $table->integer('total_sale_count')->default(0);
            $table->double('total_sale_amount')->default(0.00);
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
        Schema::dropIfExists('categories');
    }
};
