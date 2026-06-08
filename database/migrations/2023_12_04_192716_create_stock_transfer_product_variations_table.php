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
        Schema::create('stock_transfer_product_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_transfer_id');
            $table->integer('product_id');
            $table->integer('product_variation_id');
            $table->integer('qty');
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
        Schema::dropIfExists('stock_transfer_product_variations');
    }
};
