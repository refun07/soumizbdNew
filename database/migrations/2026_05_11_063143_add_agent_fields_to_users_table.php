<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('agent_commission_percentage')->default(0.00)->after('email');
            $table->double('agent_current_balance')->default(0.00)->after('agent_commission_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agent_commission_percentage', 'agent_current_balance']);
        });
    }
};
