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
        Schema::table('zone_shipping_charges', function (Blueprint $table) {
            $table->integer('mrate')->nullable()->after('charge')->comment('Multiple rate - charge multiplier based on quantity. NULL means disabled.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_shipping_charges', function (Blueprint $table) {
            $table->dropColumn('mrate');
        });
    }
};
