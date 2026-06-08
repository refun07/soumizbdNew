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
        Schema::create('return_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained('return_requests')->onDelete('cascade');
            $table->foreignId('product_variation_id')->constrained('product_variations')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('reason');
            $table->text('note')->nullable();
            $table->integer('status')->default(0)->comment('0=pending, 1=approved, 2=rejected, 3=processing, 4=completed');
            $table->boolean('is_paid')->default(false);
            $table->string('media_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_request_items');
    }
};
