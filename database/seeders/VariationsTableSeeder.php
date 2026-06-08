<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VariationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('variations')->delete();

        \DB::table('variations')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Color',
                'is_active' => 1,
                'is_color' => 1,
                'created_at' => '2022-12-05 13:06:30',
                'updated_at' => '2022-12-05 13:06:30',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Size',
                'is_active' => 1,
                'is_color' => 0,
                'created_at' => '2022-12-05 13:06:30',
                'updated_at' => '2022-12-05 13:06:30',
            ),
        ));
    }
}
