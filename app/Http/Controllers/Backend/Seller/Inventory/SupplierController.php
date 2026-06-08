<?php

namespace App\Http\Controllers\Backend\Seller\Inventory;

use App\Http\Controllers\Controller;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $response = SupplierService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.seller.inventory.suppliers.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.seller.inventory.suppliers.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = SupplierService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.suppliers.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # Display the specified resource.
    public function show($id)
    {
        //
    }

    # Show the form for editing the specified resource.
    public function edit(Request $request, $id)
    {
        $response = SupplierService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.seller.inventory.suppliers.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.suppliers.index');
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $response = SupplierService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.suppliers.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $response = SupplierService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.suppliers.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('seller.suppliers.index');
    }
}
