<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_orders'])->only(['index', 'summary', 'products', 'show', 'orders', 'customerOrders']);
        $this->middleware(['permission:manage_orders'])->only(['updateSettings', 'updateVariationSettings']);
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 20;

        $query = Product::shop()
            ->whereNotNull('pre_order')
            ->with(['variations']);

        if ($request->status) {
            $now = now();
            switch ($request->status) {
                case 'active':
                    $query->where('pre_order->is_active', true)
                        ->where(function ($q) use ($now) {
                            $q->where('pre_order->start_date', '<=', $now)
                                ->orWhereNull('pre_order->start_date');
                        })
                        ->where(function ($q) use ($now) {
                            $q->where('pre_order->end_date', '>=', $now)
                                ->orWhereNull('pre_order->end_date');
                        });
                    break;
                case 'ended':
                    $query->where('pre_order->end_date', '<', $now);
                    break;
                case 'upcoming':
                    $query->where('pre_order->start_date', '>', $now);
                    break;
            }
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate($limit);

        $products->transform(function ($product) {
            $product->totalPreOrders = 0;
            $product->totalLimit = 0;

            foreach ($product->variations as $variation) {
                $currentOrders = ProductService::getCurrentOrdersCount($variation->id);
                $totalLimit = $variation->getPreOrderTotalLimit();
                $product->totalPreOrders += $currentOrders;
                $product->totalLimit += $totalLimit;
                $variation->current_orders = $currentOrders;
                $variation->total_order_limit = $totalLimit;
            }

            return $product;
        });

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'products' => $products,
                'filters' => [
                    'statuses' => [
                        ['value' => '', 'label' => 'All'],
                        ['value' => 'active', 'label' => 'Active'],
                        ['value' => 'ended', 'label' => 'Ended'],
                        ['value' => 'upcoming', 'label' => 'Upcoming'],
                    ]
                ]
            ]
        ];
    }

    public function summary()
    {
        $now = now();

        $activeCount = Product::shop()
            ->where('pre_order->is_active', true)
            ->where(function ($q) use ($now) {
                $q->where('pre_order->start_date', '<=', $now)
                    ->orWhereNull('pre_order->start_date');
            })
            ->where(function ($q) use ($now) {
                $q->where('pre_order->end_date', '>=', $now)
                    ->orWhereNull('pre_order->end_date');
            })
            ->count();

        $totalOrders = OrderItem::whereHas('order', function ($query) {
            $query->where('shop_id', shopId());
        })
        ->whereNotNull('pre_order_info')
        ->sum('qty');

        $upcomingCount = Product::shop()
            ->where('pre_order->is_active', true)
            ->where('pre_order->start_date', '>', $now)
            ->count();

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'activePreOrders' => $activeCount,
                'totalPreOrderItems' => $totalOrders,
                'upcomingCount' => $upcomingCount
            ]
        ];
    }

    public function products(Request $request)
    {
        $query = Product::shop()
            ->where('is_published', 1)
            ->whereNotNull('pre_order')
            ->with(['variations'])
            ->select('id', 'name', 'slug', 'thumbnail_image', 'pre_order');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate($request->limit ?? 20);

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => $products
        ];
    }

    public function show($productId)
    {
        $product = Product::shop()
            ->with(['variations'])
            ->findOrFail($productId);

        foreach ($product->variations as $variation) {
            $variation->current_orders = ProductService::getCurrentOrdersCount($variation->id);
            $variation->total_order_limit = $variation->getPreOrderTotalLimit();
            $variation->is_sold_out = $variation->isSoldOut();
        }

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => $product
        ];
    }

    public function updateSettings(Request $request, $productId)
    {
        $result = ProductService::updatePreOrderSettings($request, $productId);

        return response()->json($result, $result['status']);
    }

    public function updateVariationSettings(Request $request, $productId, $variationId)
    {
        $result = ProductService::updateVariationPreOrderSettings($request, $variationId);

        return response()->json($result, $result['status']);
    }

    public function orders(Request $request, $productId)
    {
        $limit = $request->limit ?? 20;

        $orderItems = OrderItem::whereHas('order', function ($query) {
            $query->where('shop_id', shopId());
        })
        ->whereHas('productVariation', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })
        ->whereNotNull('pre_order_info')
        ->with(['order.user', 'productVariation'])
        ->latest()
        ->paginate($limit);

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => $orderItems
        ];
    }

    public function customerOrders(Request $request)
    {
        $limit = $request->limit ?? 20;

        $query = OrderItem::whereHas('order', function ($query) {
                $query->where('shop_id', shopId());
            })
            ->whereNotNull('pre_order_info')
            ->with([
                'order.user',
                'productVariation',
                'productVariation.product'
            ]);

        if ($request->search) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($uq) use ($request) {
                      $uq->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('phone', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->delivery_status) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('delivery_status', $request->delivery_status);
            });
        }

        if ($request->payment_status) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            });
        }

        $orderItems = $query->latest()->paginate($limit);

        $orderItems->transform(function ($item) {
            $item->product_name = $item->productVariation->product->name ?? 'N/A';
            $item->variation_code = $item->productVariation->sku ?? $item->productVariation->code ?? 'N/A';
            $item->expected_date = $item->getExpectedDate();
            $item->deposit_paid = $item->getDepositPaid();

            // Add missing order fields for frontend display
            $item->order->code = $item->order->order_code;
            $item->order->customerName = $item->order->user->name ?? 'N/A';
            $item->order->customerPhone = $item->order->user->phone ?? 'N/A';
            $item->order->createdDate = $item->order->created_at->toIso8601String();
            $item->order->orderPrefix = getSetting('order_code_prefix') ?? '';

            return $item;
        });

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => $orderItems
        ];
    }
}
