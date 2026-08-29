<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    # get all resources with pagination
    public function index(Request $request)
    {
        $limit  = $request->limit ?? perPage(16);

        $brands = Brand::isActive()->latest();

        // by search keyword
        if ($request->searchKey != null) {
            $brands = $brands->where('name', 'like', '%' . $request->searchKey . '%'); // [TODO:: Search with translations also]
        }

        $brands   = $brands->paginate($limit);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => BrandResource::collection($brands)->response()->getData(true)
        ];
    }

    # get brand details by slug
    public function show($slug)
    {
        $brand = Brand::isActive()->withCount('products')->where('slug', $slug)->first();

        return [
            'success'   => true,
            'status'    => $brand ? 200 : 404,
            'message'   => $brand ? '' : translate('Brand not found'),
            'result'    => $brand ? new BrandResource($brand) : null,
        ];
    }
}
