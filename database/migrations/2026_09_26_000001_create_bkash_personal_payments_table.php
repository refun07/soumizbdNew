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
        Schema::create('bkash_personal_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_group_id')->constrained('order_groups')->cascadeOnDelete();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();
            $table->string('sender_number', 20);
            $table->string('trx_id', 50)->index();
            $table->decimal('amount', 20, 2)->default(0);
            $table->string('status', 20)->default('pending')->index(); // pending, verified, rejected
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkash_personal_payments');
    }
};
