<?php

namespace App\Http\Controllers\Backend\Seller\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $response = StockTransferService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.seller.inventory.stock-transfers.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $warehouses  = Warehouse::shop()->latest()->get();
        return view('backend.seller.inventory.stock-transfers.create', compact('warehouses'));
    }

    # Show the form for editing the specified resource.
    public function getProducts(Request $request)
    {
        $response = StockTransferService::getProducts($request);
        return view('backend.seller.inventory.stock-transfers.product-search-results', $response['result']);
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = StockTransferService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.stockTransfers.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # Display the specified resource.
    public function show($id)
    {
        $stockTransfer = StockTransfer::findOrFail($id);
        $warehouses    = Warehouse::shop()->latest()->get();
        return view('backend.seller.inventory.stock-transfers.show', compact('stockTransfer', 'warehouses'));
    }
}
