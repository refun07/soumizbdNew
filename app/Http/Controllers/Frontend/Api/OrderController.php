<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderGroup;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    # get resources
    public function index(Request $request)
    {
        $limit  = $request->limit ?? perPage();

        $orders = apiUser()->orders()->latest();

        // filter by delivery status
        if ($request->deliveryStatus != null) {
            $orders->where('delivery_status', $request->deliveryStatus);
        }

        $orders = $orders->paginate($limit);
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => OrderResource::collection($orders)->response()->getData(true)
        ];
    }

    # show order
    public function show($code)
    {
        $order = Order::where('order_code', $code)->first();
        if (!is_null($order)) {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => new OrderResource($order)
            ];
        }

        return response()->json([
            'success'   => false,
            'status'    => 404,
            'message'   => translate('Order not found with this code'),
            'result'    => null
        ], 404);
    }

    # order success
    public function success($code)
    {
        $orderGroup = OrderGroup::where('code', $code)->first();
        if (!is_null($orderGroup)) {
            $summary = [
                'customerName'          => $orderGroup->user?->name ?? '',
                'shippingAddress'       => $orderGroup->shipping_address ?? '',
                'billingAddress'       => $orderGroup->billing_address ?? '',
                'phone'                 => $orderGroup->phone ?? '',
                'email'                 => $orderGroup->email ?? '',
                'paymentMethod'         => $orderGroup->transaction?->payment_method ?? '',
                'paymentMethodToShow'   => ucfirst(str_replace('_', ' ', $orderGroup->transaction?->payment_method ?? '')),
            ];

            $orders = $orderGroup->orders;
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'summary'   => $summary,
                    'orders'    => OrderResource::collection($orders),
                ]
            ];
        }

        return response()->json([
            'success'   => false,
            'status'    => 404,
            'message'   => translate('Order not found with this code'),
            'result'    => null
        ], 404);
    }
}
