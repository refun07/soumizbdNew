<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Helpers\ExpenseCategoryHelper;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:bizana_view']);
    }

    /**
     * Quick loading data for initial page load
     * Returns essential metrics that load quickly
     */
    public function quickData(Request $request)
    {
        $cacheKey = 'bizana_dashboard_quick_' . md5(auth()->id() . request('from', '') . request('to', ''));

        $data = Cache::remember($cacheKey, 300, function () {
            $dateFrom = Carbon::parse(request('from', now()->subMonth()->startOfDay()))->startOfDay();
            $dateTo = Carbon::parse(request('to', now()->endOfDay()))->endOfDay();

            return [
                'summary' => $this->getQuickSummary($dateFrom, $dateTo),
                'orderStats' => $this->getOrderStats($dateFrom, $dateTo),
                'netProfit' => $this->getNetProfit($dateFrom, $dateTo),
            ];
        });

        return response()->json($data);
    }

    /**
     * Detailed data for complete dashboard
     * Includes charts and tables that may take longer to load
     */
    public function detailedData(Request $request)
    {
        $dateFrom = Carbon::parse(request('from', now()->subMonth()->startOfDay()))->startOfDay();
        $dateTo = Carbon::parse(request('to', now()->endOfDay()))->endOfDay();

        // Get pagination parameters
        $productsLimit = request('products_limit', 6);
        $categoriesLimit = request('categories_limit', 6);
        $productsPage = request('products_page', 1);
        $categoriesPage = request('categories_page', 1);

        $data = [
            'growthComparison' => $this->getGrowthComparisonData($dateFrom, $dateTo),
            'expenseBreakdown' => $this->getExpenseBreakdownData($dateFrom, $dateTo),
            'topSellingProducts' => $this->getTopSellingProducts($dateFrom, $dateTo, $productsLimit, $productsPage),
            'topSellingCategories' => $this->getTopSellingCategories($dateFrom, $dateTo, $categoriesLimit, $categoriesPage),
            'revenueOverTime' => $this->getRevenueOverTime($dateFrom, $dateTo),
        ];

        return response()->json($data);
    }

    /**
     * Delivery partner analytics with date range filter.
     */
    public function deliveryPartnerData(Request $request)
    {
        $dateFrom = Carbon::parse($request->query('from', now()->subMonth()->startOfDay()))->startOfDay();
        $dateTo = Carbon::parse($request->query('to', now()->endOfDay()))->endOfDay();

        $partnerCase = "LOWER(COALESCE(NULLIF(order_groups.courier_partner, ''), NULLIF(orders.courier_name, ''), 'others'))";

        $rows = DB::table('orders')
            ->leftJoin('order_groups', 'orders.order_group_id', '=', 'order_groups.id')
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->selectRaw("
                {$partnerCase} as partner,
                COUNT(orders.id) as total_orders,
                SUM(CASE WHEN LOWER(COALESCE(order_groups.delivery_status, orders.delivery_status, '')) = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN LOWER(COALESCE(order_groups.delivery_status, orders.delivery_status, '')) = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                SUM(COALESCE(orders.shipping_charge_amount, 0)) as total_delivery_charge
            ")
            ->groupBy('partner')
            ->get();

        $allowedPartners = ['pathao', 'steadfast', 'redx'];
        $partnerMap = collect($rows)->keyBy(function ($row) use ($allowedPartners) {
            $partner = strtolower((string) $row->partner);
            return in_array($partner, $allowedPartners, true) ? $partner : 'others';
        });

        $formatRow = function ($row, string $partner) {
            $safeRow = is_object($row) ? $row : (object) [];
            return [
                'partner' => $partner,
                'totalOrders' => (int) ($safeRow->total_orders ?? 0),
                'deliveredOrders' => (int) ($safeRow->delivered_orders ?? 0),
                'cancelledOrders' => (int) ($safeRow->cancelled_orders ?? 0),
                'deliveryCharge' => (float) ($safeRow->total_delivery_charge ?? 0),
            ];
        };

        $partners = [
            $formatRow($partnerMap->get('pathao'), 'pathao'),
            $formatRow($partnerMap->get('steadfast'), 'steadfast'),
            $formatRow($partnerMap->get('redx'), 'redx'),
            $formatRow($partnerMap->get('others'), 'others'),
        ];

        $totals = [
            'totalOrders' => array_sum(array_column($partners, 'totalOrders')),
            'deliveredOrders' => array_sum(array_column($partners, 'deliveredOrders')),
            'cancelledOrders' => array_sum(array_column($partners, 'cancelledOrders')),
            'deliveryCharge' => array_sum(array_column($partners, 'deliveryCharge')),
        ];

        return response()->json([
            'dateRange' => [
                'from' => $dateFrom->toDateString(),
                'to' => $dateTo->toDateString(),
            ],
            'credentials' => [
                'pathao' => [
                    'configured' => !empty(config('services.pathao.client_id'))
                        && !empty(config('services.pathao.client_secret'))
                        && !empty(config('services.pathao.username'))
                        && !empty(config('services.pathao.password')),
                ],
                'steadfast' => [
                    'configured' => !empty(config('services.steadfast.api_key'))
                        && !empty(config('services.steadfast.secret_key')),
                ],
                'redx' => [
                    'configured' => !empty(config('services.redx.api_key')),
                ],
            ],
            'partners' => $partners,
            'totals' => $totals,
        ]);
    }

    /**
     * Get quick summary metrics
     */
    private function getQuickSummary($dateFrom, $dateTo)
    {
        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();

        // Calculate average purchase price per product variation
        $avgPurchasePrices = \DB::table('purchase_order_product_variations as po')
            ->select('po.product_variation_id', \DB::raw('AVG(po.unit_price) as avg_price'))
            ->groupBy('po.product_variation_id')
            ->get()
            ->keyBy('product_variation_id');

        // Calculate total purchase cost of products sold using average prices
        $totalPurchaseCost = \DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->whereBetween('o.created_at', [$dateFrom, $dateTo])
            ->selectRaw('oi.product_variation_id, oi.qty')
            ->get()
            ->sum(function ($item) use ($avgPurchasePrices) {
                $avgPrice = $avgPurchasePrices->get($item->product_variation_id)->avg_price ?? 0;
                return $item->qty * $avgPrice;
            });

        // Log for debugging
        $grossProfit = $totalRevenue - $totalPurchaseCost;
        \Log::channel('daily')->info('Bizana Dashboard - Revenue & Cost Debug', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_revenue' => $totalRevenue,
            'total_purchase_cost' => $totalPurchaseCost,
            'gross_profit' => $grossProfit,
            'total_orders' => $totalOrders,
        ]);

        // Get expenses from acc_expenses table
        $expenses = \DB::table('acc_expenses')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->sum('amount');

        // Daily expenses might not exist yet, default to 0
        $dailyExpenses = 0;
        $totalExpenses = $expenses + $dailyExpenses;

        // Calculate delivery charges (simplified - you may need to adjust based on your schema)
        $deliveryCharges = $orders->sum('shipping_charge_amount');

        // Get value of damaged items (from returns + direct entries)
        $damagedFromReturns = \DB::table('damage_stocks as ds')
            ->join('return_requests as rr', 'ds.return_request_id', '=', 'rr.id')
            ->join('return_request_items as rri', 'rr.id', '=', 'rri.return_request_id')
            ->whereBetween('ds.created_at', [$dateFrom, $dateTo])
            ->whereNotNull('ds.return_request_id')
            ->sum(\DB::raw('ds.quantity * rri.price'));

        $damagedDirect = \DB::table('damage_stocks as ds')
            ->join('product_variations as pv', 'ds.product_variation_id', '=', 'pv.id')
            ->whereBetween('ds.created_at', [$dateFrom, $dateTo])
            ->whereNull('ds.return_request_id')
            ->sum(\DB::raw('ds.quantity * pv.price'));

        $damagedOrdersValue = $damagedFromReturns + $damagedDirect;

        return [
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'grossProfit' => $grossProfit,
            'expenses' => $totalExpenses,
            'deliveryCharges' => $deliveryCharges,
            'other' => $damagedOrdersValue,  // Replace with damaged order value
        ];
    }

    /**
     * Get order statistics
     */
    private function getOrderStats($dateFrom, $dateTo)
    {
        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo]);

        // NEW: Get return requests instead of delivery_status
        $returnRequestOrders = \DB::table('return_requests')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->distinct('order_id')
            ->count('order_id');

        // Get damaged items count (from returns + direct entries)
        $damagedFromReturns = \DB::table('damage_stocks as ds')
            ->join('return_requests as rr', 'ds.return_request_id', '=', 'rr.id')
            ->whereBetween('ds.created_at', [$dateFrom, $dateTo])
            ->whereNotNull('ds.return_request_id')
            ->distinct('rr.order_id')
            ->count('rr.order_id');

        $damagedDirect = \DB::table('damage_stocks as ds')
            ->whereBetween('ds.created_at', [$dateFrom, $dateTo])
            ->whereNull('ds.return_request_id')
            ->count();

        $damagedOrders = $damagedFromReturns + $damagedDirect;

        return [
            'placed' => $orders->count(),
            'successful' => $orders->where('delivery_status', 'delivered')->count(),
            'returned' => $returnRequestOrders,  // ← Count from return_requests
            'damaged' => $damagedOrders,         // ← Count from damage_stocks
            'cancelled' => $orders->where('delivery_status', 'cancelled')->count(),
        ];
    }

    /**
     * Get net profit calculation
     */
    private function getNetProfit($dateFrom, $dateTo)
    {
        $summary = $this->getQuickSummary($dateFrom, $dateTo);

        $netProfit = $summary['grossProfit'] - $summary['expenses'] - $summary['deliveryCharges'] - $summary['other'];

        // Calculate growth percentage (comparing to previous period)
        $previousDateFrom = (new Carbon($dateFrom))->subDays($dateFrom->diffInDays($dateTo));
        $previousDateTo = (new Carbon($dateFrom))->subDay();

        $previousSummary = $this->getQuickSummary($previousDateFrom, $previousDateTo);
        $previousNetProfit = $previousSummary['grossProfit'] - $previousSummary['expenses'] - $previousSummary['deliveryCharges'] - $previousSummary['other'];

        $growth = $previousNetProfit > 0 ? (($netProfit - $previousNetProfit) / $previousNetProfit) * 100 : 0;

        return [
            'amount' => $netProfit,
            'growth' => round($growth, 2),
        ];
    }

    /**
     * Get growth comparison data for charts
     */
    private function getGrowthComparisonData($dateFrom, $dateTo)
    {
        // Current period data
        $currentPeriod = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Previous period data
        $daysDiff = $dateFrom->diffInDays($dateTo);
        $previousDateFrom = (new Carbon($dateFrom))->subDays($daysDiff);
        $previousDateTo = (new Carbon($dateFrom))->subDay();

        $previousPeriod = Order::whereBetween('created_at', [$previousDateFrom, $previousDateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'current' => $currentPeriod,
            'previous' => $previousPeriod,
        ];
    }

    /**
     * Get expense breakdown data
     */
    private function getExpenseBreakdownData($dateFrom, $dateTo)
    {
        // Get expenses by category from acc_expenses table
        $expenses = \DB::table('acc_expenses')
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderBy('total', 'desc')
            ->get();

        // Map category IDs to display names using ExpenseCategoryHelper
        $categories = [];
        $values = [];

        foreach ($expenses as $expense) {
            $categoryKey = ExpenseCategoryHelper::getCategoryKeyById($expense->category_id);
            $categoryName = $categoryKey ? ExpenseCategoryHelper::getCategoryName($categoryKey) : 'Unknown Category';

            $categories[] = $categoryName;
            $values[] = $expense->total;
        }

        return [
            'categories' => $categories,
            'values' => $values,
        ];
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts($dateFrom, $dateTo, $limit = 6, $page = 1)
    {
        // Ensure page is always an integer
        $page = (int) $page;
        $offset = ($page - 1) * $limit;

        $query = OrderItem::whereHas('order', function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('created_at', [$dateFrom, $dateTo]);
        })
            ->with(['productVariation.product'])
            ->selectRaw('product_variation_id, SUM(qty) as total_quantity, SUM(total_price) as total_revenue')
            ->groupBy('product_variation_id')
            ->orderBy('total_quantity', 'desc');

        // Get total count for pagination - we need a separate count query
        // because count() on a query with selectRaw() creates a different query
        $totalCount = OrderItem::whereHas('order', function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('created_at', [$dateFrom, $dateTo]);
        })
            ->selectRaw('COUNT(DISTINCT product_variation_id) as total')
            ->value('total');

        // Get paginated results
        $products = $query->offset($offset)->limit($limit)->get()
            ->map(function ($item) {
                $product = $item->productVariation?->product;
                return [
                    'id' => $item->product_variation_id,
                    'name' => $product?->name ?? 'Unknown Product',
                    'quantity' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                    'image' => $product?->thumbnail_image ? uploadedAsset($product->thumbnail_image) : null,
                ];
            });

        return [
            'data' => $products,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalCount,
                'last_page' => ceil($totalCount / $limit),
            ]
        ];
    }

    /**
     * Get top selling categories
     */
    private function getTopSellingCategories($dateFrom, $dateTo, $limit = 6, $page = 1)
    {
        // Ensure page is always an integer
        $page = (int) $page;
        $offset = ($page - 1) * $limit;

        // Build the base query
        $baseQuery = \DB::table('order_items as oi')
            ->join('product_variations as pv', 'oi.product_variation_id', '=', 'pv.id')
            ->join('product_categories as pc', 'pv.product_id', '=', 'pc.product_id')
            ->join('categories as c', 'pc.category_id', '=', 'c.id')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->whereBetween('o.created_at', [$dateFrom, $dateTo])
            ->selectRaw('
                COUNT(DISTINCT pv.product_id) as product_count,
                SUM(oi.qty) as total_quantity,
                SUM(oi.total_price) as total_revenue,
                c.name as category_name,
                c.id as category_id
            ')
            ->groupBy('c.id', 'c.name')
            ->orderBy('total_revenue', 'desc');

        // Get total count for pagination
        $totalCount = \DB::table(\DB::raw("({$baseQuery->toSql()}) as category_counts"))
            ->mergeBindings($baseQuery)
            ->count();

        // Get paginated results
        $categories = $baseQuery->offset($offset)->limit($limit)->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category_name,
                    'productCount' => $item->product_count,
                    'quantity' => $item->total_quantity,
                    'revenue' => $item->total_revenue,
                ];
            });

        return [
            'data' => $categories,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $totalCount,
                'last_page' => ceil($totalCount / $limit),
            ]
        ];
    }

    /**
     * Get revenue over time data
     */
    private function getRevenueOverTime($dateFrom, $dateTo)
    {
        return Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
