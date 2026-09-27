<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Services\ProductService;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Services\FacebookConversionService;


class SearchController extends Controller
{
    protected $facebook;

    public function __construct(FacebookConversionService $facebook)
    {
        $this->facebook = $facebook;
    }
    # get all products with pagination
    public function index(Request $request)
    {
        $showProducts = getSetting('searchResultShowProducts', '1') !== '0';
        $showBrands   = getSetting('searchResultShowBrands', '1') !== '0';
        $showShops    = getSetting('searchResultShowShops', '1') !== '0';

        $products = $showProducts
            ? Product::fromPublishedShops()->isPublished()->hideOnEcom()->with('variations')
            : null;
        $brands   = $showBrands ? Brand::isActive()->with('brandGroup') : null;
        $shops    = $showShops ? Shop::isApproved()->isPublished() : null;

        // by search keyword
        if ($request->searchKey != null) {
            if ($showProducts) {
                $products = $products->where('name', 'like', '%' . $request->searchKey . '%');
            }
            if ($showBrands) {
                // match by brand name or brand group name
                $brands = $brands->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->searchKey . '%')
                        ->orWhereHas('brandGroup', fn ($g) => $g->where('name', 'like', '%' . $request->searchKey . '%'));
                })->orderByRaw('name like ? desc', ['%' . $request->searchKey . '%']);
            }
            if ($showShops) {
                $shops = $shops->where('name', 'like', '%' . $request->searchKey . '%');
            }
        }

        // Send Facebook Search event
        try{

            $user = apiUser();
            if($user && config('app.FACEBOOK_PIXEL_ACTIVATION') == '1'){
                $this->facebook->sendEvent('Search', [
                    'email' => $user->email,
                    'phone' => $user->phone
                    // 'order_id' => $order->id,
                ], [
                    'search_string' => $request->searchKey
                ]);

            }

        }catch (\Exception $e) {

            dd($e);
        }

        $products = $showProducts
            ? $products->take(5)->get((new ProductService)->getSpecificColumns())
            : collect();
        $brands = $showBrands ? $brands->take(3)->get() : collect();
        $shops = $showShops ? $shops->take(3)->get() : collect();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'         => ProductResource::collection($products),
                'brands'           => BrandResource::collection($brands),
                'shops'            => ShopResource::collection($shops),
            ]
        ];
    }
}
