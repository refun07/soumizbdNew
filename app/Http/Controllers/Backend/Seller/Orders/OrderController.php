<?php

namespace App\Http\Controllers\Backend\Seller\Orders;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    # get all resources
    public function index(Request $request)
    {
        $response = $this->orderService->index($request);

        if ($response['status'] == 200) {
            return view('backend.seller.orders.index', $response['result']);
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # show orders
    public function show($code)
    {
        $response = $this->orderService->show($code);
        if ($response['status'] == 200) {
            return view('backend.seller.orders.show', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.orders.index');
    }

    # download invoice
    public function downloadInvoice($id)
    {
        return $this->orderService->downloadInvoice($id);
    }

    # download challan
    public function downloadChallan($id)
    {
        return $this->orderService->downloadChallan($id);
    }

    # store or update order tracking
    public function updateOrderTracking(Request $request)
    {
        $response = $this->orderService->updateOrderTracking($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # store new order updates
    public function storeOrderUpdates(Request $request)
    {
        $response = $this->orderService->storeOrderUpdates($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # delete order update
    public function deleteOrderUpdate($id)
    {
        $response = $this->orderService->deleteOrderUpdate($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # update payment status 
    public function updatePaymentStatus(Request $request)
    {
        $response = $this->orderService->updatePaymentStatus($request);
        if ($response['status'] == 200) {
            return true;
        }
        return false;
    }

    # update delivery status
    public function updateDeliveryStatus(Request $request)
    {
        $response = $this->orderService->updateDeliveryStatus($request);
        if ($response['status'] == 200) {
            return true;
        }
        return false;
    }


    # update Order Address
    public function updateOrderAddress(Request $request)
    {
        $response = $this->orderService->updateOrderAddress($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # order payment
    public function orderPayment(Request $request)
    {
        $response = $this->orderService->orderPayment($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }
}
