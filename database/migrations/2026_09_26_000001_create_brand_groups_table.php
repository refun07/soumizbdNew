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
        if (!Schema::hasTable('brand_groups')) {
            Schema::create('brand_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->index();
                $table->text('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->integer('created_by')->nullable();
                $table->timestamps();
                $table->integer('updated_by')->nullable();
                $table->softDeletes();
                $table->integer('deleted_by')->nullable();
            });
        }

        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'brand_group_id')) {
                $table->unsignedBigInteger('brand_group_id')->nullable()->index()->after('slug');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'brand_group_id')) {
                $table->dropIndex(['brand_group_id']);
                $table->dropColumn('brand_group_id');
            }
        });

        Schema::dropIfExists('brand_groups');
    }
};
