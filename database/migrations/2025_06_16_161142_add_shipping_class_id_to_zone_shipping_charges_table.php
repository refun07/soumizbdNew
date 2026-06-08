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
            $table->foreignId('shipping_class_id')->after('zone_id')
                ->nullable()
                ->constrained('shipping_classes')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_shipping_charges', function (Blueprint $table) {
            $table->dropForeign(['shipping_class_id']);
            $table->dropColumn('shipping_class_id');
        });
    }
};
