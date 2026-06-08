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
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('invoice_no')->index();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('request_type')->default('return')->comment('return, refund, exchange');
            $table->integer('status')->comment('0=pending,1=processing, 2=approved, 3=rejected,  4=completed')->default(0);
            $table->integer('requested_by')->comment('0=Admin', '1=Customer', '2=Delivery Partner');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
