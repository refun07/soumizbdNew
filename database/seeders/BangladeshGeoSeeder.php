<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BangladeshGeoSeeder extends Seeder
{
    /**
     * Seed Bangladesh geography using:
     * Division -> Zone, District -> State, Upazila/City -> City.
     */
    public function run(): void
    {
        $rows = require database_path('seeders/data/bangladesh_locations.php');

        if (!is_array($rows) || empty($rows)) {
            $this->command?->warn('BangladeshGeoSeeder: no rows found in data file.');
            return;
        }

        // Reset previous delivery/location data so only the provided dataset remains.
        DB::transaction(function () {
            DB::table('zone_shipping_charges')->delete();
            DB::table('warehouse_zones')->delete();
            DB::table('areas')->delete();
            DB::table('cities')->delete();
            DB::table('states')->delete();
            DB::table('zones')->delete();
            DB::table('countries')->delete();
        });

        $country = new Country();

        $country->code = 'BD';
        $country->name = 'Bangladesh';
        $country->is_active = 1;
        $country->save();

        $lastDivision = null;
        $lastDistrict = null;

        $zoneCache = [];
        $stateCache = [];

        $createdOrUpdatedCities = 0;
        $skippedRows = 0;

        foreach ($rows as $index => $row) {
            $division = trim((string)($row['division'] ?? ''));
            $district = trim((string)($row['district'] ?? ''));
            $cityName = trim((string)($row['city'] ?? ''));

            if ($division === '') {
                $division = $lastDivision;
            } else {
                $lastDivision = $division;
            }

            if ($district === '') {
                $district = $lastDistrict;
            } else {
                $lastDistrict = $district;
            }

            if (!$division || !$district || !$cityName) {
                $skippedRows++;
                $this->command?->warn('BangladeshGeoSeeder: skipped row '.($index + 1).' due to missing division/district/city.');
                continue;
            }

            if (!isset($zoneCache[$division])) {
                $zone = Zone::firstOrCreate(
                    ['name' => $division],
                    [
                        'slug' => Str::slug($division),
                        'is_active' => 1,
                    ]
                );

                if ((int)$zone->is_active !== 1) {
                    $zone->is_active = 1;
                    $zone->save();
                }

                $zoneCache[$division] = $zone;
            }

            $stateKey = $country->id.'|'.$district;
            if (!isset($stateCache[$stateKey])) {
                $state = State::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $district,
                    ],
                    ['is_active' => 1]
                );

                $stateCache[$stateKey] = $state;
            }

            City::updateOrCreate(
                [
                    'state_id' => $stateCache[$stateKey]->id,
                    'name' => $cityName,
                ],
                [
                    'zone_id' => $zoneCache[$division]->id,
                    'is_active' => 1,
                ]
            );

            $createdOrUpdatedCities++;
        }

        $this->command?->info("BangladeshGeoSeeder: previous location data cleared, Bangladesh seeded with {$createdOrUpdatedCities} cities, {$skippedRows} rows skipped.");
    }
}
