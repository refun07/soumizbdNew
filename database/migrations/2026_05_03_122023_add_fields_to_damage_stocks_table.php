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
        Schema::table('damage_stocks', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->after('product_variation_id')->constrained('warehouses')->onDelete('set null');
            $table->string('damage_type')->default('warehouse')->after('quantity')->comment('warehouse,transportation,expired,manufacturing_defect');
            $table->text('notes')->nullable()->after('reason');
            $table->foreignId('created_by')->nullable()->after('return_date')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable()->after('deleted_at')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('damage_stocks', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['deleted_by']);
            $table->dropColumn([
                'shop_id',
                'warehouse_id',
                'damage_type',
                'notes',
                'created_by',
                'updated_by',
                'deleted_at',
                'deleted_by',
            ]);
        });
    }
};
