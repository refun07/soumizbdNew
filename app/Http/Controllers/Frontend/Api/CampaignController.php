<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\ProductResource;
use App\Http\Services\ProductService;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    # get all resources with pagination
    public function index(Request $request)
    {
        $limit     = $request->limit ?? perPage();

        $megaCampaigns = Campaign::isPublished()->where('shop_id', adminShopId())->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))->latest()->get();

        $sellerCampaigns = Campaign::isPublished()->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))->latest();


        // by shop
        if ($request->shopSlug != null) {
            $shop = Shop::where('slug', $request->shopSlug)->first();

            if ($shop->id != adminShopId()) {
                $sellerCampaigns = $sellerCampaigns->where('shop_id', '!=', adminShopId());
            }

            $sellerCampaigns = $sellerCampaigns->whereHas('shopInfo', function ($q) use ($request) {
                $q->where('slug', $request->shopSlug);
            });
        }

        $sellerCampaigns   = $sellerCampaigns->paginate($limit);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'megaCampaigns'   => CampaignResource::collection($megaCampaigns),
                'sellerCampaigns' => CampaignResource::collection($sellerCampaigns)->response()->getData(true),
            ]
        ];
    }

    # show campaign details
    public function show(Request $request, $slug)
    {
        $limit     = $request->limit ?? perPage();
        $campaign  = Campaign::isPublished()->where('slug', $slug)->first();

        if (!is_null($campaign)) {

            $products = Product::isPublished()->hideOnEcom()->whereHas('campaignProducts', function ($q) use ($campaign) {
                $q->where('campaign_id', $campaign->id);
            });

            $columns    = (new ProductService)->getSpecificColumns();
            $products   = $products->paginate($limit, $columns);

            $data =  [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'campaign'  => new CampaignResource($campaign),
                    'products'  => ProductResource::collection($products)->response()->getData(true)
                ]
            ];

            return $data;
        }

        return response()->json([
            'success'   => true,
            'status'    => 404,
            'message'   => translate('Campaign not found'),
            'result'    => null
        ], 404);
    }
}
