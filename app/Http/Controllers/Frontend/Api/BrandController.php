<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandGroupResource;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Models\BrandGroup;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    # get all resources with pagination
    public function index(Request $request)
    {
        $limit  = $request->limit ?? perPage(16);

        $brands = Brand::isActive()->latest();

        // by brand group slug
        if ($request->group != null) {
            $brands = $brands->whereHas('brandGroup', fn ($q) => $q->where('slug', $request->group));
        }

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

    # get brand groups that have at least one active brand
    public function groups()
    {
        $groups = BrandGroup::ordered()
            ->whereHas('brands', fn ($q) => $q->isActive())
            ->withCount(['brands' => fn ($q) => $q->isActive()])
            ->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => BrandGroupResource::collection($groups),
        ];
    }

    # get brand details by slug
    public function show($slug)
    {
        $brand = Brand::isActive()->with('brandGroup')->withCount('products')->where('slug', $slug)->first();

        return [
            'success'   => true,
            'status'    => $brand ? 200 : 404,
            'message'   => $brand ? '' : translate('Brand not found'),
            'result'    => $brand ? new BrandResource($brand) : null,
        ];
    }
}
