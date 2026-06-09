<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ShippingClass;
use App\Models\Shop;
use App\Models\Zone;
use App\Models\ZoneShippingCharge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutShippingSeeder extends Seeder
{
    /**
     * Replace existing shipping classes with one standard class per shop and
     * configure the two customer-selectable checkout delivery zones.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $deliveryAreas = [
                'Inside Dhaka' => 70.00,
                'Outside Dhaka' => 150.00,
            ];

            $zones = collect($deliveryAreas)->mapWithKeys(function ($charge, $name) {
                $zone = Zone::withTrashed()->updateOrCreate(
                    ['name' => $name],
                    [
                        'slug' => Str::slug($name),
                        'is_active' => 1,
                        'deleted_at' => null,
                    ]
                );

                return [$name => $zone];
            });

            Product::query()->update(['shipping_class_id' => null]);
            ZoneShippingCharge::query()->whereNotNull('shipping_class_id')->delete();
            ShippingClass::query()->delete();

            Shop::query()->each(function (Shop $shop) use ($deliveryAreas, $zones) {
                $shippingClass = ShippingClass::create([
                    'shop_id' => $shop->id,
                    'name' => 'Standard Delivery',
                    'description' => 'Standard delivery pricing for Inside Dhaka and Outside Dhaka.',
                    'is_active' => 1,
                ]);

                Product::query()
                    ->where('shop_id', $shop->id)
                    ->update(['shipping_class_id' => $shippingClass->id]);

                foreach ($deliveryAreas as $zoneName => $charge) {
                    ZoneShippingCharge::updateOrCreate(
                        [
                            'shop_id' => $shop->id,
                            'zone_id' => $zones[$zoneName]->id,
                            'shipping_class_id' => $shippingClass->id,
                        ],
                        [
                            'charge' => $charge,
                            // NULL disables quantity multiplication and charges once per class.
                            'mrate' => null,
                            'deleted_at' => null,
                        ]
                    );
                }
            });
        });

        $this->command?->info(
            'Checkout shipping configured: Standard Delivery class assigned to all products; Inside Dhaka 70, Outside Dhaka 150; mrate disabled.'
        );
    }
}
