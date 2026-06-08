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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id');
            $table->integer('warehouse_id');
            $table->integer('product_variation_id');
            $table->integer('qty')->default(0);
            $table->integer('expiry_date')->nullable();
            $table->double('purchase_cost')->default(0.00);
            $table->double('selling_price')->default(0.00);
            $table->longText('document')->nullable();
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
        Schema::dropIfExists('stocks');
    }
};
