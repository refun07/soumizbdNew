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
        Schema::create('shop_sections', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id');
            $table->integer('order')->default(0);
            $table->string('type')->comment('full-width-banner/box-width-banner/products');
            $table->string('title')->nullable();
            $table->longText('section_values')->nullable();
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
        Schema::dropIfExists('shop_sections');
    }
};
