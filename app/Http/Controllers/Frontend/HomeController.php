<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SystemSetting;
use App\Models\Translation;
use Cache;
use Illuminate\Http\Request;
use Route;

class HomeController extends Controller
{
    # Helper method to extract phone number from hotline text
    # Example: "Our Hotline +88 01961 177726" → "+8801961177726"
    private function extractPhoneNumber(string $hotlineText): string
    {
        if (preg_match('/[\d\s\+]+$/', $hotlineText, $matches)) {
            $phone = preg_replace('/\s+/', '', $matches[0]);
            return $phone;
        }
        return preg_replace('/\D/', '', $hotlineText);
    }

    # return to frontend homepage with the necessary data
    public function index(Request $request, $slug = null)
    {
        # Check if skip cart is enabled and user is accessing cart page
        if (getSetting('skipCart') == '1' && request()->path() === 'cart') {
            return redirect('/checkout');
        }

        $primaryColor   = implode(' ', sscanf(getSetting('primaryColor') ?? '#5D7261', "#%02x%02x%02x"));
        $secondaryColor = implode(' ', sscanf(getSetting('secondaryColor') ?? '#BABE77', "#%02x%02x%02x"));

        # Get the selected header from database
        $selectedHeader = getSetting('defaultHeader') ?? 'header-one';

        $paymentMethods = [];
        if (getSetting('cod_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Cash On Delivery");
            $payment->value = "cash_on_delivery";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('card_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Card On Delivery");
            $payment->value = "card_on_delivery";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('paypal_activation') == 1) {
            $payment            = new SystemSetting;
            $payment->name      = translate("Paypal");
            $payment->value     = "paypal";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('stripe_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Stripe");
            $payment->value = "stripe";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('flutterwave_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Flutterwave");
            $payment->value = "flutterwave";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('paytm_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("PayTm");
            $payment->value = "paytm";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('paystack_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Paystack");
            $payment->value = "paystack";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('sslcommerz_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("SSLCommerz");
            $payment->value = "sslcommerz";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('coingate_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("CoinGate");
            $payment->value = "coingate";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('iyzico_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("IyZico");
            $payment->value = "iyzico";
            array_push($paymentMethods, $payment);
        }
        if (getSetting('razorpay_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Razorpay");
            $payment->value = "razorpay";
            array_push($paymentMethods, $payment);
        }
        if (getSetting('instamojo_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Instamojo");
            $payment->value = "instamojo";
            array_push($paymentMethods, $payment);
        }
        if (getSetting('bkash_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Bkash");
            $payment->value = "bkash";
            array_push($paymentMethods, $payment);
        }

        // social logins
        $socialLogins = [];
        if (config('app.GOOGLE_ACTIVATION') == 1) {
            $social         = new SystemSetting;
            $social->name  = "Google";
            $social->url   = route('social.login', 'google');
            array_push($socialLogins, $social);
        }

        if (config('app.FACEBOOK_ACTIVATION') == 1) {
            $social        = new SystemSetting;
            $social->name  = "Facebook";
            $social->url   = route('social.login', 'facebook');
            array_push($socialLogins, $social);
        }

        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            #theme settings
            'defaultTheme'          => getSetting('defaultTheme') ?? 'theme-one',
            'defaultHeader'         => $selectedHeader,
            'defaultProductCard'    => getSetting('defaultProductCard') ?? 'product-card-one',
            'defaultCategoryCard'   => getSetting('defaultCategoryCard') ?? 'category-card-one',

            #cart visibility setting
            'cartVisibilityToggle'  => getSetting('cartVisibilityToggle') ?? '1',
            'storefrontLayout'      => [
                'desktopSideSpacing' => (int) getSetting('storefrontDesktopSideSpacing', 14),
            ],

            #top bar settings
            'topbarHotline'         => $this->extractPhoneNumber(getSetting('topbar_hotline') ?? 'Our Hotline +88 0176 229 201'),
            'topbarHotlineLabel'    => getSetting('topbar_hotline') ?? 'Our Hotline +88 0176 229 201',
            'topbarPromoText'       => getSetting('topbar_promo_text') ?? 'FREE delivery & 40% Discount for next 3 orders! Place your 1st order!',

            #header-specific topbar configuration for the selected header
            'headerTopbarConfig'    => [
                'enabled' => filter_var(getSetting("header_{$selectedHeader}_topbar_enabled", true), FILTER_VALIDATE_BOOLEAN),
                'hotline_number' => getSetting("header_{$selectedHeader}_topbar_hotline") ?? getSetting('topbar_hotline') ?? 'Our Hotline +88 0176 229 201',
                'hotline' => $this->extractPhoneNumber(getSetting("header_{$selectedHeader}_topbar_hotline") ?? getSetting('topbar_hotline') ?? 'Our Hotline +88 0176 229 201'),
                'promo_text_icon' => getSetting("header_{$selectedHeader}_topbar_promo_text_icon") ?? '',
                'promo_text' => getSetting("header_{$selectedHeader}_topbar_promo_text") ?? getSetting('topbar_promo_text') ?? 'FREE delivery & 40% Discount for next 3 orders! Place your 1st order!',
                'bg_color' => getSetting("header_{$selectedHeader}_topbar_bg_color") ?? '#1a1a2e',
                'text_color' => getSetting("header_{$selectedHeader}_topbar_text_color") ?? '#ffffff'
            ],

            #header-specific navbar configuration for the selected header
            'headerNavbarConfig'    => [
                'custom_links_enabled' => filter_var(getSetting("header_{$selectedHeader}_nav_custom_links_enabled", false), FILTER_VALIDATE_BOOLEAN),
                'custom_links' => json_decode(getSetting("header_{$selectedHeader}_nav_custom_links", '[]'), true) ?? [],
                'action_button' => [
                    'enabled' => filter_var(getSetting("header_{$selectedHeader}_nav_action_button_enabled", false), FILTER_VALIDATE_BOOLEAN),
                    'name' => getSetting("header_{$selectedHeader}_nav_action_button_name", ''),
                    'link' => getSetting("header_{$selectedHeader}_nav_action_button_link", ''),
                ],
                'menu_style' => [
                    'font_size' => (int) getSetting("header_{$selectedHeader}_nav_menu_font_size", 14),
                    'font_weight' => getSetting("header_{$selectedHeader}_nav_menu_font_weight", '500'),
                    'text_color' => getSetting("header_{$selectedHeader}_nav_menu_text_color", '#FFFFFF'),
                    'active_border_color' => getSetting("header_{$selectedHeader}_nav_menu_active_border_color", '#FFFFFF'),
                    'active_border_width' => (int) getSetting("header_{$selectedHeader}_nav_menu_active_border_width", 4),
                ],
            ],

            # main menu configuration from menu builder
            'mainMenuConfig'        => $mainMenuConfig = json_decode(getSetting('main_menu_config', '{
                "menu_items": [],
                "show_sub_category": true,
                "design": {
                    "bg_color": "#ffffff",
                    "text_color": "#0f0f0f",
                    "hover_color": "#b3052a"
                }
            }'), true),

            # currency settings
            'currency'      => [
                'code'      => getSetting('currencyCode') ?? 'usd',
                'symbol'    => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1  : 0] : 'symbol_first',

                    'show'  => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals'     => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator'  => getSetting('decimalSeparator') ?? '.',
            ],

            # general settings
            'generalSettings' => [
                'appName' => config('app.name'),
                'appMode' => config('app.app_mode'),
                'logo'    => uploadedAsset(getSetting('websiteHeaderLogo')),
                'rootUrl' => config('app.url'),
                'apiUrl'  => config('app.url') . config('app.api_pathname'),
                // 'apiUrl' => 'https://ba11-103-176-19-44.ngrok-free.app/armtech/multivendor-ecommerce/api/v1',
                'demoMode' => config('app.demo_mode'),
                'env' => config('app.env'),
                'showCouponOnCart' => getSetting('showCouponOnCart', '1'),
                'allowBackorder' => getSetting('allowBackorder') == '1',
                'customerLoginMethod' => getSetting('customerLoginMethod') ?? 'email',
                'skipCart' => getSetting('skipCart') == '1',
                'productInfiniteScroll' => getSetting('productInfiniteScroll') == '1',
            ],
            'searchSettings' => [
                'showProducts' => getSetting('searchResultShowProducts', '1') !== '0',
                'showBrands' => getSetting('searchResultShowBrands', '1') !== '0',
                'showShops' => getSetting('searchResultShowShops', '1') !== '0',
                'productPriceColor' => getSetting('searchResultProductPriceColor', '#5D7261')
                    ?: '#5D7261',
                'productDiscountColor' => getSetting('searchResultProductDiscountColor', '#EF4444')
                    ?: '#EF4444',
            ],
            'customerMenu' => [
                'enabled' => getSetting('headerCustomerMenuEnabled', '1') !== '0',
                'socialLinks' => $this->getHeaderSocialLinks(),
            ],
            'countries'         => Country::where('is_active', 1)->get(),
            'languages'         => Language::isActive()->get(['name', 'code', 'flag', 'is_rtl']),
            'defaultLang'       => Language::where('code', config('app.default_language'))->get(['name', 'code', 'flag', 'is_rtl'])->first(),
            'paymentMethods'    => $paymentMethods,
            'socialLogins'      => $socialLogins,
            'meta_pixel_id'     => env('META_PIXEL_ID'),

            # floating chat buttons
            'floatingChat' => [
                'call_enabled' => filter_var(getSetting('floating_chat_call_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'whatsapp_enabled' => filter_var(getSetting('floating_chat_whatsapp_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'whatsapp_number' => getSetting('floating_chat_whatsapp_number', ''),
                'secondary_whatsapp_number' => getSetting('floating_chat_secondary_whatsapp_number', ''),
                'messenger_enabled' => filter_var(getSetting('floating_chat_messenger_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'messenger_url' => getSetting('floating_chat_messenger_url', ''),
                'tiktok_enabled' => filter_var(getSetting('floating_chat_tiktok_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'tiktok_url' => getSetting('floating_chat_tiktok_url', ''),
            ],

            # dynamic homepage sections
            'dynamicSections' => $this->getDynamicSections(),

        ];

        $meta = [
            'meta_title' => getSetting('meta_title'),
            'meta_description' => getSetting('meta_description'),
            'meta_image' => uploadedAsset(getSetting('meta_image')),
            'meta_keywords' => getSetting('meta_keywords'),
            'meta_url' => env('APP_URL'),


        ];
        $meta['meta_title'] = $meta['meta_title'] ? $meta['meta_title'] : config('app.name');

        if (Route::currentRouteName() == 'product') {
            $product = Product::fromPublishedShops()->isPublished()->hideOnEcom()->where('slug', $slug)->first();
            if ($product) {
                $meta['meta_title']         = $product->meta_title ? $product->meta_title : $product->name;
                $meta['meta_description']   = $product->meta_description ? $product->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $product->meta_keywords ? $product->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $product->meta_image ? uploadedAsset($product->meta_image) : uploadedAsset($product->thumbnail_img);
                $meta['meta_url']           = route('product', $slug);
            }
        } elseif (Route::currentRouteName() == 'category') {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $meta['meta_title']         = $category->meta_title ? $category->meta_title : $category->name;
                $meta['meta_description']   = $category->meta_description ? $category->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $category->meta_keywords ? $category->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $category->meta_image ? uploadedAsset($category->meta_image) : uploadedAsset($category->thumbnail_img);
                $meta['meta_url']           = route('product', $slug);
            }
        } elseif (Route::currentRouteName() == 'brand') {
            $brand = Brand::where('slug', $slug)->first();
            if ($brand) {
                $meta['meta_title']         = $brand->meta_title ? $brand->meta_title : $brand->name;
                $meta['meta_description']   = $brand->meta_description ? $brand->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $brand->meta_keywords ? $brand->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $brand->meta_image ? uploadedAsset($brand->meta_image) : uploadedAsset($brand->thumbnail_img);
                $meta['meta_url']           = route('product', $slug);
            }
        } elseif (Route::currentRouteName() == 'shop') {
            $shop = Shop::where('slug', $slug)->first();
            if ($shop) {
                $meta['meta_title']         = $shop->meta_title ? $shop->meta_title : $shop->name;
                $meta['meta_description']   = $shop->meta_description ? $shop->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $shop->meta_keywords ? $shop->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $shop->meta_image ? uploadedAsset($shop->meta_image) : uploadedAsset($shop->logo);
                $meta['meta_url']           = route('shop', $slug);
            }
        } elseif (Route::currentRouteName() == 'blog') {
            $blog = Blog::where('slug', $slug)->first();
            if ($blog) {
                $meta['meta_title']         = $blog->meta_title ? $blog->meta_title : $blog->name;
                $meta['meta_description']   = $blog->meta_description ? $blog->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $blog->meta_keywords ? $blog->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $blog->meta_image ? uploadedAsset($blog->meta_image) : uploadedAsset($blog->thumbnail_image);
                $meta['meta_url']           = route('blog', $slug);
            }
        } elseif (Route::currentRouteName() == 'page') {
            $page = Page::where('slug', $slug)->first();
            if ($page) {
                $meta['meta_title']         = $page->meta_title ? $page->meta_title : $page->title;
                $meta['meta_description']   = $page->meta_description ? $page->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $page->meta_keywords ? $page->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $page->meta_image ? uploadedAsset($page->meta_image) : $meta['meta_image'];
                $meta['meta_url']           = route('page', $slug);
            }
        }

        return view('frontend.home', compact('settings', 'meta', 'primaryColor', 'secondaryColor'));
    }

    # get enabled header social links
    private function getHeaderSocialLinks()
    {
        $socialLinks = json_decode(getSetting('headerSocialLinks', '[]'), true);

        if (!is_array($socialLinks)) {
            return [];
        }

        return collect($socialLinks)
            ->filter(function ($link) {
                return is_array($link)
                    && ($link['enabled'] ?? '0') !== '0'
                    && !empty($link['platform'])
                    && !empty($link['url']);
            })
            ->map(function ($link) {
                return [
                    'platform' => $link['platform'],
                    'url' => $link['url'],
                ];
            })
            ->values()
            ->all();
    }

    # get dynamic homepage sections
    private function getDynamicSections()
    {
        $dynamicSectionsData = null;
        if (getSetting('homepage_dynamic_sections') != null) {
            $dynamicSectionsData = json_decode(getSetting('homepage_dynamic_sections'), true);
        }

        // Filter only enabled sections and sort by order
        $sections = [];
        if ($dynamicSectionsData && isset($dynamicSectionsData['sections'])) {
            $sections = collect($dynamicSectionsData['sections'])
                ->where('enabled', true)
                ->sortBy('order')
                ->values()
                ->all();
        }

        return $sections;
    }
}
