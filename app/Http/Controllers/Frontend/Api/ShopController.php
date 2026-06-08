<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ShopReviewResource;
use App\Http\Resources\ShopSectionResource;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    # get all resources with pagination
    public function index(Request $request)
    {
        $limit  = $request->limit ?? perPage();

        $shops = Shop::isApproved()->isPublished()->latest();

        // by search keyword
        if ($request->searchKey != null) {
            $shops = $shops->where('name', 'like', '%' . $request->searchKey . '%'); // [TODO:: Search with translations also]
        }

        $shops   = $shops->paginate($limit);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ShopResource::collection($shops)->response()->getData(true)
        ];
    }

    # get resource details
    public function show($slug, $limit = 15)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $shop      = Shop::isApproved()->isPublished()->where('slug', $slug)->first();

        if (!is_null($shop)) {
            $data['result']  = new ShopResource($shop);
        } else {
            $data['message'] = translate('Shop not found');
        }

        return $data;
    }

    # get shop sections
    public function shopSections($slug)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $shop = Shop::isApproved()->isPublished()->where('slug', $slug)->first();

        if (!is_null($shop)) {
            $data['result']                 = ShopSectionResource::collection($shop->shopSections()->orderBy('order', 'ASC')->get());
            $data['justForYouProducts']     = ProductResource::collection($shop->products()->isPublished()->latest()->take(15)->get());
        } else {
            $data['message'] = translate('Shop not found');
        }

        return $data;
    }

    # get resource details
    public function profile($slug)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $shop = Shop::isApproved()->isPublished()->where('slug', $slug)->first();

        // age 
        $count = 0;
        $unit =  translate('Years');
        if (now()->diffInYears($shop->created_at) == 0) {
            if (now()->diffInMonths($shop->created_at) == 0) {
                $count = now()->diffInDays($shop->created_at);
                $unit =  translate('Days');
            } else {
                $count = now()->diffInMonths($shop->created_at);
                $unit =  translate('Months');
            }
        } else {
            $count = now()->diffInYears($shop->created_at);
        }

        // delivered orders %
        $deliveryPercentage = 0 . '%';
        $total              = $shop->orders()->count();
        $delivered          = $shop->orders()->where('delivery_status', 'delivered')->count();
        if ($total != 0 && $delivered != 0) {
            $deliveryPercentage = number_format(($delivered / $total) * 100, 2, '.', '')  . '%';
        }

        // product review
        $average = 0;
        $totalReviews   = $shop->productReviews()->count();
        $totalSum       = $shop->productReviews()->sum('rating');
        if ($totalReviews != 0 && $totalSum != 0) {
            $average = number_format(($totalSum / $totalReviews), 2, '.', '');
        }

        // positive impression %
        $positivePercentage = 0 . '%';
        $totalShopReviews   = $shop->impressions()->count();
        $positive           = $shop->impressions()->where('impression', 'positive')->count();
        if ($totalShopReviews != 0 && $positive != 0) {
            $positivePercentage = number_format(($positive / $totalShopReviews) * 100, 2, '.', '')  . '%';
        }

        $overview = [
            'age'   => [
                'count'     => $count < 10 ? '0' . $count . '+' : $count . '+',
                'unit'      => $unit
            ],

            'totalProducts'         => $shop->products()->count(),
            'deliveryPercentage'    => $deliveryPercentage,
            'overallProductReview'  => $average,
            'positiveShopReviewPercentage'    => $positivePercentage,
        ];


        // reviews
        $allReviews = ReviewResource::collection($shop->productReviews()->paginate(10))->response()->getData(true);

        // shop reviews
        $shopReviews = ShopReviewResource::collection($shop->impressions()->latest()->take(3)->get());

        // best selling products 
        $bestSellingProducts = $shop->products()->orderBy('total_sale_count', 'DESC')->take(10)->get();

        if (!is_null($shop)) {
            $data['result']  = [
                'overview'          => $overview,
                'productReviews'    => [
                    'summary'       => getShopRatings($shop->productReviews()),
                    'allReviews'    => $allReviews
                ],
                'shopReviews'    => [
                    'summary'       => [
                        'total'     => $totalShopReviews,
                        'positive'  => $positive,
                        'negative'  => $shop->impressions()->where('impression', 'negative')->count(),
                        'neutral'   => $shop->impressions()->where('impression', 'neutral')->count(),
                    ],
                    'allReviews'    => $shopReviews
                ],
                'bestSellingProducts' => ProductResource::collection($bestSellingProducts)
            ];
        } else {
            $data['message'] = translate('Shop not found');
        }

        return $data;
    }
}
