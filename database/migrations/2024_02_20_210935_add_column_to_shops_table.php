<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('shops', function (Blueprint $table) {
                $table->mediumText('meta_title')->after('bank_routing_no')->nullable();
                $table->longText('meta_description')->after('meta_title')->nullable();
                $table->longText('meta_keywords')->after('meta_description')->nullable();
                $table->string('meta_image')->after('meta_keywords')->nullable();
            });
        } catch (\Throwable $th) {
            // throw $th;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::table('shops', function (Blueprint $table) {
                $columns = ['meta_title', 'meta_description', 'meta_keywords', 'meta_image'];
                $table->dropColumn($columns);
            });
        } catch (\Throwable $th) {
            // throw $th;
        }
    }
}
