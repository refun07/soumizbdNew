{{-- Quality Products Section - 2 Column Split --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['quality_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $featureTextColor = $data['feature_text_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $buttonBgColor = $data['quality_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['quality_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full py-8 md:py-10 lg:py-16" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8" style="position: relative; z-index: 10;">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8 lg:gap-12 items-center">
            {{-- Left Column: Image --}}
            <div>
                @if(!empty($data['quality_image_url']))
                    <img src="{{ $data['quality_image_url'] }}" alt="Quality" class="w-full h-auto rounded-md lg:rounded-lg shadow-lg">
                @else
                    <div class="w-full h-48 md:h-56 lg:h-64 bg-white/10 rounded-md lg:rounded-lg flex items-center justify-center">
                        <span style="color: {{ formatColorForCssLanding($featureTextColor) }};">Quality Image</span>
                    </div>
                @endif
            </div>

            {{-- Right Column: Content --}}
            <div class="space-y-4 md:space-y-6">
                {{-- Heading --}}
                @if(!empty($data['quality_title']))
                    <h2 class="text-2xl md:text-3xl lg:text-3xl font-bold" style="color: {{ formatColorForCssLanding($titleColor) }};">
                        {{ $data['quality_title'] }}
                    </h2>
                @endif

                {{-- Feature List --}}
                <div class="space-y-3 md:space-y-4">
                    @for($i = 1; $i <= 4; $i++)
                        @php
                            $iconKey = 'item'.$i.'_icon';
                            $textKey = 'item'.$i.'_text';
                        @endphp
                        @if(!empty($data[$textKey]))
                            <div class="flex items-center gap-3">
                                @if(!empty($data[$iconKey]))
                                    <span class="text-xl md:text-2xl" style="color: {{ formatColorForCssLanding($buttonBgColor) }};">
                                        {!! $data[$iconKey] !!}
                                    </span>
                                @endif
                                <span class="text-sm md:text-base" style="color: {{ formatColorForCssLanding($featureTextColor) }};">{{ $data[$textKey] }}</span>
                            </div>
                        @endif
                    @endfor
                </div>

                {{-- CTA Button --}}
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? '#landing-page-order-form' }}"
                       class="w-full md:w-auto inline-block py-2.5 md:py-3 px-6 md:px-8 font-bold text-sm md:text-base rounded-sm transition-colors duration-200 text-center"
                       style="background-color: {{ formatColorForCssLanding($buttonBgColor) }}; color: {{ formatColorForCssLanding($buttonTextColor) }};"
                       onclick="if(this.getAttribute('href') === '#landing-page-order-form') { document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false; }"
                    >
                        {{ $data['button_text'] }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>
