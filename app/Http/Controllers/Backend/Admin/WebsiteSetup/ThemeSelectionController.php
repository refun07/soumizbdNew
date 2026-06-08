<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThemeSelectionController extends Controller
{
    # constructor
    public function __construct()
    {
        $this->middleware(['permission:homepage']);
    }

    # Helper method to extract phone number from hotline text
    # Example: "Our Hotline +88 01961 177726" → "+8801961177726"
    private function extractPhoneNumber(string $hotlineText): string
    {
        // Match phone pattern: +, digits, spaces at the end of string
        if (preg_match('/[\d\s\+]+$/', $hotlineText, $matches)) {
            $phone = preg_replace('/\s+/', '', $matches[0]); // Remove spaces
            return $phone;
        }
        // Fallback: remove all non-digit characters
        return preg_replace('/\D/', '', $hotlineText);
    }

    # Helper method to build config with both hotline_number (display) and hotline (phone for tel)
    private function buildTopbarConfig(string $headerId, string $defaultHotlineDisplay): array
    {
        $hotlineDisplay = getSetting("header_{$headerId}_topbar_hotline", $defaultHotlineDisplay);
        $phoneNumber = $this->extractPhoneNumber($hotlineDisplay);

        return [
            'enabled' => filter_var(getSetting("header_{$headerId}_topbar_enabled", $headerId !== 'header-three'), FILTER_VALIDATE_BOOLEAN),
            'hotline_number' => $hotlineDisplay,
            'hotline' => $phoneNumber,
            'promo_text_icon' => getSetting("header_{$headerId}_topbar_promo_text_icon", ''),
            'promo_text' => getSetting("header_{$headerId}_topbar_promo_text", ''),
            'bg_color' => getSetting("header_{$headerId}_topbar_bg_color", '#1a1a2e'),
            'text_color' => getSetting("header_{$headerId}_topbar_text_color", '#ffffff')
        ];
    }

    private function buildNavbarMenuStyle(string $headerId): array
    {
        return [
            'font_size' => (int) getSetting("header_{$headerId}_nav_menu_font_size", 14),
            'font_weight' => getSetting("header_{$headerId}_nav_menu_font_weight", '500'),
            'text_color' => getSetting("header_{$headerId}_nav_menu_text_color", '#FFFFFF'),
            'active_border_color' => getSetting("header_{$headerId}_nav_menu_active_border_color", '#FFFFFF'),
            'active_border_width' => (int) getSetting("header_{$headerId}_nav_menu_active_border_width", 4),
        ];
    }

    private function buildNavbarActionButton(string $headerId): array
    {
        return [
            'enabled' => filter_var(getSetting("header_{$headerId}_nav_action_button_enabled", false), FILTER_VALIDATE_BOOLEAN),
            'name' => getSetting("header_{$headerId}_nav_action_button_name", ''),
            'link' => getSetting("header_{$headerId}_nav_action_button_link", ''),
        ];
    }

    # get theme selection data
    public function index()
    {
        // Get current theme settings from system settings
        $currentTheme = getSetting('defaultTheme', 'theme-one');
        $currentHeader = getSetting('defaultHeader', 'header-one');
        $currentProductCard = getSetting('defaultProductCard', 'product-card-one');
        $currentCategoryCard = getSetting('defaultCategoryCard', 'category-card-one');
        $currentFooter = getSetting('defaultFooter', 'footer-one');

        // Define available themes with actual preview images
        $themes = [
            [
                'id' => 'theme-one',
                'name' => 'Carrot - Grocery Theme',
                'description' => 'Clean and minimalist design with focus on content',
                'image' => asset('images/backend/theme/theme_1.jpg')
            ],
            [
                'id' => 'theme-two',
                'name' => 'Lumen - Home Decor',
                'description' => 'Traditional business layout with sophisticated styling',
                'image' => asset('images/backend/theme/theme_2.jpg')
            ],
            [
                'id' => 'theme-three',
                'name' => 'Stripe - Lifestyle Theme',
                'description' => 'Vibrant colors and dynamic layouts for creative businesses',
                'image' => asset('images/backend/theme/theme_3.jpg')
            ]
        ];

        // Define available headers with actual preview images
        $headers = [
            [
                'id' => 'header-one',
                'name' => 'Header 1',
                'description' => 'Clean and simple header design',
                'image' => asset('images/backend/header_1.jpg')
            ],
            [
                'id' => 'header-two',
                'name' => 'Header 2',
                'description' => 'Traditional header with navigation',
                'image' => asset('images/backend/header_2.jpg')
            ],
            [
                'id' => 'header-three',
                'name' => 'Header 3',
                'description' => 'Contemporary header with advanced features',
                'image' => asset('images/backend/header_3.jpg')
            ]
        ];

        // Define available product cards with actual preview images
        $productCards = [
            [
                'id' => 'product-card-one',
                'name' => 'Product Card 1',
                'description' => 'Grid layout product card design',
                'image' => asset('images/backend/product_card_1.png')
            ],
            [
                'id' => 'product-card-two',
                'name' => 'Product Card 2',
                'description' => 'List view product card design',
                'image' => asset('images/backend/product_card_2.png')
            ],
            [
                'id' => 'product-card-three',
                'name' => 'Product Card 3',
                'description' => 'Minimalist product card design',
                'image' => asset('images/backend/product_card_3.png')
            ]
        ];

        $footers = [
            [
                'id' => 'footer-one',
                'name' => 'Default Footer',
                'description' => 'Classic multi-column ecommerce footer',
                'image' => asset('images/backend/footer.png')
            ],
            [
                'id' => 'footer-two',
                'name' => 'Policy + Contact Footer',
                'description' => 'Policy feature row with two-column brand and contact footer',
                'image' => asset('images/backend/footer_two.svg')
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'current_selection' => [
                    'theme' => $currentTheme,
                    'header' => $currentHeader,
                    'product_card' => $currentProductCard,
                    'category_card' => $currentCategoryCard,
                    'footer' => $currentFooter
                ],
                'available_themes' => $themes,
                'available_headers' => $headers,
                'available_product_cards' => $productCards,
                'available_footers' => $footers
            ]
        ]);
    }

    # update theme selection
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:theme-one,theme-two,theme-three',
            'header' => 'nullable|string|in:header-one,header-two,header-three',
            'product_card' => 'nullable|string|in:product-card-one,product-card-two,product-card-three',
            'footer' => 'nullable|string|in:footer-one,footer-two',
            'category_card' => 'nullable|string'
        ]);

        try {
            // Update theme settings
            $this->updateSystemSetting('defaultTheme', $request->theme);

            if ($request->has('header')) {
                $this->updateSystemSetting('defaultHeader', $request->header);
            }

            if ($request->has('product_card')) {
                $this->updateSystemSetting('defaultProductCard', $request->product_card);
            }

            if ($request->has('category_card')) {
                $this->updateSystemSetting('defaultCategoryCard', $request->category_card);
            }

            if ($request->has('footer')) {
                $this->updateSystemSetting('defaultFooter', $request->footer);
            }

            // Clear cache to ensure new settings are loaded
            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'Theme settings updated successfully',
                'data' => [
                    'theme' => $request->theme,
                    'header' => $request->header,
                    'product_card' => $request->product_card,
                    'category_card' => $request->category_card,
                    'footer' => $request->footer
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update theme settings: ' . $e->getMessage()
            ], 500);
        }
    }

    # helper method to update system setting
    private function updateSystemSetting($key, $value)
    {
        SystemSetting::updateOrCreate(
            ['type' => $key],
            ['value' => $value]
        );
    }

    # helper method for footer-two feature row defaults
    private function defaultFooterInfoItems(): array
    {
        return [
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
    }

    # helper method to sanitize embed code
    private function sanitizeEmbedCode($embedCode)
    {
        // Remove script tags - use # as delimiter to avoid issues with forward slashes
        $embedCode = preg_replace('#<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>#i', '', $embedCode);
        // Remove javascript: protocols
        $embedCode = preg_replace('#javascript:#i', '', $embedCode);

        // Check if there's an iframe tag
        if (!preg_match('/<iframe/i', $embedCode)) {
            \Log::warning('No iframe found in Google Maps embed code', [
                'user_id' => auth()->id(),
                'embed_code' => substr($embedCode, 0, 200)
            ]);
            return '';
        }

        // Extract src attribute - try multiple patterns
        $src = null;

        // Pattern 1: src="url"
        if (preg_match('/src\s*=\s*["\']([^"\']*)["\']/i', $embedCode, $matches)) {
            $src = $matches[1];
        }
        // Pattern 2: src=url (no quotes) - though this is less common
        elseif (preg_match('/src\s*=\s*([^\s>]+)/i', $embedCode, $matches)) {
            $src = trim($matches[1], '"\'');
        }

        if (!$src) {
            \Log::warning('Could not extract src from iframe', [
                'user_id' => auth()->id(),
                'embed_code' => substr($embedCode, 0, 200)
            ]);
            return '';
        }

        // Verify it's a Google Maps URL - be more lenient
        $isGoogleMaps = false;

        // Check for various Google Maps patterns
        if (strpos($src, 'google.com/maps/embed') !== false) {
            $isGoogleMaps = true;
        } elseif (strpos($src, 'googleapis.com/maps') !== false) {
            $isGoogleMaps = true;
        } elseif (strpos($src, 'google.com/maps') !== false) {
            $isGoogleMaps = true;
        }

        if (!$isGoogleMaps) {
            \Log::warning('Invalid Google Maps embed code attempted', [
                'user_id' => auth()->id(),
                'src' => $src
            ]);
            throw new \Exception('Only Google Maps embeds are allowed. Please use the embed code from Google Maps "Share > Embed a map".');
        }

        // Rebuild the iframe with only safe attributes
        $safeIframe = '<iframe src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" ';
        $safeIframe .= 'width="100%" height="250" frameborder="0" ';
        $safeIframe .= 'style="border:0" allowfullscreen loading="lazy"></iframe>';

        \Log::info('Google Maps embed code sanitized successfully', [
            'user_id' => auth()->id(),
            'original_src_length' => strlen($src)
        ]);

        return $safeIframe;
    }

    # get header configuration for specific header
    public function getHeaderConfig($headerId)
    {
        if (!in_array($headerId, ['header-one', 'header-two', 'header-three'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid header ID'
            ], 400);
        }

        try {
            // Get global default values from window.config equivalents
            $globalHotline = getSetting('topbarHotline', 'Our Hotline +88 0176 229 201');

            $config = [
                'topbar' => $this->buildTopbarConfig($headerId, $headerId === 'header-three' ? '' : $globalHotline),
                'navbar' => [
                    'custom_links_enabled' => filter_var(getSetting("header_{$headerId}_nav_custom_links_enabled", false), FILTER_VALIDATE_BOOLEAN),
                    'custom_links' => json_decode(getSetting("header_{$headerId}_nav_custom_links", '[]'), true) ?? [],
                    'action_button' => $this->buildNavbarActionButton($headerId),
                    'menu_style' => $this->buildNavbarMenuStyle($headerId),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            \Log::error('Header configuration fetch failed', [
                'headerId' => $headerId,
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get header configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    # update header configuration for specific header
    public function updateHeaderConfig(Request $request, $headerId)
    {
        if (!in_array($headerId, ['header-one', 'header-two', 'header-three'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid header ID'
            ], 400);
        }

        try {
            // Convert empty string boolean fields to proper boolean values
            $requestData = $request->all();

            // Convert topbar.enabled
            if (isset($requestData['topbar']['enabled'])) {
                $topbarEnabled = $requestData['topbar']['enabled'];
                if ($topbarEnabled === '' || $topbarEnabled === null) {
                    $requestData['topbar']['enabled'] = false;
                } elseif (is_string($topbarEnabled)) {
                    $requestData['topbar']['enabled'] = in_array(strtolower($topbarEnabled), ['true', '1', 'on']);
                }
            }

            // Convert navbar.custom_links_enabled
            if (isset($requestData['navbar']['custom_links_enabled'])) {
                $customLinksEnabled = $requestData['navbar']['custom_links_enabled'];
                if ($customLinksEnabled === '' || $customLinksEnabled === null) {
                    $requestData['navbar']['custom_links_enabled'] = false;
                } elseif (is_string($customLinksEnabled)) {
                    $requestData['navbar']['custom_links_enabled'] = in_array(strtolower($customLinksEnabled), ['true', '1', 'on']);
                }
            }

            if (isset($requestData['navbar']['action_button']['enabled'])) {
                $actionButtonEnabled = $requestData['navbar']['action_button']['enabled'];
                if ($actionButtonEnabled === '' || $actionButtonEnabled === null) {
                    $requestData['navbar']['action_button']['enabled'] = false;
                } elseif (is_string($actionButtonEnabled)) {
                    $requestData['navbar']['action_button']['enabled'] = in_array(strtolower($actionButtonEnabled), ['true', '1', 'on']);
                }
            }

            // Replace the request data with processed data
            $request->replace($requestData);

            $rules = [
                'topbar.enabled' => 'required|boolean',
                'topbar.hotline_number' => 'nullable|string|max:100',
                'topbar.promo_text_icon' => 'nullable|string',
                'topbar.promo_text' => 'nullable|string|max:500',
                'topbar.bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'topbar.text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'navbar.custom_links_enabled' => 'required|boolean',
                'navbar.custom_links' => 'nullable|array',
                'navbar.custom_links.*.name' => 'required|string|max:50',
                'navbar.custom_links.*.url' => 'nullable|string|max:255',
                'navbar.custom_links.*.icon_path' => 'nullable|string',
                'navbar.custom_links.*.icon_svg' => 'nullable|string',
                'navbar.action_button' => 'nullable|array',
                'navbar.action_button.enabled' => 'nullable|boolean',
                'navbar.action_button.name' => 'nullable|string|max:50',
                'navbar.action_button.link' => 'nullable|string|max:255',
                'navbar.menu_style' => 'nullable|array',
                'navbar.menu_style.font_size' => 'nullable|integer|min:10|max:32',
                'navbar.menu_style.font_weight' => 'nullable|string|in:300,400,500,600,700,800,900',
                'navbar.menu_style.text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'navbar.menu_style.active_border_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'navbar.menu_style.active_border_width' => 'nullable|integer|min:1|max:40',
            ];

            $validator = validator($requestData, $rules);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Header configuration validation failed', [
                'headerId' => $headerId,
                'user_id' => auth()->id(),
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            throw $e;
        }

        try {
            // Update top bar settings
            $this->updateSystemSetting("header_{$headerId}_topbar_enabled", $request->input('topbar.enabled'));
            $this->updateSystemSetting("header_{$headerId}_topbar_hotline", $request->input('topbar.hotline_number', ''));
            $this->updateSystemSetting("header_{$headerId}_topbar_promo_text_icon", $request->input('topbar.promo_text_icon', ''));
            $this->updateSystemSetting("header_{$headerId}_topbar_promo_text", $request->input('topbar.promo_text', ''));
            $this->updateSystemSetting("header_{$headerId}_topbar_bg_color", $request->input('topbar.bg_color', '#4267b2'));
            $this->updateSystemSetting("header_{$headerId}_topbar_text_color", $request->input('topbar.text_color', '#ffffff'));

            // Update navbar settings
            $this->updateSystemSetting("header_{$headerId}_nav_custom_links_enabled", $request->input('navbar.custom_links_enabled'));
            $this->updateSystemSetting("header_{$headerId}_nav_custom_links", json_encode($request->input('navbar.custom_links', [])));
            $this->updateSystemSetting("header_{$headerId}_nav_action_button_enabled", $request->boolean('navbar.action_button.enabled', false));
            $this->updateSystemSetting("header_{$headerId}_nav_action_button_name", $request->input('navbar.action_button.name', ''));
            $this->updateSystemSetting("header_{$headerId}_nav_action_button_link", $request->input('navbar.action_button.link', ''));
            $this->updateSystemSetting("header_{$headerId}_nav_menu_font_size", $request->input('navbar.menu_style.font_size', 14));
            $this->updateSystemSetting("header_{$headerId}_nav_menu_font_weight", $request->input('navbar.menu_style.font_weight', '500'));
            $this->updateSystemSetting("header_{$headerId}_nav_menu_text_color", $request->input('navbar.menu_style.text_color', '#FFFFFF'));
            $this->updateSystemSetting("header_{$headerId}_nav_menu_active_border_color", $request->input('navbar.menu_style.active_border_color', '#FFFFFF'));
            $this->updateSystemSetting("header_{$headerId}_nav_menu_active_border_width", $request->input('navbar.menu_style.active_border_width', 4));

            // Clear cache
            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'Header configuration updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Header configuration save failed', [
                'headerId' => $headerId,
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update header configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    # upload header icon
    public function uploadHeaderIcon(Request $request)
    {
        $request->validate([
            'icon' => 'required|file|mimes:png,jpg,jpeg,svg|max:2048'
        ]);

        try {
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                $fileName = 'header_icon_' . time() . '.' . $file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/header-icons');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);
                $filePath = 'uploads/header-icons/' . $fileName;

                return response()->json([
                    'success' => true,
                    'message' => 'Icon uploaded successfully',
                    'data' => [
                        'file_path' => $filePath,
                        'file_url' => asset($filePath)
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload icon: ' . $e->getMessage()
            ], 500);
        }
    }

    # get footer configuration
    public function getFooterConfig()
    {
        try {
            $currentFooter = getSetting('defaultFooter', 'footer-one');
            $footerInfoItems = json_decode(getSetting('homeFooterInfoItems', '[]'), true);
            if (!is_array($footerInfoItems) || empty($footerInfoItems)) {
                $footerInfoItems = $this->defaultFooterInfoItems();
            }

            $config = [
                // Footer Layout
                'footer_variant' => $currentFooter,

                // Company Info
                'logo' => getSetting('homeFooterLogo', ''),
                'logo_size' => getSetting('homeFooterLogoSize', 'medium'),
                'description' => getSetting('homeFooterDescription', ''),
                'company_info_alignment' => getSetting('homeFooterCompanyInfoAlignment', 'left'),
                'customer_support' => getSetting('homeFooterCustomerSupport', ''),
                'customer_support_secondary' => getSetting('homeFooterCustomerSupportSecondary', ''),
                'address' => getSetting('homeFooterAddress', ''),
                'email' => getSetting('homeFooterEmail', ''),

                // Footer Two Info Row
                'show_info_bar' => filter_var(getSetting('showHomeFooterInfoBar', true), FILTER_VALIDATE_BOOLEAN),
                'info_items' => $footerInfoItems,
                'info_bg_color' => getSetting('homeFooterInfoBgColor', '#ffffff'),
                'info_icon_color' => getSetting('homeFooterInfoIconColor', '#8f5847'),
                'info_title_color' => getSetting('homeFooterInfoTitleColor', '#111827'),
                'info_text_color' => getSetting('homeFooterInfoTextColor', '#0f172a'),

                // Social Links
                'show_facebook' => filter_var(getSetting('showHomeFacebookLink', true), FILTER_VALIDATE_BOOLEAN),
                'facebook_link' => getSetting('homeFooterFacebookLink', ''),
                'show_twitter' => filter_var(getSetting('showHomeTwitterLink', true), FILTER_VALIDATE_BOOLEAN),
                'twitter_link' => getSetting('homeFooterTwitterLink', ''),
                'show_instagram' => filter_var(getSetting('showHomeInstagramLink', true), FILTER_VALIDATE_BOOLEAN),
                'instagram_link' => getSetting('homeFooterInstagramLink', ''),
                'show_youtube' => filter_var(getSetting('showHomeYoutubeLink', true), FILTER_VALIDATE_BOOLEAN),
                'youtube_link' => getSetting('homeFooterYoutubeLink', ''),
                'show_linkedin' => filter_var(getSetting('showHomeLinkedInLink', true), FILTER_VALIDATE_BOOLEAN),
                'linkedin_link' => getSetting('homeFooterLinkedInLink', ''),
                'show_tiktok' => filter_var(getSetting('showHomeTikTokLink', true), FILTER_VALIDATE_BOOLEAN),
                'tiktok_link' => getSetting('homeFooterTikTokLink', ''),
                'show_pinterest' => filter_var(getSetting('showHomePinterestLink', true), FILTER_VALIDATE_BOOLEAN),
                'pinterest_link' => getSetting('homeFooterPinterestLink', ''),

                // Quick Links
                'quick_links_titles' => json_decode(getSetting('homeFooterQuickLinkTitles', '[]'), true) ?? [],
                'quick_links' => json_decode(getSetting('homeFooterQuickLinks', '[]'), true) ?? [],

                // Important Links
                'important_links' => json_decode(getSetting('homeFooterImportantLinks'), true) ?? [
                    ['title' => 'Home', 'link' => '/', 'is_enabled' => true],
                    ['title' => 'Shop', 'link' => '/shops', 'is_enabled' => true],
                    ['title' => 'All Products', 'link' => '/products', 'is_enabled' => true],
                    ['title' => 'About Us', 'link' => '/pages/about-us', 'is_enabled' => true],
                    ['title' => 'All Brands', 'link' => '/brands', 'is_enabled' => true]
                ],

                // App Links
                'show_playstore' => filter_var(getSetting('showHomeFooterPlayStoreLink', true), FILTER_VALIDATE_BOOLEAN),
                'playstore_link' => getSetting('homeFooterPlayStoreLink', ''),
                'show_appstore' => filter_var(getSetting('showHomeFooterAppStoreLink', true), FILTER_VALIDATE_BOOLEAN),
                'appstore_link' => getSetting('homeFooterAppStoreLink', ''),

                // Bottom
                'copyright_text' => getSetting('homeFooterCopyrightText', ''),
                'secured_payments' => getSetting('homeFooterSecuredPayments', ''),

                // Colors
                'footer_bg_color' => getSetting('footer_bg_color', $currentFooter === 'footer-two' ? '#9b7655' : '#1a1a2e'),
                'footer_text_color' => getSetting('footer_text_color', '#ffffff'),
                'copyright_bg_color' => getSetting('copyright_bg_color', $currentFooter === 'footer-two' ? '#8b5747' : '#22c55e'),
                'copyright_text_color' => getSetting('copyright_text_color', '#ffffff'),

                // Google Maps
                'show_google_maps' => filter_var(getSetting('showHomeFooterGoogleMaps', true), FILTER_VALIDATE_BOOLEAN),
                'google_maps_enabled' => filter_var(getSetting('homeFooterGoogleMapsEnabled', true), FILTER_VALIDATE_BOOLEAN),
                'google_maps_type' => getSetting('homeFooterGoogleMapsType', 'embed'),
                'google_maps_value' => getSetting('homeFooterGoogleMapsValue', '')
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            \Log::error('Footer configuration fetch failed', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get footer configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    # update footer configuration
    public function updateFooterConfig(Request $request)
    {
        try {
            $rules = [
                'logo' => 'nullable|string',
                'logo_size' => 'nullable|string|in:small,medium,large',
                'description' => 'nullable|string|max:1000',
                'company_info_alignment' => 'nullable|string|in:left,center,right,justify',
                'customer_support' => 'nullable|string|max:50',
                'customer_support_secondary' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:500',
                'email' => 'nullable|string|max:500',
                'show_info_bar' => 'nullable|boolean',
                'info_items' => 'nullable|array',
                'info_items.*.title' => 'nullable|string|max:120',
                'info_items.*.description' => 'nullable|string|max:500',
                'info_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'info_icon_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'info_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'info_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'show_facebook' => 'nullable|boolean',
                'facebook_link' => 'nullable|string|max:255',
                'show_twitter' => 'nullable|boolean',
                'twitter_link' => 'nullable|string|max:255',
                'show_instagram' => 'nullable|boolean',
                'instagram_link' => 'nullable|string|max:255',
                'show_youtube' => 'nullable|boolean',
                'youtube_link' => 'nullable|string|max:255',
                'show_linkedin' => 'nullable|boolean',
                'linkedin_link' => 'nullable|string|max:255',
                'show_tiktok' => 'nullable|boolean',
                'tiktok_link' => 'nullable|string|max:255',
                'show_pinterest' => 'nullable|boolean',
                'pinterest_link' => 'nullable|string|max:255',
                'quick_links_titles' => 'nullable|array',
                'quick_links' => 'nullable|array',
                'important_links' => 'nullable|array',
                'show_playstore' => 'nullable|boolean',
                'playstore_link' => 'nullable|string|max:255',
                'show_appstore' => 'nullable|boolean',
                'appstore_link' => 'nullable|string|max:255',
                'copyright_text' => 'nullable|string|max:500',
                'secured_payments' => 'nullable|string',
                'footer_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'footer_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'copyright_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'copyright_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'show_google_maps' => 'nullable|boolean',
                'google_maps_enabled' => 'nullable|boolean',
                'google_maps_type' => 'nullable|string|in:embed,link',
                'google_maps_value' => 'nullable|string|max:2000'
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            // Update company info
            $this->updateSystemSetting('homeFooterLogo', $request->input('logo', ''));
            $this->updateSystemSetting('homeFooterLogoSize', $request->input('logo_size', 'medium'));
            $this->updateSystemSetting('homeFooterDescription', $request->input('description', ''));
            $this->updateSystemSetting('homeFooterCompanyInfoAlignment', $request->input('company_info_alignment', 'left'));
            $this->updateSystemSetting('homeFooterCustomerSupport', $request->input('customer_support', ''));
            $this->updateSystemSetting('homeFooterCustomerSupportSecondary', $request->input('customer_support_secondary', ''));
            $this->updateSystemSetting('homeFooterAddress', $request->input('address', ''));
            $this->updateSystemSetting('homeFooterEmail', $request->input('email', ''));

            // Update footer-two info row
            $this->updateSystemSetting('showHomeFooterInfoBar', $request->boolean('show_info_bar', true));
            $this->updateSystemSetting('homeFooterInfoItems', json_encode($request->input('info_items', $this->defaultFooterInfoItems())));
            $this->updateSystemSetting('homeFooterInfoBgColor', $request->input('info_bg_color', '#ffffff'));
            $this->updateSystemSetting('homeFooterInfoIconColor', $request->input('info_icon_color', '#8f5847'));
            $this->updateSystemSetting('homeFooterInfoTitleColor', $request->input('info_title_color', '#111827'));
            $this->updateSystemSetting('homeFooterInfoTextColor', $request->input('info_text_color', '#0f172a'));

            // Update social links
            $this->updateSystemSetting('showHomeFacebookLink', $request->boolean('show_facebook', true));
            $this->updateSystemSetting('homeFooterFacebookLink', $request->input('facebook_link', ''));
            $this->updateSystemSetting('showHomeTwitterLink', $request->boolean('show_twitter', true));
            $this->updateSystemSetting('homeFooterTwitterLink', $request->input('twitter_link', ''));
            $this->updateSystemSetting('showHomeInstagramLink', $request->boolean('show_instagram', true));
            $this->updateSystemSetting('homeFooterInstagramLink', $request->input('instagram_link', ''));
            $this->updateSystemSetting('showHomeYoutubeLink', $request->boolean('show_youtube', true));
            $this->updateSystemSetting('homeFooterYoutubeLink', $request->input('youtube_link', ''));
            $this->updateSystemSetting('showHomeLinkedInLink', $request->boolean('show_linkedin', true));
            $this->updateSystemSetting('homeFooterLinkedInLink', $request->input('linkedin_link', ''));
            $this->updateSystemSetting('showHomeTikTokLink', $request->boolean('show_tiktok', true));
            $this->updateSystemSetting('homeFooterTikTokLink', $request->input('tiktok_link', ''));
            $this->updateSystemSetting('showHomePinterestLink', $request->boolean('show_pinterest', true));
            $this->updateSystemSetting('homeFooterPinterestLink', $request->input('pinterest_link', ''));

            // Update quick links
            $this->updateSystemSetting('homeFooterQuickLinkTitles', json_encode($request->input('quick_links_titles', [])));
            $this->updateSystemSetting('homeFooterQuickLinks', json_encode($request->input('quick_links', [])));

            // Update important links
            $this->updateSystemSetting('homeFooterImportantLinks', json_encode($request->input('important_links', [])));

            // Update app links
            $this->updateSystemSetting('showHomeFooterPlayStoreLink', $request->boolean('show_playstore', true));
            $this->updateSystemSetting('homeFooterPlayStoreLink', $request->input('playstore_link', ''));
            $this->updateSystemSetting('showHomeFooterAppStoreLink', $request->boolean('show_appstore', true));
            $this->updateSystemSetting('homeFooterAppStoreLink', $request->input('appstore_link', ''));

            // Update bottom section
            $this->updateSystemSetting('homeFooterCopyrightText', $request->input('copyright_text', ''));
            $this->updateSystemSetting('homeFooterSecuredPayments', $request->input('secured_payments', ''));

            // Update colors
            $this->updateSystemSetting('footer_bg_color', $request->input('footer_bg_color', '#1a1a2e'));
            $this->updateSystemSetting('footer_text_color', $request->input('footer_text_color', '#ffffff'));
            $this->updateSystemSetting('copyright_bg_color', $request->input('copyright_bg_color', '#22c55e'));
            $this->updateSystemSetting('copyright_text_color', $request->input('copyright_text_color', '#ffffff'));

            // Update Google Maps
            $showGoogleMaps = $request->boolean('show_google_maps', true);
            $this->updateSystemSetting('showHomeFooterGoogleMaps', $showGoogleMaps);

            $googleMapsEnabled = $request->boolean('google_maps_enabled', true);
            $this->updateSystemSetting('homeFooterGoogleMapsEnabled', $googleMapsEnabled);

            $googleMapsType = $request->input('google_maps_type', 'embed');
            $this->updateSystemSetting('homeFooterGoogleMapsType', $googleMapsType);

            $googleMapsValue = $request->input('google_maps_value', '');

            // Sanitize Google Maps value based on type
            if ($googleMapsType === 'embed' && !empty($googleMapsValue)) {
                try {
                    $sanitizedValue = $this->sanitizeEmbedCode($googleMapsValue);
                    if (empty($sanitizedValue)) {
                        \Log::warning('Google Maps embed code sanitization resulted in empty value', [
                            'user_id' => auth()->id(),
                            'original_value' => $googleMapsValue
                        ]);
                    }
                    $googleMapsValue = $sanitizedValue;
                } catch (\Exception $e) {
                    \Log::error('Google Maps embed code sanitization failed', [
                        'user_id' => auth()->id(),
                        'error' => $e->getMessage(),
                        'value' => $googleMapsValue
                    ]);
                    throw $e;
                }
            } elseif ($googleMapsType === 'link' && !empty($googleMapsValue)) {
                // Validate URL for link mode
                if (!filter_var($googleMapsValue, FILTER_VALIDATE_URL)) {
                    throw new \Exception('Invalid Google Maps URL format');
                }
            }

            $this->updateSystemSetting('homeFooterGoogleMapsValue', $googleMapsValue);

            // Clear cache
            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'Footer configuration updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Footer configuration validation failed', [
                'user_id' => auth()->id(),
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            throw $e;
        } catch (\Exception $e) {
            \Log::error('Footer configuration save failed', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update footer configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    # upload footer image
    public function uploadFooterImage(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:png,jpg,jpeg,svg|max:2048'
        ]);

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = 'footer_image_' . time() . '.' . $file->getClientOriginalExtension();

                // Create directory if it doesn't exist
                $uploadPath = public_path('uploads/footer-images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);
                $filePath = 'uploads/footer-images/' . $fileName;

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'data' => [
                        'file_path' => $filePath,
                        'file_url' => asset($filePath)
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    # public API to get header configuration for frontend
    public function getPublicHeaderConfig($headerId = null)
    {
        // If no headerId is provided, get the default header from settings
        if (!$headerId) {
            $headerId = getSetting('defaultHeader', 'header-one');
        }

        if (!in_array($headerId, ['header-one', 'header-two', 'header-three'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid header ID'
            ], 400);
        }

        try {
            // Get global default values
            $globalHotline = getSetting('topbar_hotline', 'Our Hotline +88 0176 229 201');

            $config = [
                'topbar' => $this->buildTopbarConfig($headerId, $headerId === 'header-three' ? '' : $globalHotline),
                'navbar' => [
                    'custom_links_enabled' => filter_var(getSetting("header_{$headerId}_nav_custom_links_enabled", false), FILTER_VALIDATE_BOOLEAN),
                    'custom_links' => json_decode(getSetting("header_{$headerId}_nav_custom_links", '[]'), true) ?? [],
                    'action_button' => $this->buildNavbarActionButton($headerId),
                    'menu_style' => $this->buildNavbarMenuStyle($headerId),
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);

        } catch (\Exception $e) {
            \Log::error('Public header configuration fetch failed', [
                'headerId' => $headerId,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get header configuration'
            ], 500);
        }
    }
}
