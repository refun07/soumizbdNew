<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages')->delete();

        \DB::table('languages')->insert(array(
            array(
                'id'        => 1,
                'name'      => 'English',
                'flag'      => 'en',
                'code'      => 'en-US',
                'is_rtl'    => 0,
                'is_active' => 1
            ),
        ));
    }
}
