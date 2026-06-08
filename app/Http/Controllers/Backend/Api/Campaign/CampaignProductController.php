<?php

namespace App\Http\Controllers\Backend\Api\Campaign;

use App\Http\Controllers\Controller;
use App\Http\Resources\Campaign\CampaignProductResource;
use App\Http\Resources\Campaign\CampaignVariationResource;
use App\Http\Services\ProductService;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use DB;
use Illuminate\Http\Request;

class CampaignProductController extends Controller
{
    # get FilterData
    public function getFilterData(Request $request)
    {
        $brands     = Brand::isActive()->latest()->get(['id', 'name']);
        $categories = Category::get(['id', 'name']);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'brands'        => $brands,
                'categories'    => $categories,
            ]
        ];
    }

    # get products
    public function index(Request $request)
    {
        $limit = $request->limit ?? perPage();

        $products = Product::where('shop_id', shopId())->isPublished()->with('variations');

        // by search keyword
        if ($request->searchKey != null) {
            $products = $products->where('name', 'like', '%' . $request->searchKey . '%'); // [TODO:: Search with translations also]
        }

        // filter by brand
        if ($request->brandId != null) {
            $products = $products->where('brand_id', $request->brandId);
        }

        // filter by category
        if ($request->categoryId != null) {
            $categoryId = $request->categoryId;
            $products = $products->whereHas('productCategories', function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            });
        }

        $columns    = (new ProductService)->getSpecificColumns();
        $products   = $products->paginate($limit, $columns);

        $blockedVariationIds = [];
        if ($request->campaignId) {
            $campaign = Campaign::where('id', (int) $request->campaignId)
                ->where('shop_id', shopId())
                ->first();

            if ($campaign) {
                $blockedVariationIds = CampaignProduct::where('shop_id', shopId())
                    ->where('campaign_id', '!=', $campaign->id)
                    ->whereHas('campaign', function ($q) use ($campaign) {
                        $q->where('start_date', '<=', $campaign->end_date)
                            ->where('end_date', '>=', $campaign->start_date);
                    })
                    ->pluck('product_variation_id')
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();
            }
        }
        $request->attributes->set('blockedVariationIds', $blockedVariationIds);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => CampaignProductResource::collection($products)->response()->getData(true)
        ];
    }

    # store product to campaign
    public function store(Request $request)
    {
        $break = false;
        $response = [
            'success'   => false,
            'status'    => 409,
            'message'   => '',
            'result'    => null
        ];

        // start order submission
        DB::beginTransaction();
        $campaignProducts = collect();
        if ($request->productVariationIds) {
            foreach ($request->productVariationIds as $productVariationId) {
                $productVariation = ProductVariation::whereId((int) $productVariationId)->first();
                $campaignProduct = CampaignProduct::where('product_variation_id', $productVariationId)->where('campaign_id', $request->campaignId)->first();
                if (!$campaignProduct) {
                    $campaign = Campaign::whereId((int) $request->campaignId)->first();

                    // check if this product is already in other campaign of this campaign timeline
                    $campaignProductInThisTimeline = CampaignProduct::where('product_variation_id', $productVariationId)
                        ->whereHas('campaign', function ($q) use ($campaign) {
                            $q->where('id', '!=', $campaign->id)
                                ->where('start_date', '<=', $campaign->end_date)
                                ->where('end_date', '>=', $campaign->start_date);
                        })->first();

                    if (!is_null($campaignProductInThisTimeline)) {
                        $response['message'] = $productVariation->product?->name . 'is already on another campaign of same timeline';
                        $break = true;
                        break;
                    } else {
                        $campaignProduct                        = new CampaignProduct;
                        $campaignProduct->shop_id               = shopId();
                        $campaignProduct->campaign_id           = $request->campaignId;
                        $campaignProduct->discount_value        = $campaign->default_discount_value;
                        $campaignProduct->discount_type         = $campaign->default_discount_type;
                        $campaignProduct->product_variation_id  = $productVariation->id;
                        $campaignProduct->product_id            = $productVariation->product->id;
                        $campaignProduct->save();

                        $campaignProducts->push($campaignProduct);
                    }
                }
            }

            if ($break == true) {
                DB::rollback();
                return response()->json($response, 409);
            }

            DB::commit();
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => count($campaignProducts) > 0 ? CampaignVariationResource::collection($campaignProducts) : null
        ];
    }

    # update product to campaign
    public function update(Request $request)
    {
        $campaignProduct = CampaignProduct::where('id', $request->id)->first();

        if ($campaignProduct) {
            $campaignProduct->discount_type   = $request->discountType;
            $campaignProduct->discount_value  = $request->discountValue;
            $campaignProduct->save();

            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => new CampaignVariationResource($campaignProduct)
            ];
        }

        return response()->json([
            'success'   => false,
            'status'    => 404,
            'message'   => translate('Product not found in the campaign'),
            'result'    => null
        ], 404);
    }

    # delete product from campaign
    public function delete(Request $request)
    {
        $campaignProduct = CampaignProduct::where('id', $request->id)->first();

        if ($campaignProduct) {
            $campaign = Campaign::where('shop_id', shopId())->where('id', $campaignProduct->campaign_id)->first();
            if (!$campaign) {
                return response()->json([
                    'success'   => false,
                    'status'    => 404,
                    'message'   => translate('Product is not found in your campaign'),
                    'result'    => null
                ], 404);
            }

            $campaignProduct->delete();
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Product has been deleted successfully'),
                'result'    => null
            ];
        }

        return response()->json([
            'success'   => false,
            'status'    => 404,
            'message'   => translate('Product is not found in the campaign'),
            'result'    => null
        ], 404);
    }

    # get resources of a campaign 
    public function getCampaignProducts(Request $request, $campaignId)
    {
        $limit = $request->limit ?? perPage();
        $campaignProducts = CampaignProduct::latest()->whereCampaignId($campaignId)->where('shop_id', shopId());

        if ($request->searchKey) {
            $campaignProducts = $campaignProducts->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->searchKey . '%');
            });
        }
        if ($request->brandId) {
            $campaignProducts = $campaignProducts->whereHas('product', function ($q) use ($request) {
                $q->where('brand_id', $request->brandId);
            });
        }

        if ($request->categoryId) {
            $categoryId       = $request->categoryId;
            $campaignProducts = $campaignProducts->whereHas('product', function ($q) use ($categoryId) {
                $q->whereHas('productCategories', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            });
        }

        $campaignProducts = $campaignProducts->paginate($limit);
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => CampaignVariationResource::collection($campaignProducts)->response()->getData(true)
        ];
    }
}
