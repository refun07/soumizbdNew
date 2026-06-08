{{-- Centered Offer Hero Section --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['offer_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $subtitleColor = $data['offer_subtitle_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $badgeTextColor = $data['offer_badge_text_color'] ?? '#ffffff';
    $oldPriceColor = $data['old_price_color'] ?? 'rgba(255,255,255,0.7)';
    $newPriceColor = $data['new_price_color'] ?? $effectivePrimary;
    $buttonBgColor = $data['centered_offer_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['centered_offer_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full min-h-[350px] md:min-h-[400px] lg:min-h-[400px] flex flex-col items-center justify-center py-8 md:py-10 lg:py-16" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="w-full max-w-2xl mx-auto px-4 md:px-6 lg:px-8 flex flex-col items-center text-center" style="position: relative; z-index: 10;">
        {{-- Offer Badge --}}
        @if(!empty($data['offer_badge_text']))
            <span class="inline-block px-3 md:px-4 py-1 md:py-1 text-xs md:text-sm font-semibold rounded-full mb-3 md:mb-4" style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($badgeTextColor) }};">
                {{ $data['offer_badge_text'] }}
            </span>
        @endif

        {{-- Main Heading --}}
        @if(!empty($data['offer_title']))
            <h1 class="text-3xl md:text-4xl lg:text-4xl font-bold text-center mb-2" style="color: {{ formatColorForCssLanding($titleColor) }};">
                {{ $data['offer_title'] }}
            </h1>
        @endif

        {{-- Subtitle --}}
        @if(!empty($data['offer_subtitle']))
            <p class="text-sm md:text-base text-center mb-4 md:mb-6 max-w-md" style="color: {{ formatColorForCssLanding($subtitleColor) }};">
                {{ $data['offer_subtitle'] }}
            </p>
        @endif

        {{-- Pricing --}}
        @if(!empty($data['old_price']) || !empty($data['new_price']))
            <div class="mb-4 md:mb-6 flex items-center gap-3 md:gap-4">
                @if(!empty($data['old_price']))
                    <span style="color: {{ formatColorForCssLanding($oldPriceColor) }}; text-decoration: line-through;">{{ $data['old_price'] }}Tk</span>
                @endif
                @if(!empty($data['new_price']))
                    <span class="text-3xl md:text-4xl font-bold" style="color: {{ formatColorForCssLanding($newPriceColor) }};">{{ $data['new_price'] }}Tk</span>
                @endif
            </div>
        @endif

        {{-- CTA Button --}}
        @if(!empty($data['button_text']))
            <button
                type="button"
                class="py-2.5 px-6 md:py-3 md:px-8 font-bold text-sm md:text-base rounded-sm transition-colors duration-200 w-full md:w-auto"
                style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($buttonTextColor) }};"
                onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'})"
            >
                {{ $data['button_text'] }}
            </button>
        @endif
    </div>
</section>
