<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Services\PurchasePaymentService;
use Illuminate\Http\Request;

class PurchasePaymentController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:show_purchase_order_payments'])->only(['index']);
        $this->middleware(['permission:make_purchase_order_payments'])->only(['store']);
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $response = PurchasePaymentService::index($request);

        if ($response['status'] == 200) {
            return view('backend.admin.inventory.purchase-orders.payments', $response['result'])->render();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }


    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = PurchasePaymentService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }
}
