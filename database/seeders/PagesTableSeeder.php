<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('pages')->delete();

        \DB::table('pages')->insert(array(
            array(
                'id'        => 1,
                'type'      => 'special',
                'title'     => 'Homepage',
                'slug'      => 'homepage',
                'is_active' => 1,
            ),
            array(
                'id'        => 2,
                'type'      => 'default',
                'title'     => 'Return Policy',
                'slug'      => 'return-policy',
                'is_active' => 1,
            ),
            array(
                'id'        => 3,
                'type'      => 'default',
                'title'     => 'Warranty Policy',
                'slug'      => 'warranty-policy',
                'is_active' => 1,
            ),
            array(
                'id'        => 4,
                'type'      => 'default',
                'title'     => 'Terms and Conditions',
                'slug'      => 'terms-and-conditions',
                'is_active' => 1,
            ),
            array(
                'id'        => 5,
                'type'      => 'default',
                'title'     => 'Privacy Policy',
                'slug'      => 'privacy-policy',
                'is_active' => 1,
            ),
        ));
    }
}
