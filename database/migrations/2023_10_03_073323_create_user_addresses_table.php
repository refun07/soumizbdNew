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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('country_id');
            $table->integer('state_id');
            $table->integer('city_id');
            $table->integer('area_id');
            $table->string('postal_code')->nullable();
            $table->longText('address');
            $table->longText('direction')->nullable();
            $table->string('type')->nullable()->comment('home/office');
            $table->boolean('is_default')->default(0)->comment('only one can be default');
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
        Schema::dropIfExists('user_addresses');
    }
};
