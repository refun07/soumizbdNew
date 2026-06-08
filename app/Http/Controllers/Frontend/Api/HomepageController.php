<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    # get featured brands
    public function featuredBrands(Request $request)
    {
        $featuredBrands = collect();

        if (getSetting('homeFeaturedBrands') != null) {
            $featuredBrands = json_decode(getSetting('homeFeaturedBrands'), true);
        }

        $brands = Brand::isActive()->whereIn('id', $featuredBrands)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'brands'                => BrandResource::collection($brands),
                'showFeaturedBrands'    => getSetting('showHomeFeaturedBrands') ?? 0,
            ],
        ];
    }

    # new arrivals
    public function newArrivals(Request $request)
    {
        // Check if product sections should be hidden (only hide when explicitly set to "1")
        $hideProductSections = getSetting('hideProductSections');
        if ($hideProductSections === '1') {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [],
            ];
        }

        $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->latest()->take(4)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ProductResource::collection($products),
        ];
    }

    # best sellers
    public function bestSellers(Request $request)
    {
        // Check if product sections should be hidden (only hide when explicitly set to "1")
        $hideProductSections = getSetting('hideProductSections');
        if ($hideProductSections === '1') {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [],
            ];
        }

        $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->orderBy('total_sale_count', 'DESC')->take(4)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ProductResource::collection($products),
        ];
    }

    # highest rated
    public function highestRated(Request $request)
    {
        // Check if product sections should be hidden (only hide when explicitly set to "1")
        $hideProductSections = getSetting('hideProductSections');
        if ($hideProductSections === '1') {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [],
            ];
        }

        //To-Do rating calculation
        $products = Product::fromPublishedShops()->isPublished()->hideOnEcom()->latest()->take(4)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ProductResource::collection($products),
        ];
    }

    # get footer
    public function footer(Request $request)
    {
        $topCategories = Category::orderBy('total_sale_amount', 'DESC')->take(6)->get();

        $quickLinks = collect();
        if (getSetting('homeFooterQuickLinks') != null) {
            $quickLinks = json_decode(getSetting('homeFooterQuickLinks'), true);
        }
        $quickLinkPages = Page::where('is_active', 1)->where('type', 'custom')->whereIn('id', $quickLinks)->get();

        $defaultPages = Page::where('is_active', 1)->where('type', 'default')->get();

        $popularTags = getSetting('homeFooterPopularTags') ? json_decode(getSetting('homeFooterPopularTags'), true) : [];

        $popularTags = collect();

        if (getSetting('homeFooterPopularTags') != null) {
            $popularTags = json_decode(getSetting('homeFooterPopularTags'), true);
        }

        $tags = Tag::whereIn('id', $popularTags)->pluck('name');

        $quickLinks        = collect();
        $quickLinkTitles    = collect();

        if (getSetting('homeFooterQuickLinks') != null) {
            $quickLinks        = json_decode(getSetting('homeFooterQuickLinks'), true);
            $quickLinkTitles    = json_decode(getSetting('homeFooterQuickLinkTitles'), true);
        }

        $mergedData = [];
        foreach ($quickLinks as $index => $link) {
            if (isset($quickLinkTitles[$index])) {
                $title = $quickLinkTitles[$index];
            } else {
                $title = null;
            }
            $mergedData[] = [
                'title' => translate($title),
                'url'   => $link,
            ];
        }

        // Important Links
        $defaultImportantLinks = [
            ['title' => 'Home', 'link' => '/', 'is_enabled' => true],
            ['title' => 'Shop', 'link' => '/shops', 'is_enabled' => true],
            ['title' => 'All Products', 'link' => '/products', 'is_enabled' => true],
            ['title' => 'About Us', 'link' => '/pages/about-us', 'is_enabled' => true],
            ['title' => 'All Brands', 'link' => '/brands', 'is_enabled' => true]
        ];

        $importantLinks = getSetting('homeFooterImportantLinks')
            ? json_decode(getSetting('homeFooterImportantLinks'), true)
            : $defaultImportantLinks;

        $importantLinkPages = [];
        foreach ($importantLinks as $link) {
            $isEnabled = $link['is_enabled'] ?? false;
            // Check for both boolean true and string "1"
            if ($isEnabled === true || $isEnabled === '1' || $isEnabled === 'true') {
                $importantLinkPages[] = [
                    'title' => translate($link['title'] ?? ''),
                    'url'   => $link['link'] ?? '',
                ];
            }
        }

        // Helper function to process image paths
        $processImage = function($value) {
            if (empty($value)) return '';
            // If it's a numeric value, treat as MediaFile ID
            if (is_numeric($value)) {
                return uploadedAsset($value);
            }
            // If it's a file path, convert to asset URL
            if (strpos($value, 'uploads/') === 0) {
                return asset($value);
            }
            // Otherwise, return as-is
            return $value;
        };

        $defaultFooter = getSetting('defaultFooter', 'footer-one');
        $defaultFooterInfoItems = [
            [
                'title' => 'Return Policy',
                'description' => 'If a product is damaged, faulty, or different than ordered, we offer a return for that product.'
            ],
            [
                'title' => 'Delivery System',
                'description' => 'We provide cash on delivery. You can check and try the product at the delivery point before receiving.'
            ],
            [
                'title' => 'Delivery Charges',
                'description' => "Delivery charges are as follows:\nInside Dhaka: 90TK\nOutside Dhaka: 150TK"
            ],
            [
                'title' => 'Delivery Time',
                'description' => "Delivery Time is as follows:\nInside Dhaka: 2-3 Days\nOutside Dhaka: 4-5 Days"
            ]
        ];
        $footerInfoItems = getSetting('homeFooterInfoItems')
            ? json_decode(getSetting('homeFooterInfoItems'), true)
            : $defaultFooterInfoItems;
        $systemName = getSetting('systemName') ?: config('app.name');
        $copyrightText = getSetting('homeFooterCopyrightText')
            ? translate(getSetting('homeFooterCopyrightText'))
            : 'All Rights Reserved By ' . $systemName;

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'defaultFooter'               => $defaultFooter,
                'homeFooterLogo'             => $processImage(getSetting('homeFooterLogo')),
                'homeFooterLogoSize'         => getSetting('homeFooterLogoSize', 'medium'),
                'homeFooterDescription'      => getSetting('homeFooterDescription'),
                'homeFooterCompanyInfoAlignment' => getSetting('homeFooterCompanyInfoAlignment', 'left'),
                'homeFooterCustomerSupport'  => getSetting('homeFooterCustomerSupport') ?? "",
                'homeFooterCustomerSupportSecondary' => getSetting('homeFooterCustomerSupportSecondary') ?? "",
                'homeFooterAddress'          => getSetting('homeFooterAddress') ?? "",
                'homeFooterEmail'            => getSetting('homeFooterEmail') ?? "",
                'showHomeFooterInfoBar'       => getSetting('showHomeFooterInfoBar', '1'),
                'homeFooterInfoItems'         => is_array($footerInfoItems) && !empty($footerInfoItems) ? $footerInfoItems : $defaultFooterInfoItems,
                'homeFooterInfoBgColor'       => getSetting('homeFooterInfoBgColor', '#ffffff'),
                'homeFooterInfoIconColor'     => getSetting('homeFooterInfoIconColor', '#8f5847'),
                'homeFooterInfoTitleColor'    => getSetting('homeFooterInfoTitleColor', '#111827'),
                'homeFooterInfoTextColor'     => getSetting('homeFooterInfoTextColor', '#0f172a'),

                'showHomeFacebookLink'       => getSetting('showHomeFacebookLink') ?? 0,
                'homeFooterFacebookLink'     => getSetting('homeFooterFacebookLink') ?? "",

                'showHomeTwitterLink'        => getSetting('showHomeTwitterLink') ?? 0,
                'homeFooterTwitterLink'      => getSetting('homeFooterTwitterLink') ?? "",

                'showHomeInstagramLink'      => getSetting('showHomeInstagramLink') ?? 0,
                'homeFooterInstagramLink'    => getSetting('homeFooterInstagramLink') ?? "",

                'showHomeYoutubeLink'        => getSetting('showHomeYoutubeLink') ?? 0,
                'homeFooterYoutubeLink'        => getSetting('homeFooterYoutubeLink') ?? "",

                'showHomeLinkedInLink'       => getSetting('showHomeLinkedInLink') ?? 0,
                'homeFooterLinkedInLink'     => getSetting('homeFooterLinkedInLink') ?? "",

                'showHomeTikTokLink'         => getSetting('showHomeTikTokLink') ?? 0,
                'homeFooterTikTokLink'       => getSetting('homeFooterTikTokLink') ?? "",

                'showHomePinterestLink'      => getSetting('showHomePinterestLink') ?? 0,
                'homeFooterPinterestLink'    => getSetting('homeFooterPinterestLink') ?? "",

                'quickLinkPages'             => $mergedData,
                'importantLinkPages'         => $importantLinkPages,
                'topCategories'              => CategoryResource::collection($topCategories),

                'popularTags'                => $tags,
                'defaultPages'               => PageResource::collection($defaultPages),

                'showHomeFooterPlayStoreLink'  => getSetting('showHomeFooterPlayStoreLink') ?? 0,
                'homeFooterPlayStoreLink'      => getSetting('homeFooterPlayStoreLink') ?? "",

                'showHomeFooterAppStoreLink'  => getSetting('showHomeFooterAppStoreLink') ?? 0,
                'homeFooterAppStoreLink'      => getSetting('homeFooterAppStoreLink') ?? "",

                'homeFooterCopyrightText'     => $copyrightText,
                'homeFooterSecuredPayments'   => $processImage(getSetting('homeFooterSecuredPayments')),

                'footer_bg_color'             => getSetting('footer_bg_color', $defaultFooter === 'footer-two' ? '#9b7655' : '#1a1a2e'),
                'footer_text_color'           => getSetting('footer_text_color', '#ffffff'),
                'copyright_bg_color'          => getSetting('copyright_bg_color', $defaultFooter === 'footer-two' ? '#8b5747' : '#22c55e'),
                'copyright_text_color'        => getSetting('copyright_text_color', '#ffffff'),

                'showHomeFooterGoogleMaps'    => getSetting('showHomeFooterGoogleMaps') ?? 0,
                'homeFooterGoogleMapsEnabled' => getSetting('homeFooterGoogleMapsEnabled') ?? 0,
                'homeFooterGoogleMapsType'    => getSetting('homeFooterGoogleMapsType', 'embed'),
                'homeFooterGoogleMapsValue'   => getSetting('homeFooterGoogleMapsValue', '')
            ],
        ];
    }

    # page details
    public function pageDetails($slug)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $page = Page::where('is_active', 1)->where('slug', $slug)->first();

        if (!is_null($page)) {
            $data['result'] = new PageResource($page);
        } else {
            $data['message'] = translate('Page not found');
        }

        return $data;
    }

    # get homepage popup (theme-independent)
    public function homepagePopup(Request $request)
    {
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'showHomepagePopup'  => getSetting('showHomepagePopup') ?? 0,
                'homepagePopupImage' => uploadedAsset(getSetting('homepagePopupImage')),
                'homepagePopupLink'  => getSetting('homepagePopupLink'),
                'homepagePopupFullSize' => getSetting('homepagePopupFullSize') ?? 0,
                'homepagePopupAutoHideEnabled' => getSetting('homepagePopupAutoHideEnabled') ?? 1,
                'homepagePopupDuration' => getSetting('homepagePopupDuration') ?? 5,
            ],
        ];
    }
}
