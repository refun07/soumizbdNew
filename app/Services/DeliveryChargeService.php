<?php

namespace App\Services;

use App\Models\ShippingClass;
use App\Models\Zone;
use App\Models\ZoneShippingCharge;

class DeliveryChargeService
{
    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;
        $limit     = $request->limit ?? perPage();
        $zones     = Zone::latest();
        $shippingClasses = ShippingClass::shop()->latest()->get();

        if ($request->search != null) {
            $zones     = $zones->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }
        $zones = $zones->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'zones'           => $zones,
                'searchKey'       => $searchKey,
                'shippingClasses' => $shippingClasses
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {

            $deliveryCharges = $request->delivery_charges ?? [];
            $mrateData = $request->mrate ?? [];

            foreach ($deliveryCharges as $zoneId => $classCharges) {
                foreach ($classCharges as $classId => $charge) {
                    // Skip if charge is empty or null
                    if ($charge === null || $charge === '') {
                        continue;
                    }

                    // Get mrate for this zone/class combination
                    $mrate = $mrateData[$zoneId][$classId] ?? null;

                    // Create or update
                    ZoneShippingCharge::updateOrCreate(
                        [
                            'zone_id'            => $zoneId,
                            'shipping_class_id'  => $classId,
                            'shop_id'            => shopId(),
                        ],
                        [
                            'charge' => $charge,
                            'mrate'  => $mrate !== null && $mrate !== '' ? $mrate : null,
                        ]
                    );
                }
            }

            $data = [
                'status'    => 200,
                'message'   => translate('Shipping charges have been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
