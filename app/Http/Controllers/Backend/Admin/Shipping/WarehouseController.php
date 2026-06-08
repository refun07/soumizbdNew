<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{

    # constructor
    public function __construct()
    {
        $this->middleware(['permission:view_warehouses'])->only(['index']);
        $this->middleware(['permission:create_warehouses'])->only(['store']);
        $this->middleware(['permission:edit_warehouses'])->only(['edit', 'update', 'updateStatus', 'updateDefault']);
        $this->middleware(['permission:delete_warehouses'])->only(['destroy']);
    }

    # get all data
    public function index(Request $request)
    {
        $response = WarehouseService::index($request);

        if ($response['status'] == 200) {
            return view('backend.admin.shipping.warehouses.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # add new data
    public function store(Request $request)
    {
        $response = WarehouseService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.warehouses.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = WarehouseService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.admin.shipping.warehouses.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.warehouses.index');
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
        return redirect()->route('admin.warehouses.index');
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

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $response = WarehouseService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.warehouses.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.warehouses.index');
    }
}
