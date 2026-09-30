<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    # get all resources, optionally with pagination
    public function index(Request $request)
    {
        $limit  = $request->limit ?? perPage(16);

        $brands = Brand::isActive()->latest();

        // by search keyword
        if ($request->searchKey != null) {
            $brands = $brands->where('name', 'like', '%' . $request->searchKey . '%'); // [TODO:: Search with translations also]
        }

        $brands = $limit === 'all' ? $brands->get() : $brands->paginate($limit);
        $result = BrandResource::collection($brands)->response()->getData(true);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $limit === 'all' ? ['data' => $result] : $result
        ];
    }

    # get brand details by slug
    public function show($slug)
    {
        $brand = Brand::isActive()->withCount('products')->where('slug', $slug)->first();

        // collections of this brand that have published products, for the brand page filter
        $brand?->load(['collections' => fn ($q) => $q->withCount(['products' => fn ($p) => $p->where('brand_id', $brand->id)->isPublished()])
            ->whereHas('products', fn ($p) => $p->where('brand_id', $brand->id)->isPublished())]);

        return [
            'success'   => true,
            'status'    => $brand ? 200 : 404,
            'message'   => $brand ? '' : translate('Brand not found'),
            'result'    => $brand ? new BrandResource($brand) : null,
        ];
    }
}
