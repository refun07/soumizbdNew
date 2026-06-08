<?php

namespace App\Http\Controllers\Backend\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RecentProductResource;
use App\Http\Resources\Admin\TopBrandResource;
use App\Http\Resources\Admin\TopCategoryResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderUpdateResource;
use App\Http\Resources\ShopResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderUpdate;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    # get new
    public function new(Request $request)
    {
        // new
        $newOrders      = Order::shopOrders()->isPlaced()->count();
        $newSellers     = User::where('user_type', 'seller')->where('created_at', '>', new DateTime('-1 months'))->count();
        $newCustomers   = User::where('user_type', 'customer')->where('created_at', '>',  new DateTime('-1 months'))->count();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'newOrders'      => $newOrders,
                'newSellers'     => $newSellers,
                'newCustomers'   => $newCustomers,
            ]
        ];
    }

    # get total Orders
    public function totalOrders(Request $request)
    {
        $totalOrders    = Order::shopOrders();
        $totalOrdersComparison = Order::shopOrders();

        $timeline       = $request->timeline ?? "overall";
        $today          = Carbon::today();
        $todayQuery     = $today->format('Y-m-d');

        $yesterday      = $today->subDay();
        $yesterdayQuery = $yesterday->format('Y-m-d');

        switch ($timeline) {
            case 'today':
                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=',  $todayQuery);
                // comparison data 
                $totalOrdersComparison    = $totalOrdersComparison->whereDate('created_at', '>=',  $yesterdayQuery)->whereDate('created_at', '<', $todayQuery);

                break;
            case 'yesterday':
                $previousDay        = $yesterday->subDay();
                $previousDayQuery   = $previousDay->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $previousDayQuery)->whereDate('created_at', '<=', $yesterdayQuery);
                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereDate('created_at', '>=',  $previousDayQuery)->whereDate('created_at', '<', $yesterdayQuery);
                break;
            case 'thisWeek':
                $weekStart      = Carbon::now()->startOfWeek();
                $weekStartQuery = $weekStart->format('Y-m-d');
                $previousWeek   = $weekStart->subDay()->startOfWeek();
                $previousWeekQuery = $previousWeek->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $weekStartQuery);

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereDate('created_at', '>=', $previousWeekQuery)->whereDate('created_at', '<', $weekStartQuery);
                break;
            case 'thisMonth':
                $monthStart         = Carbon::now()->startOfMonth();
                $monthStartQuery    = $monthStart->format('Y-m-d');
                $previousMonth      = $monthStart->subDay()->startOfMonth();
                $previousMonthQuery = $previousMonth->format('Y-m-d');
                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $monthStartQuery);
                // comparison data 
                $totalOrdersComparison    = $totalOrdersComparison->whereBetween('created_at', [$previousMonthQuery, $monthStartQuery]);
                break;
            case 'thisYear':
                $yearStart          = Carbon::now()->startOfYear();
                $yearStartQuery     = $yearStart->format('Y-m-d');
                $previousYear       = $yearStart->subDay()->startOfYear();
                $previousYearQuery  = $previousYear->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $yearStartQuery);

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereBetween('created_at', [$previousYearQuery, $yearStartQuery]);
            default:
                break;
        }

        $totalOrders = $totalOrders->count();

        $totalOrdersComparison  = $totalOrdersComparison->count();
        $diff                   = $totalOrders - $totalOrdersComparison == 0 ? 1 : $totalOrders - $totalOrdersComparison;
        // cardRowTwo end 
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalOrders'                           => $totalOrders,
                'totalOrdersComparisonPercentage'       => $totalOrdersComparison != 0 ? number_format((($diff) / $totalOrdersComparison) * 100, 2) : 100,
            ]
        ];
    }

    # get total Orders
    public function totalSales(Request $request)
    {
        $totalOrders    = Order::shopOrders();
        $totalSales     = Order::shopOrders();

        $totalOrdersComparison    = Order::shopOrders();
        $totalSalesComparison     = Order::shopOrders();


        $timeline       = $request->timeline ?? "overall";
        $today          = Carbon::today();
        $todayQuery     = $today->format('Y-m-d');

        $yesterday      = $today->subDay();
        $yesterdayQuery = $yesterday->format('Y-m-d');

        switch ($timeline) {
            case 'today':
                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=',  $todayQuery);
                $totalSales  = $totalOrders->sum('total_amount');

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereDate('created_at', '>=',  $yesterdayQuery)->whereDate('created_at', '<', $todayQuery);
                $totalSalesComparison     = $totalOrdersComparison->sum('total_amount');

                break;
            case 'yesterday':
                $previousDay        = $yesterday->subDay();
                $previousDayQuery   = $previousDay->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $previousDayQuery)->whereDate('created_at', '<=', $yesterdayQuery);
                $totalSales  = $totalOrders->sum('total_amount');

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereDate('created_at', '>=',  $previousDayQuery)->whereDate('created_at', '<', $yesterdayQuery);
                $totalSalesComparison     = $totalOrdersComparison->sum('total_amount');
                break;
            case 'thisWeek':
                $weekStart      = Carbon::now()->startOfWeek();
                $weekStartQuery = $weekStart->format('Y-m-d');
                $previousWeek   = $weekStart->subDay()->startOfWeek();
                $previousWeekQuery = $previousWeek->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $weekStartQuery);

                $totalSales  = $totalOrders->sum('total_amount');

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereDate('created_at', '>=', $previousWeekQuery)->whereDate('created_at', '<', $weekStartQuery);
                $totalSalesComparison     = $totalOrdersComparison->sum('total_amount');
                break;
            case 'thisMonth':
                $monthStart         = Carbon::now()->startOfMonth();
                $monthStartQuery    = $monthStart->format('Y-m-d');
                $previousMonth      = $monthStart->subDay()->startOfMonth();
                $previousMonthQuery = $previousMonth->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $monthStartQuery);
                $totalSales  = $totalOrders->sum('total_amount');

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereBetween('created_at', [$previousMonthQuery, $monthStartQuery]);
                $totalSalesComparison     = $totalOrdersComparison->sum('total_amount');
                break;
            case 'thisYear':
                $yearStart          = Carbon::now()->startOfYear();
                $yearStartQuery     = $yearStart->format('Y-m-d');
                $previousYear       = $yearStart->subDay()->startOfYear();
                $previousYearQuery  = $previousYear->format('Y-m-d');

                // data
                $totalOrders = $totalOrders->whereDate('created_at', '>=', $yearStartQuery);
                $totalSales  = $totalOrders->sum('total_amount');

                // comparison data
                $totalOrdersComparison    = $totalOrdersComparison->whereBetween('created_at', [$previousYearQuery, $yearStartQuery]);
                $totalSalesComparison     = $totalOrdersComparison->sum('total_amount');
                break;
            default:
                $totalSales               = $totalOrders->sum('total_amount');
                $totalSalesComparison     = $totalOrdersComparison->sum('total_amount');
                break;
        }
        $diff   = $totalSales - $totalSalesComparison == 0 ? 1 : $totalSales - $totalSalesComparison;
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalSalesAmount'                      => $totalSales,
                'totalSalesComparisonPercentage'        => $totalSalesComparison != 0 ? number_format((($diff) / $totalSalesComparison) * 100, 2) : 100,
            ]
        ];
    }

    # get total sellers
    public function totalSellers(Request $request)
    {
        $totalSellers   = User::where('user_type', 'seller');
        $totalSellersComparison   = User::where('user_type', 'seller');


        $timeline       = $request->timeline ?? "overall";
        $today          = Carbon::today();
        $todayQuery     = $today->format('Y-m-d');

        $yesterday      = $today->subDay();
        $yesterdayQuery = $yesterday->format('Y-m-d');

        switch ($timeline) {
            case 'today':
                // data
                $totalSellers   = $totalSellers->whereDate('created_at', '>=',  $todayQuery);
                // comparison data
                $totalSellersComparison   = $totalSellersComparison->whereDate('created_at', '>=',  $yesterdayQuery)->whereDate('created_at', '<', $todayQuery);

                break;
            case 'yesterday':
                $previousDay        = $yesterday->subDay();
                $previousDayQuery   = $previousDay->format('Y-m-d');

                // data
                $totalSellers   = $totalSellers->whereDate('created_at', '>=', $previousDayQuery)->whereDate('created_at', '<=', $yesterdayQuery);

                // comparison data
                $totalSellersComparison   = $totalSellersComparison->whereDate('created_at', '>=',  $previousDayQuery)->whereDate('created_at', '<', $yesterdayQuery);
                break;
            case 'thisWeek':
                $weekStart      = Carbon::now()->startOfWeek();
                $weekStartQuery = $weekStart->format('Y-m-d');
                $previousWeek   = $weekStart->subDay()->startOfWeek();
                $previousWeekQuery = $previousWeek->format('Y-m-d');

                // data
                $totalSellers   = $totalSellers->whereDate('created_at', '>=', $weekStartQuery);

                // comparison data
                $totalSellersComparison   = $totalSellersComparison->whereDate('created_at', '>=', $previousWeekQuery)->whereDate('created_at', '<', $weekStartQuery);
                break;
            case 'thisMonth':
                $monthStart         = Carbon::now()->startOfMonth();
                $monthStartQuery    = $monthStart->format('Y-m-d');
                $previousMonth      = $monthStart->subDay()->startOfMonth();
                $previousMonthQuery = $previousMonth->format('Y-m-d');

                // data
                $totalSellers   = $totalSellers->whereDate('created_at', '>=', $monthStartQuery);

                // comparison data
                $totalSellersComparison   = $totalSellersComparison->whereBetween('created_at', [$previousMonthQuery, $monthStartQuery]);
                break;
            case 'thisYear':
                $yearStart          = Carbon::now()->startOfYear();
                $yearStartQuery     = $yearStart->format('Y-m-d');
                $previousYear       = $yearStart->subDay()->startOfYear();
                $previousYearQuery  = $previousYear->format('Y-m-d');

                // data
                $totalSellers   = $totalSellers->whereDate('created_at', '>=', $yearStartQuery);

                // comparison data
                $totalSellersComparison   = $totalSellersComparison->whereBetween('created_at', [$previousYearQuery, $yearStartQuery]);
                break;
            default:
                break;
        }

        $totalSellers = $totalSellers->count();

        $totalSellersComparison = $totalSellersComparison->count();
        $diff   = $totalSellers - $totalSellersComparison == 0 ? 1 : $totalSellers - $totalSellersComparison;
        // cardRowTwo end

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalSellers'                          => $totalSellers,
                'totalSellersComparisonPercentage'      => $totalSellersComparison != 0 ? number_format((($diff) / $totalSellersComparison) * 100, 2) : 100,
            ]
        ];
    }

    # get total customers
    public function totalCustomers(Request $request)
    {
        // cardRowTwo start
        $totalCustomers = User::where('user_type', 'customer');

        $totalCustomersComparison = User::where('user_type', 'customer');


        $timeline       = $request->timeline ?? "overall";
        $today          = Carbon::today();
        $todayQuery     = $today->format('Y-m-d');

        $yesterday      = $today->subDay();
        $yesterdayQuery = $yesterday->format('Y-m-d');

        switch ($timeline) {
            case 'today':
                // data
                $totalCustomers = $totalCustomers->whereDate('created_at', '>=',  $todayQuery);

                // comparison data
                $totalCustomersComparison = $totalCustomersComparison->whereDate('created_at', '>=',  $yesterdayQuery)->whereDate('created_at', '<', $todayQuery);

                break;
            case 'yesterday':
                $previousDay        = $yesterday->subDay();
                $previousDayQuery   = $previousDay->format('Y-m-d');

                // data
                $totalCustomers = $totalCustomers->whereDate('created_at', '>=', $previousDayQuery)->whereDate('created_at', '<=', $yesterdayQuery);

                // comparison data
                $totalCustomersComparison = $totalCustomersComparison->whereDate('created_at', '>=',  $previousDayQuery)->whereDate('created_at', '<', $yesterdayQuery);
                break;
            case 'thisWeek':
                $weekStart      = Carbon::now()->startOfWeek();
                $weekStartQuery = $weekStart->format('Y-m-d');
                $previousWeek   = $weekStart->subDay()->startOfWeek();
                $previousWeekQuery = $previousWeek->format('Y-m-d');

                // data
                $totalCustomers = $totalCustomers->whereDate('created_at', '>=', $weekStartQuery);

                // comparison data
                $totalCustomersComparison = $totalCustomersComparison->whereDate('created_at', '>=', $previousWeekQuery)->whereDate('created_at', '<', $weekStartQuery);
                break;
            case 'thisMonth':
                $monthStart         = Carbon::now()->startOfMonth();
                $monthStartQuery    = $monthStart->format('Y-m-d');
                $previousMonth      = $monthStart->subDay()->startOfMonth();
                $previousMonthQuery = $previousMonth->format('Y-m-d');

                // data
                $totalCustomers = $totalCustomers->whereDate('created_at', '>=', $monthStartQuery);

                // comparison data
                $totalCustomersComparison = $totalCustomersComparison->whereBetween('created_at', [$previousMonthQuery, $monthStartQuery]);
                break;
            case 'thisYear':
                $yearStart          = Carbon::now()->startOfYear();
                $yearStartQuery     = $yearStart->format('Y-m-d');
                $previousYear       = $yearStart->subDay()->startOfYear();
                $previousYearQuery  = $previousYear->format('Y-m-d');

                // data
                $totalCustomers = $totalCustomers->whereDate('created_at', '>=', $yearStartQuery);

                // comparison data
                $totalCustomersComparison = $totalCustomersComparison->whereBetween('created_at', [$previousYearQuery, $yearStartQuery]);
                break;
            default:
                break;
        }

        $totalCustomers = $totalCustomers->count();
        $totalCustomersComparison = $totalCustomersComparison->count();
        $diff   = $totalCustomers - $totalCustomersComparison == 0 ? 1 : $totalCustomers - $totalCustomersComparison;
        // cardRowTwo end

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalCustomers'                        => $totalCustomers,
                'totalCustomersComparisonPercentage'    => $totalCustomersComparison != 0 ? number_format((($diff) / $totalCustomersComparison) * 100, 2) : 100,
            ]
        ];
    }

    # get sales amount chart
    public function salesAmount(Request $request)
    {
        $timeline   = $request->timeline ?? "thisWeek";
        $endDate    = Carbon::today()->addDay(1);

        switch ($timeline) {
            case 'thisYear':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'thisMonth':
                $startDate = Carbon::now()->startOfMonth();
                break;
            default:
                $startDate = Carbon::now()->startOfWeek();
                break;
        }


        // Get the distinct days within the date range
        $days = [];

        // Loop through each day between start and end date
        for ($date = $startDate; $date->lt($endDate); $date->addDay()) {
            $days[] = $date->format('Y-m-d');
        }

        // Initialize an array to store the total amount for each day
        $totalAmounts = [];

        // Loop through each day and calculate the total amount sold
        foreach ($days as $day) {
            $totalAmount = Order::shopOrders()->whereDate('created_at', $day)->sum('total_amount');
            $totalAmounts[] = $totalAmount;
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'days'            => $days,
                'totalAmounts'    => $totalAmounts,
            ]
        ];
    }

    # get top categories
    public function topCategories(Request $request)
    {
        $totalCategories = Category::count();

        $getBy = $request->getBy ?? "orderCount";

        if ($getBy == "orderCount") {
            $categories = Category::orderBy('total_sale_count', 'DESC')->take(5)->get(['name', 'total_sale_count', 'total_sale_amount']);
        } else {
            $categories = Category::orderBy('total_sale_amount', 'DESC')->take(5)->get(['name', 'total_sale_count', 'total_sale_amount']);
        }


        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'topCategories'     => TopCategoryResource::collection($categories),
                'totalCategories'   => $totalCategories
            ]
        ];
    }

    # get top brands
    public function topBrands(Request $request)
    {
        $totalBrands = Brand::count();
        $getBy = $request->getBy ?? "orderCount";

        if ($getBy == "orderCount") {
            $brands = Brand::orderBy('total_sale_count', 'DESC')->take(5)->get(['name', 'thumbnail_image', 'total_sale_count', 'total_sale_amount']);
        } else {
            $brands = Brand::orderBy('total_sale_amount', 'DESC')->take(5)->get(['name', 'thumbnail_image', 'total_sale_count', 'total_sale_amount']);
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'topBrands'     => TopBrandResource::collection($brands),
                'totalBrands'   => $totalBrands
            ]
        ];
    }

    # get recent Orders
    public function recentOrders(Request $request)
    {
        $orders = Order::shopOrders()->latest()->take(10)->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => OrderResource::collection($orders)
        ];
    }

    # get order updates
    public function orderUpdates(Request $request)
    {
        $orderUpdates = OrderUpdate::whereHas('order', function ($q) {
            $q->shopOrders();
        })->latest()->take(10)->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => OrderUpdateResource::collection($orderUpdates)
        ];
    }

    # get recent products
    public function recentProducts(Request $request)
    {
        $totalProducts = Product::shop()->count();
        $stockOutProducts = Product::shop()->where('stock_qty', '<', 1)->count();
        $products   = Product::shop()->latest()->take(10)->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalProducts'     => $totalProducts,
                'stockOutProducts'  => $stockOutProducts,
                'recentProducts'    => RecentProductResource::collection($products)
            ]
        ];
    }

    # get most selling products
    public function mostSellingProducts(Request $request)
    {
        $products   = Product::shop()->orderBy('total_sale_count', 'DESC')->take(10)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'mostSellingProducts' => RecentProductResource::collection($products)
            ]
        ];
    }

    # get topRatedSellers
    public function topRatedSellers(Request $request)
    {
        $topShops = Shop::where('id', '!=', shopId())->select('*')->selectRaw('(SELECT AVG(rating) FROM product_reviews WHERE shop_id = shops.id) as average_rating')
            ->orderByDesc('average_rating')
            ->take(10)
            ->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'topShops' => ShopResource::collection($topShops)
            ]
        ];
    }

    # get topSellers
    public function topSellers(Request $request)
    {
        $topShops = Shop::where('id', '!=', shopId())->withSum('orders', 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(10)
            ->get(['*', 'orders_sum_total_amount']);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'topShops' => ShopResource::collection($topShops)
            ]
        ];
    }

    # get earning From Sellers
    public function earningFromSellers(Request $request)
    {
        $topShops = Shop::where('id', '!=', shopId())->withSum(['commissionHistories' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('delivery_status', '!=', 'cancelled');
            });
        }], 'admin_earning_amount')
            ->orderBy('commission_histories_sum_admin_earning_amount', 'desc')
            ->take(7)
            ->get(['*', 'commission_histories_sum_admin_earning_amount']);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'topShops' => ShopResource::collection($topShops)
            ]
        ];
    }
}
