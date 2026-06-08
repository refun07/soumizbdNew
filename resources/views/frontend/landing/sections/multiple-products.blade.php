{{-- Multiple Products Section - Three Column Product Cards --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback per product
    $product1TitleColor = $data['product1_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $product1SubtitleColor = $data['product1_subtitle_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $product1ButtonBgColor = $data['product1_button_bg_color'] ?? $effectivePrimary;
    $product1ButtonTextColor = $data['product1_button_text_color'] ?? getContrastColorLanding($product1ButtonBgColor);
    
    $product2TitleColor = $data['product2_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $product2SubtitleColor = $data['product2_subtitle_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $product2ButtonBgColor = $data['product2_button_bg_color'] ?? $effectivePrimary;
    $product2ButtonTextColor = $data['product2_button_text_color'] ?? getContrastColorLanding($product2ButtonBgColor);
    
    $product3TitleColor = $data['product3_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $product3SubtitleColor = $data['product3_subtitle_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $product3ButtonBgColor = $data['product3_button_bg_color'] ?? $effectivePrimary;
    $product3ButtonTextColor = $data['product3_button_text_color'] ?? getContrastColorLanding($product3ButtonBgColor);
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            {{-- Product Card 1 --}}
            <div class="group">
                <div class="relative mb-3 md:mb-4 overflow-hidden rounded-lg">
                    @if(!empty($data['product1_image']))
                        <img src="{{ $data['product1_image'] }}" alt="{{ $data['product1_title'] ?? 'Product 1' }}" class="w-full h-64 md:h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="w-full h-64 md:h-80 rounded-lg flex items-center justify-center transition-all duration-300" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                            <span style="color: {{ formatColorForCssLanding($product1SubtitleColor) }};">No image selected</span>
                        </div>
                    @endif
                </div>
                @if(!empty($data['product1_title']))
                    <h3 class="text-lg md:text-xl font-semibold text-center mb-1 md:mb-2" style="color: {{ formatColorForCssLanding($product1TitleColor) }};">
                        {{ $data['product1_title'] }}
                    </h3>
                @endif
                @if(!empty($data['product1_subtitle']))
                    <p class="text-center text-sm mb-3 md:mb-4" style="color: {{ formatColorForCssLanding($product1SubtitleColor) }};">
                        {{ $data['product1_subtitle'] }}
                    </p>
                @endif
                @if(!empty($data['product1_button_text']))
                    <div class="flex justify-center">
                        <a href="#landing-page-order-form"
                           class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 font-semibold text-sm transition-opacity rounded-md"
                           style="background-color: {{ formatColorForCssLanding($product1ButtonBgColor) }}; color: {{ formatColorForCssLanding($product1ButtonTextColor) }};"
                           onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            {{ $data['product1_button_text'] }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Product Card 2 --}}
            <div class="group">
                <div class="relative mb-3 md:mb-4 overflow-hidden rounded-lg">
                    @if(!empty($data['product2_image']))
                        <img src="{{ $data['product2_image'] }}" alt="{{ $data['product2_title'] ?? 'Product 2' }}" class="w-full h-64 md:h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="w-full h-64 md:h-80 rounded-lg flex items-center justify-center transition-all duration-300" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                            <span style="color: {{ formatColorForCssLanding($product2SubtitleColor) }};">No image selected</span>
                        </div>
                    @endif
                </div>
                @if(!empty($data['product2_title']))
                    <h3 class="text-lg md:text-xl font-semibold text-center mb-1 md:mb-2" style="color: {{ formatColorForCssLanding($product2TitleColor) }};">
                        {{ $data['product2_title'] }}
                    </h3>
                @endif
                @if(!empty($data['product2_subtitle']))
                    <p class="text-center text-sm mb-3 md:mb-4" style="color: {{ formatColorForCssLanding($product2SubtitleColor) }};">
                        {{ $data['product2_subtitle'] }}
                    </p>
                @endif
                @if(!empty($data['product2_button_text']))
                    <div class="flex justify-center">
                        <a href="#landing-page-order-form"
                           class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 font-semibold text-sm transition-opacity rounded-md"
                           style="background-color: {{ formatColorForCssLanding($product2ButtonBgColor) }}; color: {{ formatColorForCssLanding($product2ButtonTextColor) }};"
                           onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            {{ $data['product2_button_text'] }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Product Card 3 --}}
            <div class="group">
                <div class="relative mb-3 md:mb-4 overflow-hidden rounded-lg">
                    @if(!empty($data['product3_image']))
                        <img src="{{ $data['product3_image'] }}" alt="{{ $data['product3_title'] ?? 'Product 3' }}" class="w-full h-64 md:h-80 object-cover transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="w-full h-64 md:h-80 rounded-lg flex items-center justify-center transition-all duration-300" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                            <span style="color: {{ formatColorForCssLanding($product3SubtitleColor) }};">No image selected</span>
                        </div>
                    @endif
                </div>
                @if(!empty($data['product3_title']))
                    <h3 class="text-lg md:text-xl font-semibold text-center mb-1 md:mb-2" style="color: {{ formatColorForCssLanding($product3TitleColor) }};">
                        {{ $data['product3_title'] }}
                    </h3>
                @endif
                @if(!empty($data['product3_subtitle']))
                    <p class="text-center text-sm mb-3 md:mb-4" style="color: {{ formatColorForCssLanding($product3SubtitleColor) }};">
                        {{ $data['product3_subtitle'] }}
                    </p>
                @endif
                @if(!empty($data['product3_button_text']))
                    <div class="flex justify-center">
                        <a href="#landing-page-order-form"
                           class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 font-semibold text-sm transition-opacity rounded-md"
                           style="background-color: {{ formatColorForCssLanding($product3ButtonBgColor) }}; color: {{ formatColorForCssLanding($product3ButtonTextColor) }};"
                           onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            {{ $data['product3_button_text'] }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
