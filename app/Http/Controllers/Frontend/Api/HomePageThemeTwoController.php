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

class HomePageThemeTwoController extends Controller
{
    # get banner
    public function banner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeTwoMainBanners'       => getSetting('showThemeTwoMainBanners') ?? '1',
                'themeTwoBannerImageOne'        => uploadedAsset(getSetting('themeTwoBannerImageOne')),
                'themeTwoBannerOneLink'         => getSetting('themeTwoBannerOneLink'),
                'themeTwoBannerOneHeadline'     => getSetting('themeTwoBannerOneHeadline'),
                'themeTwoBannerOneTitle'        => getSetting('themeTwoBannerOneTitle'),
                'themeTwoBannerOneSubTitle'     => getSetting('themeOneBannerSubTitle'),

                'themeTwoBannerImageTwo'        => uploadedAsset(getSetting('themeTwoBannerImageTwo')),
                'themeTwoBannerTwoLink'         => getSetting('themeTwoBannerTwoLink'),
                'themeTwoBannerTwoHeadline'     => getSetting('themeTwoBannerTwoHeadline'),
                'themeTwoBannerTwoTitle'        => getSetting('themeTwoBannerTwoTitle'),

                'themeTwoBannerOneHeadlineColor'        => getSetting('themeTwoBannerOneHeadlineColor'),
                'themeTwoBannerOneTitleColor'        => getSetting('themeTwoBannerOneTitleColor'),
                'themeTwoBannerOneSubTitleColor'        => getSetting('themeTwoBannerOneSubTitleColor'),
                'themeTwoBannerTwoHeadlineColor'        => getSetting('themeTwoBannerTwoHeadlineColor'),
                'themeTwoBannerTwoTitleColor'        => getSetting('themeTwoBannerTwoTitleColor'),

            ],
        ];
    }

    # get featured categories
    public function featuredCategories(Request $request)
    {
        $featuredCategories = collect();

        if (getSetting('themeTwoFeaturedCategories') != null) {
            $featuredCategories = json_decode(getSetting('themeTwoFeaturedCategories'), true);
        }

        $categories = Category::whereIn('id', $featuredCategories)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'categories'                => CategoryResource::collection($categories),
                'showFeaturedCategories'    => getSetting('showThemeTwoFeaturedCategories') ?? 0,
            ],
        ];
    }

    # get categoryProducts
    public function categoryProducts(Request $request)
    {
        $themeTwoCategories         = collect();

        if (getSetting('themeTwoCategories') != null) {
            $themeTwoCategories         = json_decode(getSetting('themeTwoCategories'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        foreach ($themeTwoCategories as $index => $id) {

            $category = Category::where('id', $id)->first();
            $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->with('productCategories')->whereHas('productCategories', function ($query) use ($category) {
                return $query->where('category_id', $category->id);
            })->take(10)->get();

            $mergedData[] = [
                'showThemeTwoCategoryProducts' => getSetting('showThemeTwoCategoryProducts') ?? '1',
                'category'  => new CategoryResource($category),
                'products'  => ProductResource::collection($products),
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get three image banner
    public function threeImageBanner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeTwoThreeImageBanner'           => getSetting('showThemeTwoThreeImageBanner') ?? 0,
                'themeTwoThreeBannerImageOne'            => uploadedAsset(getSetting('themeTwoThreeBannerImageOne')),
                'themeTwoThreeBannerOneLink'             => getSetting('themeTwoThreeBannerOneLink'),
                'themeTwoThreeBannerImageTwo'            => uploadedAsset(getSetting('themeTwoThreeBannerImageTwo')),
                'themeTwoThreeBannerTwoLink'             => getSetting('themeTwoThreeBannerTwoLink'),
                'themeTwoThreeBannerImageThree'          => uploadedAsset(getSetting('themeTwoThreeBannerImageThree')),
                'themeTwoThreeBannerThreeLink'           => getSetting('themeTwoThreeBannerThreeLink')
            ],
        ];
    }

    # get flash sale products
    public function flashSaleProducts(Request $request)
    {
        $campaign          = null;
        $flashSaleProducts = collect();

        if (getSetting('themeTwoFlashSale') != null) {
            $campaign = Campaign::where('id', getSetting('themeTwoFlashSale'))->first();
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
                'showHomeFlashSale'         => getSetting('showThemeTwoFlashSale') ?? 0,
                'flashSaleCampaign'         => $campaign ? new CampaignResource($campaign) : null,
                'flashSaleProducts'         => ProductResource::collection($flashSaleProducts),
            ],
        ];
    }

    # get four image banner
    public function fourImageBanner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeTwoFourImageBanner'      => getSetting('showThemeTwoFourImageBanner') ?? 0,
                //images & links
                'themeTwoFourBannerImageOne'       => uploadedAsset(getSetting('themeTwoFourBannerImageOne')),
                'themeTwoFourBannerOneLink'        => getSetting('themeTwoFourBannerOneLink'),
                'themeTwoFourBannerImageTwo'       => uploadedAsset(getSetting('themeTwoFourBannerImageTwo')),
                'themeTwoFourBannerTwoLink'        => getSetting('themeTwoFourBannerTwoLink'),
                'themeTwoFourBannerImageThree'     => uploadedAsset(getSetting('themeTwoFourBannerImageThree')),
                'themeTwoFourBannerThreeLink'      => getSetting('themeTwoFourBannerThreeLink'),
                'themeTwoFourBannerImageFour'      => uploadedAsset(getSetting('themeTwoFourBannerImageFour')),
                'themeTwoFourBannerFourLink'       => getSetting('themeTwoFourBannerFourLink'),
            ],
        ];
    }

    # get about us section
    public function aboutUsSection(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeTwoAboutUs'                 => getSetting('showThemeTwoAboutUs') ?? 0,
                'themeTwoAboutUsImage'                => uploadedAsset(getSetting('themeTwoAboutUsImage')),
                'themeTwoAboutUsHeadline'             => getSetting('themeTwoAboutUsHeadline'),
                'themeTwoAboutUsTitle'                => getSetting('themeTwoAboutUsTitle'),
                'themeTwoAboutUsSubTitle'             => getSetting('themeTwoAboutUsSubTitle'),

                'themeTwoAboutUsSectionOneIcon'       => uploadedAsset(getSetting('themeTwoAboutUsSectionOneIcon')),
                'themeTwoAboutUsSectionOneTitle'      => getSetting('themeTwoAboutUsSectionOneTitle'),
                'themeTwoAboutUsSectionOneSubTitle'   => getSetting('themeTwoAboutUsSectionOneSubTitle'),

                'themeTwoAboutUsSectionTwoIcon'       => uploadedAsset(getSetting('themeTwoAboutUsSectionTwoIcon')),
                'themeTwoAboutUsSectionTwoTitle'      => getSetting('themeTwoAboutUsSectionTwoTitle'),
                'themeTwoAboutUsSectionTwoSubTitle'   => getSetting('themeTwoAboutUsSectionTwoSubTitle'),

                'themeTwoAboutUsSectionThreeIcon'     => uploadedAsset(getSetting('themeTwoAboutUsSectionThreeIcon')),
                'themeTwoAboutUsSectionThreeTitle'    => getSetting('themeTwoAboutUsSectionThreeTitle'),
                'themeTwoAboutUsSectionThreeSubTitle' => getSetting('themeTwoAboutUsSectionThreeSubTitle'),

                'themeTwoAboutUsSectionFourIcon'      => uploadedAsset(getSetting('themeTwoAboutUsSectionFourIcon')),
                'themeTwoAboutUsSectionFourTitle'     => getSetting('themeTwoAboutUsSectionFourTitle'),
                'themeTwoAboutUsSectionFourSubTitle'  => getSetting('themeTwoAboutUsSectionFourSubTitle')
            ],
        ];
    }

    # get trndy products
    public function trendyProducts(Request $request)
    {
        $topProducts = collect();

        if (getSetting('themeTwoTrendyProducts') != null) {
            $topProducts = json_decode(getSetting('themeTwoTrendyProducts'), true);
        }

        $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->whereIn('id', $topProducts)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'                     => ProductResource::collection($products),
                'showThemeTwoTrendyProducts'   => getSetting('showThemeTwoTrendyProducts') ?? 0
            ],
        ];
    }

    # get reviews
    public function reviewSection(Request $request)
    {
        $reviews             = collect();
        $reviewDesignations  = collect();
        $reviewTexts         = collect();
        $reviewNames         = collect();
        $reviewRatings       = collect();

        if (getSetting('themeTwoReviewAvatars') != null) {
            $reviews            = json_decode(getSetting('themeTwoReviewAvatars'), true);
            $reviewDesignations = json_decode(getSetting('themeTwoReviewDesignations'), true);
            $reviewTexts        = json_decode(getSetting('themeTwoReviewTexts'), true);
            $reviewNames        = json_decode(getSetting('themeTwoReviewNames'), true);
            $reviewRatings      = json_decode(getSetting('themeTwoReviewRatings'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $reviews for this example
        foreach ($reviews as $index => $id) {
            // Check if the index exists in $reviewTitles
            if (isset($reviewNames[$index])) {
                // If it exists, extract the URL corresponding to the index
                $designation  = translate($reviewDesignations[$index]);
                $text         = translate($reviewTexts[$index]);
                $name         = translate($reviewNames[$index]);
                $rating       = $reviewRatings[$index];
            } else {
                // If not, set the URL to null or handle it as needed
                $designation  = null;
                $text         = null;
                $name         = null;
                $rating       = null;
            }

            // Add the ID and URL to the merged data array
            $mergedData[] = [
                'avatar'        => uploadedAsset($id),
                'name'          => $name,
                'designation'   => $designation,
                'text'          => $text,
                'rating'        => $rating,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'reviews'                     => $mergedData,
                'showThemeTwoReviewSection'   => getSetting('showThemeTwoReviewSection') ?? 0
            ],
        ];
    }
}
