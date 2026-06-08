<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_commission_histories', function (Blueprint $table) {
            $table->double('commission_fixed_amount')->default(0.00)->after('commission_percentage');
            $table->string('commission_mode_used')->nullable()->after('commission_fixed_amount');
        });
    }

    public function down(): void
    {
        Schema::table('agent_commission_histories', function (Blueprint $table) {
            $table->dropColumn(['commission_fixed_amount', 'commission_mode_used']);
        });
    }
};
