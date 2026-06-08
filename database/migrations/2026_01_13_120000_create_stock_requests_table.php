<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('guest_user_id')->nullable()->comment('Same as cart guest_user_id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_variation_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('product_image')->nullable();
            $table->json('product_details')->nullable()->comment('Additional product info');
            $table->integer('quantity')->default(1);
            $table->text('customer_note')->nullable();
            $table->integer('status')->default(0)->comment('0=pending, 1=fulfilled, 2=cancelled');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'guest_user_id', 'product_id', 'product_variation_id', 'status'], 'stock_request_lookup_idx');
            $table->index(['product_id', 'status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
