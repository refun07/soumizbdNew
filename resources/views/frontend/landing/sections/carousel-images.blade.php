{{-- Carousel Image Section --}}
@include('frontend.landing.sections._color-helpers')

@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    
    // Inline color fields with auto-contrast fallback
    $titleColor = $data['carousel_title_color'] ?? getContrastColorLanding($bgColor ?? '#1a472a');
@endphp

<section class="w-full py-12 md:py-16 lg:py-20" style="position: relative; {{ $bgColor ? 'background-color: ' . formatColorForCssLanding($bgColor) : '' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position: relative; z-index: 10;">
        {{-- Section Title --}}
        @if(!empty($data['carousel_title']))
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-center mb-8 md:mb-12" style="color: {{ formatColorForCssLanding($titleColor) }};">
                {{ $data['carousel_title'] }}
            </h2>
        @endif

        {{-- Carousel --}}
        @if(!empty($data['carousel_image_urls']) && is_array($data['carousel_image_urls']) && count($data['carousel_image_urls']) > 0)
            @php
                $imageCount = count($data['carousel_image_urls']);
            @endphp
            <div class="relative carousel-wrapper">
                {{-- Carousel Container --}}
                <div class="carousel-container overflow-hidden" style="margin: 0 -12px;">
                    <div class="carousel-track flex transition-transform duration-300 ease-in-out" id="carousel-track-{{ uniqid() }}" style="width: max-content;">
                        @foreach($data['carousel_image_urls'] as $imageUrl)
                            <div class="carousel-item flex-shrink-0 px-3">
                                <div class="flex justify-center">
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="Carousel image"
                                        class="carousel-image"
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                @if($imageCount > 1)
                    <button class="carousel-btn carousel-prev absolute left-0 top-1/2 -translate-y-1/2 -ml-3 z-10 w-8 h-8 md:w-10 md:h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-700 hover:bg-gray-100 transition-colors" style="color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button class="carousel-btn carousel-next absolute right-0 top-1/2 -translate-y-1/2 -mr-3 z-10 w-8 h-8 md:w-10 md:h-10 bg-white rounded-full shadow-lg flex items-center justify-center text-gray-700 hover:bg-gray-100 transition-colors" style="color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 md:w-6 md:h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                @endif

                {{-- Dots Indicators --}}
                @if($imageCount > 0)
                    <div class="flex justify-center gap-2 mt-4 md:mt-6">
                        @for($i = 0; $i < ceil($imageCount / 4); $i++)
                            <button class="carousel-dot w-2 h-2 md:w-3 md:h-3 rounded-full transition-colors" data-index="{{ $i }}" @if($i === 0) style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};" @else style="background-color: #d1d5db;" @endif></button>
                        @endfor
                    </div>
                @endif
            </div>
        @else
            <div class="flex justify-center">
                <div class="w-full max-w-2xl aspect-video rounded-lg flex items-center justify-center" style="background-color: {{ formatColorForCssLanding($bgColor ?? '#1a472a') }};">
                    <span style="color: {{ formatColorForCssLanding($titleColor) }};">Carousel Images</span>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Carousel JavaScript --}}
<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize carousels on the page
        document.querySelectorAll('.carousel-container').forEach(function(container) {
            const wrapper = container.closest('.carousel-wrapper');
            const track = container.querySelector('.carousel-track');
            const items = track.querySelectorAll('.carousel-item');
            const images = track.querySelectorAll('.carousel-image');
            const prevBtn = wrapper.querySelector('.carousel-prev');
            const nextBtn = wrapper.querySelector('.carousel-next');
            const dots = wrapper.querySelectorAll('.carousel-dot');

            if (!track || items.length === 0) return;

            let currentIndex = 0;
            let itemsPerView = 4;
            let itemWidth = 0;
            let imageHeight = 200;

            function calculateDimensions() {
                const containerWidth = container.offsetWidth;
                const width = window.innerWidth;

                // Determine items per view based on window width
                if (width >= 1024) {
                    itemsPerView = 4;
                } else if (width >= 768) {
                    itemsPerView = 3;
                } else if (width >= 640) {
                    itemsPerView = 2;
                } else {
                    itemsPerView = 1;
                }

                // Calculate item width to fit exactly itemsPerView in container
                const totalPadding = 24;
                itemWidth = (containerWidth - totalPadding) / itemsPerView;

                // Calculate image height (taller aspect ratio)
                imageHeight = Math.round(itemWidth * 1.2);

                // Set explicit width on each item
                items.forEach(function(item) {
                    item.style.width = itemWidth + 'px';
                });

                // Set image dimensions
                images.forEach(function(img) {
                    img.style.width = itemWidth + 'px';
                    img.style.height = imageHeight + 'px';
                });

                return itemWidth;
            }

            function updateCarousel() {
                const maxIndex = Math.max(0, items.length - itemsPerView);
                currentIndex = Math.min(currentIndex, maxIndex);
                const offset = currentIndex * itemWidth;
                track.style.transform = 'translateX(-' + offset + 'px)';

                // Update dots
                dots.forEach(function(dot, i) {
                    const isActive = i === currentIndex;
                    dot.style.backgroundColor = isActive ? 'var(--landing-theme-color)' : '#d1d5db';
                });

                // Update button states
                if (prevBtn) {
                    prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
                    prevBtn.style.pointerEvents = currentIndex === 0 ? 'none' : 'auto';
                }
                if (nextBtn) {
                    nextBtn.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
                    nextBtn.style.pointerEvents = currentIndex >= maxIndex ? 'none' : 'auto';
                }
            }

            function goToSlide(index) {
                const maxIndex = Math.max(0, items.length - itemsPerView);
                currentIndex = Math.max(0, Math.min(index, maxIndex));
                updateCarousel();
            }

            function nextSlide() {
                goToSlide(currentIndex + 1);
            }

            function prevSlide() {
                goToSlide(currentIndex - 1);
            }

            // Event listeners
            if (prevBtn) {
                prevBtn.addEventListener('click', prevSlide);
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', nextSlide);
            }
            dots.forEach(function(dot, i) {
                dot.addEventListener('click', function() {
                    goToSlide(i);
                });
            });

            // Handle resize
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    calculateDimensions();
                    currentIndex = Math.min(currentIndex, Math.max(0, items.length - itemsPerView));
                    updateCarousel();
                }, 100);
            });

            // Touch/swipe support
            let touchStartX = 0;
            let touchEndX = 0;

            track.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            track.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, { passive: true });

            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
            }

            // Initialize
            calculateDimensions();
            updateCarousel();
        });
    });
})();
</script>

<style>
.carousel-image {
    width: 100%;
    min-height: 180px;
    object-fit: cover;
    border-radius: 0.5rem;
}
.carousel-btn:focus {
    outline: none;
}
.carousel-btn:focus-visible {
    outline: 2px solid var(--landing-theme-color);
    outline-offset: 2px;
}
.carousel-dot:focus {
    outline: none;
}
.carousel-dot:focus-visible {
    outline: 2px solid var(--landing-theme-color);
    outline-offset: 2px;
}
</style>
