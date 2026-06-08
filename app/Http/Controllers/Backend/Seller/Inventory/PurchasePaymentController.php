<?php

namespace App\Http\Controllers\Backend\Seller\Inventory;

use App\Http\Controllers\Controller;
use App\Services\PurchasePaymentService;
use Illuminate\Http\Request;

class PurchasePaymentController extends Controller
{
    # Display a listing of the resource.
    public function index(Request $request)
    {
        $response = PurchasePaymentService::index($request);

        if ($response['status'] == 200) {
            return view('backend.seller.inventory.purchase-orders.payments', $response['result'])->render();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
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
