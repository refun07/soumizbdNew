{{-- Featured Products Section --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $sectionTitleColor = $data['featured_section_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $productTitleColor = $data['featured_product_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $productDescriptionColor = $data['featured_product_description_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $buttonBgColor = $data['featured_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['featured_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        {{-- Section Title --}}
        @if(!empty($data['section_title']))
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-8 md:mb-12" style="color: {{ formatColorForCssLanding($sectionTitleColor) }};">
                {{ $data['section_title'] }}
            </h2>
        @endif

        {{-- Products Grid --}}
        @if(!empty($data['featured_products']) && is_array($data['featured_products']) && count($data['featured_products']) > 0)
            @php
                $products = $data['featured_products'];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 max-w-5xl mx-auto">
                @foreach($products as $product)
                    @if(!empty($product['title']))
                        <div class="group bg-white/10 rounded-lg overflow-hidden hover:ring-2 transition-all duration-300 backdrop-blur-sm" style="--tw-ring-color: {{ formatColorForCssLanding($buttonBgColor) }};">
                            {{-- Product Image --}}
                            <div class="aspect-[4/3] md:aspect-[16/10] relative overflow-hidden">
                                @if(!empty($product['image']))
                                    <img
                                        src="{{ $product['image'] }}"
                                        alt="{{ $product['title'] }}"
                                        class="w-full h-full object-cover"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                                        <span style="color: {{ formatColorForCssLanding($productDescriptionColor) }};">No Image</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Content --}}
                            <div class="p-4 md:p-6 text-center">
                                {{-- Title --}}
                                @if(!empty($product['title']))
                                    <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-3" style="color: {{ formatColorForCssLanding($productTitleColor) }};">
                                        {{ $product['title'] }}
                                    </h3>
                                @endif

                                {{-- Description --}}
                                @if(!empty($product['description']))
                                    <p class="mb-4 md:mb-6 text-sm md:text-base" style="color: {{ formatColorForCssLanding($productDescriptionColor) }};">
                                        {{ $product['description'] }}
                                    </p>
                                @endif

                                {{-- CTA Button --}}
                                @if(!empty($product['button_text']))
                                    <button
                                        onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'})"
                                        class="inline-flex items-center gap-2 px-4 md:px-6 py-2 md:py-3 font-bold uppercase tracking-wide transition-opacity text-xs md:text-sm"
                                        style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($buttonTextColor) }};"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        {{ $product['button_text'] }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex justify-center">
                <div class="w-full max-w-xl aspect-video rounded-lg flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                    <span style="color: {{ formatColorForCssLanding($sectionTitleColor) }};">Featured Products Section</span>
                </div>
            </div>
        @endif
    </div>
</section>
