<?php

namespace App\Http\Controllers\Backend\Api\Order;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\Order;
use App\Models\Country;
use App\Http\Resources\OrderResource;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;

        $this->middleware(['permission:view_orders|manage_assigned_orders|manage_orders'])->only(['index', 'downloadInvoice', 'export', 'getReturnDetails']);
        $this->middleware(['permission:manage_assigned_orders|manage_orders'])->only(['show', 'updateOrderTracking', 'storeOrderUpdates', 'deleteOrderUpdate', 'updatePaymentStatus', 'updateDeliveryStatus', 'updateOrderAddress', 'updateOrderStatus', 'bulkUpdateOrderStatus', 'updateOrder', 'destroy']);
    }

    private function authorizeOrderManagement(Order $order)
    {
        $user = auth()->user();
        if ($user->can('manage_orders')) {
            return;
        }
        if ($user->can('manage_assigned_orders') && $order->agent_id === $user->id) {
            return;
        }
        abort(403, 'You are not authorized to manage this order.');
    }

    private function authorizeOrderManagementById($orderId)
    {
        $order = Order::findOrFail($orderId);
        $this->authorizeOrderManagement($order);
    }

    # get all resources
    public function index(Request $request)
    {

        $limit  = $request->limit ?? perPage();

        $orders = apiUser()->orders()->latest();


        $orders = $this->orderService->index($request);
        // dd($orders["result"]["orders"]);
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            # currency settings
            'currency'      => [
                'code'      => getSetting('currencyCode') ?? 'usd',
                'symbol'    => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1  : 0] : 'symbol_first',

                    'show'  => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals'     => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator'  => getSetting('decimalSeparator') ?? '.',
            ],
            'countries'     => Country::where('is_active', 1)->get(),
        ];

        // $view = view('backend.admin.pos.index', compact('settings'));
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => OrderResource::collection($orders["result"]["orders"])->response()->getData(true)
            ];
    }

    # export orders as CSV
    public function export(Request $request)
    {
        return $this->orderService->export($request);
    }

    public function updateOrderStatus(Request $request){
        $this->authorizeOrderManagementById($request->order_id);

         $response = $this->orderService->updateDeliveryStatus($request);
        if ($response['status'] == 200) {
            return $response;
        }
        return [
                'success'   => false,
                'status'    => 200,
                'message'   => 'Something wnet wrong',
            ];
    }

    public function bulkUpdateOrderStatus(Request $request)
    {
        $allowedStatuses = ['order_placed', 'confirmed', 'processing', 'shipped', 'delivered'];

        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'required|integer|exists:orders,id',
            'status'      => 'required|string|in:' . implode(',', $allowedStatuses),
        ]);

        foreach ($request->order_ids as $orderId) {
            $this->authorizeOrderManagementById($orderId);
        }

        try {
            DB::beginTransaction();

            $updatedOrders = [];
            foreach ($request->order_ids as $orderId) {
                $bulkRequest = new Request([
                    'order_id' => $orderId,
                    'status'   => $request->status,
                ]);

                $response = $this->orderService->updateDeliveryStatus($bulkRequest);

                if ($response['status'] != 200) {
                    throw new \Exception("Failed to update order ID {$orderId}");
                }

                $updatedOrders[] = $response['result']['order'];
            }

            DB::commit();

            return [
                'success' => true,
                'status'  => 200,
                'message' => count($updatedOrders) . ' order(s) updated successfully',
                'result'  => [
                    'updated_count' => count($updatedOrders),
                    'orders'       => $updatedOrders,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk order status update failed: ' . $e->getMessage());
            return [
                'success' => false,
                'status'  => 500,
                'message' => 'Bulk status update failed: ' . $e->getMessage(),
            ];
        }
    }

    # get order items
    public function getOrderItems($orderId)
    {
        try {
            $order = Order::with('orderItems.productVariation.product')->findOrFail($orderId);

            $orderItems = $order->orderItems->map(function ($item) {
                $variantName = null;
                if ($item->productVariation && $item->productVariation->code) {
                    $variantName = generateVariationName($item->productVariation->code);
                }

                return [
                    'id' => $item->id,
                    'product_name' => $item->productVariation->product->name ?? 'N/A',
                    'variant' => $variantName,
                    'product_image' => $item->productVariation->product->thumbnail_image ? uploadedAsset($item->productVariation->product->thumbnail_image) : null,
                    'quantity' => $item->qty,
                    'unit_price' => (float) $item->unit_price,
                    'total_discount' => (float) ($item->total_discount ?? 0),
                    'total_tax' => (float) ($item->total_tax ?? 0),
                    'total_price' => (float) $item->total_price,
                ];
            });

            return response()->json([
                'success' => true,
                'status' => 200,
                'result' => $orderItems,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching order items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    # update order
    public function updateOrder(Request $request, $orderId)
    {
        // dd($orderId);
        try {
           // $order = Order::whereOrderCode($orderId)->first();
            $order = Order::findOrFail($orderId);
            $this->authorizeOrderManagement($order);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Order not found',
                ], 404);
            }

            $response = $this->orderService->updateOrder($request, $order->id);

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error updating order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($orderId)
    {
        try {
            $order = Order::shopOrders()->findOrFail($orderId);
            $this->authorizeOrderManagement($order);

            $order->delete();

            return response()->json([
                'success' => true,
                'status'  => 200,
                'message' => 'Order deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'status'  => 404,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting order: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status'  => 500,
                'message' => 'Failed to delete order',
            ], 500);
        }
    }

    # get return details
    public function getReturnDetails(Request $request)
    {
        try {
            $limit = $request->limit ?? perPage();
            $type = $request->type;
            $from = $request->from;
            $to = $request->to;

            // Get orders with return requests
            $query = Order::with(['user', 'returnRefunds.items'])
                ->whereHas('returnRefunds'); // Only orders that have return requests

            // Filter by date range
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to);
            }

            // Filter by type if specified (damaged items)
            if ($type === 'damaged') {
                $query->whereHas('returnRefunds.items', function ($q) {
                    $q->where('reason', 'like', '%damage%');
                });
            }

            $orders = $query->latest()->paginate($limit);

            // Transform the data to match the expected format
            $returnDetails = $orders->getCollection()->map(function ($order) {
                $returnRequest = $order->returnRefunds->first();

                return [
                    'id' => $returnRequest->id,
                    'orderId' => $order->id,
                    'code' => $order->order_code,
                    'customerName' => $order->user?->name,
                    'customerPhone' => $order->user?->phone,
                    'createdDate' => $order->created_at->toIso8601String(),
                    'totalAmount' => (float) $order->total_amount,
                    'paymentStatus' => $order->payment_status,
                    'address' => $order->shipping_address,
                    'orderPrefix' => getSetting('orderCodePrefix'),
                    'isPosOrder' => $order->is_pos_order,
                    'returnReason' => $returnRequest->items->pluck('reason')->implode(', '),
                    'returnStatus' => $returnRequest->status == 1 ? 'pending' : ($returnRequest->status == 4 ? 'refunded' : 'processing'),
                ];
            });

            return response()->json([
                'success' => true,
                'status' => 200,
                'result' => [
                    'data' => $returnDetails,
                    'meta' => [
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                        'total' => $orders->total(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching return details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
