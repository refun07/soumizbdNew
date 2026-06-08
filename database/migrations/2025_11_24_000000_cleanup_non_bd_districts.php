<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Zone;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Find Bangladesh ID
        $bd = Country::where('name', 'Bangladesh')->first();

        if ($bd) {
            $bdId = $bd->id;

            // 2. Get IDs of states that are NOT Bangladesh
            $nonBdStateIds = State::where('country_id', '!=', $bdId)->pluck('id');

            if ($nonBdStateIds->count() > 0) {
                // 3. Delete cities belonging to these states
                City::whereIn('state_id', $nonBdStateIds)->delete();

                // 4. Delete the states
                State::whereIn('id', $nonBdStateIds)->delete();
            }

            // 5. Create "Outside Dhaka" zone if it doesn't exist
            $zone = Zone::where('name', 'Outside Dhaka')->first();
            if (!$zone) {
                $zone = new Zone();
                $zone->name = 'Outside Dhaka';
                $zone->is_active = 1;
                $zone->save();
            }

            // 6. Make all remaining States active
            State::query()->update(['is_active' => 1]);

            // 7. Make all remaining Cities active and assign to the new zone
            City::query()->update([
                'is_active' => 1,
                'zone_id' => $zone->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot easily reverse deletion without backup
    }
};
