<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Models\WarehouseZone;
use App\Models\Zone;
use App\Models\ZoneShippingCharge;

class WarehouseService
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
        $warehouses     = Warehouse::shop()->latest();

        if ($request->search != null) {
            $warehouses     = $warehouses->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }
        $warehouses = $warehouses->paginate($limit);

        $hasWarehousesOfZoneIds = WarehouseZone::shop()->pluck('zone_id');
        $zones                  = Zone::isActive()->whereNotIn('id', $hasWarehousesOfZoneIds)->get();

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'warehouses'    => $warehouses,
                'zones'          => $zones,
                'searchKey'     => $searchKey,
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
            $existingCount = Warehouse::where('shop_id', shopId())->count();
            $warehouse = new Warehouse;
            $warehouse->name                 = $request->name;
            $warehouse->shop_id              = shopId();
            $warehouse->address              = $request->address;
            $warehouse->description          = $request->description;
            $warehouse->thumbnail_image      = $request->thumbnail_image;

            if ($existingCount < 1) {
                $warehouse->is_default       = 1;
            }
            $warehouse->save();

            # warehouse zones
            $zone_ids            = $request->zone_ids;
            $warehouseZonesData  = array();
            if ($request->zone_ids) {
                foreach ($request->zone_ids as $zone_id) {
                    array_push($warehouseZonesData, [
                        'shop_id'      => shopId(),
                        'warehouse_id' => $warehouse->id
                    ]);
                }
            }

            $warehouseZones = array_combine($zone_ids ?? [], $warehouseZonesData);
            $warehouse->zones()->sync($warehouseZones);

            # shipping charges for these zones
            if ($request->zone_ids && count($request->zone_ids) > 0) {
                foreach ($request->zone_ids as $key => $zoneId) {
                    $zoneShippingCharge = ZoneShippingCharge::where('shop_id', shopId())->firstOrNew(['zone_id' => $zoneId]);
                    $zoneShippingCharge->shop_id = shopId();
                    $zoneShippingCharge->charge = shop()->default_shipping_charge;
                    $zoneShippingCharge->save();
                }
            }
            $data = [
                'status'    => 200,
                'message'   => translate('Warehouse has been added successfully'),
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

    # return view of edit form
    public static function edit($request, $id)
    {
        try {
            $lang_key               = $request->lang_key;
            $warehouse              = Warehouse::findOrFail($id);

            $hasWarehousesOfZoneIds = WarehouseZone::where('shop_id', shopId())->whereNot('warehouse_id', $warehouse->id)->pluck('zone_id');
            $zones                  = Zone::isActive()->whereNotIn('id', $hasWarehousesOfZoneIds)->get();

            $warehouseZoneIds = $warehouse->warehouseZones()->pluck('zone_id')->toArray();

            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'zones'             => $zones,
                    'warehouse'         => $warehouse,
                    'warehouseZoneIds'  => $warehouseZoneIds,
                    'lang_key'          => $lang_key,
                ],
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

    # add new data
    public static function update($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $warehouse                       = Warehouse::findOrFail((int) $id);
            $warehouse->shop_id              = shopId();
            $warehouse->name                 = $request->name;
            $warehouse->address              = $request->address;
            $warehouse->description          = $request->description;
            $warehouse->thumbnail_image      = $request->thumbnail_image;

            $warehouse->save();

            # warehouse zones
            $zone_ids            = $request->zone_ids;
            $warehouseZonesData  = array();
            if ($request->zone_ids) {
                foreach ($request->zone_ids as $zone_id) {
                    array_push($warehouseZonesData, [
                        'shop_id'      => shopId(),
                        'warehouse_id' => $warehouse->id
                    ]);
                }
            }

            $warehouseZones = array_combine($zone_ids ?? [], $warehouseZonesData);
            $warehouse->zones()->sync($warehouseZones);

            $data = [
                'status'    => 200,
                'message'   => translate('Warehouse has been updated successfully'),
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

    # delete data
    public static function destroy($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->zones()->detach();

            $warehouse->delete();

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Warehouse has been deleted successfully'),
                'result'    => null
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # update status
    public static function updateStatus($request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];

        try {
            $warehouse = Warehouse::findOrFail($request->id);
            $warehouse->is_active = !$warehouse->is_active;
            $warehouse->save();

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # update default
    public static function updateDefault($request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];

        try {
            Warehouse::where('shop_id', shopId())->update([
                'is_default'    => 0
            ]);

            $warehouse = Warehouse::findOrFail($request->id);
            $warehouse->is_default = $request->isDefault;
            $warehouse->save();
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
