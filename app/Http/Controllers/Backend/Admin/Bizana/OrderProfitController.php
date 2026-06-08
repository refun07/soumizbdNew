<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderProfitLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderProfitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin', 'unbanned', 'xss']);
        $this->middleware('permission:bizana_view_profits')->only(['index', 'show', 'summary', 'export']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->buildFilteredQuery($request);

        $perPage = (int) $request->input('per_page', 25);
        $profitLogs = $query->orderByDesc('order_profit_logs.created_at')
            ->paginate($perPage);

        return response()->json($profitLogs);
    }

    public function show(int $orderId): JsonResponse
    {
        $profitLog = OrderProfitLog::where('order_id', $orderId)
            ->with([
                'order.orderItems.productVariation.product',
                'order.shop',
                'order.commissionHistory',
                'order.agentCommissionHistory',
            ])
            ->firstOrFail();

        $orderItems = $profitLog->order->orderItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->productVariation?->product?->name ?? 'N/A',
                'variation_sku' => $item->productVariation?->sku ?? '',
                'qty' => $item->qty,
                'unit_price' => $item->unit_price,
                'unit_cost_price' => $item->unit_cost_price,
                'total_price' => $item->total_price,
                'total_cost' => $item->total_cost,
                'total_tax' => $item->total_tax,
                'total_discount' => $item->total_discount,
                'item_profit' => $item->total_price - $item->total_cost,
                'profit_margin_pct' => $item->total_price > 0
                    ? round((($item->total_price - $item->total_cost) / $item->total_price) * 100, 2)
                    : 0,
            ];
        });

        return response()->json([
            'profit_log' => $profitLog,
            'item_breakdown' => $orderItems,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $query = $this->buildFilteredQuery($request);

        $summary = DB::query()
            ->fromSub($query->toBase(), 'filtered_logs')
            ->selectRaw('
                COUNT(*) as total_orders,
                COALESCE(SUM(revenue), 0) as total_revenue,
                COALESCE(SUM(cogs), 0) as total_cogs,
                COALESCE(SUM(admin_commission), 0) as total_admin_commission,
                COALESCE(SUM(seller_earning), 0) as total_seller_earning,
                COALESCE(SUM(agent_commission), 0) as total_agent_commission,
                COALESCE(SUM(shipping_cost), 0) as total_shipping_cost,
                COALESCE(SUM(tax_collected), 0) as total_tax_collected,
                COALESCE(SUM(discount_given), 0) as total_discount_given,
                COALESCE(SUM(gross_profit), 0) as total_gross_profit,
                COALESCE(SUM(net_profit), 0) as total_net_profit,
                CASE WHEN SUM(revenue) > 0
                    THEN ROUND((SUM(gross_profit) / SUM(revenue)) * 100, 2)
                    ELSE 0
                END as avg_profit_margin_pct
            ')
            ->first();

        return response()->json($summary);
    }

    public function export(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $profitLogs = $query->orderByDesc('order_profit_logs.created_at')->get();

        $filename = 'order-profit-report-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($profitLogs) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Order Code',
                'Shop',
                'Order Date',
                'Delivery Status',
                'Payment Status',
                'Revenue',
                'COGS',
                'Admin Commission',
                'Agent Commission',
                'Seller Earning',
                'Shipping Cost',
                'Tax Collected',
                'Discount Given',
                'Gross Profit',
                'Net Profit',
                'Margin %',
            ]);

            foreach ($profitLogs as $log) {
                fputcsv($file, [
                    $log->order->order_code ?? '',
                    $log->order->shop->name ?? '',
                    $log->order->created_at?->format('Y-m-d H:i:s') ?? '',
                    $log->order->delivery_status ?? '',
                    $log->order->payment_status ?? '',
                    $log->revenue,
                    $log->cogs,
                    $log->admin_commission,
                    $log->agent_commission ?? 0,
                    $log->seller_earning,
                    $log->shipping_cost,
                    $log->tax_collected,
                    $log->discount_given,
                    $log->gross_profit,
                    $log->net_profit,
                    $log->profit_margin_pct,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = OrderProfitLog::query()
            ->select('order_profit_logs.*')
            ->join('orders', 'order_profit_logs.order_id', '=', 'orders.id')
            ->with(['order.shop']);

        if ($request->filled('date_from')) {
            $query->where('order_profit_logs.created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('order_profit_logs.created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        if ($request->filled('shop_id')) {
            $query->where('orders.shop_id', $request->shop_id);
        }

        if ($request->filled('delivery_status') && $request->delivery_status !== 'all') {
            $query->where('orders.delivery_status', $request->delivery_status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('orders.payment_status', $request->payment_status);
        }

        if ($request->filled('min_profit')) {
            $query->where('order_profit_logs.gross_profit', '>=', (float) $request->min_profit);
        }

        if ($request->filled('max_profit')) {
            $query->where('order_profit_logs.gross_profit', '<=', (float) $request->max_profit);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('orders.order_code', 'like', "%{$search}%")
                    ->orWhereHas('order.shop', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }
}
