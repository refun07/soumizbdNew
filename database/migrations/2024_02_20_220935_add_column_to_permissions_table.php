<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('group_name')->after('name');
                $table->string('format')->after('group_name')->nullable();
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
            Schema::table('permissions', function (Blueprint $table) {
                $columns = ['group_name', 'format'];
                $table->dropColumn($columns);
            });
        } catch (\Throwable $th) {
            // throw $th;
        }
    }
}
