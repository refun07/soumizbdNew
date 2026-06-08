<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->delete();
        \DB::table('user_addresses')->delete();

        \DB::table('users')->insert(array(

            array('id' => '1', 'role_id' => NULL, 'user_type' => 'admin', 'name' => 'Admin', 'email' => 'admin@epikcart.com', 'phone' => '01511240995', 'email_or_otp_verified' => '0', 'verification_code' => NULL, 'new_email_verification_code' => NULL, 'password' => '$2y$10$PdcHDGQ8NoI4wlSPrcGz6Olnaj7vuPmtve9CcGIxIduLPEU1x3c76', 'remember_token' => NULL, 'provider_id' => NULL, 'avatar' => '6', 'postal_code' => '10001', 'user_balance' => '0', 'is_banned' => '0', 'shop_id' => '1', 'email_verified_at' => NULL, 'created_at' => NULL, 'updated_at' => '2023-03-12 05:32:38', 'deleted_at' => NULL),
            array('id' => '2', 'role_id' => NULL, 'user_type' => 'customer', 'name' => 'Customer 1', 'email' => 'customer1@epikcart.com', 'phone' => '01572797380', 'email_or_otp_verified' => '0', 'verification_code' => NULL, 'new_email_verification_code' => NULL, 'password' => '$2y$10$PdcHDGQ8NoI4wlSPrcGz6Olnaj7vuPmtve9CcGIxIduLPEU1x3c76', 'remember_token' => NULL, 'provider_id' => NULL, 'avatar' => '6', 'postal_code' => '10001', 'user_balance' => '0', 'is_banned' => '0', 'shop_id' => '1', 'email_verified_at' => NULL, 'created_at' => NULL, 'updated_at' => '2023-03-12 05:32:38', 'deleted_at' => NULL),
            array('id' => '3', 'role_id' => '2', 'user_type' => 'staff', 'name' => 'Staff 1', 'email' => 'staff@epikcart.com', 'phone' => '01572797381', 'email_or_otp_verified' => '0', 'verification_code' => NULL, 'new_email_verification_code' => NULL, 'password' => '$2y$10$ErK8Z1couSrDJXHGLI60rucFsGvnHlEiC9HhZM4wcmIJQxgkayBZi', 'remember_token' => NULL, 'provider_id' => NULL, 'avatar' => '6', 'postal_code' => '10001', 'user_balance' => '0', 'is_banned' => '0', 'shop_id' => '1', 'email_verified_at' => NULL, 'created_at' => NULL, 'updated_at' => '2023-03-12 05:32:38', 'deleted_at' => NULL),
            array('id' => '4', 'role_id' => '3', 'user_type' => 'staff', 'name' => 'Moderator 1', 'email' => 'moderator@epikcart.com', 'phone' => '01572797382', 'email_or_otp_verified' => '0', 'verification_code' => NULL, 'new_email_verification_code' => NULL, 'password' => '$2y$10$ErK8Z1couSrDJXHGLI60rucFsGvnHlEiC9HhZM4wcmIJQxgkayBZi', 'remember_token' => NULL, 'provider_id' => NULL, 'avatar' => '6', 'postal_code' => '10001', 'user_balance' => '0', 'is_banned' => '0', 'shop_id' => '1', 'email_verified_at' => NULL, 'created_at' => NULL, 'updated_at' => '2023-03-12 05:32:38', 'deleted_at' => NULL),
        ));

        // Add default address for admin user
        \DB::table('user_addresses')->insert(array(
            array(
                'user_id' => '1',
                'country_id' => '1',
                'state_id' => '1',
                'city_id' => '1',
                'area_id' => '1',
                'postal_code' => '10001',
                'address' => '123 Admin Street, Suite 100',
                'direction' => 'Near Main Office Building',
                'type' => 'office',
                'is_default' => '1',
                'created_by' => '1',
                'created_at' => '2023-03-12 05:32:38',
                'updated_by' => '1',
                'updated_at' => '2023-03-12 05:32:38',
                'deleted_at' => NULL,
                'deleted_by' => NULL,
            ),
            array(
                'user_id' => '2',
                'country_id' => '1',
                'state_id' => '1',
                'city_id' => '1',
                'area_id' => '1',
                'postal_code' => '10001',
                'address' => '456 Customer Avenue, Apt 200',
                'direction' => 'Near Shopping Center',
                'type' => 'home',
                'is_default' => '1',
                'created_by' => '1',
                'created_at' => '2023-03-12 05:32:38',
                'updated_by' => '1',
                'updated_at' => '2023-03-12 05:32:38',
                'deleted_at' => NULL,
                'deleted_by' => NULL,
            ),
            array(
                'user_id' => '3',
                'country_id' => '1',
                'state_id' => '1',
                'city_id' => '1',
                'area_id' => '1',
                'postal_code' => '10001',
                'address' => '789 Staff Lane, Office 300',
                'direction' => 'Near Warehouse',
                'type' => 'office',
                'is_default' => '1',
                'created_by' => '1',
                'created_at' => '2023-03-12 05:32:38',
                'updated_by' => '1',
                'updated_at' => '2023-03-12 05:32:38',
                'deleted_at' => NULL,
                'deleted_by' => NULL,
            ),
            array(
                'user_id' => '4',
                'country_id' => '1',
                'state_id' => '1',
                'city_id' => '1',
                'area_id' => '1',
                'postal_code' => '10001',
                'address' => '101 Moderator Blvd, Office 400',
                'direction' => 'Near Admin Building',
                'type' => 'office',
                'is_default' => '1',
                'created_by' => '1',
                'created_at' => '2023-03-12 05:32:38',
                'updated_by' => '1',
                'updated_at' => '2023-03-12 05:32:38',
                'deleted_at' => NULL,
                'deleted_by' => NULL,
            ),
        ));
    }
}
