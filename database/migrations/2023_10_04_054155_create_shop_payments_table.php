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
        Schema::create('shop_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id');
            $table->double('demanded_amount')->default(0.00);
            $table->longText('additional_info')->nullable();
            $table->double('given_amount')->default(0.00);
            $table->text('document_of_proof')->nullable();
            $table->string('payment_method')->nullable();
            $table->longText('payment_details')->nullable();
            $table->string('status')->default('requested')->comment('requested/paid/cancelled');
            $table->longText('reason_for_cancellation')->nullable();
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
        Schema::dropIfExists('shop_payments');
    }
};
