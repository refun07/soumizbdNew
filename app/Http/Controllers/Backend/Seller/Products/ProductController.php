<?php

namespace App\Http\Controllers\Backend\Seller\Products;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    # construct
    public function __construct()
    {
        // 
    }

    # resources list
    public function index(Request $request)
    {
        $response = ProductService::index($request);

        if ($response['status'] == 200) {
            return view('backend.seller.products.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.dashboard');
    }

    # return view of create form
    public function create()
    {
        $response = ProductService::create();
        return view('backend.seller.products.create', $response['result']);
    }

    # generate variation combinations
    public function generateVariationCombinations(Request $request)
    {
        $response = ProductService::generateVariationCombinations($request);
        return view('backend.seller.products.new-variation-combinations', $response['result'])->render();
    }

    # get variation values to add new product
    public function getVariationValues(Request $request)
    {
        $response = ProductService::getVariationValues($request);
        return view('backend.seller.products.new-variation-values', $response['result']);
    }

    # new chosen variation
    public function getNewVariation(Request $request)
    {
        $response = ProductService::getNewVariation($request);
        $variations = $response['result']['variations'];
        $count      = $response['result']['count'];
        $chosenCounter      = $response['result']['chosenCounter'];

        if ($count == -1) {
            return array(
                'count' => $count,
                'chosenCounter' => $chosenCounter,
                'view' => view('backend.admin.products.new-variation', compact('variations', 'chosenCounter'))->render(),
            );
        } else if ($count > 0) {
            return array(
                'count' => $count,
                'chosenCounter' => $chosenCounter,
                'view'  => view('backend.admin.products.new-variation', compact('variations', 'chosenCounter'))->render(),
            );
        } else {
            return false;
        }
    }

    # add new data
    public function store(Request $request)
    {
        $response = ProductService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # import products from CSV
    public function import(Request $request)
    {
        $response = ProductService::import($request);

        if ($response['status'] == 200) {
            flash($response['message'])->success();
        } else {
            flash($response['message'] ?? translate('Something went wrong'))->error();
        }

        return redirect()->route('seller.products.index');
    }

    # download product import sample CSV
    public function sampleCsv()
    {
        return ProductService::sampleCsv();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = ProductService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.seller.products.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.products.index');
    }

    # update data
    public function update(Request $request, $id)
    {
        $response = ProductService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # update status
    public function updateStatus(Request $request)
    {
        $response = ProductService::updateStatus($request);
        return $response;
    }

    # duplicate data
    public function duplicate($id)
    {
        $response = ProductService::duplicate($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('seller.products.index');
    }

    # delete data
    public function destroy($id)
    {
        $response = ProductService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('seller.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('seller.products.index');
    }
}
