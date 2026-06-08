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
        Schema::create('checkout_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->bigInteger('guest_user_id')->nullable();
            $table->index(['user_id']);
            $table->index(['guest_user_id']);
            
            // Customer Details
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('customer_note')->nullable();
            
            // Address Data
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->text('address_line')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address_type')->default('home');
            $table->string('direction')->nullable();
            
            // Checkout Progress
            $table->string('payment_method')->nullable();
            $table->string('current_step')->default('details'); // details, shipping, payment
            $table->json('cart_data')->nullable(); // Snapshot of cart items
            $table->decimal('cart_total', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            
            // Status
            $table->boolean('is_converted')->default(false); // Became an order?
            $table->timestamp('converted_at')->nullable();
            $table->string('order_code')->nullable(); // If converted
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_sessions');
    }
};
