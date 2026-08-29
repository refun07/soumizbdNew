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

        $orders = apiUser()->orders()->with('orderGroup')->latest();

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
        $order = Order::with('orderGroup')->where('order_code', $code)->first();
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
            $orders = $orderGroup->orders;
            $orders->load('orderGroup');
            $orderTotal = (float) $orders->sum('total_amount');

            $summary = [
                'orderCode'             => (string) $orderGroup->code,
                'totalAmount'           => $orderTotal,
                'customerName'          => $orderGroup->user?->name ?? '',
                'shippingAddress'       => $orderGroup->shipping_address ?? '',
                'billingAddress'       => $orderGroup->billing_address ?? '',
                'phone'                 => $orderGroup->phone ?? '',
                'email'                 => $orderGroup->email ?? '',
                'paymentMethod'         => $orderGroup->transaction?->payment_method ?? '',
                'paymentMethodToShow'   => ucfirst(str_replace('_', ' ', $orderGroup->transaction?->payment_method ?? '')),
            ];

            $successPage = [
                'customEnabled'              => getSetting('orderSuccessCustomEnabled', '1') !== '0',
                'headline'                   => getSetting('orderSuccessHeadline', 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে।'),
                'headlineColor'              => getSetting('orderSuccessHeadlineColor', '#1f9bad'),
                'subtitle'                   => getSetting('orderSuccessSubtitle', 'আমাদের কল সেন্টার থেকে এক্ষন করে আপনার অর্ডারটি কনফার্ম করা হবে'),
                'subtitleColor'              => getSetting('orderSuccessSubtitleColor', '#ef4444'),
                'centerImage'                => uploadedAsset(getSetting('orderSuccessCenterImage')),
                'orderNumberTemplate'        => getSetting('orderSuccessOrderNumberTemplate', 'অর্ডার নম্বর : {order_code}'),
                'orderTotalTemplate'         => getSetting('orderSuccessOrderTotalTemplate', 'অর্ডারের মোট মূল্য {order_total}'),
                'orderTotalColor'            => getSetting('orderSuccessOrderTotalColor', '#ef4444'),
                'stepsImage'                 => uploadedAsset(getSetting('orderSuccessStepsImage')),
                'showCustomerDetails'        => getSetting('orderSuccessShowCustomerDetails', '1') !== '0',
                'showOrderInformation'       => getSetting('orderSuccessShowOrderInformation', '1') !== '0',
            ];

            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'summary'   => $summary,
                    'orders'    => OrderResource::collection($orders),
                    'successPage' => $successPage,
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
