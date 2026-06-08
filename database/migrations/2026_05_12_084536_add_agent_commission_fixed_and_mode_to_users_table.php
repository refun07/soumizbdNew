<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->double('agent_commission_fixed_amount')->default(0.00)->after('agent_commission_percentage');
            $table->string('agent_commission_mode')->default('percentage')->after('agent_commission_fixed_amount');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['agent_commission_fixed_amount', 'agent_commission_mode']);
        });
    }
};
