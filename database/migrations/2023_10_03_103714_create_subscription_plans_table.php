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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price')->default(0.00);
            $table->double('admin_commission_percentage')->default(0.00);
            $table->boolean('has_cat_wise_commission')->default(0);
            $table->integer('validity_in_days')->default(30);
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
        Schema::dropIfExists('subscription_plans');
    }
};
