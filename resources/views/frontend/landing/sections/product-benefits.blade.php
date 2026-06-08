{{-- Product Benefits Section - Features List with Checkmark Icons --}}
@php
    // Helper function to calculate luminance and get contrast color
    if (!function_exists('getContrastColorLanding')) {
        function getContrastColorLanding($hexColor, $lightColor = '#ffffff', $darkColor = '#1a1a1a') {
            $hexColor = ltrim($hexColor, '#');
            if (strlen($hexColor) !== 6) return $lightColor;
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            return $luminance > 0.5 ? $darkColor : $lightColor;
        }
    }
    
    // Helper to get secondary text color
    if (!function_exists('getSecondaryColorLanding')) {
        function getSecondaryColorLanding($bgHexColor) {
            $hexColor = ltrim($bgHexColor, '#');
            if (strlen($hexColor) !== 6) return 'rgba(255,255,255,0.7)';
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            return $luminance > 0.5 ? '#4a4a4a' : 'rgba(255,255,255,0.7)';
        }
    }
    
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective typography colors
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    $textColor = $data['text_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $textSecondaryColor = $data['text_secondary_color'] ?? getSecondaryColorLanding($bgColor ?? '#1a472a');
    $buttonBgColor = $data['button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['button_text_color'] ?? getContrastColorLanding($buttonBgColor);
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . $bgColor : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        <div class="max-w-4xl mx-auto p-6 md:p-8 rounded-lg" style="background-color: {{ $bgColor ?? '#1a472a' }};">
            {{-- Heading --}}
            @if(!empty($data['benefits_title']))
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-6 md:mb-8" style="color: {{ $textColor }};">
                    {{ $data['benefits_title'] }}
                </h2>
            @endif

            {{-- Feature List --}}
            @if(!empty($data['benefits_items']) && is_array($data['benefits_items']))
                <div class="space-y-3 md:space-y-4 mb-6 md:mb-8">
                    @foreach($data['benefits_items'] as $index => $item)
                        @if(!empty($item['text']))
                            <div class="flex items-start gap-3 md:gap-4">
                                {{-- Checkmark Icon --}}
                                <div class="flex-shrink-0 w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center" style="background-color: {{ $buttonBgColor }};">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </div>
                                {{-- Item Text --}}
                                <p class="text-base md:text-lg leading-relaxed" style="color: {{ $textSecondaryColor }};">
                                    {{ $item['text'] }}
                                </p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- CTA Button --}}
            @if(!empty($data['benefits_button_text']))
                <div class="flex justify-center">
                    <a href="#landing-page-order-form"
                       class="inline-flex items-center gap-2 px-6 md:px-8 py-3 md:py-4 font-bold uppercase tracking-wide text-sm md:text-base transition-opacity"
                       style="background-color: {{ $buttonBgColor }}; color: {{ $buttonTextColor }};"
                       onclick="document.getElementById('landing-page-order-form')?.scrollIntoView({behavior: 'smooth'}); return false;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        {{ $data['benefits_button_text'] }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
