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
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class HomePageThemeOneController extends Controller
{
    public function reviewSection(Request $request)
    {
        $avatars = json_decode(getSetting('themeOneReviewAvatars') ?: getSetting('themeTwoReviewAvatars') ?: '[]', true) ?: [];
        $names = json_decode(getSetting('themeOneReviewNames') ?: getSetting('themeTwoReviewNames') ?: '[]', true) ?: [];
        $designations = json_decode(getSetting('themeOneReviewDesignations') ?: getSetting('themeTwoReviewDesignations') ?: '[]', true) ?: [];
        $texts = json_decode(getSetting('themeOneReviewTexts') ?: getSetting('themeTwoReviewTexts') ?: '[]', true) ?: [];
        $ratings = json_decode(getSetting('themeOneReviewRatings') ?: getSetting('themeTwoReviewRatings') ?: '[]', true) ?: [];

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'reviews' => \App\Services\StorefrontTestimonials::rows($avatars, $names, $designations, $texts, $ratings, fn ($avatar) => uploadedAsset($avatar)),
                'showThemeOneReviewSection' => getSetting('showThemeOneReviewSection') ?? getSetting('showThemeTwoReviewSection') ?? 0,
            ],
        ];
    }

    # get banner image
    public function banner(Request $request)
    {
        $banners = [];
        if (getSetting('themeOneMainBanners') != null) {
            $bannersData = json_decode(getSetting('themeOneMainBanners'), true);
            foreach ($bannersData as $banner) {
                $useBackgroundVideo = ($banner['useBackgroundVideo'] ?? '0') == '1';
                $banners[] = [
                    'foregroundImage' => uploadedAsset($banner['foregroundImage'] ?? null),
                    'showForegroundImage' => ($banner['showForegroundImage'] ?? '1') == '1' ? '1' : '0',
                    'useBackgroundVideo' => $useBackgroundVideo ? '1' : '0',
                    'isVideo' => $useBackgroundVideo,
                    'backgroundImage' => uploadedAsset($banner['backgroundImage'] ?? null),
                    'backgroundVideoLink' => $banner['backgroundVideoLink'] ?? '',
                    'backgroundVideoAudio' => ($banner['backgroundVideoAudio'] ?? '0') == '1' ? '1' : '0',
                    'link' => $banner['link'] ?? '',
                    'badgeText' => $banner['badgeText'] ?? '',
                    'title' => $banner['title'] ?? '',
                    'titleColor' => $banner['titleColor'] ?? '',
                    'subTitle' => $banner['subTitle'] ?? '',
                    'subTitleColor' => $banner['subTitleColor'] ?? '',
                ];
            }
        } else {
            // Fallback to legacy single banner format (for backward compatibility)
            $banners[] = [
                'foregroundImage' => uploadedAsset(getSetting('themeOneBannerForegroundImage')),
                'showForegroundImage' => '1',
                'useBackgroundVideo' => '0',
                'isVideo' => false,
                'backgroundImage' => uploadedAsset(getSetting('themeOneBannerBackgroundImage')),
                'backgroundVideoLink' => '',
                'backgroundVideoAudio' => '0',
                'link' => getSetting('themeOneBannerLink'),
                'badgeText' => getSetting('themeOneBannerBadgeText'),
                'title' => getSetting('themeOneBannerTitle'),
                'titleColor' => getSetting('themeOneBannerTitleColor'),
                'subTitle' => getSetting('themeOneBannerSubTitle'),
                'subTitleColor' => getSetting('themeOneBannerSubTitleColor'),
            ];
        }

        $sideBanners = [];
        if (getSetting('themeOneMainSideBanners') != null) {
            $sideBannersData = json_decode(getSetting('themeOneMainSideBanners'), true);
            foreach ($sideBannersData as $banner) {
                $sideBanners[] = [
                    'backgroundImage' => uploadedAsset($banner['backgroundImage'] ?? null),
                    'link' => $banner['link'] ?? '',
                ];
            }
        }

        $bannerSettings = [];
        if (getSetting('themeOneMainBannerSettings') != null) {
            $bannerSettings = json_decode(getSetting('themeOneMainBannerSettings'), true);
            $bannerSettings['autoplay'] = $bannerSettings['autoplay'] === '1' || $bannerSettings['autoplay'] === true;
            $bannerSettings['delay'] = intval($bannerSettings['delay'] ?? 5000);
            $bannerSettings['contentAlignment'] = $bannerSettings['contentAlignment'] ?? 'left';
        } else {
            $bannerSettings = [
                'autoplay' => true,
                'delay' => 5000,
                'contentAlignment' => 'left',
            ];
        }

        $secondaryBannerData = getSetting('themeOneSecondaryBanner') != null ? json_decode(getSetting('themeOneSecondaryBanner'), true) : [];
        $useSecondaryBackgroundVideo = ($secondaryBannerData['useBackgroundVideo'] ?? '0') == '1';
        $secondaryBanner = [
            'foregroundImage' => '',
            'showForegroundImage' => '0',
            'useBackgroundVideo' => $useSecondaryBackgroundVideo ? '1' : '0',
            'isVideo' => $useSecondaryBackgroundVideo,
            'contentAlignment' => $secondaryBannerData['contentAlignment'] ?? 'left',
            'backgroundImage' => uploadedAsset($secondaryBannerData['backgroundImage'] ?? null),
            'backgroundVideoLink' => $secondaryBannerData['backgroundVideoLink'] ?? '',
            'backgroundVideoAudio' => ($secondaryBannerData['backgroundVideoAudio'] ?? '0') == '1' ? '1' : '0',
            'link' => $secondaryBannerData['link'] ?? '',
            'badgeText' => $secondaryBannerData['badgeText'] ?? '',
            'title' => $secondaryBannerData['title'] ?? '',
            'titleColor' => $secondaryBannerData['titleColor'] ?? '',
            'subTitle' => $secondaryBannerData['subTitle'] ?? '',
            'subTitleColor' => $secondaryBannerData['subTitleColor'] ?? '',
        ];

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeOneMainBanner' => getSetting('showThemeOneMainBanner') ?? '0',
                'showThemeOneSecondaryBanner' => getSetting('showThemeOneSecondaryBanner') ?? '0',
                'banners' => $banners,
                'sideBanners' => $sideBanners,
                'secondaryBanner' => $secondaryBanner,
                'settings' => $bannerSettings,
            ],
        ];
    }

    # get promo cards
    public function promoCards(Request $request)
    {
        $settings = getSetting('themeOnePromoCardSettings') != null ? json_decode(getSetting('themeOnePromoCardSettings'), true) : [];
        $cardsData = getSetting('themeOnePromoCards') != null ? json_decode(getSetting('themeOnePromoCards'), true) : [];
        $cards = [];

        foreach ($cardsData as $card) {
            if (empty($card['image']) && empty($card['mainText']) && empty($card['subText'])) {
                continue;
            }

            $cards[] = [
                'image' => uploadedAsset($card['image'] ?? null),
                'mainText' => $card['mainText'] ?? '',
                'subText' => $card['subText'] ?? '',
            ];
        }

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeOnePromoCards' => getSetting('showThemeOnePromoCards') ?? '0',
                'sectionBackgroundColor' => $settings['sectionBackgroundColor'] ?? '#f7f7f7',
                'backgroundColor' => $settings['backgroundColor'] ?? '#ffffff',
                'mainTextColor' => $settings['mainTextColor'] ?? '#B3052A',
                'secondaryTextColor' => $settings['secondaryTextColor'] ?? '#3f3f46',
                'cards' => $cards,
            ],
        ];
    }

    # get sliders
    public function sliders(Request $request)
    {
        $sliders        = collect();
        $sliderLinks    = collect();

        if (getSetting('themeOneSliders') != null) {
            $sliders        = json_decode(getSetting('themeOneSliders'), true);
            $sliderLinks    = json_decode(getSetting('themeOneSliderLinks'), true);
        }

        $mergedData = [];

        foreach ($sliders as $index => $id) {
            $url = $sliderLinks[$index] ?? null;
            $mergedData[] = [
                'showThemeOneSliderSection' => getSetting('showThemeOneSliderSection'),
                'image' => uploadedAsset($id),
                'url'   => $url,
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
        $featuredCategories = [];

        if (getSetting('themeOneFeaturedCategories') != null) {
            $featuredCategories = collect(json_decode(getSetting('themeOneFeaturedCategories'), true))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->values()
                ->all();
        }

        $featuredCategories = \App\Services\BotanicalHomeContent::content(false)['categories']['selection'];
        $categories = Category::whereIn('id', $featuredCategories)
            ->get()
            ->sortBy(fn ($category) => array_search((int) $category->id, $featuredCategories))
            ->values();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'categories'                => CategoryResource::collection($categories),
                'showFeaturedCategories'    => getSetting('showThemeOneFeaturedCategories') ?? 0,
            ],
        ];
    }

    # get top products
    public function topProducts(Request $request)
    {
        $topProducts = [];

        if (getSetting('themeOneTopProducts') != null) {
            $topProducts = json_decode(getSetting('themeOneTopProducts'), true) ?: [];
        }

        $products = Product::fromPublishedShops()
            ->isPublished()
            ->hideOnEcom()
            ->with('categories')
            ->whereIn('id', $topProducts)
            ->get()
            ->sortBy(fn ($product) => array_search($product->id, $topProducts))
            ->values();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'                     => ProductResource::collection($products),
                'showThemeOneTopProducts'      => getSetting('showThemeOneTopProducts') ?? 0
            ],
        ];
    }

    # get on sale products
    public function onSaleProducts(Request $request)
    {
        $settings = getSetting('themeOneOnSaleProductSettings') != null
            ? json_decode(getSetting('themeOneOnSaleProductSettings'), true)
            : [];
        $secondaryOnSaleSetting = SystemSetting::where('type', 'showThemeOneSecondaryOnSaleProducts')->first();
        $showSecondaryOnSaleProducts = $secondaryOnSaleSetting
            ? ($secondaryOnSaleSetting->value == '1' ? '1' : '0')
            : '1';
        $topGridSetting = SystemSetting::where('type', 'showThemeOneTopGridProducts')->first();
        $showTopGridProducts = $topGridSetting
            ? ($topGridSetting->value == '1' ? '1' : '0')
            : '0';
        $onSaleProductIds = array_slice($this->themeOneOnSaleProductIds(), 0, 20);
        $topGridProductIds = array_slice($this->themeOneTopGridProductIds(), 0, 20);
        $secondaryOnSaleProductIds = array_slice($this->themeOneSecondaryOnSaleProductIds(), 0, 20);
        $products = collect();
        $topProducts = collect();
        $secondaryProducts = collect();
        $columns = (new ProductService)->getSpecificColumns();

        if (count($onSaleProductIds) > 0) {
            $products = Product::fromPublishedShops()
                ->isPublished()
                ->hideOnEcom()
                ->whereIn('id', $onSaleProductIds)
                ->get($columns)
                ->sortBy(fn ($product) => array_search($product->id, $onSaleProductIds))
                ->values();
        }

        if (count($topGridProductIds) > 0) {
            $topProducts = Product::fromPublishedShops()
                ->isPublished()
                ->hideOnEcom()
                ->whereIn('id', $topGridProductIds)
                ->get($columns)
                ->sortBy(fn ($product) => array_search($product->id, $topGridProductIds))
                ->values();
        }

        if (count($secondaryOnSaleProductIds) > 0) {
            $secondaryProducts = Product::fromPublishedShops()
                ->isPublished()
                ->hideOnEcom()
                ->whereIn('id', $secondaryOnSaleProductIds)
                ->get($columns)
                ->sortBy(fn ($product) => array_search($product->id, $secondaryOnSaleProductIds))
                ->values();
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'showThemeOneOnSaleProducts'          => getSetting('showThemeOneOnSaleProducts') ?? 0,
                'showThemeOneTopGridProducts'         => $showTopGridProducts,
                'showThemeOneSecondaryOnSaleProducts' => $showSecondaryOnSaleProducts,
                'backgroundColor'                     => $settings['backgroundColor'] ?? '#B3052A',
                'endsAt'                              => $settings['endsAt'] ?? null,
                'products'                            => ProductResource::collection($products),
                'topProducts'                         => ProductResource::collection($topProducts),
                'secondaryProducts'                   => ProductResource::collection($secondaryProducts),
            ],
        ];
    }

    # get single image banner
    public function singleImageBanner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeOneSingleImageBanner'                       => getSetting('showThemeOneSingleImageBanner') ?? 0,
                'themeOneSingleImageBannerForegroundImage'            => uploadedAsset(getSetting('themeOneSingleImageBannerForegroundImage')),
                'themeOneSingleImageBannerBackgroundImage'            => uploadedAsset(getSetting('themeOneSingleImageBannerBackgroundImage')),
                'themeOneSingleImageBannerLink'                       => getSetting('themeOneSingleImageBannerLink'),
                'themeOneSingleImageBannerBadgeText'                  => getSetting('themeOneSingleImageBannerBadgeText'),
                'themeOneSingleImageBannerBadgeTextColor'             => getSetting('themeOneSingleImageBannerBadgeTextColor'),
                'themeOneSingleImageBannerBadgeBackgroundColor'       => getSetting('themeOneSingleImageBannerBadgeBackgroundColor'),
                'themeOneSingleImageBannerHeadline'                   => getSetting('themeOneSingleImageBannerHeadline'),
                'themeOneSingleImageBannerHeadlineColor'              => getSetting('themeOneSingleImageBannerHeadlineColor'),
                'themeOneSingleImageBannerTitle'                      => getSetting('themeOneSingleImageBannerTitle'),
                'themeOneSingleImageBannerTitleColor'                 => getSetting('themeOneSingleImageBannerTitleColor'),
                'themeOneSingleImageBannerSubTitle'                   => getSetting('themeOneSingleImageBannerSubTitle'),
                'themeOneSingleImageBannerSubTitleColor'              => getSetting('themeOneSingleImageBannerSubTitleColor'),
                'themeOneSingleImageBannerButtonTextColor'            => getSetting('themeOneSingleImageBannerButtonTextColor'),
                'themeOneSingleImageBannerButtonBackgroundColor'      => getSetting('themeOneSingleImageBannerButtonBackgroundColor'),
            ],
        ];
    }

    # get flash sale products
    public function flashSaleProducts(Request $request)
    {
        $campaign          = null;
        $flashSaleProducts = collect();

        if (getSetting('themeOneFlashSale') != null) {
            $campaign = Campaign::where('id', getSetting('themeOneFlashSale'))->first();
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
                'showHomeFlashSale'         => getSetting('showThemeOneFlashSale') ?? 0,
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
                'showThemeOneFourImageBanner'                => getSetting('showThemeOneFourImageBanner') ?? 0,
                //images & links
                'themeOneFourImageBannerImageOne'            => uploadedAsset(getSetting('themeOneFourImageBannerImageOne')),
                'themeOneFourImageBannerImageOneLink'        => getSetting('themeOneFourImageBannerImageOneLink'),
                'themeOneFourImageBannerImageTwo'            => uploadedAsset(getSetting('themeOneFourImageBannerImageTwo')),
                'themeOneFourImageBannerImageTwoLink'        => getSetting('themeOneFourImageBannerImageTwoLink'),
                'themeOneFourImageBannerImageThree'          => uploadedAsset(getSetting('themeOneFourImageBannerImageThree')),
                'themeOneFourImageBannerImageThreeLink'      => getSetting('themeOneFourImageBannerImageThreeLink'),
                'themeOneFourImageBannerImageFour'           => uploadedAsset(getSetting('themeOneFourImageBannerImageFour')),
                'themeOneFourImageBannerImageFourLink'       => getSetting('themeOneFourImageBannerImageFourLink'),
            ],
        ];
    }

    # get categoryProducts
    public function categoryProducts(Request $request)
    {
        $themeOneCategories         = collect();
        $themeOneCategoryImages     = collect();
        $themeOneCategoryLinks      = collect();

        if (getSetting('themeOneCategories') != null) {
            $themeOneCategories         = json_decode(getSetting('themeOneCategories'), true);
            $themeOneCategoryImages     = json_decode(getSetting('themeOneCategoryImages'), true);
            $themeOneCategoryLinks      = json_decode(getSetting('themeOneCategoryLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        foreach ($themeOneCategories as $index => $id) {

            if (isset($themeOneCategoryImages[$index])) {
                $image = uploadedAsset($themeOneCategoryImages[$index]);
            } else {
                $image = uploadedAsset(0);
            }

            if (isset($themeOneCategoryLinks[$index])) {
                $url = $themeOneCategoryLinks[$index];
            } else {
                $url = null;
            }

            $category = Category::where('id', $id)->first();
            $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->with('productCategories')->whereHas('productCategories', function ($query) use ($category) {
                return $query->where('category_id', $category->id);
            })->take(10)->get();

            $mergedData[] = [
                'showThemeOneCategoryProducts' => getSetting('showThemeOneCategoryProducts') ?? '1',
                'category'  => new CategoryResource($category),
                'products'  => ProductResource::collection($products),
                'image'     => $image,
                'url'       => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get bottom image banner
    public function bottomImageBanner(Request $request)
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => [
                'showThemeOneBottomFourImageBanner'                => getSetting('showThemeOneBottomFourImageBanner') ?? 0,
                'themeOneBottomFourImageBannerTitle'               => getSetting('themeOneBottomFourImageBannerTitle'),
                'themeOneBottomFourImageBannerSubTitle'            => getSetting('themeOneBottomFourImageBannerSubTitle'),
                //images & links
                'themeOneBottomFourImageBannerImageOne'            => uploadedAsset(getSetting('themeOneBottomFourImageBannerImageOne')),
                'themeOneBottomFourImageBannerImageOneBG'          => getSetting('themeOneBottomFourImageBannerImageOneBG'),
                'themeOneBottomFourImageBannerImageOneSubTitle'    => getSetting('themeOneBottomFourImageBannerImageOneSubTitle'),
                'themeOneBottomFourImageBannerImageOneTitle'       => getSetting('themeOneBottomFourImageBannerImageOneTitle'),
                'themeOneBottomFourImageBannerImageOneDescription' => getSetting('themeOneBottomFourImageBannerImageOneDescription'),
                'themeOneBottomFourImageBannerImageOneLink'        => getSetting('themeOneBottomFourImageBannerImageOneLink'),

                'themeOneBottomFourImageBannerImageTwo'            => uploadedAsset(getSetting('themeOneBottomFourImageBannerImageTwo')),
                'themeOneBottomFourImageBannerImageTwoBG'          => getSetting('themeOneBottomFourImageBannerImageTwoBG'),
                'themeOneBottomFourImageBannerImageTwoSubTitle'    => getSetting('themeOneBottomFourImageBannerImageTwoSubTitle'),
                'themeOneBottomFourImageBannerImageTwoTitle'       => getSetting('themeOneBottomFourImageBannerImageTwoTitle'),
                'themeOneBottomFourImageBannerImageTwoDescription' => getSetting('themeOneBottomFourImageBannerImageTwoDescription'),
                'themeOneBottomFourImageBannerImageTwoLink'        => getSetting('themeOneBottomFourImageBannerImageTwoLink'),

                'themeOneBottomFourImageBannerImageThree'          => uploadedAsset(getSetting('themeOneBottomFourImageBannerImageThree')),
                'themeOneBottomFourImageBannerImageThreeBG'        => getSetting('themeOneBottomFourImageBannerImageThreeBG'),
                'themeOneBottomFourImageBannerImageThreeSubTitle'  => getSetting('themeOneBottomFourImageBannerImageThreeSubTitle'),
                'themeOneBottomFourImageBannerImageThreeTitle'     => getSetting('themeOneBottomFourImageBannerImageThreeTitle'),
                'themeOneBottomFourImageBannerImageThreeDescription' => getSetting('themeOneBottomFourImageBannerImageThreeDescription'),
                'themeOneBottomFourImageBannerImageThreeLink'      => getSetting('themeOneBottomFourImageBannerImageThreeLink'),

                'themeOneBottomFourImageBannerImageFour'           => uploadedAsset(getSetting('themeOneBottomFourImageBannerImageFour')),
                'themeOneBottomFourImageBannerImageFourBG'         => getSetting('themeOneBottomFourImageBannerImageFourBG'),
                'themeOneBottomFourImageBannerImageFourSubTitle'   => getSetting('themeOneBottomFourImageBannerImageFourSubTitle'),
                'themeOneBottomFourImageBannerImageFourTitle'      => getSetting('themeOneBottomFourImageBannerImageFourTitle'),
                'themeOneBottomFourImageBannerImageFourDescription'=> getSetting('themeOneBottomFourImageBannerImageFourDescription'),
                'themeOneBottomFourImageBannerImageFourLink'       => getSetting('themeOneBottomFourImageBannerImageFourLink'),
            ],
        ];
    }

    private function themeOneOnSaleProductIds(): array
    {
        $products = getSetting('themeOneOnSaleProducts');
        return $this->settingProductIds($products);
    }

    private function themeOneSecondaryOnSaleProductIds(): array
    {
        $products = getSetting('themeOneSecondaryOnSaleProducts');
        return $this->settingProductIds($products);
    }

    private function themeOneTopGridProductIds(): array
    {
        $products = getSetting('themeOneTopGridProducts');
        return $this->settingProductIds($products);
    }

    private function settingProductIds($products): array
    {
        if ($products == null) {
            return [];
        }

        $products = json_decode($products, true);
        if (!is_array($products)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $products))));
    }
}
