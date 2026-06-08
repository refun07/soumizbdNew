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
        Schema::create('order_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('guest_user_id')->nullable();
            $table->bigInteger('code');
            $table->text('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternative_phone')->nullable();
            $table->string('note')->nullable();
            $table->integer('shipping_address_id')->nullable();
            $table->integer('billing_address_id')->nullable();
            $table->longText('shipping_address_type')->nullable();
            $table->longText('shipping_address')->nullable();
            $table->longText('billing_address_type')->nullable();
            $table->longText('billing_address')->nullable();
            $table->longText('direction')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->boolean('is_pos_order')->default(0);
            $table->longText('pos_order_address')->nullable();
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
        Schema::dropIfExists('order_groups');
    }
};
