<?php

namespace App\Services;

use App\Models\ProductVariation;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentProductVariation;
use App\Models\Supplier;
use App\Models\Warehouse;

class StockAdjustmentService
{
    # get data
    public static function index($request, $limit)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;

        $stockAdjustments = StockAdjustment::shop()->latest();
        if ($request->search != null) {
            $search         = $request->search;
            $stockAdjustments = $stockAdjustments->whereHas('warehouse', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
            $searchKey = $request->search;
        }

        $stockAdjustments = $stockAdjustments->paginate($limit);


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'stockAdjustments'    => $stockAdjustments,
                'searchKey'         => $searchKey,
            ],
        ];

        return $data;
    }

    # Show the form for creating a new resource.
    public static function create()
    {
        $warehouses  = Warehouse::shop()->latest()->get();
        $suppliers   = Supplier::shop()->latest()->get();
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'warehouses'  => $warehouses,
                'suppliers'   => $suppliers,
            ],
        ];
        return $data;
    }

    # Show the form for editing the specified resource.
    public static function getProducts($request)
    {
        $warehouseId = $request->warehouseId;
        $selectedVariationIds  = $request->selectedVariationIds ?? [];

        $productVariations = ProductVariation::whereNotIn('id', $selectedVariationIds)
            ->whereHas('product', function ($q) use ($request) {
                $q->shop()->where('name', 'like', '%' . $request->searchKey . '%');
            })
            ->whereHas('productVariationStocks', function ($q1) use ($warehouseId) {
                $q1->where('warehouse_id', $warehouseId);
            })
            ->paginate(perPage(20));

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'productVariations'     => $productVariations,
                'selectedVariationIds'  => $selectedVariationIds,
                'warehouseId'           => $warehouseId,
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
            // Validate expiry dates for stock additions if inventory is enabled
            if (useInventory() && isset($request->expiryDates)) {
                foreach ($request->expiryDates as $key => $expiryDate) {
                    if (!empty($expiryDate) && isset($request->actions[$key]) && $request->actions[$key] == "addition") {
                        $validation = ExpiryValidationService::validateExpiryDate($expiryDate, 'adjustment');
                        if ($validation['status'] != 200) {
                            // Log validation error
                            \Log::warning('Stock Adjustment Expiry Date Validation Failed', [
                                'user_id' => auth()->id(),
                                'expiry_date' => $expiryDate,
                                'action' => $request->actions[$key],
                                'variation_id' => $request->selectedVariationIds[$key] ?? null,
                                'validation_errors' => $validation['errors'],
                            ]);

                            $data = [
                                'status'    => 403,
                                'message'   => implode(', ', $validation['errors']),
                                'result'    => [],
                            ];
                            return $data;
                        }
                    }
                }
            }

            $stockAdjustment = new StockAdjustment;
            StockAdjustmentService::__insertIntoDb($stockAdjustment, $request);

            $data = [
                'status'    => 200,
                'message'   => translate('Stocks have been adjusted successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            // Log detailed error information
            \Log::error('Stock Adjustment Service Error', [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'error_message' => $th->getMessage(),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'error_trace' => $th->getTraceAsString(),
                'warehouse_id' => $request->warehouse_id ?? null,
            ]);

            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # store/update db data
    private static function __insertIntoDb($stockAdjustment, $request)
    {
        $stockAdjustment->shop_id         = shopId();
        $stockAdjustment->warehouse_id    = $request->warehouse_id;
        $stockAdjustment->note            = $request->note;
        $stockAdjustment->save();

        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation  = ProductVariation::find((int) $productVariationId);
            $product           = $productVariation->product;

            $stockAdjustmentProductVariation                          = new StockAdjustmentProductVariation;
            $stockAdjustmentProductVariation->product_id              = $product->id;
            $stockAdjustmentProductVariation->stock_adjustment_id     = $stockAdjustment->id;
            $stockAdjustmentProductVariation->product_variation_id    = $productVariationId;
            $stockAdjustmentProductVariation->qty                     = $request->stockQty[$key];
            $stockAdjustmentProductVariation->action                  = $request->actions[$key];
            $stockAdjustmentProductVariation->save();

            $pStock = $productVariation->productVariationStocks()->where('warehouse_id',  $stockAdjustment->warehouse_id)->first();
            if (!is_null($pStock)) {
                if ($request->actions[$key] == "addition") {
                    $pStock->stock_qty  += $request->stockQty[$key];

                    $product->stock_qty += $request->stockQty[$key];
                    $product->save();

                    // Create stock entry with expiry date for inventory-enabled scenario
                    if (useInventory()) {
                        StockAdjustmentService::createStockEntry(
                            $stockAdjustment,
                            $productVariation,
                            $request->stockQty[$key],
                            $request->expiryDates[$key] ?? null,
                            $request->supplier_id
                        );
                    }
                } else {
                    $pStock->stock_qty  -= $request->stockQty[$key];

                    $product->stock_qty -= $request->stockQty[$key];
                    $product->save();
                }
            }
            $pStock->save();
        }
    }

    /**
     * Create stock entry with expiry date for stock adjustments
     */
    private static function createStockEntry($stockAdjustment, $productVariation, $quantity, $expiryDate, $supplierId)
    {
        try {
            $stock = new Stock();
            $stock->supplier_id = $supplierId; // Use supplier from request
            $stock->warehouse_id = $stockAdjustment->warehouse_id;
            $stock->product_variation_id = $productVariation->id;
            $stock->qty = $quantity;
            $stock->purchase_cost = 0; // No purchase cost for stock adjustments
            $stock->selling_price = $productVariation->price;
            $stock->created_by = auth()->user()->id ?? null;

            // Convert expiry date to timestamp if provided
            if (!empty($expiryDate)) {
                try {
                    // Parse date in DD-MM-YYYY format (from frontend)
                    $parsedDate = \DateTime::createFromFormat('d-m-Y', $expiryDate);
                    if ($parsedDate) {
                        $stock->expiry_date = $parsedDate->getTimestamp();
                    } else {
                        // Fallback to strtotime if parsing fails
                        $stock->expiry_date = strtotime($expiryDate);
                    }
                } catch (\Exception $e) {
                    // Log date parsing error but continue
                    \Log::warning('Stock Adjustment Expiry Date Parsing Error', [
                        'stock_adjustment_id' => $stockAdjustment->id,
                        'product_variation_id' => $productVariation->id,
                        'expiry_date_raw' => $expiryDate,
                        'supplier_id' => $supplierId,
                        'parsing_error' => $e->getMessage(),
                    ]);
                    // Fallback to strtotime if parsing fails
                    $stock->expiry_date = strtotime($expiryDate);
                }
            }

            $stock->save();
        } catch (\Throwable $th) {
            // Log stock entry creation error
            \Log::error('Stock Adjustment Stock Entry Creation Failed', [
                'stock_adjustment_id' => $stockAdjustment->id,
                'product_variation_id' => $productVariation->id,
                'quantity' => $quantity,
                'expiry_date' => $expiryDate,
                'supplier_id' => $supplierId,
                'error_message' => $th->getMessage(),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
            ]);

            // Re-throw to be caught by the main try-catch
            throw $th;
        }
    }
}
