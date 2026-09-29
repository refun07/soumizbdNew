<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Brand groups (one group per brand) are replaced by collections: a brand can have many
     * collections, a collection can be shared by many brands, and a product can optionally
     * sit in one collection of its brand.
     */
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            if (Schema::hasColumn('brands', 'brand_group_id')) {
                $table->dropIndex(['brand_group_id']);
                $table->dropColumn('brand_group_id');
            }
        });

        Schema::dropIfExists('brand_groups');

        if (!Schema::hasTable('collections')) {
            Schema::create('collections', function (Blueprint $table) {
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

        if (!Schema::hasTable('brand_collection')) {
            Schema::create('brand_collection', function (Blueprint $table) {
                $table->unsignedBigInteger('brand_id');
                $table->unsignedBigInteger('collection_id')->index();
                $table->primary(['brand_id', 'collection_id']);
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'collection_id')) {
                $table->unsignedBigInteger('collection_id')->nullable()->index()->after('brand_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'collection_id')) {
                $table->dropIndex(['collection_id']);
                $table->dropColumn('collection_id');
            }
        });

        Schema::dropIfExists('brand_collection');
        Schema::dropIfExists('collections');

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
};
