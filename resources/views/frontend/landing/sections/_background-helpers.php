<?php
// Shared pattern definitions for landing page sections
// Usage: include this in any blade template that needs patterns

if (!function_exists('getLandingPagePatterns')) {
    function getLandingPagePatterns() {
        return [
            'dots' => [
                'image' => 'radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px)',
                'size' => '20px 20px'
            ],
            'grid' => [
                'image' => 'linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px)',
                'size' => '30px 30px'
            ],
            'diagonal-lines' => [
                'image' => 'repeating-linear-gradient(45deg, rgba(255,255,255,0.1) 0px, rgba(255,255,255,0.1) 2px, transparent 2px, transparent 10px)'
            ],
            'waves' => [
                'image' => 'repeating-radial-gradient(circle at 0 0, transparent 0, rgba(255,255,255,0.05) 10px), repeating-linear-gradient(rgba(255,255,255,0.05), rgba(255,255,255,0.05))'
            ],
            'hexagons' => [
                'image' => 'conic-gradient(from 60deg at 56.25% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 180deg at 43.75% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 300deg at calc(100%/12) 50%, rgba(255,255,255,0.1) 60deg, transparent 0%)',
                'size' => '40px 35px'
            ],
            'dots-dark' => [
                'image' => 'radial-gradient(circle, rgba(0,0,0,0.15) 1px, transparent 1px)',
                'size' => '20px 20px'
            ],
            'grid-dark' => [
                'image' => 'linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px)',
                'size' => '30px 30px'
            ],
        ];
    }
}

if (!function_exists('getLandingPagePositionMap')) {
    function getLandingPagePositionMap() {
        return [
            'top-left' => 'left top',
            'top-center' => 'center top',
            'top-right' => 'right top',
            'center-left' => 'left center',
            'center' => 'center center',
            'center-right' => 'right center',
            'bottom-left' => 'left bottom',
            'bottom-center' => 'center bottom',
            'bottom-right' => 'right bottom',
        ];
    }
}

if (!function_exists('extractBackgroundSettings')) {
    function extractBackgroundSettings($data) {
        return [
            'bgColor' => $data['background_color'] ?? null,
            'bgImageUrl' => $data['background_image_url'] ?? null,
            'bgImagePosition' => $data['background_image_position'] ?? 'center',
            'bgImageSize' => $data['background_image_size'] ?? 'cover',
            'bgImageRepeat' => $data['background_image_repeat'] ?? 'no-repeat',
            'bgImageOpacity' => ($data['background_image_opacity'] ?? 100) / 100,
            'bgImageOverlayColor' => $data['background_image_overlay_color'] ?? null,
            'bgImageOverlayOpacity' => ($data['background_image_overlay_opacity'] ?? 50) / 100,
            'bgImageParallax' => $data['background_image_parallax'] ?? false,
            'bgPattern' => $data['background_pattern'] ?? null,
            'sectionId' => 'section-' . uniqid(),
        ];
    }
}
?>
