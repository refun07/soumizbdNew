<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_exchange_order')->default(false)->after('pos_order');
            $table->unsignedBigInteger('original_order_id')->nullable()->after('is_exchange_order');

            $table->foreign('original_order_id')->references('id')->on('orders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['original_order_id']);
            $table->dropColumn(['is_exchange_order', 'original_order_id']);
        });
    }
};
