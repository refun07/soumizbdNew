<?php

namespace App\Services;

use App\Models\Language;
use App\Models\MediaFile;
use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProductVariation;
use App\Models\PurchaseReturnOrder;
use App\Models\PurchaseReturnOrderProductVariation;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Warehouse;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class PurchaseOrderService
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

        $purchaseOrders = PurchaseOrder::shop()->latest();
        if ($request->search != null) {
            $search         = str_replace('#PO-', '', $request->search);
            $search         = str_replace('PO-', '', $search);
            $purchaseOrders = $purchaseOrders->where('reference_code', 'like', '%' . $search . '%');
            $searchKey = $request->search;
        }

        $purchaseOrders = $purchaseOrders->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'purchaseOrders'    => $purchaseOrders,
                'searchKey'         => $searchKey,
            ],
        ];

        return $data;
    }

    # Show the form for creating a new resource.
    public static function create()
    {
        $suppliers   = Supplier::shop()->latest()->get();
        $warehouses  = Warehouse::shop()->latest()->get();
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'suppliers'  => $suppliers,
                'warehouses' => $warehouses,
            ],
        ];
        return $data;
    }

    # Show purchase order details
    public static function show($id)
    {
        try {
            $purchaseOrder = PurchaseOrder::shop()
                ->with([
                    'supplier',
                    'warehouse',
                    'return',
                    'payments',
                    'orders.productVariation.product',
                ])
                ->whereId((int) $id)
                ->firstOrFail();

            return [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'purchaseOrder' => $purchaseOrder,
                ],
            ];
        } catch (\Throwable $th) {
            return [
                'status'    => 404,
                'message'   => translate('Purchase order not found'),
                'result'    => [],
            ];
        }
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
            // Validate supplier is selected
            if (empty($request->supplier_id)) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Please select a supplier before creating the order'),
                    'result'    => [],
                ];
                return $data;
            }

            // Validate expiry dates if inventory is enabled
            if (useInventory() && isset($request->expiryDates)) {
                foreach ($request->expiryDates as $key => $expiryDate) {
                    if (!empty($expiryDate)) {
                        $validation = ExpiryValidationService::validateExpiryDate($expiryDate, 'purchase');
                        if ($validation['status'] != 200) {
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

            $purchaseOrder                  = new PurchaseOrder;
            DB::transaction(function () use ($purchaseOrder, $request) {
                PurchaseOrderService::__insertIntoDb($purchaseOrder, $request);
            });

            $data = [
                'status'    => 200,
                'message'   => translate('Purchase order has been created successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            Log::error('Purchase Order creation failed', [
                'user_id' => auth()->id(),
                'supplier_id' => $request->supplier_id ?? null,
                'warehouse_id' => $request->warehouse_id ?? null,
                'error_message' => $th->getMessage(),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'request_data' => $request->all()
            ]);

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

            $purchaseOrder = PurchaseOrder::findOrFail((int)$id);
            if ($purchaseOrder->return) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Returned order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status == "cancelled") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Cancelled order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            $suppliers   = Supplier::shop()->latest()->get();
            $warehouses  = Warehouse::shop()->latest()->get();
            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'purchaseOrder'      => $purchaseOrder,
                    'suppliers'          => $suppliers,
                    'warehouses'         => $warehouses,
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

            $purchaseOrder = PurchaseOrder::findOrFail((int) $id);

            if ($purchaseOrder->return) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Returned order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status == "cancelled") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Cancelled order can not be updated'),
                    'result'    => [],
                ];
                return $data;
            }

            DB::transaction(function () use ($purchaseOrder, $request) {
                PurchaseOrderService::__insertIntoDb($purchaseOrder, $request);
            });

            $data = [
                'status'    => 200,
                'message'   => translate('Purchase Order has been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            Log::error('Purchase Order update failed', [
                'user_id' => auth()->id(),
                'purchase_order_id' => $id,
                'supplier_id' => $request->supplier_id ?? null,
                'warehouse_id' => $request->warehouse_id ?? null,
                'status' => $request->status ?? null,
                'error_message' => $th->getMessage(),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'request_data' => $request->all()
            ]);

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

            $purchaseOrder = PurchaseOrder::where('id', $id)->first();
            if (!is_null($purchaseOrder)) {

                if ($purchaseOrder->status == "received") {
                    $data = [
                        'success'   => false,
                        'status'    => 403,
                        'message'   => translate('Received order can not be cancelled'),
                        'result'    => [],
                    ];
                    return $data;
                }

                $purchaseOrder->status = "cancelled";
                $purchaseOrder->save();
            }

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Purchase order has been cancelled successfully'),
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

    # download purchase invoice
    public static function downloadInvoice($id)
    {
        if (session()->has('locale')) {
            $languageCode = session()->get('locale', Config::get('app.locale'));
        } else {
            $languageCode = env('DEFAULT_LANGUAGE');
        }

        if (getSetting('currencyCode')) {
            $currencyCode = getSetting('currencyCode');
        } else {
            $currencyCode = env('DEFAULT_CURRENCY');
        }

        $language = Language::where('code', $languageCode)->first();
        $isRtl = $language?->is_rtl == 1;
        $direction = $isRtl ? 'rtl' : 'ltr';
        $defaultTextAlign = $isRtl ? 'right' : 'left';
        $reverseTextAlign = $isRtl ? 'left' : 'right';

        if ($currencyCode == 'BDT' || $currencyCode == 'bdt' || $languageCode == 'bd' || $languageCode == 'bn') {
            $fontFamily = "'Hind Siliguri','sans-serif'";
        } elseif ($currencyCode == 'KHR' || $languageCode == 'kh') {
            $fontFamily = "'Khmeros','sans-serif'";
        } elseif ($currencyCode == 'AMD') {
            $fontFamily = "'arnamu','sans-serif'";
        } elseif ($currencyCode == 'AED' || $currencyCode == 'EGP' || $languageCode == 'sa' || $currencyCode == 'IQD' || $languageCode == 'ir') {
            $fontFamily = "'XBRiyaz','sans-serif'";
        } else {
            $fontFamily = "'Roboto','sans-serif'";
        }

        $purchaseOrder = PurchaseOrder::shop()
            ->with(['orders.productVariation.product', 'supplier', 'warehouse', 'payments'])
            ->whereId((int) $id)
            ->firstOrFail();

        $logo = null;
        $logoFileId = getSetting('websiteHeaderLogo');

        if ($logoFileId) {
            $mediaFile = MediaFile::find($logoFileId);
            if ($mediaFile && $mediaFile->media_file) {
                $logo = public_path($mediaFile->media_file);

                // Convert webp/png to pdf-safe PNG
                $logo = self::makePdfSafeLogo($logo);
            }
        }

        $systemName = getSetting('systemName');
        $systemAddress = getSetting('systemAddress');

        return PDF::loadView('backend.admin.inventory.purchase-orders.invoice', [
            'purchaseOrder' => $purchaseOrder,
            'font_family' => $fontFamily,
            'direction' => $direction,
            'default_text_align' => $defaultTextAlign,
            'reverse_text_align' => $reverseTextAlign,
            'logo' => $logo,
            'systemName' => $systemName,
            'systemAddress' => $systemAddress,
        ], [], [])->download('PO-' . $purchaseOrder->reference_code . '.pdf');
    }

    private static function makePdfSafeLogo(string $absolutePath): string
    {
        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        // If already jpg/jpeg, it's generally safe
        if (in_array($ext, ['jpg', 'jpeg'])) {
            return $absolutePath;
        }

        $cacheDir = storage_path('app/pdf-cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $hash = md5($absolutePath . '|' . @filemtime($absolutePath));
        $out  = $cacheDir . DIRECTORY_SEPARATOR . "logo_pdf_safe_{$hash}.png";

        if (file_exists($out)) {
            return $out;
        }

        // Load source image (webp/png) using GD
        if (!extension_loaded('gd')) {
            return $absolutePath;
        }

        $src = null;

        if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
            $src = @imagecreatefromwebp($absolutePath);
        } elseif ($ext === 'png' && function_exists('imagecreatefrompng')) {
            $src = @imagecreatefrompng($absolutePath);
        } elseif (in_array($ext, ['jpg', 'jpeg']) && function_exists('imagecreatefromjpeg')) {
            $src = @imagecreatefromjpeg($absolutePath);
        }

        if (!$src) {
            // GD may not have WebP support enabled -> fallback: return original
            return $absolutePath;
        }

        $w = imagesx($src);
        $h = imagesy($src);

        // Create white canvas to avoid black background from transparency
        $dst = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $w, $h, $white);

        imagealphablending($dst, true);
        imagesavealpha($dst, false);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);

        imagepng($dst, $out, 9);

        imagedestroy($src);
        imagedestroy($dst);

        return $out;
    }

    # store/update db data
    private static function __insertIntoDb($purchaseOrder, $request)
    {
        if ($request->warehouse_id) {
            // means new resource
            $purchaseOrder->warehouse_id    = $request->warehouse_id;
        } else {
            // updating -- as in update method warehouse_id is not in requests
            $purchaseOrderProductVariations = $purchaseOrder->orders;
            foreach ($purchaseOrderProductVariations as $purchaseOrderProductVariation) {
                $productVariationStock = ProductVariationStock::where('product_variation_id', $purchaseOrderProductVariation->product_variation_id)->where('warehouse_id', $purchaseOrder->warehouse_id)->first();
                if (!is_null($productVariationStock)) {
                    # if purchaseOrder status is received - remove qty from stocks
                    if ($purchaseOrder->status == "received") {
                        $productVariationStock->stock_qty -= $purchaseOrderProductVariation->qty;
                        $productVariationStock->save();

                        $product                = $purchaseOrderProductVariation->product;
                        $product->stock_qty     -= $purchaseOrderProductVariation->qty;
                        $product->save();
                    }
                }

                # delete purchaseOrderProductVariations
                $purchaseOrderProductVariation->delete();
            }
        }

        $purchaseOrder->shop_id         = shopId();
        $purchaseOrder->supplier_id     = $request->supplier_id;
        $purchaseOrder->date            = strtotime($request->date);
        $purchaseOrder->status          = $request->status;
        $purchaseOrder->sub_total       = array_sum($request->subtotal) ?? 0;
        $purchaseOrder->tax_percentage  = $request->tax;
        $purchaseOrder->tax_value       = ($purchaseOrder->sub_total *  $request->tax) / 100;
        $purchaseOrder->shipping        = $request->shipping;
        $purchaseOrder->discount        = $request->discount;
        $purchaseOrder->grand_total     = $purchaseOrder->sub_total + $purchaseOrder->tax_value + $purchaseOrder->shipping - $purchaseOrder->discount;
        $purchaseOrder->due             = $purchaseOrder->grand_total;
        $purchaseOrder->note            = $request->note;
        $purchaseOrder->save();

        // purchase order items
        $grandTotal = 0;
        foreach ($request->selectedVariationIds as $key => $productVariationId) {
            $productVariation               = ProductVariation::withTrashed()->find((int) $productVariationId);
            $product                        = $productVariation->product;

            $prevStock = 0;
            $pStock = $productVariation->productVariationStocks()->where('warehouse_id',  $purchaseOrder->warehouse_id)->first();
            if (!is_null($pStock)) {
                $prevStock = $pStock->stock_qty;
                if ($purchaseOrder->status == "received") {
                    $pStock->stock_qty += $request->stockQty[$key];

                    $product->stock_qty += $request->stockQty[$key];
                    $product->save();
                }
            } else {
                $pStock = new ProductVariationStock;
                $pStock->product_variation_id  = $productVariationId;
                $pStock->warehouse_id          = $purchaseOrder->warehouse_id;
                if ($purchaseOrder->status == "received") {
                    $pStock->stock_qty  += $request->stockQty[$key];

                    $product->stock_qty += $request->stockQty[$key];
                    $product->save();
                } else {
                    $pStock->stock_qty = 0;
                }
            }
            $pStock->save();

            $unitDiscount                   = variationPrice($product, $productVariation, false) - variationDiscountedPrice($product, $productVariation, false);
            $unitTax                        =  variationPrice($product, $productVariation) - variationPrice($product, $productVariation, false);

            $purchaseVariation              = new PurchaseOrderProductVariation;

            $purchaseVariation->prev_stock              = $prevStock;
            $purchaseVariation->product_id              = $product->id;
            $purchaseVariation->purchase_order_id       = $purchaseOrder->id;
            $purchaseVariation->product_variation_id    = $productVariationId;
            $purchaseVariation->base_unit_price         = $productVariation->price;
            $purchaseVariation->unit_price              = $request->unitPrice[$key];
            $purchaseVariation->qty                     = $request->stockQty[$key];
            $purchaseVariation->sub_total               = $purchaseVariation->unit_price *  $purchaseVariation->qty;
            $purchaseVariation->discount                = $unitDiscount *  $purchaseVariation->qty;
            $purchaseVariation->tax                     = $unitTax *  $purchaseVariation->qty;
            $purchaseVariation->grand_total             =  $purchaseVariation->sub_total + $purchaseVariation->tax - $purchaseVariation->discount;
            $purchaseVariation->save();

            // Create stock entry with expiry date for inventory-enabled scenario
            if (useInventory() && $purchaseOrder->status == "received") {
                PurchaseOrderService::createStockEntry(
                    $purchaseOrder,
                    $productVariation,
                    $request->stockQty[$key],
                    $request->unitPrice[$key],
                    $request->expiryDates[$key] ?? null,
                    $purchaseVariation->id
                );
            }

            $grandTotal += $purchaseVariation->grand_total;
        }

        $purchaseOrder->grand_total     = $grandTotal + $purchaseOrder->tax_value + $purchaseOrder->shipping - $purchaseOrder->discount;

        $payments   = $purchaseOrder->payments;
        $totalPaid  = $payments->sum('paid_amount');
        $purchaseOrder->due = $purchaseOrder->grand_total - $totalPaid;

        if ($purchaseOrder->due > 0) {
            $purchaseOrder->payment_status = "unpaid";
        } else {
            $purchaseOrder->payment_status = "paid";
        }


        $purchaseOrder->save();
    }

    # Display a listing of the resource.
    public static function returnIndex($request, $limit = 15)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];


        $searchKey = null;

        $returnOrders = PurchaseReturnOrder::shop()->latest();
        if ($request->search != null) {
            $search         = str_replace('#PR-', '', $request->search);
            $search         = str_replace('PR-', '', $search);
            $returnOrders = $returnOrders->where('reference_code', 'like', '%' . $search . '%');
            $searchKey = $request->search;
        }

        $returnOrders = $returnOrders->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'returnOrders'    => $returnOrders,
                'searchKey'       => $searchKey,
            ],
        ];

        return $data;
    }

    # Show the form for returning a new resource.
    public static function returnCreate($request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail((int)$id);
        if ($purchaseOrder->status == "cancelled") {
            $data = [
                'status'    => 403,
                'message'   => translate('Cancelled order can not be returned'),
                'result'    => [],
            ];
            return $data;
        }

        if ($purchaseOrder->status != "received") {
            $data = [
                'status'    => 403,
                'message'   => translate('Only received orders can be returned'),
                'result'    => [],
            ];
            return $data;
        }

        $suppliers   = Supplier::shop()->latest()->get();
        $warehouses  = Warehouse::shop()->latest()->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'purchaseOrder' => $purchaseOrder,
                'suppliers'     => $suppliers,
                'warehouses'    => $warehouses,
            ],
        ];
        return $data;
    }

    # add new data
    public static function returnStore($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $purchaseOrder                    = PurchaseOrder::findOrFail((int) $id);
            $purchaseOrderProductVariations   = $purchaseOrder->orders;

            if ($purchaseOrder->status == "cancelled") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Cancelled order can not be returned'),
                    'result'    => [],
                ];
                return $data;
            }

            if ($purchaseOrder->status != "received") {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Only received orders can be returned'),
                    'result'    => [],
                ];
                return $data;
            }

            $selectedVariationIds = $request->input('selectedVariationIds', []);
            $stockQtyInputs = $request->input('stockQty', []);
            $unitPriceInputs = $request->input('unitPrice', []);
            $subtotalInputs = $request->input('subtotal', []);

            if (!is_array($selectedVariationIds) || !is_array($stockQtyInputs) || !is_array($unitPriceInputs)) {
                return [
                    'status'    => 403,
                    'message'   => translate('Invalid return request data'),
                    'result'    => [],
                ];
            }

            $itemInputMap = [];
            foreach ($selectedVariationIds as $index => $variationId) {
                $itemInputMap[(int) $variationId] = [
                    'stockQty' => (float) ($stockQtyInputs[$index] ?? 0),
                    'unitPrice' => (float) ($unitPriceInputs[$index] ?? 0),
                ];
            }

            DB::transaction(function () use ($purchaseOrder, $purchaseOrderProductVariations, $request, $itemInputMap, $subtotalInputs) {
                $returnOrder = $purchaseOrder->return;
                $oldReturnOrderProductVariations = collect();

                if (is_null($returnOrder)) {
                    $returnOrder = new PurchaseReturnOrder;
                } else {
                    $oldReturnOrderProductVariations = $returnOrder->orders;
                }

                foreach ($oldReturnOrderProductVariations as $oldReturnOrderProductVariation) {
                    if ($returnOrder->status == "completed") {
                        // add to stock again which were previously returned
                        $productVariationStock = ProductVariationStock::where('product_variation_id', $oldReturnOrderProductVariation->product_variation_id)
                            ->where('warehouse_id', $purchaseOrder->warehouse_id)
                            ->first();

                        if (!is_null($productVariationStock)) {
                            $productVariationStock->stock_qty += $oldReturnOrderProductVariation->returned_qty;
                            $productVariationStock->save();

                            $product = $oldReturnOrderProductVariation->product;
                            if (!is_null($product)) {
                                $product->stock_qty += $oldReturnOrderProductVariation->returned_qty;
                                $product->save();
                            }
                        }
                    }
                    $oldReturnOrderProductVariation->delete();
                }

                // update with new requests
                $returnOrder->purchase_order_id  = $purchaseOrder->id;
                $returnOrder->shop_id            = $purchaseOrder->shop_id;
                $returnOrder->supplier_id        = $purchaseOrder->supplier_id;
                $returnOrder->warehouse_id       = $purchaseOrder->warehouse_id;
                $returnOrder->date               = strtotime($request->date);
                $returnOrder->status             = $request->status;
                $returnOrder->sub_total          = array_sum(array_map('floatval', (array) $subtotalInputs));
                $returnOrder->tax_percentage     = (float) $request->tax;
                $returnOrder->tax_value          = ($returnOrder->sub_total *  $returnOrder->tax_percentage) / 100;
                $returnOrder->shipping           = (float) $request->shipping;
                $returnOrder->discount           = (float) $request->discount;
                $returnOrder->grand_total        = $returnOrder->sub_total + $returnOrder->tax_value + $returnOrder->shipping - $returnOrder->discount;
                $returnOrder->due                = $returnOrder->grand_total;
                $returnOrder->note               = $request->note;
                $returnOrder->save();

                $grandTotal = 0;
                foreach ($purchaseOrderProductVariations as $purchaseOrderProductVariation) {
                    $productVariation = ProductVariation::withTrashed()->find((int) $purchaseOrderProductVariation->product_variation_id);
                    if (is_null($productVariation)) {
                        continue;
                    }

                    $product = $productVariation->product;

                    $itemInput = $itemInputMap[(int) $purchaseOrderProductVariation->product_variation_id] ?? [
                        'stockQty' => 0,
                        'unitPrice' => (float) $purchaseOrderProductVariation->unit_price,
                    ];

                    // Returned qty must be within purchased qty bounds.
                    $returnedQty = max(0, min((float) $itemInput['stockQty'], (float) $purchaseOrderProductVariation->qty));
                    $unitPrice = (float) $itemInput['unitPrice'];

                    $pStock = $productVariation->productVariationStocks()->where('warehouse_id',  $purchaseOrder->warehouse_id)->first();
                    if (!is_null($pStock)) {
                        if ($returnOrder->status == "completed") {
                            $pStock->stock_qty -= $returnedQty;

                            if (!is_null($product)) {
                                $product->stock_qty -= $returnedQty;
                                $product->save();
                            }
                        }
                        $pStock->save();
                    }

                    $unitDiscount  = $purchaseOrderProductVariation->qty > 0 ? ($purchaseOrderProductVariation->discount / $purchaseOrderProductVariation->qty) : 0;
                    $unitTax       = $purchaseOrderProductVariation->qty > 0 ? ($purchaseOrderProductVariation->tax / $purchaseOrderProductVariation->qty) : 0;

                    $returnOrderProductVariation = new PurchaseReturnOrderProductVariation;
                    $returnOrderProductVariation->product_id                = $product?->id ?? $purchaseOrderProductVariation->product_id;
                    $returnOrderProductVariation->purchase_return_order_id  = $returnOrder->id;
                    $returnOrderProductVariation->purchase_order_id         = $purchaseOrder->id;
                    $returnOrderProductVariation->product_variation_id      = $productVariation->id;
                    $returnOrderProductVariation->base_unit_price           = $productVariation->price;
                    $returnOrderProductVariation->unit_price                = $unitPrice;
                    $returnOrderProductVariation->qty                       = $purchaseOrderProductVariation->qty;
                    $returnOrderProductVariation->returned_qty              = $returnedQty;
                    $returnOrderProductVariation->sub_total                 = $unitPrice * $returnedQty;
                    $returnOrderProductVariation->discount                  = $unitDiscount * $returnedQty;
                    $returnOrderProductVariation->tax                       = $unitTax * $returnedQty;
                    $returnOrderProductVariation->grand_total               = $returnOrderProductVariation->sub_total + $returnOrderProductVariation->tax - $returnOrderProductVariation->discount;
                    $returnOrderProductVariation->save();

                    $grandTotal += $returnOrderProductVariation->grand_total;
                }

                $returnOrder->grand_total = $grandTotal + $returnOrder->tax_value + $returnOrder->shipping - $returnOrder->discount;

                $payments = $returnOrder->payments;
                $totalPaid = $payments->sum('paid_amount');

                $returnOrder->due = $returnOrder->grand_total - $totalPaid;
                $returnOrder->payment_status = $returnOrder->due > 0 ? "unpaid" : "paid";
                $returnOrder->save();
            });

            $data = [
                'status'    => 200,
                'message'   => translate('Purchase return has been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            Log::error('Purchase return save failed', [
                'user_id' => auth()->id(),
                'purchase_order_id' => $id,
                'request_data' => $request->all(),
                'error_message' => $th->getMessage(),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
            ]);

            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    /**
     * Create stock entry with expiry date
     */
    private static function createStockEntry($purchaseOrder, $productVariation, $quantity, $unitPrice, $expiryDate, $purchaseOrderItemId = null)
    {
        $stock = new Stock();
        $stock->supplier_id = $purchaseOrder->supplier_id;
        $stock->warehouse_id = $purchaseOrder->warehouse_id;
        $stock->product_variation_id = $productVariation->id;
        $stock->purchase_order_item_id = $purchaseOrderItemId;
        $stock->qty = $quantity;
        $stock->purchase_cost = $unitPrice;
        $stock->selling_price = $productVariation->price;
        $stock->created_by = auth()->user()->id ?? null;

        // Convert expiry date to timestamp if provided
        if (!empty($expiryDate)) {
            try {
                // Parse date in DD-MM-YYYY format (from frontend)
                $parsedDate = \DateTime::createFromFormat('d-m-Y', $expiryDate);
                if ($parsedDate) {
                    // Store as Unix timestamp (database expects integer)
                    $stock->expiry_date = $parsedDate->getTimestamp();
                    Log::error('Expiry date parsed successfully', [
                        'original_date' => $expiryDate,
                        'parsed_timestamp' => $stock->expiry_date,
                        'formatted_date' => date('Y-m-d H:i:s', $stock->expiry_date)
                    ]);
                } else {
                    Log::error('Failed to parse expiry date - invalid format', [
                        'expiry_date' => $expiryDate,
                        'expected_format' => 'DD-MM-YYYY',
                        'purchase_order_id' => $purchaseOrder->id,
                        'product_variation_id' => $productVariation->id
                    ]);
                    // Don't set expiry date if parsing fails
                }
            } catch (\Exception $e) {
                Log::error('Exception parsing expiry date', [
                    'expiry_date' => $expiryDate,
                    'error' => $e->getMessage(),
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_variation_id' => $productVariation->id
                ]);
                // Don't set expiry date if parsing fails
            }
        }

        $stock->save();
    }

    # Get parent products with variations for React component
    public static function getParentProducts($request)
    {
        $warehouseId = $request->warehouseId;
        $selectedVariationIds = $request->selectedVariationIds ?? [];
        $searchKey = $request->searchKey ?? '';
        $limit = $request->limit ?? 20;

        $products = \App\Models\Product::shop()
            ->where('name', 'like', '%' . $searchKey . '%')
            ->whereHas('variations', function ($q) use ($selectedVariationIds) {
                $q->whereNotIn('id', $selectedVariationIds);
            })
            ->with(['variations' => function ($q) use ($warehouseId, $selectedVariationIds) {
                $q->whereNotIn('id', $selectedVariationIds)
                    ->with(['productVariationStocks' => function ($q) use ($warehouseId) {
                        $q->where('warehouse_id', $warehouseId);
                    }]);
            }, 'unit'])
            ->limit($limit)
            ->get();

        return [
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->collectTranslation('name'),
                    'thumbnailImage' => uploadedAsset($product->thumbnail_image),
                    'unitName' => $product->unit?->collectTranslation('name') ?? 'N/A',
                    'hasVariation' => $product->has_variation,
                    'variations' => $product->variations->map(function ($variation) {
                        return [
                            'id' => $variation->id,
                            'sku' => $variation->sku,
                            'code' => $variation->code,
                            'price' => $variation->price,
                            'stockQty' => $variation->productVariationStocks->first()?->stock_qty ?? 0,
                            'variationName' => self::generateVariationName($variation->code),
                        ];
                    }),
                ];
            }),
        ];
    }

    # Get product variations for a specific product
    public static function getProductVariations($request, $productId)
    {
        $warehouseId = $request->warehouseId;
        $selectedVariationIds = $request->selectedVariationIds ?? [];

        $product = \App\Models\Product::shop()
            ->where('id', $productId)
            ->with(['variations' => function ($q) use ($warehouseId, $selectedVariationIds) {
                $q->whereNotIn('id', $selectedVariationIds)
                    ->with(['productVariationStocks' => function ($q) use ($warehouseId) {
                        $q->where('warehouse_id', $warehouseId);
                    }]);
            }, 'unit'])
            ->firstOrFail();

        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->collectTranslation('name'),
                'thumbnailImage' => uploadedAsset($product->thumbnail_image),
                'unitName' => $product->unit?->collectTranslation('name') ?? 'N/A',
                'hasVariation' => $product->has_variation,
                'variations' => $product->variations->map(function ($variation) {
                    return [
                        'id' => $variation->id,
                        'sku' => $variation->sku,
                        'code' => $variation->code,
                        'price' => $variation->price,
                        'stockQty' => $variation->productVariationStocks->first()?->stock_qty ?? 0,
                        'variationName' => self::generateVariationName($variation->code),
                    ];
                }),
            ],
        ];
    }

    # Helper function to generate variation name from code
    private static function generateVariationName($code)
    {
        if (empty($code)) {
            return 'Default';
        }

        $name = '';
        $code_array = array_filter(explode('/', $code));

        foreach ($code_array as $comb) {
            $comb = explode(':', $comb);
            if (count($comb) >= 2) {
                $option = \App\Models\Variation::find($comb[0]);
                $choice = \App\Models\VariationValue::find($comb[1]);
                if ($option && $choice) {
                    if (!empty($name)) {
                        $name .= ' · ';
                    }
                    $name .= $choice->collectTranslation('name');
                }
            }
        }

        return $name ?: 'Default';
    }
}
