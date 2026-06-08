<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Services\ProductService;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomePageThemeThreeController extends Controller
{
    # get sliders
    public function sliders(Request $request)
    {
        $sliders            = collect();
        $sliderHeadlines    = collect();
        $sliderTitles       = collect();
        $sliderSubtitles    = collect();
        $sliderLinks        = collect();

        if (getSetting('themeThreeSliders') != null) {
            $sliders            = json_decode(getSetting('themeThreeSliders'), true);
            $sliderHeadlines    = json_decode(getSetting('themeThreeSliderHeadlines'), true);
            $sliderTitles       = json_decode(getSetting('themeThreeSliderTitles'), true);
            $sliderSubtitles    = json_decode(getSetting('themeThreeSliderSubtitles'), true);
            $sliderLinks        = json_decode(getSetting('themeThreeSliderLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $sliders for this example
        foreach ($sliders as $index => $id) {
            $mergedData[] = [
                'showThemeThreeSliderSection' => getSetting('showThemeThreeSliderSection') ?? '1',
                'image'     => uploadedAsset($id),
                'headline'  => $sliderHeadlines[$index] ?? null,
                'title'     => $sliderTitles[$index] ?? null,
                'subtitle'  => $sliderSubtitles[$index] ?? null,
                'url'       => $sliderLinks[$index] ?? null,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get featured categories
    public function featuredCategories(Request $request)
    {
        $featuredCategories = collect();

        if (getSetting('themeThreeFeaturedCategories') != null) {
            $featuredCategories = json_decode(getSetting('themeThreeFeaturedCategories'), true);
        }

        $categories = Category::whereIn('id', $featuredCategories)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'themeThreeFeaturedCategoriesHeadline'    => getSetting('themeThreeFeaturedCategoriesHeadline'),
                'themeThreeFeaturedCategoriesSubtitle'    => getSetting('themeThreeFeaturedCategoriesSubtitle'),
                'categories'                              => CategoryResource::collection($categories),
                'showFeaturedCategories'                  => getSetting('showThemeTwoFeaturedCategories') ?? 0
            ],
        ];
    }

     # get featured products
     public function featuredProducts(Request $request)
     {
         $featuredProducts = collect();
 
         if (getSetting('themeThreeFeaturedProducts') != null) {
             $featuredProducts = json_decode(getSetting('themeThreeFeaturedProducts'), true);
         }
 
         $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->whereIn('id', $featuredProducts)->get();
         return [
             'success'   => true,
             'status'    => 200,
             'message'   => '',
             'result'    => [
                 'products'                     => ProductResource::collection($products),
                 'showthemeThreeFeaturedProducts'      => getSetting('showthemeThreeFeaturedProducts') ?? 0,
                 'viewMoreUrl'                  => getSetting('themeThreeFeaturedProductsViewMoreUrl') ?? '/products'
             ],
         ];
     }

     # get popular categories
    public function popularCategories(Request $request)
    {
        $popularCategories = collect();

        if (getSetting('themeThreePopularCategories') != null) {
            $popularCategories = json_decode(getSetting('themeThreePopularCategories'), true);
        }

        $categories = Category::whereIn('id', $popularCategories)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'themeThreePopularCategoriesHeadline'    => getSetting('themeThreePopularCategoriesHeadline'),
                'themeThreePopularCategoriesSubtitle'    => getSetting('themeThreePopularCategoriesSubtitle'),
                'categories'                             => CategoryResource::collection($categories),
                'showPopularCategories'                  => getSetting('showthemeThreePopularCategories') ?? 0
            ],
        ];
    }

    # get four image banner
    public function twoImageBanner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeThreeTwoImageBanner'               => getSetting('showThemeThreeTwoImageBanner') ?? 0,
                //images & links
                'themeThreeTwoImageBannerImageOne'           => uploadedAsset(getSetting('themeThreeTwoImageBannerImageOne')),
                'themeThreeTwoImageBannerImageOneTitle'      => getSetting('themeThreeTwoImageBannerImageOneTitle'),
                'themeThreeTwoImageBannerImageOneSubtitle'   => getSetting('themeThreeTwoImageBannerImageOneSubtitle'),
                'themeThreeTwoImageBannerImageOneLink'       => getSetting('themeThreeTwoImageBannerImageOneLink'),

                'themeThreeTwoImageBannerImageTwo'           => uploadedAsset(getSetting('themeThreeTwoImageBannerImageTwo')),
                'themeThreeTwoImageBannerImageTwoTitle'      => getSetting('themeThreeTwoImageBannerImageTwoTitle'),
                'themeThreeTwoImageBannerImageTwoSubtitle'   => getSetting('themeThreeTwoImageBannerImageTwoSubtitle'),
                'themeThreeTwoImageBannerImageTwoLink'       => getSetting('themeThreeTwoImageBannerImageTwoLink'),
            ],
        ];
    }

    # get flash sale products
    public function flashSaleProducts(Request $request)
    {
        $campaign          = null;
        $flashSaleProducts = collect();

        if (getSetting('themeThreeFlashSale') != null) {
            $campaign = Campaign::where('id', getSetting('themeThreeFlashSale'))->first();
        }

        if (!is_null($campaign)) {

            $flashSaleProducts = Product::isPublished()->hideOnEcom()->whereHas('campaignProducts', function ($q) use ($campaign) {
                $q->where('campaign_id', $campaign->id);
            });

            $columns    = (new ProductService)->getSpecificColumns();
            $flashSaleProducts   = $flashSaleProducts->take(15)->get($columns);
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'showHomeFlashSale'         => getSetting('showThemeThreeFlashSale') ?? 0,
                'flashSaleCampaign'         => $campaign ? new CampaignResource($campaign) : null,
                'flashSaleProducts'         => ProductResource::collection($flashSaleProducts),
            ],
        ];
    }

    # get categoryProducts
    public function categoryProducts(Request $request)
    {
        $themeThreeCategories         = collect();

        if (getSetting('themeThreeCategories') != null) {
            $themeThreeCategories         = json_decode(getSetting('themeThreeCategories'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        foreach ($themeThreeCategories as $index => $id) {

            $category = Category::where('id', $id)->first();
            $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->with('productCategories')->whereHas('productCategories', function ($query) use ($category) {
                return $query->where('category_id', $category->id);
            })->take(10)->get();

            $mergedData[] = [
                'category'  => new CategoryResource($category),
                'products'  => ProductResource::collection($products),
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'showThemeThreeCategoryProducts' => getSetting('showThemeThreeCategoryProducts') ?? '1',
            'result'    => $mergedData,
        ];
    }

    # get four image banner
    public function bottomTwoImageBanner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeThreeBottomTwoImageBanner'               => getSetting('showThemeThreeBottomTwoImageBanner') ?? 0,
                //images & links
                'themeThreeBottomTwoImageBannerImageOne'           => uploadedAsset(getSetting('themeThreeBottomTwoImageBannerImageOne')),
                'themeThreeBottomTwoImageBannerImageOneHeadline'   => getSetting('themeThreeBottomTwoImageBannerImageOneHeadline'),
                'themeThreeBottomTwoImageBannerImageOneTitle'      => getSetting('themeThreeBottomTwoImageBannerImageOneTitle'),
                'themeThreeBottomTwoImageBannerImageOneSubtitle'   => getSetting('themeThreeBottomTwoImageBannerImageOneSubtitle'),
                'themeThreeBottomTwoImageBannerImageOneLink'       => getSetting('themeThreeBottomTwoImageBannerImageOneLink'),

                'themeThreeBottomTwoImageBannerImageTwo'           => uploadedAsset(getSetting('themeThreeBottomTwoImageBannerImageTwo')),
                'themeThreeBottomTwoImageBannerImageTwoHeadline'   => getSetting('themeThreeBottomTwoImageBannerImageTwoHeadline'),
                'themeThreeBottomTwoImageBannerImageTwoTitle'      => getSetting('themeThreeBottomTwoImageBannerImageTwoTitle'),
                'themeThreeBottomTwoImageBannerImageTwoSubtitle'   => getSetting('themeThreeBottomTwoImageBannerImageTwoSubtitle'),
                'themeThreeBottomTwoImageBannerImageTwoLink'       => getSetting('themeThreeBottomTwoImageBannerImageTwoLink'),
            ],
        ];
    }
}
