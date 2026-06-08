{{-- Product Highlights Section --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['highlights_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $benefitTitleColor = $data['benefit_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $benefitDescriptionColor = $data['benefit_description_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $buttonBgColor = $data['highlights_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['highlights_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        {{-- Section Title --}}
        @if(!empty($data['benefits_title']))
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-8 md:mb-12" style="color: {{ formatColorForCssLanding($titleColor) }};">
                {{ $data['benefits_title'] }}
            </h2>
        @endif

        {{-- Benefits Grid --}}
        @if(!empty($data['benefits_items']) && is_array($data['benefits_items']) && count($data['benefits_items']) > 0)
            @php
                $benefits = $data['benefits_items'];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                @foreach($benefits as $benefit)
                    @if(!empty($benefit['title']))
                        <div class="rounded-xl p-4 md:p-6 text-center" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                            {{-- Icon --}}
                            @if(!empty($benefit['icon']))
                                <div class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-3 md:mb-4 rounded-full flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}20;">
                                    <div class="w-6 h-6 md:w-8 md:h-8 flex items-center justify-center">
                                        {!! $benefit['icon'] !!}
                                    </div>
                                </div>
                            @endif
                            {{-- Title --}}
                            @if(!empty($benefit['title']))
                                <h3 class="text-lg md:text-xl font-bold mb-1 md:mb-2" style="color: {{ formatColorForCssLanding($benefitTitleColor) }};">
                                    {{ $benefit['title'] }}
                                </h3>
                            @endif
                            {{-- Description --}}
                            @if(!empty($benefit['description']))
                                <p class="text-sm md:text-base leading-relaxed" style="color: {{ formatColorForCssLanding($benefitDescriptionColor) }};">
                                    {{ $benefit['description'] }}
                                </p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex justify-center">
                <div class="w-full max-w-2xl aspect-square rounded-lg flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                    <span style="color: {{ formatColorForCssLanding($titleColor) }};">Benefits Section</span>
                </div>
            </div>
        @endif

        {{-- CTA Button --}}
        @if(!empty($data['button_text']))
            <div class="flex justify-center mt-8 md:mt-12">
                <a href="#landing-page-order-form"
                   class="inline-flex items-center gap-2 px-6 md:px-8 py-3 md:py-4 font-bold uppercase tracking-wide text-sm md:text-base transition-opacity"
                   style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($buttonTextColor) }};"
                   onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false;"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    {{ $data['button_text'] }}
                </a>
            </div>
        @endif
    </div>
</section>
