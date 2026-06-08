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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->nullable();
            $table->string('type')->default('private')->comment('private/mega');
            $table->text('name');
            $table->text('slug')->nullable();
            $table->text('short_description')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->string('banner')->nullable();
            $table->text('start_date')->nullable();
            $table->text('end_date')->nullable();
            $table->string('default_discount_type')->default('flat')->comment('flat/percentage');
            $table->double('default_discount_value')->default(0.00);
            $table->boolean('is_published')->default(1);
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
        Schema::dropIfExists('campaigns');
    }
};
