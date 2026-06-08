<?php

namespace App\Http\Controllers\Backend\Seller\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $response = StockAdjustmentService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.seller.inventory.stock-adjustments.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $response = StockAdjustmentService::create();
        return view('backend.seller.inventory.stock-adjustments.create', $response['result']);
    }

    # Show the form for editing the specified resource.
    public function getProducts(Request $request)
    {
        $response = StockAdjustmentService::getProducts($request);
        return view('backend.seller.inventory.stock-adjustments.product-search-results', $response['result']);
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = StockAdjustmentService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.stockAdjustments.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # Display the specified resource.
    public function show($id)
    {
        $stockAdjustment = StockAdjustment::findOrFail($id);
        $warehouses      = Warehouse::shop()->latest()->get();
        $suppliers       = Supplier::shop()->latest()->get();
        return view('backend.seller.inventory.stock-adjustments.show', compact('stockAdjustment', 'warehouses', 'suppliers'));
    }
}
