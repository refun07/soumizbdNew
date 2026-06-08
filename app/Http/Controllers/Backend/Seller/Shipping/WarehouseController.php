<?php

namespace App\Http\Controllers\Backend\Seller\Shipping;

use App\Http\Controllers\Controller;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    # constructor
    public function __construct()
    {
    }

    # get all data
    public function index(Request $request)
    {
        $response = WarehouseService::index($request);

        if ($response['status'] == 200) {
            return view('backend.seller.shipping.warehouses.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # add new data
    public function store(Request $request)
    {
        $response = WarehouseService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.warehouses.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = WarehouseService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.seller.shipping.warehouses.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.warehouses.index');
    }

    # update state
    public function update(Request $request, $id)
    {
        $response = WarehouseService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.warehouses.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $response = WarehouseService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.warehouses.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.warehouses.index');
    }

    # update status
    public function updateStatus(Request $request)
    {

        $response = WarehouseService::updateStatus($request);
        return $response;
    }

    # update default
    public function updateDefault(Request $request)
    {
        $response = WarehouseService::updateDefault($request);
        return $response;
    }
}
