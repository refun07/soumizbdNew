<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_stock_adjustment'])->only(['index']);
        $this->middleware(['permission:create_stock_adjustment'])->only(['create', 'store']);
        $this->middleware(['permission:show_adjustment_details'])->only(['show']);
    }

    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $response = StockAdjustmentService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.admin.inventory.stock-adjustments.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $response = StockAdjustmentService::create();
        return view('backend.admin.inventory.stock-adjustments.create', $response['result']);
    }

    # Show the form for editing the specified resource.
    public function getProducts(Request $request)
    {
        $response = StockAdjustmentService::getProducts($request);
        return view('backend.admin.inventory.stock-adjustments.product-search-results', $response['result']);
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = StockAdjustmentService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.stockAdjustments.index');
        }

        // Log error details for debugging
        \Log::error('Stock Adjustment Store Failed', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'response_status' => $response['status'],
            'response_message' => $response['message'] ?? 'Unknown error',
            'warehouse_id' => $request->warehouse_id ?? null,
            'selected_variations_count' => count($request->selectedVariationIds ?? []),
        ]);

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # Display the specified resource.
    public function show($id)
    {
        $stockAdjustment = StockAdjustment::findOrFail($id);
        $warehouses      = Warehouse::shop()->latest()->get();
        $suppliers       = Supplier::shop()->latest()->get();
        return view('backend.admin.inventory.stock-adjustments.show', compact('stockAdjustment', 'warehouses', 'suppliers'));
    }
}
