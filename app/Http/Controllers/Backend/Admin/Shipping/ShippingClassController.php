<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingClassRequest;
use App\Services\ShippingClassService;
use Illuminate\Http\Request;

class ShippingClassController extends Controller
{
    public function __construct(protected ShippingClassService $shippingClassService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $response = $this->shippingClassService->index($request);
            return view('backend.admin.shipping.class.index', $response['result']);
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $route = routePrefix();
        return view('backend.admin.shipping.class.create', compact('route'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShippingClassRequest $request)
    {
        try {
            $response = $this->shippingClassService->store($request->validated());
            if ($response['status'] == 200) {
                flash($response['message'])->success();
                return redirect()->route(routePrefix() . '.shipping-classes.index');
            }
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $route = routePrefix();
            $shippingClass = $this->shippingClassService->getById($id);
            if ($shippingClass) {
                return view('backend.admin.shipping.class.edit', compact('shippingClass', 'route'));
            }
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreShippingClassRequest $request, $id)
    {
        try {
            $response = $this->shippingClassService->update($request->validated(), $id);
            if ($response['status'] == 200) {
                flash($response['message'])->success();
                return redirect()->route(routePrefix() . '.shipping-classes.index');
            }
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $response = $this->shippingClassService->destroy($id);
            if ($response['status'] == 200) {
                flash($response['message'])->success();
                return redirect()->back();
            }
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $response = $this->shippingClassService->getProducts($request);
            return view('backend.admin.shipping.class.product-search-results', $response['result']);
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }
}
