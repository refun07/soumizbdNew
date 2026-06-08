<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesReportController extends Controller
{
    private const DELIVERY_STATUSES = [
        'order_placed' => 'Order Placed',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'unbanned', 'xss']);
        $this->middleware('permission:bizana_view_sales')->only(['getData', 'export', 'getShops']);
    }

    public function getShops(): JsonResponse
    {
        $shops = Shop::isApproved()->get(['id', 'name']);

        return response()->json([
            'shops' => $shops,
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        $dateFrom = Carbon::parse($request->input('date_from', now()->subMonth()->startOfDay()))->startOfDay();
        $dateTo = Carbon::parse($request->input('date_to', now()->endOfDay()))->endOfDay();
        $shopId = $request->input('shop_id');
        $deliveryStatus = $request->input('delivery_status', 'all');
        $page = (int) $request->input('page', 1);
        $perPage = (int) ($request->input('per_page', 25));

        $productSales = $this->getProductSalesQuery($dateFrom, $dateTo, $shopId, $deliveryStatus)
            ->paginate($perPage, ['*'], 'page', $page);

        $summary = $this->getSummary($dateFrom, $dateTo, $shopId, $deliveryStatus);

        return response()->json([
            'productSales' => $productSales->items(),
            'pagination' => [
                'current_page' => $productSales->currentPage(),
                'last_page' => $productSales->lastPage(),
                'per_page' => $productSales->perPage(),
                'total' => $productSales->total(),
                'from' => $productSales->firstItem(),
                'to' => $productSales->lastItem(),
            ],
            'summary' => $summary,
            'filters' => [
                'date_from' => $dateFrom->format('Y-m-d'),
                'date_to' => $dateTo->format('Y-m-d'),
                'shop_id' => $shopId,
                'delivery_status' => $deliveryStatus,
            ],
        ]);
    }

    public function export(Request $request): Response
    {
        $dateFrom = Carbon::parse($request->input('date_from', now()->subMonth()->startOfDay()))->startOfDay();
        $dateTo = Carbon::parse($request->input('date_to', now()->endOfDay()))->endOfDay();
        $shopId = $request->input('shop_id');
        $deliveryStatus = $request->input('delivery_status', 'all');

        $shopName = $shopId ? Shop::find($shopId)?->name : 'All Shops';

        $productSales = $this->getProductSalesQuery($dateFrom, $dateTo, $shopId, $deliveryStatus)->get();
        $summary = $this->getSummary($dateFrom, $dateTo, $shopId, $deliveryStatus);

        $csv = \League\Csv\Writer::createFromString('');
        $csv->setOutputBOM(\League\Csv\ByteSequence::BOM_UTF8);

        $csv->insertOne(['Sales Report']);
        $csv->insertOne([
            sprintf('Period: %s to %s', $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')),
        ]);
        $statusLabel = $deliveryStatus === 'all' ? 'All (excl. Cancelled)' : (self::DELIVERY_STATUSES[$deliveryStatus] ?? $deliveryStatus);
        $csv->insertOne([sprintf('Shop: %s', $shopName)]);
        $csv->insertOne([sprintf('Delivery Status: %s', $statusLabel)]);
        $csv->insertOne([sprintf('Generated at: %s', now()->format('Y-m-d H:i:s'))]);
        $csv->insertOne([]);

        $csv->insertOne(['SKU', 'Product Name', 'Qty', 'Unit Price', 'Total Price', 'Discount']);

        foreach ($productSales as $sale) {
            $csv->insertOne([
                $sale->sku ?: 'N/A',
                $sale->product_name,
                $sale->total_qty,
                number_format($sale->unit_price, 2, '.', ''),
                number_format($sale->total_price, 2, '.', ''),
                number_format($sale->total_discount, 2, '.', ''),
            ]);
        }

        $csv->insertOne([]);
        $csv->insertOne(['--- Summary ---']);
        $csv->insertOne(['Total Unique Products', $summary['total_products']]);
        $csv->insertOne(['Total Quantity Sold', $summary['total_qty']]);
        $csv->insertOne(['Total Revenue', number_format($summary['total_revenue'], 2, '.', '')]);
        $csv->insertOne(['Total Discount Given', number_format($summary['total_discount'], 2, '.', '')]);
        $csv->insertOne(['Total Delivery Charges', number_format($summary['total_delivery_charge'], 2, '.', '')]);
        $csv->insertOne(['Total Tax', number_format($summary['total_tax'], 2, '.', '')]);
        $csv->insertOne(['Net Amount', number_format($summary['net_amount'], 2, '.', '')]);

        $filename = sprintf(
            'sales_report_%s_to_%s_%s.csv',
            $dateFrom->format('Y-m-d'),
            $dateTo->format('Y-m-d'),
            now()->format('His')
        );

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function buildSalesQuery(Carbon $dateFrom, Carbon $dateTo, ?string $shopId, string $deliveryStatus = 'all')
    {
        $query = OrderItem::query()
            ->join('orders as o', 'order_items.order_id', '=', 'o.id')
            ->join('product_variations as pv', 'order_items.product_variation_id', '=', 'pv.id')
            ->join('products as p', 'pv.product_id', '=', 'p.id')
            ->whereBetween('o.created_at', [$dateFrom, $dateTo])
            ->whereNull('o.deleted_at');

        if ($deliveryStatus === 'all') {
            $query->where('o.delivery_status', '!=', 'cancelled');
        } else {
            $query->where('o.delivery_status', $deliveryStatus);
        }

        if ($shopId) {
            $query->where('o.shop_id', $shopId);
        }

        return $query;
    }

    private function getProductSalesQuery(Carbon $dateFrom, Carbon $dateTo, ?string $shopId, string $deliveryStatus = 'all')
    {
        return $this->buildSalesQuery($dateFrom, $dateTo, $shopId, $deliveryStatus)
            ->groupBy('order_items.product_variation_id', 'order_items.unit_price')
            ->selectRaw('
                order_items.product_variation_id,
                pv.sku as sku,
                p.name as product_name,
                SUM(order_items.qty) as total_qty,
                order_items.unit_price,
                SUM(order_items.total_price) as total_price,
                SUM(order_items.total_discount) as total_discount
            ')
            ->orderByDesc('total_qty');
    }

    private function getSummary(Carbon $dateFrom, Carbon $dateTo, ?string $shopId, string $deliveryStatus = 'all'): array
    {
        $orderQuery = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($deliveryStatus === 'all') {
            $orderQuery->where('delivery_status', '!=', 'cancelled');
        } else {
            $orderQuery->where('delivery_status', $deliveryStatus);
        }

        if ($shopId) {
            $orderQuery->where('shop_id', $shopId);
        }

        $aggregates = (clone $orderQuery)->selectRaw('
            SUM(total_amount) as total_revenue,
            SUM(discount_amount) as total_discount,
            SUM(coupon_discount_amount) as coupon_discount,
            SUM(shipping_charge_amount) as total_delivery_charge,
            SUM(tax_amount) as total_tax,
            COUNT(*) as total_orders
        ')->first();

        $itemSummary = $this->buildSalesQuery($dateFrom, $dateTo, $shopId, $deliveryStatus)
            ->selectRaw('
                COUNT(DISTINCT order_items.product_variation_id) as total_products,
                SUM(order_items.qty) as total_qty,
                SUM(order_items.total_discount) as total_item_discount
            ')
            ->first();

        $totalRevenue = (float) ($aggregates->total_revenue ?? 0);
        $totalDiscount = (float) ($aggregates->total_discount ?? 0);
        $couponDiscount = (float) ($aggregates->coupon_discount ?? 0);
        $totalItemDiscount = (float) ($itemSummary->total_item_discount ?? 0);

        return [
            'total_products' => (int) ($itemSummary->total_products ?? 0),
            'total_qty' => (int) ($itemSummary->total_qty ?? 0),
            'total_revenue' => $totalRevenue,
            'total_discount' => $totalDiscount + $couponDiscount + $totalItemDiscount,
            'total_delivery_charge' => (float) ($aggregates->total_delivery_charge ?? 0),
            'total_tax' => (float) ($aggregates->total_tax ?? 0),
            'net_amount' => $totalRevenue + (float) ($aggregates->total_delivery_charge ?? 0) - $totalDiscount - $couponDiscount - $totalItemDiscount,
            'total_orders' => (int) ($aggregates->total_orders ?? 0),
        ];
    }
}
