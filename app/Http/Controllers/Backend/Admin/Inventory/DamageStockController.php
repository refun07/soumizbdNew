<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Services\DamageStockService;
use Illuminate\Http\Request;

class DamageStockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_damage_stocks'])->only(['index', 'apiIndex']);
        $this->middleware(['permission:create_damage_stocks'])->only(['create', 'store', 'apiStore']);
        $this->middleware(['permission:delete_damage_stocks'])->only(['destroy', 'apiDestroy']);
    }

    public function index(Request $request, $limit = 15)
    {
        $response = DamageStockService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.admin.inventory.damage-stocks.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    public function create()
    {
        $response = DamageStockService::create();
        return view('backend.admin.inventory.damage-stocks.create', $response['result']);
    }

    public function getProducts(Request $request)
    {
        $response = DamageStockService::getProducts($request);
        return view('backend.admin.inventory.damage-stocks.product-search-results', $response['result']);
    }

    public function store(Request $request)
    {
        $response = DamageStockService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.damageStocks.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    public function show($id)
    {
        $response = DamageStockService::show($id);

        if ($response['status'] == 200) {
            return view('backend.admin.inventory.damage-stocks.show', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.damageStocks.index');
    }

    public function destroy($id)
    {
        $response = DamageStockService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
        } else {
            flash($response['message'] ?? translate('Something went wrong'))->error();
        }

        return back();
    }

    public function apiIndex(Request $request)
    {
        return DamageStockService::apiIndex($request);
    }

    public function apiStore(Request $request)
    {
        return DamageStockService::apiStore($request);
    }

    public function apiDestroy($id)
    {
        return DamageStockService::apiDestroy($id);
    }
}
