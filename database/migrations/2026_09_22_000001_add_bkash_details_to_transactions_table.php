<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('bkash_account_number', 20)->nullable()->after('payment_method');
            $table->string('bkash_transaction_id', 30)->nullable()->after('bkash_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['bkash_account_number', 'bkash_transaction_id']);
        });
    }
};
