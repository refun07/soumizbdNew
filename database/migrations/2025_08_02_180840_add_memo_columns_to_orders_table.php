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
        Schema::table('orders', function (Blueprint $table) {
            $table->date('memo_date')->nullable()->after('pos_order');
            $table->string('memo_no')->nullable()->after('memo_date');
            $table->string('mushak_challan_no')->nullable()->after('memo_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['memo_date', 'memo_no', 'mushak_challan_no']);
        });
    }
};
