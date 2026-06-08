<?php

namespace App\Http\Controllers\Backend\Seller\Promotions;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    # construct
    public function __construct()
    {
        // 
    }

    # resource list
    public function index(Request $request)
    {
        $response = CouponService::index($request);

        if ($response['status'] == 200) {
            return view('backend.seller.coupons.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # return view of create form
    public function create()
    {
        $response = CouponService::create();
        return view('backend.seller.coupons.create', $response['result']);
    }

    # add new data
    public function store(Request $request)
    {
        $response = CouponService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.coupons.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = CouponService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.seller.coupons.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.coupons.index');
    }

    # update data
    public function update(Request $request, $id)
    {
        $response = CouponService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.coupons.index');
    }

    # delete data
    public function destroy($id)
    {
        $response = CouponService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.coupons.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('seller.coupons.index');
    }
}
