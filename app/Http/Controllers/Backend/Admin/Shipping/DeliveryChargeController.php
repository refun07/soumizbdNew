<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Services\DeliveryChargeService;
use Illuminate\Http\Request;

class DeliveryChargeController extends Controller
{

    # constructor
    public function __construct()
    {
        $this->middleware(['permission:delivery_charges'])->only(['index', 'store']);
    }

    # get all data
    public function index(Request $request)
    {
        $response = DeliveryChargeService::index($request);

        if ($response['status'] == 200) {
            return view('backend.admin.shipping.delivery-charges.index', $response['result'])->render();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # add / update new data (update only for this controller)
    public function store(Request $request)
    {
        $response = DeliveryChargeService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # show area & cities
    public function show(Request $request, $zone_id)
    {
        //  
    }
}
