<?php

namespace App\Http\Controllers\Backend\Seller\Inventory;

use App\Http\Controllers\Controller;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $response = PurchaseOrderService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.seller.inventory.purchase-orders.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $response = PurchaseOrderService::create();
        return view('backend.seller.inventory.purchase-orders.create', $response['result']);
    }

    # Show the form for editing the specified resource.
    public function getProducts(Request $request)
    {
        $response = PurchaseOrderService::getProducts($request);
        return view('backend.seller.inventory.purchase-orders.product-search-results', $response['result']);
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = PurchaseOrderService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.purchase-orders.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = PurchaseOrderService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.seller.inventory.purchase-orders.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.purchase-orders.index');
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $response = PurchaseOrderService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.purchase-orders.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # cancel purchase
    public function destroy($id)
    {
        $response = PurchaseOrderService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.purchase-orders.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.purchase-orders.index');
    }

    # download purchase invoice
    public function downloadInvoice($id)
    {
        return PurchaseOrderService::downloadInvoice($id);
    }

    # Display a listing of the resource.
    public function returnIndex(Request $request, $limit = 15)
    {
        $response = PurchaseOrderService::returnIndex($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.seller.inventory.purchase-orders.return', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # Show the form for returning a new resource.
    public function returnCreate(Request $request, $id)
    {
        $response = PurchaseOrderService::returnCreate($request, $id);
        return view('backend.seller.inventory.purchase-orders.edit', $response['result']);
    }

    # Show the form for returning a new resource.
    public function returnStore(Request $request, $id)
    {
        $response = PurchaseOrderService::returnStore($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.purchase-return.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.purchase-return.index');
    }
}
