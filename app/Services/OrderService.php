<?php

namespace App\Services;

use App\Models\CommissionHistory;
use App\Models\Exchange;
use App\Models\Language;
use App\Models\MediaFile;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use Carbon\Carbon;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class OrderService
{
    public function buildFilteredQuery($request)
    {
        $orders = Order::shopOrders()->with([
            'orderGroup.shippingAddress',
            'orderItems.productVariation.product',
            'user',
            'agent',
            'latestDeliveryStatusUpdate.creator',
            'latestDeliveryStatusUpdate.user',
            'exchangeAsOriginal.exchangeOrder',
            'originalOrder',
        ])->withCount('orderItems')
            ->withSum('orderItems as total_item_quantity', 'qty')
            ->latest();

        if (auth()->user()->user_type === 'staff' && !auth()->user()->can('view_orders') && !auth()->user()->can('manage_orders')) {
            $orders->where('agent_id', auth()->id());
        }

        if ($request->has('delivery_status') && $request->query('delivery_status') !== '') {
            $orders->where('delivery_status', $request->delivery_status);
        }
        if (($request->has('from') && $request->query('from') !== '') && ($request->has('to') && $request->query('to') !== '')) {
            try {
                $fromDate = trim($request->query('from'));
                $toDate = trim($request->query('to'));

                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
                    $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
                } else {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', $fromDate)->startOfDay();
                }

                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate)) {
                    $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $toDate)->endOfDay();
                } else {
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', $toDate)->endOfDay();
                }

                $orders->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
            }
        }

        if ($request->has('payment_status') && $request->query('payment_status') !== '') {
            $orders->where('payment_status', $request->query('payment_status'));
        }

        if ($request->has('pos_order') && $request->query('pos_order') !== '') {
            $posOrder = $request->query('pos_order');
            if (in_array((string) $posOrder, ['0', '1'], true)) {
                $orders->where('pos_order', (int) $posOrder);
            }
        }

        if ($request->has('has_exchange') && $request->query('has_exchange') !== '') {
            $orders->whereHas('exchangeAsOriginal');
        }

        if ($request->has('search') && $request->query('search') !== '') {
            $searchKey = $request->query('search');
            $orders = $orders->whereHas('user', function ($q) use ($searchKey) {
                $q->where('name', 'like', '%'.$searchKey.'%')
                    ->orWhere('phone', 'like', '%'.$searchKey.'%');
            })->orWhere('order_code', 'like', '%'.$searchKey.'%');
        }

        if ($request->date_range != null) {
            try {
                [$start, $end] = explode(' - ', $request->date_range);

                $startDateStr = trim($start);
                $endDateStr = trim($end);

                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDateStr)) {
                    $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $startDateStr)->startOfDay();
                } else {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', $startDateStr)->startOfDay();
                }

                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDateStr)) {
                    $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $endDateStr)->endOfDay();
                } else {
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', $endDateStr)->endOfDay();
                }

                $orders->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
            }
        }

        if ($request->customerId != null) {
            $orders->where('user_id', $request->customerId);
        }

        return $orders;
    }

    // get shop orders
    public function index($request)
    {
        $searchKey = null;
        if ($request->has('search') && $request->query('search') !== '') {
            $searchKey = $request->query('search');
        }

        $limit = $request->limit ?? 15;
        $orders = $this->buildFilteredQuery($request)->paginate($limit);

        return [
            'status' => 200,
            'message' => '',
            'result' => [
                'searchKey' => $searchKey,
                'orders' => $orders,
            ],
        ];
    }

    // export orders as CSV
    public function export($request)
    {
        $query = $this->buildFilteredQuery($request)
            ->with(['orderGroup', 'orderItems.productVariation.product']);

        if ($request->has('order_ids') && ! empty($request->input('order_ids'))) {
            $query->whereIn('id', $request->input('order_ids'));
        }

        $orders = $query->get();

        $csv = \League\Csv\Writer::createFromString('');
        $csv->insertOne([
            'Invoice No',
            'Date',
            'Delivery Status',
            'Customer Phone',
            'First Name (Shipping)',
            'Customer Note',
            'Shipping Address',
            'Subtotal',
            'Shipping Charge',
            'Total Amount',
            'Product Name',
            'Quantity',
            'Item Cost',
        ]);

        $orderPrefix = getSetting('orderCodePrefix');

        foreach ($orders as $order) {
            $orderGroup = $order->orderGroup;

            foreach ($order->orderItems as $orderItem) {
                $csv->insertOne([
                    $orderPrefix.($order->orderGroup?->code ?? $order->order_code),
                    $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                    ucfirst(str_replace('_', ' ', $order->delivery_status)),
                    $orderGroup?->phone ?? $order->user?->phone ?? '',
                    $orderGroup?->name ?? $order->user?->name ?? '',
                    $orderGroup?->note ?? '',
                    $orderGroup?->shipping_address ?? '',
                    $order->amount,
                    $order->shipping_charge_amount,
                    $order->total_amount,
                    $orderItem->productVariation?->product?->collectTranslation('name') ?? $orderItem->productVariation?->product?->name ?? '',
                    $orderItem->qty,
                    $orderItem->unit_price,
                ]);
            }
        }

        $filename = 'orders_'.now()->format('Y-m-d_H-i-s').'.csv';

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    // show orders
    public function show($code)
    {
        try {
            Notification::where('link_info', $code)->where('type', 'order')->update([
                'is_read' => 1,
            ]);
        } catch (\Throwable $th) {
        }

        $data = [
            'status' => 200,
            'message' => '',
            'result' => [],
        ];

        $order = Order::shopOrders()->with([
            'orderGroup.shippingAddress',
            'user',
            'latestDeliveryStatusUpdate.creator',
            'latestDeliveryStatusUpdate.user',
            'orderUpdates.creator',
            'orderUpdates.user',
            'exchangeAsOriginal.items.originalVariation.product',
            'exchangeAsOriginal.items.exchangeVariation.product',
            'exchangeAsOriginal.exchangeOrder',
            'exchangeAsReplacement.items.originalVariation.product',
            'exchangeAsReplacement.items.exchangeVariation.product',
            'exchangeAsReplacement.originalOrder',
        ])->whereOrderCode($code)->first();
        if (is_null($order)) {
            $data = [
                'status' => 404,
                'message' => translate('Order not found'),
                'result' => [],
            ];

            return $data;
        }

        $orderGroup = $order->orderGroup;
        $orderUpdates = $order->orderUpdates;
        $orderItems = $order->orderItems;
        $returnRefunds = $order->returnRefunds;

        $exchange = null;
        if ($order->is_exchange_order && $order->exchangeAsReplacement) {
            $exchange = $order->exchangeAsReplacement;
        } elseif ($order->exchangeAsOriginal) {
            $exchange = $order->exchangeAsOriginal;
        }

        $data = [
            'status' => 200,
            'message' => '',
            'result' => [
                'order' => $order,
                'orderGroup' => $orderGroup,
                'orderUpdates' => $orderUpdates,
                'orderItems' => $orderItems,
                'returnRefunds' => $returnRefunds,
                'exchange' => $exchange,
            ],
        ];

        return $data;
    }

    // update order
    public function updateOrder($request, $code)
    {
        DB::beginTransaction();

        try {
            $order = Order::shopOrders()->whereOrderCode($code)->first();

            if (is_null($order)) {
                return [
                    'status' => 404,
                    'message' => translate('Order not found'),
                    'result' => [],
                ];
            }

            $orderGroup = $order->orderGroup;

            // Update customer information
            if ($request->has('customer_name')) {
                $orderGroup->name = $request->customer_name;
            }
            if ($request->has('customer_phone')) {
                $orderGroup->phone = $request->customer_phone;
            }
            if ($request->has('customer_email')) {
                $orderGroup->email = $request->customer_email;
            }

            // Update addresses
            if ($request->has('billing_address')) {
                $orderGroup->billing_address = $request->billing_address;
            }
            if ($request->has('shipping_address')) {
                $orderGroup->shipping_address = $request->shipping_address;
            }
            if ($request->has('direction')) {
                $orderGroup->direction = $request->direction;
            }

            // Update payment method
            if ($request->has('payment_method')) {
                $transaction = $orderGroup->transaction;
                $transaction->payment_method = $request->payment_method;
                $transaction->save();
            }

            $orderGroup->save();

            // Update order tracking
            if ($request->has('courier_name')) {
                $order->courier_name = $request->courier_name;
            }
            if ($request->has('tracking_number')) {
                $order->tracking_number = $request->tracking_number;
            }
            if ($request->has('tracking_url')) {
                $order->tracking_url = $request->tracking_url;
            }

            // Handle order items (add, edit, delete)
            $existingItemIds = [];

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (isset($itemData['is_new']) && $itemData['is_new'] == 1) {
                        // Add new product to order
                        $orderItem = new OrderItem;
                        $orderItem->order_id = $order->id;
                        $orderItem->product_variation_id = $itemData['product_variation_id'];
                        $orderItem->qty = $itemData['qty'] ?? 1;
                        $orderItem->unit_price = $itemData['unit_price'] ?? 0;

                        // Handle discount and tax
                        $discount = $itemData['discount'] ?? 0; // Total discount for the line
                        $taxPercentage = $itemData['tax_percentage'] ?? 0;

                        $orderItem->total_discount = $discount;

                        // Calculate totals
                        $itemSubtotal = ($orderItem->unit_price * $orderItem->qty) - $discount;
                        $orderItem->total_tax = ($itemSubtotal * $taxPercentage) / 100;
                        $orderItem->total_price = $itemSubtotal + $orderItem->total_tax;
                        $orderItem->unit_cost_price = getAvgPurchasePrice($itemData['product_variation_id']);
                        $orderItem->total_cost = $orderItem->unit_cost_price * $orderItem->qty;

                        $orderItem->save();

                        // Reduce stock for new item
                        $stock = $orderItem->productVariation->productVariationStocks()
                            ->where('warehouse_id', $order->warehouse_id)
                            ->first();
                        if ($stock) {
                            $stock->stock_qty -= $orderItem->qty;
                            $stock->save();
                        }

                        $existingItemIds[] = $orderItem->id;
                    } elseif (isset($itemData['id'])) {
                        // Update existing item
                        $orderItem = OrderItem::find($itemData['id']);
                        if ($orderItem && $orderItem->order_id == $order->id) {
                            $oldQty = $orderItem->qty;
                            $newQty = $itemData['qty'] ?? $oldQty;
                            $qtyDiff = $newQty - $oldQty;

                            // Update stock if quantity changed
                            if ($qtyDiff != 0) {
                                $stock = $orderItem->productVariation->productVariationStocks()
                                    ->where('warehouse_id', $order->warehouse_id)
                                    ->first();

                                if ($stock) {
                                    $stock->stock_qty -= $qtyDiff;
                                    $stock->save();
                                }
                            }

                            // Update item details
                            $orderItem->qty = $newQty;
                            $orderItem->unit_price = $itemData['unit_price'] ?? $orderItem->unit_price;

                            // Handle discount and tax
                            $discount = $itemData['discount'] ?? 0; // Total discount for the line
                            $taxPercentage = $itemData['tax_percentage'] ?? 0;

                            $orderItem->total_discount = $discount;

                            // Recalculate totals
                            $itemSubtotal = ($orderItem->unit_price * $orderItem->qty) - $discount;
                            $orderItem->total_tax = ($itemSubtotal * $taxPercentage) / 100;
                            $orderItem->total_price = $itemSubtotal + $orderItem->total_tax;

                            $orderItem->save();
                            $existingItemIds[] = $orderItem->id;
                        }
                    }
                }
            }

            // Delete items that were removed
            $allOrderItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($allOrderItems as $item) {
                if (! in_array($item->id, $existingItemIds)) {
                    // Return stock before deleting
                    $stock = $item->productVariation->productVariationStocks()
                        ->where('warehouse_id', $order->warehouse_id)
                        ->first();
                    if ($stock) {
                        $stock->stock_qty += $item->qty;
                        $stock->save();
                    }
                    $item->delete();
                }
            }

            // Recalculate order totals
            $subtotal = 0;
            $totalTax = 0;
            $orderItems = $order->orderItems()->get();
            foreach ($orderItems as $item) {
                $subtotal += $item->total_price;
                $totalTax += $item->total_tax;
            }

            $order->amount = $subtotal;
            $order->tax_amount = $totalTax;

            // Update shipping charge
            if ($request->has('shipping_charge')) {
                $order->shipping_charge_amount = $request->shipping_charge;
            }

            // Update discount
            if ($request->has('discount_amount')) {
                $order->discount_amount = $request->discount_amount;
            }

            // Calculate final total
            $order->total_amount = $order->amount + $order->tax_amount + $order->shipping_charge_amount - $order->discount_amount - $order->coupon_discount_amount;

            // Update order status
            if ($request->has('payment_status')) {
                $oldPaymentStatus = $order->payment_status;
                $order->payment_status = $request->payment_status;

                if ($oldPaymentStatus != $request->payment_status) {
                    OrderUpdate::create([
                        'order_id' => $order->id,
                        'user_id' => userId(),
                        'status' => ucwords(str_replace('_', ' ', $request->payment_status)),
                        'note' => 'Payment status updated to '.ucwords(str_replace('_', ' ', $request->payment_status)).'.',
                        'created_by' => userId(),
                    ]);
                }
            }

            if ($request->has('delivery_status')) {
                $oldDeliveryStatus = $order->delivery_status;
                $order->delivery_status = $request->delivery_status;

                if ($oldDeliveryStatus != $request->delivery_status) {
                    OrderUpdate::create([
                        'order_id' => $order->id,
                        'user_id' => userId(),
                        'status' => ucwords(str_replace('_', ' ', $request->delivery_status)),
                        'note' => 'Delivery status updated to '.ucwords(str_replace('_', ' ', $request->delivery_status)).'.',
                        'created_by' => userId(),
                    ]);
                }
            }

            $order->save();

            // Update commission history
            $this->addCommissionHistory($order);

            syncOrderProfitLog($order);

            DB::commit();

            return [
                'status' => 200,
                'message' => translate('Order updated successfully'),
                'result' => [],
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'message' => translate('Failed to update order: ').$e->getMessage(),
                'result' => [],
            ];
        }
    }

    // download invoice
    public function downloadInvoice($id, $multiple = false)
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', Config::get('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        if (getSetting('currencyCode')) {
            $currency_code = getSetting('currencyCode');
        } else {
            $currency_code = env('DEFAULT_CURRENCY');
        }

        $language = Language::where('code', $language_code)->first();
        if ($language?->is_rtl == 1) {
            $direction = 'rtl';
            $default_text_align = 'right';
            $reverse_text_align = 'left';
        } else {
            $direction = 'ltr';
            $default_text_align = 'left';
            $reverse_text_align = 'right';
        }

        $font_family = 'roboto';
        $pdfTempDir = storage_path('app/mpdf-temp');
        if (! is_dir($pdfTempDir)) {
            @mkdir($pdfTempDir, 0755, true);
        }

        $pdfConfig = [
            'custom_font_dir' => public_path('fonts').DIRECTORY_SEPARATOR,
            'custom_font_data' => [
                'hindsiliguri' => [
                    'R' => 'HindSiliguri-Regular.ttf',
                    'B' => 'HindSiliguri-Regular.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
                'roboto' => [
                    'R' => 'Roboto-Regular.ttf',
                    'B' => 'Roboto-Regular.ttf',
                ],
            ],
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => is_dir($pdfTempDir) && is_writable($pdfTempDir) ? $pdfTempDir : sys_get_temp_dir(),
        ];

        if ($currency_code == 'BDT' || $currency_code == 'bdt' || $language_code == 'bd' || $language_code == 'bn') {
            $font_family = 'hindsiliguri';
        }

        $fontFiles = [
            'hindsiliguri' => 'HindSiliguri-Regular.ttf',
            'roboto' => 'Roboto-Regular.ttf',
        ];
        if (! is_file(public_path('fonts/'.($fontFiles[$font_family] ?? '')))) {
            $font_family = is_file(public_path('fonts/Roboto-Regular.ttf')) ? 'roboto' : 'sans-serif';
        }

        $pdfConfig['default_font'] = $font_family;

        $order = Order::shopOrders()
            ->with([
                'orderGroup.transaction',
                'orderGroup.billingAddress',
                'orderGroup.shippingAddress',
                'orderItems.productVariation.product',
                'orderItems.productVariation.combinations.variationValue',
                'user',
            ])
            ->whereId((int) $id)
            ->first();

        if (! $order) {
            abort(404, translate('Order not found'));
        }

        $exchange = null;
        if ($order) {
            if ($order->is_exchange_order) {
                $exchange = Exchange::where('exchange_order_id', $order->id)
                    ->with([
                        'items.originalVariation.product',
                        'items.exchangeVariation.product',
                    ])
                    ->first();
            } else {
                $exchange = Exchange::where('original_order_id', $order->id)
                    ->with([
                        'items.originalVariation.product',
                        'items.exchangeVariation.product',
                    ])
                    ->first();
            }
        }

        // $logo = null;
        // $logoFileId = getSetting('websiteHeaderLogo');
        // if ($logoFileId) {
        //     $mediaFile = \App\Models\MediaFile::find($logoFileId);
        //     if ($mediaFile && $mediaFile->media_file) {
        //         $logo = public_path($mediaFile->media_file);
        //     }
        // }

        $logo = null;
        $logoFileId = getSetting('invoiceLogo') ?: getSetting('websiteHeaderLogo');

        if ($logoFileId) {
            $mediaFile = MediaFile::find($logoFileId);
            if ($mediaFile && $mediaFile->media_file) {
                $logo = public_path($mediaFile->media_file);

                // Convert webp/png to a pdf-safe image before mPDF reads it.
                $logo = is_file($logo) ? self::makePdfSafeLogo($logo) : null;
            }
        }

        $systemName = getSetting('invoiceCompanyName') ?: getSetting('systemName');
        $systemAddress = getSetting('invoiceCompanyAddress') ?: getSetting('systemAddress');
        $preparedByTitle = getSetting('invoicePreparedByTitle') ?: translate('Prepared By');
        $preparedByName = getSetting('invoicePreparedByName') ?: $systemName;
        $preparedByDepartment = getSetting('invoicePreparedByDepartment') ?: translate('Sales Department');
        $invoiceQrCodeEnabled = getSetting('invoiceQrCodeEnabled') != '0';

        // Generate QR code for invoice verification
        $qrCodeImage = null;
        if ($invoiceQrCodeEnabled) {
            try {
                $orderHash = encodeInvoiceId($order->id);
                $qrUrl = route('invoice.public.verify', ['hash' => $orderHash]);

                $qrCode = new \Endroid\QrCode\QrCode(
                    data: $qrUrl,
                    size: 200,
                    margin: 10,
                    foregroundColor: new \Endroid\QrCode\Color\Color(0, 0, 0)
                );

                $writer = new \Endroid\QrCode\Writer\PngWriter;
                $result = $writer->write($qrCode);
                $qrCodeImage = self::makePdfSafeImageDataUri($result->getString()) ?: null;
            } catch (\Exception $e) {
                Log::error('Failed to generate QR code for invoice: '.$e->getMessage());
                $qrCodeImage = null;
            }
        }

        $viewData = [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'default_text_align' => $default_text_align,
            'reverse_text_align' => $reverse_text_align,
            'logo' => $logo,
            'systemName' => $systemName,
            'systemAddress' => $systemAddress,
            'preparedByTitle' => $preparedByTitle,
            'preparedByName' => $preparedByName,
            'preparedByDepartment' => $preparedByDepartment,
            'qrCodeImage' => $qrCodeImage,
            'exchange' => $exchange,
        ];

        if ($multiple) {
            return PDF::loadView('backend.admin.orders.invoice', $viewData, [], $pdfConfig);
        } else {
            $invoiceCode = trim((string) getSetting('order_code_prefix', '').(string) ($order->orderGroup?->code ?? $order->order_code));
            $filename = preg_replace('/[^A-Za-z0-9._-]+/', '-', $invoiceCode);
            $filename = trim((string) $filename, '-_.');
            $filename = ($filename !== '' ? $filename : 'invoice-'.$order->id).'.pdf';

            return PDF::loadView('backend.admin.orders.invoice', $viewData, [], $pdfConfig)->stream($filename);
        }
    }

    public function downloadSinglePageInvoice(array $ids)
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', Config::get('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        if (getSetting('currencyCode')) {
            $currency_code = getSetting('currencyCode');
        } else {
            $currency_code = env('DEFAULT_CURRENCY');
        }

        if (Language::where('code', $language_code)->first()->is_rtl == 1) {
            $direction = 'rtl';
            $default_text_align = 'right';
            $reverse_text_align = 'left';
        } else {
            $direction = 'ltr';
            $default_text_align = 'left';
            $reverse_text_align = 'right';
        }

        $font_family = 'roboto';
        $pdfConfig = [
            'custom_font_dir' => public_path('fonts').DIRECTORY_SEPARATOR,
            'custom_font_data' => [
                'hindsiliguri' => [
                    'R' => 'HindSiliguri-Regular.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ];

        if ($currency_code == 'BDT' || $currency_code == 'bdt' || $language_code == 'bd' || $language_code == 'bn') {
            $font_family = 'hindsiliguri';
        }

        $pdfConfig['default_font'] = $font_family;

        $orders = Order::shopOrders()
            ->with([
                'orderGroup.transaction',
                'orderGroup.shippingAddress',
                'orderGroup.billingAddress',
                'orderItems.productVariation.product',
                'orderItems.productVariation.combinations.variationValue',
                'user',
            ])
            ->whereIn('id', array_map('intval', $ids))
            ->orderByRaw('FIELD(id, '.implode(',', array_map('intval', $ids)).')')
            ->get();

        $logo = null;
        $logoFileId = getSetting('invoiceLogo') ?: getSetting('websiteHeaderLogo');

        if ($logoFileId) {
            $mediaFile = MediaFile::find($logoFileId);
            if ($mediaFile && $mediaFile->media_file) {
                $logo = public_path($mediaFile->media_file);
                $logo = self::makePdfSafeLogo($logo);
            }
        }

        return PDF::loadView('backend.admin.orders.invoice-compact', [
            'orders' => $orders,
            'font_family' => $font_family,
            'direction' => $direction,
            'default_text_align' => $default_text_align,
            'reverse_text_align' => $reverse_text_align,
            'logo' => $logo,
            'systemName' => getSetting('invoiceCompanyName') ?: getSetting('systemName'),
            'systemAddress' => getSetting('invoiceCompanyAddress') ?: getSetting('systemAddress'),
        ], [], $pdfConfig)->stream('single_page_invoices_'.now()->format('Y-m-d_H-i-s').'.pdf');
    }

    private static function makePdfSafeLogo(string $absolutePath): string
    {
        return self::makePdfSafeImage($absolutePath, 'logo_pdf_safe');
    }

    public static function makePdfSafeImage(string $absolutePath, string $prefix = 'image_pdf_safe'): string
    {
        if (! is_file($absolutePath) || filesize($absolutePath) <= 0) {
            return '';
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        // If already jpg/jpeg, it's generally safe
        if (in_array($ext, ['jpg', 'jpeg'])) {
            return @getimagesize($absolutePath) ? $absolutePath : '';
        }

        $cacheDir = storage_path('app/pdf-cache');
        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $hash = md5($absolutePath.'|'.@filemtime($absolutePath));
        $safePrefix = preg_replace('/[^a-z0-9_-]/i', '_', $prefix);
        $out = $cacheDir.DIRECTORY_SEPARATOR."{$safePrefix}_{$hash}.jpg";

        if (is_file($out) && filesize($out) > 0 && @getimagesize($out)) {
            return $out;
        }

        if (! extension_loaded('gd')) {
            return '';
        }

        $src = null;

        if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
            $src = @imagecreatefromwebp($absolutePath);
        } elseif ($ext === 'png' && function_exists('imagecreatefrompng')) {
            $src = @imagecreatefrompng($absolutePath);
        } elseif (in_array($ext, ['jpg', 'jpeg']) && function_exists('imagecreatefromjpeg')) {
            $src = @imagecreatefromjpeg($absolutePath);
        }

        if (! $src) {
            return @getimagesize($absolutePath) ? $absolutePath : '';
        }

        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= 0 || $h <= 0) {
            imagedestroy($src);

            return '';
        }

        // Create white canvas to avoid black background from transparency
        $dst = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $w, $h, $white);

        imagealphablending($dst, true);
        imagesavealpha($dst, false);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);

        $saved = imagejpeg($dst, $out, 90);

        imagedestroy($src);
        imagedestroy($dst);

        return $saved && is_file($out) && filesize($out) > 0 && @getimagesize($out) ? $out : '';
    }

    private static function makePdfSafeImageDataUri(string $imageContents): string
    {
        if ($imageContents === '' || ! extension_loaded('gd')) {
            return '';
        }

        $src = @imagecreatefromstring($imageContents);
        if (! $src) {
            return '';
        }

        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= 0 || $h <= 0) {
            imagedestroy($src);

            return '';
        }

        $dst = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $w, $h, $white);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);

        ob_start();
        $saved = imagejpeg($dst, null, 90);
        $jpg = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        return $saved && $jpg ? 'data:image/jpeg;base64,'.base64_encode($jpg) : '';
    }

    // download Challan
    public function downloadChallan($id)
    {
        if (session()->has('locale')) {
            $language_code = session()->get('locale', Config::get('app.locale'));
        } else {
            $language_code = env('DEFAULT_LANGUAGE');
        }

        if (getSetting('currencyCode')) {
            $currency_code = getSetting('currencyCode');
        } else {
            $currency_code = env('DEFAULT_CURRENCY');
        }

        if (Language::where('code', $language_code)->first()->is_rtl == 1) {
            $direction = 'rtl';
            $default_text_align = 'right';
            $reverse_text_align = 'left';
        } else {
            $direction = 'ltr';
            $default_text_align = 'left';
            $reverse_text_align = 'right';
        }

        if ($currency_code == 'BDT' || $currency_code == 'bdt' || $language_code == 'bd' || $language_code == 'bn') {
            // bengali font
            $font_family = "'Hind Siliguri','sans-serif'";
        } elseif ($currency_code == 'KHR' || $language_code == 'kh') {
            // khmer font
            $font_family = "'Khmeros','sans-serif'";
        } elseif ($currency_code == 'AMD') {
            // Armenia font
            $font_family = "'arnamu','sans-serif'";
        } elseif ($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa' || $currency_code == 'IQD' || $language_code == 'ir') {
            // middle east/arabic font
            $font_family = "'XBRiyaz','sans-serif'";
        } else {
            // general for all
            $font_family = "'Roboto','sans-serif'";
        }

        $order = Order::shopOrders()->whereId((int) $id)->with('shop')->first();
        $logoSetting = uploadedAsset($order->shop?->logo);

        return PDF::loadView('backend.admin.orders.challan', [
            'order' => $order,
            'logo' => $logoSetting,
            'font_family' => $font_family,
            'direction' => $direction,
            'default_text_align' => $default_text_align,
            'reverse_text_align' => $reverse_text_align,
        ], [], [])->stream(getSetting('order_code_prefix').$order->order_code.'.pdf');
    }

    // store new order updates
    public function storeOrderUpdates($request)
    {
        $data = [
            'status' => 200,
            'message' => '',
            'result' => [],
        ];

        $order = Order::shopOrders()->whereId($request->id)->first();

        if (is_null($order)) {
            $data = [
                'status' => 404,
                'message' => translate('Order not found'),
                'result' => [],
            ];

            return $data;
        }

        // order update
        OrderUpdate::create([
            'order_id' => $order->id,
            'user_id' => userId(),
            'type' => 'custom',
            'status' => $request->status,
            'note' => $request->note,
            'created_by' => userId(),
        ]);
        try {
            $sms = app(SmsService::class); // Dynamically resolve again
            if ($user && $user->phone && $user->phone != '') {
                $message = $user->name.' Your order Status Updated To'.$request->note.' In '.env('APP_NAME').'Order Code:'.$order->order_code;
                $sms->send(normalizeBangladeshiNumber($to), $message);
            }
        } catch (\Exception $e) {
        }
        $data = [
            'status' => 200,
            'message' => translate('Order note published successfully'),
            'result' => [],
        ];

        return $data;
    }

    // update order tracking
    public function updateOrderTracking($request)
    {
        $data = [
            'status' => 200,
            'message' => '',
            'result' => [],
        ];

        $order = Order::shopOrders()->whereId($request->id)->first();

        if (is_null($order)) {
            $data = [
                'status' => 404,
                'message' => translate('Order not found'),
                'result' => [],
            ];

            return $data;
        }

        $order->courier_name = $request->courier_name;
        $order->tracking_number = $request->tracking_number;
        $order->tracking_url = $request->tracking_url;
        $order->save();

        $data = [
            'status' => 200,
            'message' => translate('Order tracking info updated successfully'),
            'result' => [],
        ];

        return $data;
    }

    // delete order update
    public function deleteOrderUpdate($id)
    {
        $data = [
            'status' => 200,
            'message' => '',
            'result' => [],
        ];

        $orderUpdate = OrderUpdate::whereId($id)->first();
        if (is_null($orderUpdate)) {
            $data = [
                'status' => 404,
                'message' => translate('Note not found'),
                'result' => [],
            ];

            return $data;
        }
        if (! $orderUpdate->order || $orderUpdate->order->shop_id != shopId()) {
            $data = [
                'status' => 404,
                'message' => translate('This order does not belongs to you'),
                'result' => [],
            ];

            return $data;
        }

        $orderUpdate->delete();

        $data = [
            'status' => 200,
            'message' => translate('Order note deleted successfully'),
            'result' => [],
        ];

        return $data;
    }

    // update payment status
    public function updatePaymentStatus($request)
    {
        $order = Order::shopOrders()->whereId((int) $request->order_id)->first();
        $oldStatus = $order->payment_status;
        $order->payment_status = $request->status;
        $order->save();

        $this->addCommissionHistory($order);

        syncOrderProfitLog($order);

        if (user()->user_type == 'seller' && $order->delivery_status != 'cancelled') {
            if ($request->status == 'paid') {
                $this->addShopBalance($order);
            } else {
                $this->reduceShopBalance($order);
            }
        }

        if ($oldStatus !== $request->status) {
            OrderUpdate::create([
                'order_id' => $order->id,
                'user_id' => userId(),
                'status' => ucwords(str_replace('_', ' ', $request->status)),
                'note' => 'Payment status updated to '.ucwords(str_replace('_', ' ', $request->status)).'.',
                'created_by' => userId(),
            ]);
        }

        // todo::['mail notification']
        $order->loadMissing([
            'latestDeliveryStatusUpdate.creator',
            'latestDeliveryStatusUpdate.user',
        ]);

        $data = [
            'status' => 200,
            'message' => translate('Payment status updated successfully'),
            'result' => [
                'order' => $this->formatStatusUpdatePayload($order),
            ],
        ];

        return $data;
    }

    // update delivery status
    public function updateDeliveryStatus($request)
    {
        $order = Order::shopOrders()->whereId((int) $request->order_id)->first();
        $this->addCommissionHistory($order);
        $oldStatus = $order->delivery_status;

        if ($order->delivery_status != 'cancelled' && $request->status == 'cancelled') {
            $this->addQtyToStock($order);

            if (user()->user_type == 'seller') {
                $this->reduceShopBalance($order);
            }

            cancelAgentCommission($order);
        }

        if ($order->delivery_status == 'cancelled' && $request->status != 'cancelled') {
            $this->removeQtyFromStock($order);

            if (user()->user_type == 'seller') {
                $this->addShopBalance($order);
            }
        }

        $order->delivery_status = $request->status;
        $order->save();

        syncOrderProfitLog($order);

        if ($request->status == 'delivered') {
            earnAgentCommission($order);
        }

        if ($oldStatus !== $request->status) {
            OrderUpdate::create([
                'order_id' => $order->id,
                'user_id' => userId(),
                'status' => ucwords(str_replace('_', ' ', $request->status)),
                'created_by' => userId(),
                'note' => 'Delivery status updated to '.ucwords(str_replace('_', ' ', $request->status)).'.',
            ]);
        }

        // todo::['mail notification']
        $order->loadMissing([
            'latestDeliveryStatusUpdate.creator',
            'latestDeliveryStatusUpdate.user',
        ]);

        $data = [
            'status' => 200,
            'message' => translate('Delivery status updated successfully'),
            'result' => [
                'order' => $this->formatStatusUpdatePayload($order),
            ],
        ];

        return $data;
    }

    private function formatStatusUpdatePayload(Order $order): array
    {
        $latestDeliveryStatusUpdate = $order->latestDeliveryStatusUpdate;

        return [
            'id' => $order->id,
            'deliveryStatus' => $order->delivery_status,
            'deliveryStatusToShow' => ucfirst(str_replace('_', ' ', $order->delivery_status)),
            'latestDeliveryUpdate' => $latestDeliveryStatusUpdate ? [
                'status' => $latestDeliveryStatusUpdate->status,
                'note' => $latestDeliveryStatusUpdate->note,
                'actorName' => $latestDeliveryStatusUpdate->getActorName() ?? 'System',
                'actorRole' => $latestDeliveryStatusUpdate->getActorRole(),
                'createdDate' => date('d M, Y', strtotime($latestDeliveryStatusUpdate->created_at)),
                'createdTime' => date('h:i A', strtotime($latestDeliveryStatusUpdate->created_at)),
            ] : null,
        ];
    }

    // updateOrderAddress
    public function updateOrderAddress($request)
    {
        $order = Order::shopOrders()->whereId((int) $request->id)->first();

        if (is_null($order)) {
            $data = [
                'status' => 404,
                'message' => translate('Order not found'),
                'result' => [],
            ];

            return $data;
        }

        $orderGroup = $order->orderGroup;
        if (in_array($request->type, ['shipping_address', 'shipping'], true)) {
            $orderGroup->shipping_address = $request->address;
            $orderGroup->direction = $request->direction;
            $orderGroup->phone = $request->phone;
        } else {
            $orderGroup->billing_address = $request->address;
        }
        $orderGroup->save();

        $data = [
            'status' => 200,
            'message' => translate('Address updated successfully'),
            'result' => [],
        ];

        return $data;
    }

    // orderPayment
    public function orderPayment($request)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($request->payable_id);

            $order->payment_status = 'paid';
            $order->save();

            $transaction = $order->orderGroup->transaction;
            $transaction->status = 'paid';
            $transaction->payment_method = $request->payment_method;
            $transaction->is_manual_payment = true;
            $transaction->manual_payment_details = $request->note;
            $transaction->updated_at = Carbon::parse($request->date);
            $transaction->updated_by = userId();
            $transaction->save();

            DB::commit();

            return [
                'status' => 200,
                'message' => __('Manual payment successful'),
                'result' => [],
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'message' => __('Payment failed'),
                'result' => [],
            ];
        }
    }

    // add qty to stock
    public function addQtyToStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $stock = $orderItem->productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();
            if ($stock) {
                $stock->stock_qty += $orderItem->qty;
                $stock->save();
            }

            $product = $orderItem->productVariation->product;
            $product->total_sale_count -= $orderItem->qty;
            $product->save();

            // todo:: handle category, brands sale count & amount
            // if ($product->categories()->count() > 0) {
            //     foreach ($product->categories as $category) {
            //         $category->total_sale_count -= $orderItem->qty;
            //         $category->save();
            //     }
            // }
        }
    }

    // remove qty from stock
    public function removeQtyFromStock($order)
    {
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $stock = $orderItem->productVariation->productVariationStocks()->where('warehouse_id', $order->warehouse_id)->first();
            if ($stock) {
                $stock->stock_qty -= $orderItem->qty;
                $stock->save();
            }

            $product = $orderItem->productVariation->product;
            $product->total_sale_count += $orderItem->qty;
            $product->save();
            // todo:: handle category, brads sale count & amount
            // if ($product->categories()->count() > 0) {
            //     foreach ($product->categories as $category) {
            //         $category->total_sale_count += $orderItem->qty;
            //         $category->save();
            //     }
            // }
        }
    }

    // reduceShopBalance
    public function reduceShopBalance($order)
    {
        $commission = $order->commissionHistory;
        $shop = $order->shop;
        $shop->current_balance -= $commission->shop_earning_amount;
        $shop->save();
    }

    // addShopBalance
    public function addShopBalance($order)
    {
        $commission = $order->commissionHistory;
        $shop = $order->shop;
        $shop->current_balance += $commission->shop_earning_amount;
        $shop->save();
    }

    // addShopBalance
    public function addCommissionHistory($order)
    {
        $commission = $order->commissionHistory;
        if (is_null($commission)) {
            $shop = $order->shop;

            $adminCommissionRate = $shop->admin_commission_percentage;
            $adminCommission = 0;
            $sellerEarning = $order->total_amount;

            if ($adminCommissionRate > 0) {
                $adminCommission = ($adminCommissionRate * $sellerEarning) / 100;
                $sellerEarning -= $adminCommission;
            }

            $commission = new CommissionHistory;
            $commission->shop_id = $shop->id;
            $commission->order_id = $order->id;
            $commission->admin_commission_percentage = $adminCommissionRate;
            $commission->amount = $order->total_amount;
            $commission->admin_earning_amount = $adminCommission;
            $commission->shop_earning_amount = $sellerEarning;
            $commission->save();
        }
    }
}
