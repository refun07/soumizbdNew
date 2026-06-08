<?php

namespace App\Http\Controllers\Backend\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RecentProductResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderUpdateResource;
use App\Models\Campaign;
use App\Models\CommissionHistory;
use App\Models\Order;
use App\Models\OrderUpdate;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    # get new
    public function new(Request $request)
    {
        // new
        $newOrders      = Order::shopOrders()->isPlaced()->count();
        $newCustomers   = User::where('user_type', 'customer')
            ->where('created_at', '>', new DateTime('-1 months'))
            ->whereHas('orders', function ($query) {
                $query->where('shop_id', shopId());
            })->count();
        $stockLow       = Product::shop()->where('stock_qty', '<=', 'alert_qty')->count();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'shopName'       => shop()->name,
                'newOrders'      => $newOrders,
                'newCustomers'   => $newCustomers,
                'stockLow'       => $stockLow
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

    # get total earnings
    public function totalEarnings(Request $request)
    {
        $totalEarnings              = CommissionHistory::shopCommissions()->whereHas('order', function ($q) {
            $q->where('delivery_status', '!=', 'cancelled');
        });
        $totalEarningsComparison    = CommissionHistory::shopCommissions()->whereHas('order', function ($q) {
            $q->where('delivery_status', '!=', 'cancelled');
        });


        $timeline       = $request->timeline ?? "overall";
        $today          = Carbon::today();
        $todayQuery     = $today->format('Y-m-d');

        $yesterday      = $today->subDay();
        $yesterdayQuery = $yesterday->format('Y-m-d');


        switch ($timeline) {
            case 'today':
                // data
                $totalEarnings = $totalEarnings->whereDate('created_at', '>=',  $todayQuery)->sum('shop_earning_amount');
                // comparison data
                $totalEarningsComparison    = $totalEarningsComparison->whereDate('created_at', '>=',  $yesterdayQuery)->whereDate('created_at', '<', $todayQuery)->sum('shop_earning_amount');
                break;
            case 'yesterday':
                $previousDay        = $yesterday->subDay();
                $previousDayQuery   = $previousDay->format('Y-m-d');

                // data
                $totalEarnings = $totalEarnings->whereDate('created_at', '>=', $previousDayQuery)->whereDate('created_at', '<=', $yesterdayQuery)->sum('shop_earning_amount');
                // comparison data
                $totalEarningsComparison    = $totalEarningsComparison->whereDate('created_at', '>=',  $previousDayQuery)->whereDate('created_at', '<', $yesterdayQuery)->sum('shop_earning_amount');

                break;
            case 'thisWeek':
                $weekStart      = Carbon::now()->startOfWeek();
                $weekStartQuery = $weekStart->format('Y-m-d');
                $previousWeek   = $weekStart->subDay()->startOfWeek();
                $previousWeekQuery = $previousWeek->format('Y-m-d');

                // data
                $totalEarnings = $totalEarnings->whereDate('created_at', '>=', $weekStartQuery)->sum('shop_earning_amount');
                // comparison data
                $totalEarningsComparison    = $totalEarningsComparison->whereDate('created_at', '>=', $previousWeekQuery)->whereDate('created_at', '<', $weekStartQuery)->sum('shop_earning_amount');
                break;
            case 'thisMonth':
                $monthStart         = Carbon::now()->startOfMonth();
                $monthStartQuery    = $monthStart->format('Y-m-d');
                $previousMonth      = $monthStart->subDay()->startOfMonth();
                $previousMonthQuery = $previousMonth->format('Y-m-d');

                // data
                $totalEarnings = $totalEarnings->whereDate('created_at', '>=', $monthStartQuery)->sum('shop_earning_amount');
                // comparison data
                $totalEarningsComparison    = $totalEarningsComparison->whereBetween('created_at', [$previousMonthQuery, $monthStartQuery])->sum('shop_earning_amount');
                break;
            case 'thisYear':
                $yearStart          = Carbon::now()->startOfYear();
                $yearStartQuery     = $yearStart->format('Y-m-d');
                $previousYear       = $yearStart->subDay()->startOfYear();
                $previousYearQuery  = $previousYear->format('Y-m-d');

                // data
                $totalEarnings = $totalEarnings->whereDate('created_at', '>=', $yearStartQuery)->sum('shop_earning_amount');
                // comparison data
                $totalEarningsComparison    = $totalEarningsComparison->whereBetween('created_at', [$previousYearQuery, $yearStartQuery])->sum('shop_earning_amount');
                break;
            default:
                $totalEarnings               = $totalEarnings->sum('shop_earning_amount');
                $totalEarningsComparison     = $totalEarningsComparison->sum('shop_earning_amount');
                break;
        }


        $diff   = $totalEarnings - $totalEarningsComparison == 0 ? 1 : $totalEarnings - $totalEarningsComparison;

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalEarnings'             => $totalEarnings,
                'totalEarningsComparison'   => $totalEarningsComparison != 0 ? number_format((($$diff) / $totalEarningsComparison) * 100, 2) : 100,
            ]
        ];
    }

    # get total products
    public function totalProducts(Request $request)
    {
        $totalProducts              = Product::shop();
        $totalProductsComparison    = Product::shop();

        $timeline       = $request->timeline ?? "overall";
        $today          = Carbon::today();
        $todayQuery     = $today->format('Y-m-d');

        $yesterday      = $today->subDay();
        $yesterdayQuery = $yesterday->format('Y-m-d');


        switch ($timeline) {
            case 'today':
                // data
                $totalProducts = $totalProducts->whereDate('created_at', '>=',  $todayQuery)->count();
                // comparison data
                $totalProductsComparison    = $totalProductsComparison->whereDate('created_at', '>=',  $yesterdayQuery)->whereDate('created_at', '<', $todayQuery)->count();
                break;
            case 'yesterday':
                $previousDay        = $yesterday->subDay();
                $previousDayQuery   = $previousDay->format('Y-m-d');

                // data
                $totalProducts = $totalProducts->whereDate('created_at', '>=', $previousDayQuery)->whereDate('created_at', '<=', $yesterdayQuery)->count();
                // comparison data
                $totalProductsComparison    = $totalProductsComparison->whereDate('created_at', '>=',  $previousDayQuery)->whereDate('created_at', '<', $yesterdayQuery)->count();

                break;
            case 'thisWeek':
                $weekStart      = Carbon::now()->startOfWeek();
                $weekStartQuery = $weekStart->format('Y-m-d');
                $previousWeek   = $weekStart->subDay()->startOfWeek();
                $previousWeekQuery = $previousWeek->format('Y-m-d');


                // data
                $totalProducts = $totalProducts->whereDate('created_at', '>=', $weekStartQuery)->count();
                // comparison data
                $totalProductsComparison    = $totalProductsComparison->whereDate('created_at', '>=', $previousWeekQuery)->whereDate('created_at', '<', $weekStartQuery)->count();
                break;
            case 'thisMonth':
                $monthStart         = Carbon::now()->startOfMonth();
                $monthStartQuery    = $monthStart->format('Y-m-d');
                $previousMonth      = $monthStart->subDay()->startOfMonth();
                $previousMonthQuery = $previousMonth->format('Y-m-d');

                // data
                $totalProducts = $totalProducts->whereDate('created_at', '>=', $monthStartQuery)->count();
                // comparison data
                $totalProductsComparison    = $totalProductsComparison->whereBetween('created_at', [$previousMonthQuery, $monthStartQuery])->count();
                break;
            case 'thisYear':
                $yearStart          = Carbon::now()->startOfYear();
                $yearStartQuery     = $yearStart->format('Y-m-d');
                $previousYear       = $yearStart->subDay()->startOfYear();
                $previousYearQuery  = $previousYear->format('Y-m-d');

                // data
                $totalProducts = $totalProducts->whereDate('created_at', '>=', $yearStartQuery)->count();
                // comparison data
                $totalProductsComparison    = $totalProductsComparison->whereBetween('created_at', [$previousYearQuery, $yearStartQuery])->count();
                break;
            default:
                $totalProducts               = $totalProducts->count();
                $totalProductsComparison     = $totalProductsComparison->count();
                break;
        }


        $diff   = $totalProducts - $totalProductsComparison == 0 ? 1 : $totalProducts - $totalProductsComparison;
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'totalProducts'             => $totalProducts,
                'totalProductsComparison'   => $totalProductsComparison != 0 ? number_format((($diff) / $totalProductsComparison) * 100, 2) : 100,
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

    # get orderCount
    public function orderCount(Request $request)
    {
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'orderPlaced'    => Order::shopOrders()->isPlaced()->count(),
                'confirmed'      => Order::shopOrders()->isConfirmed()->count(),
                'processing'     => Order::shopOrders()->isProcessing()->count(),
                'shipped'        => Order::shopOrders()->isShipped()->count(),
                'delivered'      => Order::shopOrders()->isDelivered()->count(),
                'cancelled'      => Order::shopOrders()->isCancelled()->count(),
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


    # get most selling products
    public function mostSellingProducts(Request $request)
    {
        $products   = Product::shop()->orderBy('total_sale_count', 'DESC')->take(10)->get();
        $stockOut   = Product::shop()->where('stock_qty', 0)->count();
        $totalCommission = CommissionHistory::shopCommissions()->whereHas('order', function ($q) {
            $q->where('delivery_status', '!=', 'cancelled');
        })->sum('shop_earning_amount');
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'mostSellingProducts' => RecentProductResource::collection($products),
                'stockOut'            => $stockOut,
                'totalCommission'     => $totalCommission,
            ]
        ];
    }


    # get activeCampaigns
    public function activeCampaigns(Request $request)
    {
        $campaigns   = Campaign::shop()->isPublished()->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))->latest()->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'activeCampaigns' => CampaignResource::collection($campaigns),
            ]
        ];
    }
}
