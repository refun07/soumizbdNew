<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $landingPage->seo_data['meta_title'] ?? $landingPage->title }}</title>

    @if(!empty($landingPage->seo_data['meta_description']))
        <meta name="description" content="{{ $landingPage->seo_data['meta_description'] }}">
    @endif
    @if(!empty($landingPage->seo_data['meta_keywords']))
        <meta name="keywords" content="{{ $landingPage->seo_data['meta_keywords'] }}">
    @endif

    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $landingPage->seo_data['meta_title'] ?? $landingPage->title }}">
    @if(!empty($landingPage->seo_data['meta_description']))
        <meta property="og:description" content="{{ $landingPage->seo_data['meta_description'] }}">
    @endif
    @if(!empty($landingPage->seo_data['meta_image']))
        <meta property="og:image" content="{{ $landingPage->seo_data['meta_image'] }}">
    @endif

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- React and ReactDOM --}}
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Landing Page Color Variables --}}
    <style>
        :root {
            --landing-theme-color: {{ $themeColor ?? '#1a472a' }};
            --landing-primary-color: {{ $primaryColor ?? '#E68A3D' }};
            --landing-theme-light: color-mix(in srgb, {{ $themeColor ?? '#1a472a' }} 80%, white);
            --landing-theme-dark: color-mix(in srgb, {{ $themeColor ?? '#1a472a' }} 80%, black);
            --landing-primary-light: color-mix(in srgb, {{ $primaryColor ?? '#E68A3D' }} 80%, white);
            --landing-primary-dark: color-mix(in srgb, {{ $primaryColor ?? '#E68A3D' }} 80%, black);
            /* Contrast colors will be set by JavaScript based on luminance */
            --landing-theme-contrast: {{ $themeColor ?? '#1a472a' }};
            --landing-primary-contrast: {{ $primaryColor ?? '#E68A3D' }};
            --landing-theme-text: #ffffff;
            --landing-primary-text: #000000;
        }
    </style>

    <script>
        window.config = window.config || {};
        window.config.paymentMethods = @json($paymentMethods ?? []);
        window.config.landingColors = {
            themeColor: '{{ $themeColor ?? "#1a472a" }}',
            primaryColor: '{{ $primaryColor ?? "#E68A3D" }}'
        };
        window.config.generalSettings = @json($generalSettings ?? []);

        // Color contrast utility - calculates luminance and returns appropriate text color
        window.getContrastColor = function(hexColor, lightColor = '#ffffff', darkColor = '#1a1a1a') {
            // Remove # if present
            hexColor = hexColor.replace('#', '');
            
            // Convert to RGB
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            
            // Calculate relative luminance using WCAG formula
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            
            // Return light color for dark backgrounds, dark color for light backgrounds
            return luminance > 0.5 ? darkColor : lightColor;
        };

        // Get contrasting shade of the same color family
        window.getContrastingShade = function(hexColor) {
            hexColor = hexColor.replace('#', '');
            
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            
            if (luminance > 0.6) {
                // Light color - return darker version (mix with black)
                const darken = (c) => Math.round(c * 0.4);
                return `rgb(${darken(r)}, ${darken(g)}, ${darken(b)})`;
            } else {
                // Dark color - return lighter version (mix with white)
                const lighten = (c) => Math.round(c + (255 - c) * 0.6);
                return `rgb(${lighten(r)}, ${lighten(g)}, ${lighten(b)})`;
            }
        };

        // Apply contrast colors on page load - only for dynamic elements that need it
        document.addEventListener('DOMContentLoaded', function() {
            const themeColor = window.config.landingColors.themeColor;
            const primaryColor = window.config.landingColors.primaryColor;
            
            // Set CSS variables for contrast colors (these are used by dynamic elements)
            document.documentElement.style.setProperty('--landing-theme-contrast', window.getContrastingShade(themeColor));
            document.documentElement.style.setProperty('--landing-primary-contrast', window.getContrastingShade(primaryColor));
            // Note: --landing-theme-text and --landing-primary-text are set in CSS above
            // We don't override them here to prevent flashing color changes
        });

        // Facebook Pixel configuration
        window.config.facebookPixel = {
            active: {{ $facebookPixelActive ? 'true' : 'false' }},
            enabled: {{ $facebookPixelEnabled ? 'true' : 'false' }},
            pixelId: '{{ $metaPixelId }}'
        };
    </script>

    {{-- Facebook Pixel Code --}}
    @if($facebookPixelActive && $facebookPixelEnabled && $metaPixelId)
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $metaPixelId }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1"/>
    </noscript>
    @endif
    {{-- End Facebook Pixel Code --}}
</head>
<body>
    <div class="landing-page">
        {{-- Render all visible sections in order --}}
        @foreach($landingPage->sections as $section)
            @if($section->is_visible)
                @php
                    // Calculate typography colors for this section
                    $sectionData = $section->section_data ?? [];
                    $sectionBg = $sectionData['background_color'] ?? null;
                    $effectiveBg = !empty($sectionBg) ? $sectionBg : ($themeColor ?? '#1a472a');
                    $effectivePrimary = $primaryColor ?? '#E68A3D';
                    
                    // Calculate contrast colors if not explicitly set
                    $textColor = $sectionData['text_color'] ?? null;
                    $textSecondaryColor = $sectionData['text_secondary_color'] ?? null;
                    $bodyBgTextColor = $sectionData['body_bg_text_color'] ?? null;
                    $buttonBgColor = $sectionData['button_bg_color'] ?? null;
                    $buttonTextColor = $sectionData['button_text_color'] ?? null;
                @endphp
                @switch($section->type)
                    @case('centered_product_offer_hero')
                        @include('frontend.landing.sections.centered-product-offer-hero', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('centered_video_hero')
                        @include('frontend.landing.sections.centered-video-hero', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('centered_offer_hero')
                        @include('frontend.landing.sections.centered-offer-hero', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('quality_products')
                        @include('frontend.landing.sections.quality-products', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('quality_about')
                        @include('frontend.landing.sections.quality-about', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('multiple_products')
                        @include('frontend.landing.sections.multiple-products', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('product_benefits')
                        @include('frontend.landing.sections.product-benefits', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('product_highlights')
                        @include('frontend.landing.sections.product-highlights', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('carousel_images')
                        @include('frontend.landing.sections.carousel-images', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('faq_section')
                        @include('frontend.landing.sections.faq-section', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('featured_products')
                        @include('frontend.landing.sections.featured-products', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor])
                        @break

                    @case('product_order')
                        @include('frontend.landing.sections.product-order', ['data' => $section->section_data, 'themeColor' => $themeColor, 'primaryColor' => $primaryColor, 'landingPage' => $landingPage, 'products' => $products])
                        @break

                    @default
                        {{-- Fallback for unknown section types --}}
                        <div class="py-16" style="background-color: {{ $themeColor }}">
                            <div class="container">
                                <div class="bg-gray-100 p-4 rounded">
                                    <p class="text-gray-600">Unknown section type: {{ $section->type }}</p>
                                </div>
                            </div>
                        </div>
                @endswitch
            @endif
        @endforeach

        {{-- Always render product order section --}}
        <x-landing-order-form :products="$products" :landing-page-id="$landingPage->id" :payment-methods="$paymentMethods" :coupon="$coupon" :country-id="$bangladeshCountryId" />

        {{-- Show message if no sections --}}
        @if($landingPage->sections->count() === 0)
            <div class="container py-20 text-center">
                <p class="text-gray-600">No content available for this landing page.</p>
            </div>
        @endif
    </div>
    
    @stack('scripts')
</body>
</html>
