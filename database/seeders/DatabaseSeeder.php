<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ShopsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(VariationsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(BangladeshGeoSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);

        // Run development seeders only in development environment
        if (app()->environment('local', 'testing', 'development')) {
            $this->call(ProductSeeder::class);
            $this->call(ProductVariationsTableSeeder::class);
            $this->call(OrdersTableSeeder::class);
            $this->call(DevDataSeeder::class);
        }
    }
}
