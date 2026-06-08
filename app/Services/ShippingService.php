<?php

namespace App\Services;

use App\Models\City;
use App\Models\Coupon;
use App\Models\Shop;
use App\Models\ZoneShippingCharge;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Calculate total shipping charge for cart items grouped by shop and shipping class.
     *
     * Supports mrate (multiple rate) for quantity-based shipping charges.
     * If mrate is NULL/0, charge is applied once per shipping class (backward compatible).
     * If mrate > 0, charge is multiplied by ceil(total_quantity / mrate).
     *
     * @param  array  $cartItems  Array of ['shop_id' => int, 'shipping_class_id' => int|null, 'quantity' => int]
     */
    public static function calculate(int $cityId, array $cartItems, array $couponCodes = []): float
    {
        Log::debug('[SHIPPING-SERVICE] calculate() called', [
            'city_id' => $cityId,
            'cart_items_count' => count($cartItems),
            'cart_items' => $cartItems,
            'coupon_codes' => $couponCodes,
        ]);

        if (empty($cartItems)) {
            Log::debug('[SHIPPING-SERVICE] Empty cart items, returning 0');

            return 0.0;
        }

        $zoneId = City::findOrFail($cityId)->zone_id;
        Log::debug('[SHIPPING-SERVICE] Zone ID resolved', ['zone_id' => $zoneId]);

        $coupons = collect($couponCodes)->isNotEmpty()
            ? Coupon::whereIn('code', $couponCodes)->get()
            : collect();

        Log::debug('[SHIPPING-SERVICE] Coupons found', [
            'coupon_count' => $coupons->count(),
            'coupons' => $coupons->pluck('code', 'shop_id')->toArray(),
        ]);

        $totalShippingCharge = 0.0;

        $groupedItems = self::groupItemsByShopAndClass($cartItems);

        Log::debug('[SHIPPING-SERVICE] Items grouped', [
            'shop_count' => count($groupedItems),
            'grouped_items' => $groupedItems,
        ]);

        foreach ($groupedItems as $shopId => $byClass) {
            Log::debug('[SHIPPING-SERVICE] Processing shop', ['shop_id' => $shopId]);

            foreach ($byClass as $key => $data) {
                $classId = $data['shipping_class_id'];
                $items = $data;

                Log::debug('[SHIPPING-SERVICE] Processing shipping class', [
                    'shop_id' => $shopId,
                    'shipping_class_id' => $classId,
                    'total_quantity' => $items['total_quantity'],
                    'items' => $items['items'],
                ]);

                $isFreeShipping = $coupons->first(fn ($c) => $c->shop_id == $shopId && $c->is_free_shipping);
                if ($isFreeShipping) {
                    Log::debug('[SHIPPING-SERVICE] Free shipping coupon found, skipping', [
                        'shop_id' => $shopId,
                        'coupon' => $isFreeShipping->code,
                    ]);

                    continue;
                }

                $query = ZoneShippingCharge::where('shop_id', $shopId)
                    ->where('zone_id', $zoneId);

                if (is_null($classId)) {
                    $query->whereNull('shipping_class_id');
                } else {
                    $query->where('shipping_class_id', $classId);
                }

                $zoneData = $query->select('charge', 'mrate')->first();

                Log::debug('[SHIPPING-SERVICE] Zone shipping charge lookup', [
                    'shop_id' => $shopId,
                    'zone_id' => $zoneId,
                    'shipping_class_id' => $classId,
                    'zone_data' => $zoneData ? ['charge' => $zoneData->charge, 'mrate' => $zoneData->mrate] : null,
                ]);

                $baseCharge = null;
                $mrate = null;

                if ($zoneData) {
                    $baseCharge = (float) $zoneData->charge;
                    $mrate = $zoneData->mrate;
                    Log::debug('[SHIPPING-SERVICE] Using zone shipping charge', [
                        'base_charge' => $baseCharge,
                        'mrate' => $mrate,
                    ]);
                } else {
                    $defaultCharge = Shop::where('id', $shopId)->value('default_shipping_charge');
                    $baseCharge = $defaultCharge !== null ? (float) $defaultCharge : 0.0;
                    Log::debug('[SHIPPING-SERVICE] No zone charge found, using shop default', [
                        'default_charge' => $baseCharge,
                    ]);
                }

                $totalQuantity = $items['total_quantity'];

                if ($mrate !== null && $mrate > 0) {
                    $batchCount = (int) ceil($totalQuantity / $mrate);
                    Log::debug('[SHIPPING-SERVICE] mrate enabled', [
                        'mrate' => $mrate,
                        'total_quantity' => $totalQuantity,
                        'batch_count' => $batchCount,
                    ]);
                } else {
                    $batchCount = 1;
                    Log::debug('[SHIPPING-SERVICE] mrate disabled (null or 0)', [
                        'mrate' => $mrate,
                        'batch_count' => $batchCount,
                    ]);
                }

                $chargeForThisClass = $baseCharge * $batchCount;
                $totalShippingCharge += $chargeForThisClass;

                Log::debug('[SHIPPING-SERVICE] Charge calculated for class', [
                    'shop_id' => $shopId,
                    'shipping_class_id' => $classId,
                    'base_charge' => $baseCharge,
                    'batch_count' => $batchCount,
                    'charge_for_class' => $chargeForThisClass,
                    'running_total' => $totalShippingCharge,
                ]);
            }
        }

        Log::debug('[SHIPPING-SERVICE] Final result', ['total_shipping_charge' => $totalShippingCharge]);

        return $totalShippingCharge;
    }

    /**
     * Group cart items by shop_id and shipping_class_id with summed quantities.
     */
    private static function groupItemsByShopAndClass(array $cartItems): array
    {
        $grouped = [];

        foreach ($cartItems as $item) {
            $shopId = $item['shop_id'];
            $shippingClassId = $item['shipping_class_id'] ?? null;
            $quantity = $item['quantity'] ?? 1;

            // Use a marker for null shipping class ID to preserve it as a key
            $key = $shippingClassId === null ? '__NULL_CLASS__' : $shippingClassId;

            if (! isset($grouped[$shopId][$key])) {
                $grouped[$shopId][$key] = [
                    'shipping_class_id' => $shippingClassId,
                    'total_quantity' => 0,
                    'items' => [],
                ];
            }

            $grouped[$shopId][$key]['total_quantity'] += $quantity;
            $grouped[$shopId][$key]['items'][] = $item;
        }

        return $grouped;
    }
}
