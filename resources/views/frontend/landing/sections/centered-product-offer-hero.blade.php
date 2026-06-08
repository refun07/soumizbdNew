{{-- Centered Product Offer Hero Section --}}
@include('frontend.landing.sections._color-helpers')

@php
    $embedUrl = getYoutubeEmbedUrlLanding($data['product_video_url'] ?? null);
    
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['product_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $subtitleColor = $data['product_subtitle_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $badgeTextColor = $data['product_offer_badge_text_color'] ?? '#ffffff';
    $oldPriceColor = $data['product_old_price_color'] ?? 'rgba(255,255,255,0.7)';
    $newPriceColor = $data['product_new_price_color'] ?? '#ffffff';
    $buttonBgColor = $data['product_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['product_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full min-h-[400px] md:min-h-[500px] lg:min-h-screen flex flex-col items-center justify-center py-8 md:py-12 lg:py-16" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center" style="position: relative; z-index: 10;">
        {{-- Brand Logo --}}
        @if(!empty($data['brand_logo_url']))
            <div class="mb-4 md:mb-6">
                <img
                    src="{{ $data['brand_logo_url'] }}"
                    alt="Brand Logo"
                    class="h-8 md:h-10 lg:h-12 w-auto object-contain"
                />
            </div>
        @endif

        {{-- Product Title --}}
        @if(!empty($data['product_title']))
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-2 md:mb-4" style="color: {{ formatColorForCssLanding($titleColor) }};">
                {{ $data['product_title'] }}
            </h1>
        @endif

        {{-- Product Subtitle --}}
        @if(!empty($data['product_subtitle']))
            <p class="text-base md:text-lg text-center mb-4 md:mb-6 max-w-md mx-auto" style="color: {{ formatColorForCssLanding($subtitleColor) }};">
                {{ $data['product_subtitle'] }}
            </p>
        @endif

        {{-- Product Image or Video --}}
        @if(!empty($data['product_image_url']))
            <div class="w-full mb-4 md:mb-6">
                <div class="aspect-video rounded-xl md:rounded-2xl overflow-hidden" style="background-color: var(--landing-theme-light);">
                    <img
                        src="{{ $data['product_image_url'] }}"
                        alt="{{ $data['product_title'] ?? 'Product' }}"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>
        @elseif($embedUrl)
            <div class="w-full mb-4 md:mb-6">
                <div class="aspect-video rounded-xl md:rounded-2xl overflow-hidden" style="background-color: var(--landing-theme-light);">
                    <iframe
                        src="{{ $embedUrl }}"
                        class="w-full h-full"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        @else
            <div class="w-full aspect-square md:aspect-video rounded-xl md:rounded-2xl mb-4 md:mb-6 flex items-center justify-center" style="background-color: var(--landing-theme-light);">
                <span class="text-sm md:text-base" style="color: var(--landing-theme-text); opacity: 0.5;">Product Image or Video</span>
            </div>
        @endif

        {{-- Offer Badge with Pricing --}}
        @if(!empty($data['product_offer_badge_text']) || !empty($data['product_new_price']))
            <div class="mb-4 md:mb-6">
                <span class="inline-flex items-center gap-2 px-4 md:px-6 py-2 text-xl md:text-2xl lg:text-3xl font-semibold rounded-md" style="background-color: {{ formatColorForCssLanding($effectivePrimary) }};">
                    @if(!empty($data['product_offer_badge_text']))
                        <span style="color: {{ formatColorForCssLanding($badgeTextColor) }};">{{ $data['product_offer_badge_text'] }}</span>
                    @endif
                    @if(!empty($data['product_old_price']))
                        <span class="line-through opacity-70" style="color: {{ formatColorForCssLanding($oldPriceColor) }};">{{ $data['product_old_price'] }}</span>
                    @endif
                    @if(!empty($data['product_new_price']))
                        <span style="color: {{ formatColorForCssLanding($newPriceColor) }};">{{ $data['product_new_price'] }}</span>
                    @endif
                </span>
            </div>
        @endif

        {{-- CTA Button --}}
        @if(!empty($data['product_button_text']))
            <button
                type="button"
                class="w-full sm:w-auto py-3 px-4 font-bold text-xs sm:text-sm md:text-base rounded-sm flex items-center justify-center gap-2 transition-colors duration-200 whitespace-nowrap"
                style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($buttonTextColor) }};"
                onmouseover="this.style.backgroundColor='{{ formatColorForCssLanding($buttonBgColor) }}'; this.style.opacity='0.9';"
                onmouseout="this.style.backgroundColor='{{ formatColorForCssLanding($buttonBgColor) }}'; this.style.opacity='1';"
                onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'})"
            >
                @if(!empty($data['button_icon']))
                    <span>{!! $data['button_icon'] !!}</span>
                @else
                    <i class="fa-solid fa-cart-shopping"></i>
                @endif
                {{ $data['product_button_text'] }}
            </button>
        @endif
    </div>
</section>
