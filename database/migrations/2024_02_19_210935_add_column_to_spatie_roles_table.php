<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToSpatieRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->boolean('is_active')->after('guard_name')->default(1);
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
        Schema::table('roles', function (Blueprint $table) {
            $columns = ['is_active'];
            $table->dropColumn($columns);
        });
    }
}
