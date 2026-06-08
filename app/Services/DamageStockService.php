<?php

namespace App\Services;

use App\Models\DamageStock;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class DamageStockService
{
    public static function index($request, $limit = 15)
    {
        $searchKey = null;

        $damageStocks = DamageStock::with(['productVariation.product', 'warehouse', 'creator']);

        if (auth()->user()->hasRole('Seller')) {
            $damageStocks = $damageStocks->shop();
        }

        if ($request->search != null) {
            $search = $request->search;
            $damageStocks = $damageStocks->whereHas('productVariation.product', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
            $searchKey = $request->search;
        }

        if ($request->damage_type != null) {
            $damageStocks = $damageStocks->where('damage_type', $request->damage_type);
        }

        if ($request->warehouse_id != null) {
            $damageStocks = $damageStocks->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->date_from != null && $request->date_to != null) {
            $damageStocks = $damageStocks->whereBetween('return_date', [$request->date_from, $request->date_to]);
        }

        $damageStocks = $damageStocks->latest()->paginate($limit);

        $warehouses = auth()->user()->hasRole('Seller')
            ? Warehouse::shop()->latest()->get()
            : Warehouse::latest()->get();

        return [
            'status'  => 200,
            'message' => '',
            'result'  => [
                'damageStocks' => $damageStocks,
                'searchKey'    => $searchKey,
                'warehouses'   => $warehouses,
            ],
        ];
    }

    public static function create()
    {
        $warehouses = auth()->user()->hasRole('Seller')
            ? Warehouse::shop()->latest()->get()
            : Warehouse::latest()->get();

        $defaultWarehouseId = null;
        if (!useInventory()) {
            $defaultWarehouse = auth()->user()->hasRole('Seller')
                ? Warehouse::shop()->where('is_default', 1)->first()
                : Warehouse::where('is_default', 1)->first();
            $defaultWarehouseId = $defaultWarehouse?->id;
        }

        return [
            'status'  => 200,
            'message' => '',
            'result'  => [
                'warehouses'          => $warehouses,
                'defaultWarehouseId'  => $defaultWarehouseId,
            ],
        ];
    }

    public static function getProducts($request)
    {
        $warehouseId = $request->warehouseId;

        $productVariations = ProductVariation::whereHas('product', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->searchKey . '%');
            if (auth()->user()->hasRole('Seller')) {
                $q->shop();
            }
        })
            ->whereHas('productVariationStocks', function ($q1) use ($warehouseId) {
                $q1->where('warehouse_id', $warehouseId);
            })
            ->paginate(perPage(20));

        return [
            'status'  => 200,
            'message' => '',
            'result'  => [
                'productVariations' => $productVariations,
                'warehouseId'       => $warehouseId,
            ],
        ];
    }

    public static function store($request)
    {
        DB::beginTransaction();
        try {
            $warehouseId = $request->warehouse_id;
            if (empty($warehouseId) && !useInventory()) {
                $defaultWarehouse = auth()->user()->hasRole('Seller')
                    ? Warehouse::shop()->where('is_default', 1)->first()
                    : Warehouse::where('is_default', 1)->first();
                $warehouseId = $defaultWarehouse?->id;
            }

            foreach ($request->product_variation_ids as $key => $productVariationId) {
                $productVariation = ProductVariation::find((int) $productVariationId);
                if (!$productVariation) {
                    continue;
                }

                $product = $productVariation->product;
                $quantity = (int) $request->quantities[$key];

                $pStock = $productVariation->productVariationStocks()
                    ->where('warehouse_id', $warehouseId)
                    ->first();

                if (!is_null($pStock) && ($pStock->stock_qty - $quantity) < 0) {
                    DB::rollBack();
                    return [
                        'status'  => 403,
                        'message' => translate('Insufficient stock for') . ' ' . $product->collectTranslation('name') . '. ' . translate('Available') . ': ' . $pStock->stock_qty . ', ' . translate('Requested') . ': ' . $quantity,
                        'result'  => [],
                    ];
                }

                $damageStock = new DamageStock;
                $damageStock->shop_id              = shopId();
                $damageStock->product_variation_id = $productVariationId;
                $damageStock->warehouse_id         = $warehouseId;
                $damageStock->quantity             = $quantity;
                $damageStock->damage_type          = $request->damage_types[$key] ?? 'warehouse';
                $damageStock->reason               = $request->reasons[$key] ?? null;
                $damageStock->notes                = $request->notes[$key] ?? null;
                $damageStock->return_date          = $request->damage_date
                    ? \Carbon\Carbon::createFromFormat('d-m-Y', $request->damage_date)->toDateString()
                    : now()->toDateString();
                $damageStock->created_by           = auth()->id();
                $damageStock->save();

                if (!is_null($pStock)) {
                    $pStock->stock_qty -= $quantity;
                    $pStock->save();
                }

                $product->stock_qty -= $quantity;
                $product->save();
            }

            DB::commit();

            return [
                'status'  => 200,
                'message' => translate('Damage stocks have been recorded successfully'),
                'result'  => [],
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Damage Stock Store Error', [
                'user_id'        => auth()->id(),
                'request_data'   => $request->all(),
                'error_message'  => $th->getMessage(),
                'error_file'     => $th->getFile(),
                'error_line'     => $th->getLine(),
            ]);

            return [
                'status'  => 403,
                'message' => translate('Something went wrong'),
                'result'  => [],
            ];
        }
    }

    public static function show($id)
    {
        $damageStock = DamageStock::with(['productVariation.product', 'warehouse', 'creator', 'returnRequest'])
            ->when(
                auth()->user()->hasRole('Seller'),
                fn ($q) => $q->shop()
            )
            ->findOrFail($id);

        return [
            'status'  => 200,
            'message' => '',
            'result'  => [
                'damageStock' => $damageStock,
            ],
        ];
    }

    public static function destroy($id)
    {
        DB::beginTransaction();
        try {
            $damageStock = DamageStock::when(
                auth()->user()->hasRole('Seller'),
                fn ($q) => $q->shop()
            )->findOrFail($id);

            $productVariation = $damageStock->productVariation;
            if ($productVariation) {
                $product = $productVariation->product;

                $pStock = $productVariation->productVariationStocks()
                    ->where('warehouse_id', $damageStock->warehouse_id)
                    ->first();

                if (!is_null($pStock)) {
                    $pStock->stock_qty += $damageStock->quantity;
                    $pStock->save();
                }

                if ($product) {
                    $product->stock_qty += $damageStock->quantity;
                    $product->save();
                }
            }

            $damageStock->deleted_by = auth()->id();
            $damageStock->save();
            $damageStock->delete();

            DB::commit();

            return [
                'status'  => 200,
                'message' => translate('Damage stock has been deleted successfully'),
                'result'  => [],
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Damage Stock Delete Error', [
                'user_id'         => auth()->id(),
                'damage_stock_id' => $id,
                'error_message'   => $th->getMessage(),
            ]);

            return [
                'status'  => 403,
                'message' => translate('Something went wrong'),
                'result'  => [],
            ];
        }
    }

    public static function apiIndex($request)
    {
        $damageStocks = DamageStock::with(['productVariation.product', 'warehouse', 'creator']);

        if (auth()->user()->hasRole('Seller')) {
            $damageStocks = $damageStocks->shop();
        }

        if ($request->damage_type) {
            $damageStocks = $damageStocks->where('damage_type', $request->damage_type);
        }

        if ($request->warehouse_id) {
            $damageStocks = $damageStocks->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->date_from && $request->date_to) {
            $damageStocks = $damageStocks->whereBetween('return_date', [$request->date_from, $request->date_to]);
        }

        $damageStocks = $damageStocks->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data'    => $damageStocks,
        ]);
    }

    public static function apiStore($request)
    {
        $response = self::store($request);

        return response()->json([
            'success' => $response['status'] == 200,
            'message' => $response['message'],
        ], $response['status'] == 200 ? 200 : 422);
    }

    public static function apiDestroy($id)
    {
        $response = self::destroy($id);

        return response()->json([
            'success' => $response['status'] == 200,
            'message' => $response['message'],
        ], $response['status'] == 200 ? 200 : 422);
    }
}
