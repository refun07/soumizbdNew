{{-- Quality Products About Section - 2 Column Split --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['quality_about_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $descriptionColor = $data['quality_about_description_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $buttonBgColor = $data['quality_about_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['quality_about_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
            {{-- Left Column: Image --}}
            <div class="order-2 md:order-1">
                @if(!empty($data['quality_about_image_url']))
                    <img src="{{ $data['quality_about_image_url'] }}" alt="Product" class="w-full h-auto rounded-lg shadow-lg">
                @else
                    <div class="w-full h-64 md:h-80 rounded-lg flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                        <span style="color: {{ formatColorForCssLanding($descriptionColor) }};">No image selected</span>
                    </div>
                @endif
            </div>

            {{-- Right Column: Content --}}
            <div class="order-1 md:order-2">
                {{-- Heading --}}
                @if(!empty($data['quality_about_title']))
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6" style="color: {{ formatColorForCssLanding($titleColor) }};">
                        {{ $data['quality_about_title'] }}
                    </h2>
                @endif

                {{-- Description --}}
                @if(!empty($data['quality_about_description']))
                    <p class="text-base md:text-lg leading-relaxed mb-6 md:mb-8" style="color: {{ formatColorForCssLanding($descriptionColor) }};">
                        {{ $data['quality_about_description'] }}
                    </p>
                @endif

                {{-- CTA Button --}}
                @if(!empty($data['quality_about_button_text']))
                    <a href="#landing-page-order-form"
                       class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 font-semibold text-sm md:text-base transition-opacity"
                       style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($buttonTextColor) }};"
                       onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        {{ $data['quality_about_button_text'] }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
