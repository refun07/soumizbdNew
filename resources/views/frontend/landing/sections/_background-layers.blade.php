{{-- Reusable Background Layers Component for Landing Page Sections --}}
{{-- Usage: @include('frontend.landing.sections._background-layers', ['data' => $section_data]) --}}

@php
    // Extract all background settings
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
    $bgImageUrl = $data['background_image_url'] ?? null;
    $bgImagePosition = $data['background_image_position'] ?? 'center';
    $bgImageSize = $data['background_image_size'] ?? 'cover';
    $bgImageRepeat = $data['background_image_repeat'] ?? 'no-repeat';
    $bgImageOpacity = (int)($data['background_image_opacity'] ?? 100) / 100;
    $bgImageOverlayColor = $data['background_image_overlay_color'] ?? null;
    $bgImageOverlayOpacity = (int)($data['background_image_overlay_opacity'] ?? 50) / 100;
    $bgImageParallax = $data['background_image_parallax'] ?? false;
    $bgPattern = $data['background_pattern'] ?? null;
    
    // Position mapping
    $positionMap = [
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
    
    // Pattern definitions
    $patterns = [
        'dots' => ['image' => 'radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px)', 'size' => '20px 20px'],
        'grid' => ['image' => 'linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px)', 'size' => '30px 30px'],
        'diagonal-lines' => ['image' => 'repeating-linear-gradient(45deg, rgba(255,255,255,0.1) 0px, rgba(255,255,255,0.1) 2px, transparent 2px, transparent 10px)'],
        'waves' => ['image' => 'repeating-radial-gradient(circle at 0 0, transparent 0, rgba(255,255,255,0.05) 10px), repeating-linear-gradient(rgba(255,255,255,0.05), rgba(255,255,255,0.05))'],
        'hexagons' => ['image' => 'conic-gradient(from 60deg at 56.25% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 180deg at 43.75% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 300deg at calc(100%/12) 50%, rgba(255,255,255,0.1) 60deg, transparent 0%)', 'size' => '40px 35px'],
        'dots-dark' => ['image' => 'radial-gradient(circle, rgba(0,0,0,0.15) 1px, transparent 1px)', 'size' => '20px 20px'],
        'grid-dark' => ['image' => 'linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px)', 'size' => '30px 30px'],
    ];
@endphp

@if($bgImageUrl)
    <div style="
        position: absolute; 
        top: 0; 
        left: 0; 
        right: 0; 
        bottom: 0; 
        width: 100%; 
        min-height: 100%;
        background-image: url('{{ $bgImageUrl }}'); 
        background-position: {{ $positionMap[$bgImagePosition] ?? 'center center' }}; 
        background-size: {{ $bgImageSize }}; 
        background-repeat: {{ $bgImageRepeat }}; 
        opacity: {{ $bgImageOpacity }}; 
        z-index: 0;
        {{ $bgImageParallax ? 'background-attachment: fixed;' : '' }}
    "></div>
@endif

@if($bgImageOverlayColor)
    <div style="
        position: absolute; 
        top: 0; 
        left: 0; 
        right: 0; 
        bottom: 0; 
        width: 100%; 
        min-height: 100%;
        background-color: {{ $bgImageOverlayColor }}; 
        opacity: {{ $bgImageOverlayOpacity }}; 
        z-index: 1;
    "></div>
@endif

@if($bgPattern && isset($patterns[$bgPattern]))
    <div style="
        position: absolute; 
        top: 0; 
        left: 0; 
        right: 0; 
        bottom: 0; 
        width: 100%; 
        min-height: 100%;
        background-image: {{ $patterns[$bgPattern]['image'] }}; 
        {{ isset($patterns[$bgPattern]['size']) ? 'background-size: ' . $patterns[$bgPattern]['size'] . ';' : '' }} 
        z-index: 2; 
        pointer-events: none;
    "></div>
@endif
