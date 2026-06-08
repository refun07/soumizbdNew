{{-- FAQ Section --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Calculate effective primary color
    $effectivePrimary = $primaryColor ?? '#E68A3D';
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['faq_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $questionColor = $data['faq_question_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
    $answerColor = $data['faq_answer_color'] ?? '#374151';
    $buttonBgColor = $data['faq_button_bg_color'] ?? $effectivePrimary;
    $buttonTextColor = $data['faq_button_text_color'] ?? getContrastColorLanding($buttonBgColor);
    $themeDark = darkenColorLanding($bgColor ?? '#1a472a', 15);
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        {{-- Section Title --}}
        @if(!empty($data['faq_title']))
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-8 md:mb-12" style="color: {{ formatColorForCssLanding($titleColor) }};">
                {{ $data['faq_title'] }}
            </h2>
        @endif

        {{-- FAQ Accordion --}}
        @if(!empty($data['faq_items']) && is_array($data['faq_items']) && count($data['faq_items']) > 0)
            <div class="space-y-3 md:space-y-4">
                @foreach($data['faq_items'] as $index => $faq)
                    @if(!empty($faq['question']))
                        <div class="faq-item rounded-lg overflow-hidden">
                            {{-- Question Header (Accordion Trigger) --}}
                            <button
                                type="button"
                                class="faq-toggle w-full text-left p-3 md:p-4 flex items-center gap-3 md:gap-4 transition-all duration-300 focus:outline-none"
                                data-target="faq-content-{{ $index }}"
                                data-theme-color="{{ $bgColor }}"
                                data-theme-dark="{{ $themeDark }}"
                                style="background-color: {{ $index === 0 ? formatColorForCssLanding($bgColor ?? '#1a472a') : formatColorForCssLanding($themeDark) }};"
                                onclick="toggleFaq(this)"
                            >
                                {{-- Checkmark Badge --}}
                                <span class="flex-shrink-0 w-6 h-6 md:w-8 md:h-8 rounded-full flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($buttonBgColor) }};">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>

                                {{-- Question Text --}}
                                <span class="faq-question-text flex-1 font-semibold text-base md:text-lg" style="color: {{ formatColorForCssLanding($questionColor) }};">
                                    {{ $faq['question'] }}
                                </span>

                                {{-- Expand/Collapse Icon --}}
                                <span class="faq-icon faq-chevron-icon flex-shrink-0 transition-transform duration-300" style="color: {{ formatColorForCssLanding($questionColor) }};">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transform rotate-0">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </span>
                            </button>

                            {{-- Answer Content --}}
                            <div
                                id="faq-content-{{ $index }}"
                                class="faq-content overflow-hidden transition-all duration-300"
                                style="max-height: {{ $index === 0 ? '500px' : '0' }};"
                            >
                                <div class="p-4 md:p-6" style="background-color: white;">
                                    <p class="leading-relaxed text-sm md:text-base" style="color: {{ formatColorForCssLanding($answerColor) }};">
                                        {{ $faq['answer'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="flex justify-center">
                <div class="w-full max-w-2xl aspect-square rounded-lg flex items-center justify-center" style="background-color: rgba(255,255,255,0.5);">
                    <span style="color: {{ formatColorForCssLanding($questionColor) }};">FAQ Section</span>
                </div>
            </div>
        @endif

        {{-- CTA Button --}}
        @if(!empty($data['button_text']))
            <div class="flex justify-center mt-8 md:mt-12">
                <a href="#landing-page-order-form"
                   class="inline-flex items-center gap-2 px-6 md:px-8 py-3 md:py-4 font-bold uppercase tracking-wide text-sm md:text-base transition-opacity rounded-lg"
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

{{-- Accordion Toggle Script --}}
<script>
    function toggleFaq(button) {
        const targetId = button.getAttribute('data-target');
        const content = document.getElementById(targetId);
        const icon = button.querySelector('.faq-icon svg');

        if (!content) return;
        
        const themeColor = button.getAttribute('data-theme-color');
        const themeDark = button.getAttribute('data-theme-dark');
        const textColor = button.style.color;

        // Close all other FAQs
        document.querySelectorAll('.faq-content').forEach(item => {
            if (item.id !== targetId) {
                item.style.maxHeight = '0';
            }
        });

        document.querySelectorAll('.faq-toggle').forEach(btn => {
            if (btn !== button) {
                btn.style.backgroundColor = themeDark;
                const otherIcon = btn.querySelector('.faq-icon svg');
                if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
            }
        });

        // Toggle current FAQ
        if (content.style.maxHeight === '0px' || content.style.maxHeight === '') {
            content.style.maxHeight = content.scrollHeight + 'px';
            button.style.backgroundColor = themeColor;
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.maxHeight = '0';
            button.style.backgroundColor = themeDark;
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    // Open first FAQ by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstToggle = document.querySelector('.faq-toggle');
        if (firstToggle) {
            const firstContent = document.querySelector('.faq-content');
            if (firstContent) {
                firstContent.style.maxHeight = firstContent.scrollHeight + 'px';
            }
            const firstIcon = firstToggle.querySelector('.faq-icon svg');
            if (firstIcon) firstIcon.style.transform = 'rotate(180deg)';
        }
    });
</script>
