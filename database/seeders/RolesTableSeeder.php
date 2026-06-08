<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();
        // Reset the auto-increment counter to 1
        \DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1');

        \DB::table('roles')->insert([
            0 => [
                'id' => '1',
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'is_active' => 1,
            ],
            1 => [
                'id' => '2',
                'name' => 'Staff',
                'guard_name' => 'web',
                'is_active' => 1,
            ],
            2 => [
                'id' => '3',
                'name' => 'Moderator',
                'guard_name' => 'web',
                'is_active' => 1,
            ],
        ]);
    }
}
