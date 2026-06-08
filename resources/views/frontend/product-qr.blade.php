@php
    use Illuminate\Support\Str;
    $qrData = $product->qr_data ?? [];
    $qrLinks = $qrData['links'] ?? [];
    $qrColor = $qrData['color'] ?? '#dc2626';
    $qrColor = '#e92528';
    $qrIconClassMap = [
        'fa-globe' => 'fa-solid fa-globe',
        'fa-store' => 'fa-solid fa-store',
        'fa-cart-shopping' => 'fa-solid fa-cart-shopping',
        'fa-bag-shopping' => 'fa-solid fa-bag-shopping',
        'fa-circle-info' => 'fa-solid fa-circle-info',
        'fa-address-card' => 'fa-solid fa-address-card',
        'fa-link' => 'fa-solid fa-link',
        'fa-play' => 'fa-solid fa-play',
        'fa-youtube' => 'fa-brands fa-youtube',
        'fa-facebook-f' => 'fa-brands fa-facebook-f',
        'fa-instagram' => 'fa-brands fa-instagram',
        'fa-x-twitter' => 'fa-brands fa-x-twitter',
        'fa-pinterest' => 'fa-brands fa-pinterest',
        'fa-pinterest-p' => 'fa-brands fa-pinterest-p',
        'fa-whatsapp' => 'fa-brands fa-whatsapp',
        'fa-phone' => 'fa-solid fa-phone',
        'fa-envelope' => 'fa-solid fa-envelope',
        'fa-location-dot' => 'fa-solid fa-location-dot',
        'fa-map' => 'fa-solid fa-map',
        'fa-map-marker-alt' => 'fa-solid fa-map-marker-alt',
        'fa-user' => 'fa-solid fa-user',
        'fa-users' => 'fa-solid fa-users',
        'fa-id-card' => 'fa-solid fa-id-card',
        'fa-building' => 'fa-solid fa-building',
        'fa-briefcase' => 'fa-solid fa-briefcase',
        'fa-calendar' => 'fa-solid fa-calendar',
        'fa-clock' => 'fa-solid fa-clock',
        'fa-image' => 'fa-solid fa-image',
        'fa-camera' => 'fa-solid fa-camera',
        'fa-music' => 'fa-solid fa-music',
        'fa-file' => 'fa-solid fa-file',
        'fa-file-pdf' => 'fa-solid fa-file-pdf',
        'fa-download' => 'fa-solid fa-download',
        'fa-upload' => 'fa-solid fa-upload',
        'fa-share' => 'fa-solid fa-share',
        'fa-share-nodes' => 'fa-solid fa-share-nodes',
        'fa-heart' => 'fa-solid fa-heart',
        'fa-star' => 'fa-solid fa-star',
        'fa-thumbs-up' => 'fa-solid fa-thumbs-up',
        'fa-comment' => 'fa-solid fa-comment',
        'fa-comments' => 'fa-solid fa-comments',
        'fa-headset' => 'fa-solid fa-headset',
        'fa-lock' => 'fa-solid fa-lock',
        'fa-unlock' => 'fa-solid fa-unlock',
        'fa-key' => 'fa-solid fa-key',
        'fa-qrcode' => 'fa-solid fa-qrcode',
        'fa-barcode' => 'fa-solid fa-barcode',
        'fa-wifi' => 'fa-solid fa-wifi',
        'fa-bluetooth' => 'fa-brands fa-bluetooth',
        'fa-telegram' => 'fa-brands fa-telegram',
        'fa-linkedin-in' => 'fa-brands fa-linkedin-in',
        'fa-github' => 'fa-brands fa-github',
        'fa-discord' => 'fa-brands fa-discord',
        'fa-tiktok' => 'fa-brands fa-tiktok',
    ];
    $qrIconStyleMap = [
        'fa-globe' => 'globe',
        'fa-store' => 'shopping',
        'fa-cart-shopping' => 'shopping',
        'fa-bag-shopping' => 'shopping',
        'fa-circle-info' => 'default',
        'fa-address-card' => 'profile',
        'fa-link' => 'default',
        'fa-play' => 'video',
        'fa-youtube' => 'youtube',
        'fa-facebook-f' => 'facebook',
        'fa-instagram' => 'instagram',
        'fa-x-twitter' => 'twitter',
        'fa-pinterest' => 'pinterest',
        'fa-pinterest-p' => 'pinterest',
        'fa-whatsapp' => 'whatsapp',
        'fa-phone' => 'phone',
        'fa-envelope' => 'email',
        'fa-location-dot' => 'location',
        'fa-map' => 'location',
        'fa-map-marker-alt' => 'location',
        'fa-user' => 'profile',
        'fa-users' => 'profile',
        'fa-id-card' => 'profile',
        'fa-building' => 'business',
        'fa-briefcase' => 'business',
        'fa-calendar' => 'calendar',
        'fa-clock' => 'calendar',
        'fa-image' => 'media',
        'fa-camera' => 'media',
        'fa-music' => 'media',
        'fa-file' => 'document',
        'fa-file-pdf' => 'document',
        'fa-download' => 'document',
        'fa-upload' => 'document',
        'fa-share' => 'share',
        'fa-share-nodes' => 'share',
        'fa-heart' => 'favorite',
        'fa-star' => 'favorite',
        'fa-thumbs-up' => 'favorite',
        'fa-comment' => 'chat',
        'fa-comments' => 'chat',
        'fa-headset' => 'support',
        'fa-lock' => 'security',
        'fa-unlock' => 'security',
        'fa-key' => 'security',
        'fa-qrcode' => 'tech',
        'fa-barcode' => 'tech',
        'fa-wifi' => 'tech',
        'fa-bluetooth' => 'bluetooth',
        'fa-telegram' => 'telegram',
        'fa-linkedin-in' => 'linkedin',
        'fa-github' => 'github',
        'fa-discord' => 'discord',
        'fa-tiktok' => 'tiktok',
    ];
    $productImage = $product->thumbnail_image ? uploadedAsset($product->thumbnail_image) : null;
    $recipeImage = $product->recipe_image ? uploadedAsset($product->recipe_image) : null;
    $showRecipeDetails = $product->show_recipe_details && ($recipeImage || filled($product->recipe_details));
    $productUrl = route('product', ['slug' => $product->slug]);
    $companyLogo = uploadedAsset(getSetting('websiteHeaderLogo'));
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --qr-primary-color: {{ $qrColor }};
            --qr-primary-rgb: {{ hexToRgb($qrColor) }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            color: #333;
        }

        /* Main container for large screens */
        .page-container {
            max-width: 480px;
            margin: 0 auto;
            background: #f8f9fa;
            min-height: 100vh;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }

        /* Header with brand colors */
        .header {
            background: linear-gradient(135deg, {{ $qrColor }} 0%, {{ $qrColor }}dd 100%);
            padding: 30px 20px 40px;
            text-align: center;
            position: relative;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 4px 20px rgba(var(--qr-primary-rgb), 0.3);
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            background: #e92528 !important;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-logo .placeholder-icon {
            font-size: 50px;
            color: {{ $qrColor }};
        }

        .brand-name {
            color: white;
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* Product Info Section */
        .product-info {
            background: #b37d2f;
            padding: 20px;
            margin: 20px 15px 20px;
            border-radius: 15px;
            color: white;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 15px rgba(var(--qr-primary-rgb), 0.3);
        }

        .product-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .product-description {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.95;
        }

        .product-price {
            font-size: 28px;
            font-weight: 700;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .product-price .original-price {
            font-size: 16px;
            font-weight: 400;
            text-decoration: line-through;
            opacity: 0.7;
            margin-right: 10px;
        }

        .product-price .discount-badge {
            font-size: 12px;
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: 8px;
            font-weight: 500;
        }

        /* Links Section */
        .links-container {
            padding: 0 20px 100px;
            max-width: 600px;
            margin: 0 auto;
        }

        .link-item {
            display: flex;
            align-items: center;
            background: white;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .link-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .link-item-button {
            width: 100%;
            border: none;
            cursor: pointer;
            text-align: left;
            font: inherit;
        }

        .link-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 20px;
        }

        .link-icon.globe {
            background: #6b7280;
            color: white;
        }

        .link-icon.youtube {
            background: #dc2626;
            color: white;
        }

        .link-icon.facebook {
            background: #2563eb;
            color: white;
        }

        .link-icon.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            color: white;
        }

        .link-icon.twitter {
            background: #000;
            color: white;
        }

        .link-icon.whatsapp {
            background: #22c55e;
            color: white;
        }

        .link-icon.shopping {
            background: #f59e0b;
            color: white;
        }

        .link-icon.profile {
            background: #7c3aed;
            color: white;
        }

        .link-icon.video {
            background: #ef4444;
            color: white;
        }

        .link-icon.pinterest {
            background: #e60023;
            color: white;
        }

        .link-icon.phone {
            background: #16a34a;
            color: white;
        }

        .link-icon.email {
            background: #ea4335;
            color: white;
        }

        .link-icon.location {
            background: #f97316;
            color: white;
        }

        .link-icon.business {
            background: #0f766e;
            color: white;
        }

        .link-icon.calendar {
            background: #4f46e5;
            color: white;
        }

        .link-icon.media {
            background: #0284c7;
            color: white;
        }

        .link-icon.document {
            background: #475569;
            color: white;
        }

        .link-icon.share {
            background: #0891b2;
            color: white;
        }

        .link-icon.favorite {
            background: #e11d48;
            color: white;
        }

        .link-icon.chat {
            background: #0d9488;
            color: white;
        }

        .link-icon.support {
            background: #d97706;
            color: white;
        }

        .link-icon.security {
            background: #334155;
            color: white;
        }

        .link-icon.tech {
            background: #2563eb;
            color: white;
        }

        .link-icon.telegram {
            background: #229ed9;
            color: white;
        }

        .link-icon.linkedin {
            background: #0a66c2;
            color: white;
        }

        .link-icon.github {
            background: #111827;
            color: white;
        }

        .link-icon.discord {
            background: #5865f2;
            color: white;
        }

        .link-icon.tiktok {
            background: linear-gradient(135deg, #111827 0%, #25f4ee 50%, #fe2c55 100%);
            color: white;
        }

        .link-icon.bluetooth {
            background: #1d4ed8;
            color: white;
        }

        .link-icon.default {
            background: linear-gradient(135deg, {{ $qrColor }} 0%, {{ $qrColor }}cc 100%);
            color: white;
        }

        .link-icon img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .link-icon.recipe-image {
            background: #fff7ed;
            overflow: hidden;
            border: 1px solid rgba(var(--qr-primary-rgb), 0.12);
        }

        .link-icon.recipe-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .link-content {
            flex: 1;
            min-width: 0;
        }

        .link-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .link-url {
            font-size: 13px;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .link-arrow {
            color: #9ca3af;
            font-size: 14px;
            margin-left: 10px;
        }

        /* Default Product Link */
        .default-link {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 2px dashed #d1d5db;
        }

        /* Share Button */
        .share-button {
            position: fixed;
            bottom: 25px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, {{ $qrColor }} 0%, {{ $qrColor }}cc 100%);
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(var(--qr-primary-rgb), 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, box-shadow 0.2s;
            z-index: 100;
            /* Position relative to the centered container */
            right: calc(50% - 240px + 25px);
            /* 50% - half container width + margin */
        }

        .share-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(var(--qr-primary-rgb), 0.5);
        }

        /* Empty State */
        .empty-links {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-links i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #d1d5db;
        }

        .recipe-modal {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.68);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 200;
        }

        .recipe-modal.is-open {
            display: flex;
        }

        .recipe-modal-card {
            width: min(100%, 420px);
            max-height: 90vh;
            overflow-y: auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.24);
        }

        .recipe-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 18px 0;
        }

        .recipe-modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .recipe-modal-close {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 50%;
            background: #f3f4f6;
            color: #374151;
            cursor: pointer;
            font-size: 16px;
        }

        .recipe-modal-body {
            padding: 18px;
        }

        .recipe-modal-image {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #f9fafb;
            margin-bottom: 16px;
        }

        .recipe-modal-image img {
            width: 100%;
            display: block;
            object-fit: cover;
        }

        .recipe-modal-details {
            font-size: 14px;
            line-height: 1.7;
            color: #374151;
            white-space: pre-line;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .header {
                padding: 25px 15px 35px;
            }

            .brand-logo {
                width: 100px;
                height: 100px;
            }

            .product-info {
                margin: 15px 10px 15px;
                padding: 15px;
            }

            .product-name {
                font-size: 20px;
            }

            .links-container {
                padding: 0 15px 80px;
            }

            .link-item {
                padding: 12px;
            }

            .link-icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
            }

            .share-button {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                right: 20px;
                /* Reset to edge on mobile */
            }
        }

        /* Loading Screen Styles */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .loading-screen.fade-out {
            opacity: 0;
            visibility: hidden;
        }

        .loading-logo {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: logoPulse 1.5s ease-in-out infinite;
        }

        .loading-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .loading-logo .placeholder-icon {
            font-size: 80px;
            color: {{ $qrColor }};
        }

        @keyframes logoPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        /* Ensure page content is hidden during loading */
        body.loading .page-container {
            opacity: 0;
        }

        body:not(.loading) .page-container {
            opacity: 1;
            transition: opacity 0.3s ease-in;
        }
    </style>
</head>

<body class="loading">
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <div class="loading-logo">
            @if ($companyLogo)
                <img src="{{ $companyLogo }}" alt="{{ translate('Loading') }}">
            @else
                <i class="fa-solid fa-spinner placeholder-icon"></i>
            @endif
        </div>
    </div>

    <div class="page-container">
        <!-- Header with Product Image -->
        <div class="header">
            <div class="brand-logo" style="background:#e92528;">
                @if ($productImage)
                    <img src="{{ $productImage }}" alt="{{ $product->name }}">
                @else
                    <i class="fa-solid fa-box placeholder-icon"></i>
                @endif
            </div>

        </div>

        <!-- Product Info -->
        <div class="product-info">
            <h1 class="product-name">{{ $product->name }}</h1>
            @if ($product->short_description || $product->description)
                <p class="product-description">
                    {{ strip_tags($product->short_description ?: Str::limit(strip_tags($product->description), 150)) }}
                </p>
            @endif

            @php
                $minPrice = $product->min_price ?? 0;
                $maxPrice = $product->max_price ?? 0;
                $hasVariations = $product->has_variation == 1;

                $discountedMin = null;
                $discountedMax = null;
                $hasDiscount = false;
                $now = strtotime(date('d-m-Y H:i:s'));

                if ($hasVariations && $product->variations->isNotEmpty()) {
                    $variationOriginalPrices = [];
                    $variationDiscountedPrices = [];

                    foreach ($product->variations as $variation) {
                        $original = $variation->price;
                        $discounted = $variation->price;
                        $discountApplicable = false;

                        $discountType = $variation->discount_type;
                        $discountValue = $variation->discount_value;

                        if ($product->discount_start_date && $product->discount_end_date) {
                            if ($now >= $product->discount_start_date && $now <= $product->discount_end_date) {
                                $discountApplicable = true;
                            }
                        }

                        $campaignProduct = $variation
                            ->campaignProducts()
                            ->whereHas('campaign', function ($q) use ($now) {
                                $q->where('start_date', '<=', $now)->where('end_date', '>=', $now);
                            })
                            ->first();

                        if ($campaignProduct) {
                            $discountApplicable = true;
                            $discountType = $campaignProduct->discount_type;
                            $discountValue = $campaignProduct->discount_value;
                        }

                        if ($discountApplicable && $discountType && $discountValue !== null) {
                            $hasDiscount = true;
                            if ($discountType == 'flat' || $discountType == 'amount') {
                                $discounted = max(0, $discounted - $discountValue);
                            } else {
                                $discounted = $discounted - ($discounted * $discountValue) / 100;
                            }
                        }

                        $variationOriginalPrices[] = $original;
                        $variationDiscountedPrices[] = $discounted;
                    }

                    $minPrice = min($variationOriginalPrices);
                    $maxPrice = max($variationOriginalPrices);

                    if ($hasDiscount) {
                        $discountedMin = min($variationDiscountedPrices);
                        $discountedMax = max($variationDiscountedPrices);
                    }
                }

                $hasRange = $maxPrice > $minPrice;
            @endphp
            <div class="product-price">
                @if ($hasDiscount)
                    @if ($hasRange)
                        <span class="original-price">{{ formatPrice($minPrice) }} -
                            {{ formatPrice($maxPrice) }}</span>
                    @else
                        <span class="original-price">{{ formatPrice($minPrice) }}</span>
                    @endif

                    @if ($discountedMax > $discountedMin)
                        {{ formatPrice($discountedMin) }} - {{ formatPrice($discountedMax) }}
                    @else
                        {{ formatPrice($discountedMin ?? $minPrice) }}
                    @endif

                    @php
                        $originalAvg = ($minPrice + $maxPrice) / 2;
                        $discountedAvg = ($discountedMin ?? ($minPrice + $discountedMax ?? $minPrice)) / 2;
                        $discountPercent =
                            $originalAvg > 0 ? round((($originalAvg - $discountedAvg) / $originalAvg) * 100) : 0;
                    @endphp
                    <span class="discount-badge">-{{ $discountPercent }}%</span>
                @else
                    @if ($hasRange)
                        {{ formatPrice($minPrice) }} - {{ formatPrice($maxPrice) }}
                    @else
                        {{ formatPrice($minPrice) }}
                    @endif
                @endif
            </div>
        </div>

        <!-- Links Section -->
        <div class="links-container">


            <!-- Always show product page link at the end -->
            @if ($showRecipeDetails)
                <button type="button" class="link-item link-item-button" onclick="openRecipeModal()">
                    <div class="link-icon recipe-image">
                        @if ($recipeImage)
                            <img src="{{ $recipeImage }}" alt="{{ translate('Recipe') }}">
                        @else
                            <i class="fa-solid fa-utensils"></i>
                        @endif
                    </div>
                    <div class="link-content">
                        <div class="link-title">{{ translate('Recipe') }}</div>
                        <div class="link-url">{{ translate('Tap to view') }}</div>
                    </div>
                    <div class="link-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </button>
            @endif


            @if (count($qrLinks) > 0)
                @foreach ($qrLinks as $link)
                    @php
                        $url = $link['url'] ?? '#';
                        $title = $link['title'] ?? (parse_url($url, PHP_URL_HOST) ?? 'Link');
                        $icon = $link['icon'] ?? '';

                        // Determine icon class based on URL or provided icon
                        $iconClass = 'default';
                        $iconContent = '<i class="fa-solid fa-link"></i>';
                        $resolvedIconClass = null;

                        // Check if icon is a URL (custom image)
                        $isCustomIcon = filter_var($icon, FILTER_VALIDATE_URL) || strpos($icon, '/') === 0;

                        if (!$isCustomIcon) {
                            $urlLower = strtolower($url);
                            $iconLower = strtolower($icon);

                            if (
                                str_contains($urlLower, 'youtube') ||
                                str_contains($urlLower, 'youtu.be') ||
                                str_contains($iconLower, 'youtube')
                            ) {
                                $iconClass = 'youtube';
                                $iconContent = '<i class="fa-brands fa-youtube"></i>';
                            } elseif (
                                str_contains($urlLower, 'facebook') ||
                                str_contains($urlLower, 'fb.com') ||
                                str_contains($iconLower, 'facebook')
                            ) {
                                $iconClass = 'facebook';
                                $iconContent = '<i class="fa-brands fa-facebook-f"></i>';
                            } elseif (str_contains($urlLower, 'instagram') || str_contains($iconLower, 'instagram')) {
                                $iconClass = 'instagram';
                                $iconContent = '<i class="fa-brands fa-instagram"></i>';
                            } elseif (
                                str_contains($urlLower, 'twitter') ||
                                str_contains($urlLower, 'x.com') ||
                                str_contains($iconLower, 'twitter')
                            ) {
                                $iconClass = 'twitter';
                                $iconContent = '<i class="fa-brands fa-x-twitter"></i>';
                            } elseif (
                                str_contains($urlLower, 'whatsapp') ||
                                str_contains($urlLower, 'wa.me') ||
                                str_contains($iconLower, 'whatsapp')
                            ) {
                                $iconClass = 'whatsapp';
                                $iconContent = '<i class="fa-brands fa-whatsapp"></i>';
                            } elseif (
                                str_contains($urlLower, 'daraz') ||
                                str_contains($urlLower, 'amazon') ||
                                str_contains($urlLower, 'shop') ||
                                str_contains($iconLower, 'cart') ||
                                str_contains($iconLower, 'shopping') ||
                                str_contains($iconLower, 'buy')
                            ) {
                                $iconClass = 'shopping';
                                $iconContent = '<i class="fa-solid fa-cart-shopping"></i>';
                            } elseif (
                                str_contains($iconLower, 'globe') ||
                                str_contains($iconLower, 'web') ||
                                str_contains($iconLower, 'site')
                            ) {
                                $iconClass = 'globe';
                                $iconContent = '<i class="fa-solid fa-globe"></i>';
                            } elseif (!empty($icon)) {
                                $iconLookupKey = $icon;

                                if (str_contains($icon, ' ')) {
                                    preg_match_all('/fa-[a-z0-9-]+/i', $icon, $iconMatches);
                                    $iconTokens = array_values(
                                        array_filter($iconMatches[0] ?? [], function ($token) {
                                            return !in_array($token, [
                                                'fa-solid',
                                                'fa-brands',
                                                'fa-regular',
                                                'fa-light',
                                                'fa-thin',
                                                'fa-duotone',
                                                'fa-sharp',
                                            ]);
                                        }),
                                    );
                                    $iconLookupKey = end($iconTokens) ?: $icon;
                                }

                                $iconClass = $qrIconStyleMap[$iconLookupKey] ?? $iconClass;
                                $resolvedIconClass = str_contains($icon, ' ')
                                    ? $icon
                                    : ($qrIconClassMap[$icon] ?? null);

                                if (!$resolvedIconClass) {
                                    $brandKeywords = [
                                        'youtube',
                                        'facebook',
                                        'instagram',
                                        'twitter',
                                        'x-twitter',
                                        'whatsapp',
                                        'pinterest',
                                        'bluetooth',
                                        'telegram',
                                        'linkedin',
                                        'github',
                                        'discord',
                                        'tiktok',
                                    ];
                                    $prefix = collect($brandKeywords)->contains(fn($keyword) => str_contains($iconLower, $keyword))
                                        ? 'fa-brands'
                                        : 'fa-solid';
                                    $resolvedIconClass = $prefix . ' ' . $icon;
                                }

                                $iconContent = '<i class="' . e($resolvedIconClass) . '"></i>';
                            }
                        }
                    @endphp

                    <a href="{{ $url }}" target="_blank" class="link-item">
                        <div class="link-icon {{ $isCustomIcon ? '' : $iconClass }}">
                            @if ($isCustomIcon)
                                <img src="{{ $icon }}" alt="">
                            @else
                                {!! $iconContent !!}
                            @endif
                        </div>
                        <div class="link-content">
                            <div class="link-title">{{ $title }}</div>
                            <div class="link-url">{{ str_replace(['https://', 'http://'], '', $url) }}</div>
                        </div>
                        <div class="link-arrow">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    </a>
                @endforeach
            @else
                <!-- Default Product Link when no custom links -->
                <a href="{{ $productUrl }}" target="_blank" class="link-item default-link">
                    <div class="link-icon shopping">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div class="link-content">
                        <div class="link-title">{{ translate('View Product') }}</div>
                        <div class="link-url">{{ str_replace(['https://', 'http://'], '', $productUrl) }}</div>
                    </div>
                    <div class="link-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
            @endif


            @if (count($qrLinks) > 0)
                <a href="{{ $productUrl }}" target="_blank" class="link-item default-link">
                    <div class="link-icon shopping">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div class="link-content">
                        <div class="link-title">{{ translate('View on Store') }}</div>
                        <div class="link-url">{{ str_replace(['https://', 'http://'], '', $productUrl) }}</div>
                    </div>
                    <div class="link-arrow">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>
                </a>
            @endif
        </div>

        <!-- Share Button -->
        <button class="share-button" onclick="shareProduct()" title="{{ translate('Share Product') }}">
            <i class="fa-solid fa-share-nodes"></i>
        </button>
    </div>

    @if ($showRecipeDetails)
        <div id="recipe-modal" class="recipe-modal" onclick="closeRecipeModal(event)">
            <div class="recipe-modal-card" onclick="event.stopPropagation()">
                <div class="recipe-modal-header">
                    {{-- <div class="recipe-modal-title">{{ translate('Recipe/Additional Details') }}</div> --}}
                    <button type="button" class="recipe-modal-close" onclick="closeRecipeModal()">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="recipe-modal-body">
                    @if ($recipeImage)
                        <div class="recipe-modal-image">
                            <img src="{{ $recipeImage }}" alt="{{ translate('Recipe image') }}">
                        </div>
                    @endif

                    @if (filled($product->recipe_details))
                        <div class="recipe-modal-details">{!! nl2br(e($product->recipe_details)) !!}</div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <script>
        function openRecipeModal() {
            const modal = document.getElementById('recipe-modal');
            if (!modal) return;
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeRecipeModal(event) {
            if (event && event.target && event.target.closest('.recipe-modal-card') && event.target.id !== 'recipe-modal') {
                return;
            }

            const modal = document.getElementById('recipe-modal');
            if (!modal) return;
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeRecipeModal();
            }
        });

        function shareProduct() {
            const shareData = {
                title: '{{ $product->name }}',
                text: '{{ strip_tags($product->short_description ?: $product->name) }}',
                url: window.location.href
            };

            if (navigator.share) {
                navigator.share(shareData).catch(console.error);
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('{{ translate('Link copied to clipboard!') }}');
                }).catch(() => {
                    alert('{{ translate('Unable to share. Please copy the URL manually.') }}');
                });
            }
        }

        // Loading Screen Handler
        document.addEventListener('DOMContentLoaded', function() {
            const loadingScreen = document.getElementById('loading-screen');
            const body = document.body;

            // Wait 2 seconds then fade out
            setTimeout(function() {
                if (loadingScreen) {
                    loadingScreen.classList.add('fade-out');
                    body.classList.remove('loading');

                    // Remove loading screen from DOM after fade animation
                    setTimeout(function() {
                        loadingScreen.remove();
                    }, 500);
                }
            }, 2000);
        });
    </script>
</body>

</html>
