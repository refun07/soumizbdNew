<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeatureFlag::updateOrCreate(['key' => 'color_image_enabled'], [
            'is_enabled' => false,
            'description' => 'Show color images instead of color swatches on product page'
        ]);
    }
}
