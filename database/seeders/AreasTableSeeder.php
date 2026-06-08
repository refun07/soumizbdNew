<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AreasTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('areas')->delete();

        \DB::table('areas')->insert(
            array(
                array('id' => '1', 'city_id' => '7291', 'zone_id' => '1', 'name' => 'Area One', 'is_active' => 1, 'created_by' => NULL, 'created_at' => NULL, 'updated_at' => '2023-12-10 13:03:08', 'updated_by' => NULL, 'deleted_at' => NULL, 'deleted_by' => NULL),

                array('id' => '2', 'city_id' => '7291', 'zone_id' => '2', 'name' => 'Area Two', 'is_active' => 1, 'created_by' => NULL, 'created_at' => NULL, 'updated_at' => '2023-12-10 13:03:09', 'updated_by' => NULL, 'deleted_at' => NULL, 'deleted_by' => NULL)
            )
        );
    }
}
