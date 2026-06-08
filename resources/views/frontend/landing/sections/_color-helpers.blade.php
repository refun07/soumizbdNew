@php
    // Helper function to get YouTube embed URL
    if (!function_exists('getYoutubeEmbedUrlLanding')) {
        function getYoutubeEmbedUrlLanding($url) {
            if (empty($url)) return null;
            $videoId = null;
            if (preg_match('/youtube\.com\/watch\?v=([^\&]+)/', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/youtu\.be\/([^\?]+)/', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/youtube\.com\/embed\/([^\?]+)/', $url, $matches)) {
                $videoId = $matches[1];
            }
            return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
        }
    }

    // Helper function to calculate luminance and get contrast color
    // Supports both 6-char hex (#RRGGBB) and 8-char hex with alpha (#RRGGBBAA)
    if (!function_exists('getContrastColorLanding')) {
        function getContrastColorLanding($hexColor, $lightColor = '#ffffff', $darkColor = '#1a1a1a') {
            if (empty($hexColor)) return $lightColor;
            $hexColor = ltrim($hexColor, '#');
            if (!in_array(strlen($hexColor), [6, 8])) return $lightColor;
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            return $luminance > 0.5 ? $darkColor : $lightColor;
        }
    }

    // Helper to get secondary text color (slightly different from main)
    // Supports both 6-char hex (#RRGGBB) and 8-char hex with alpha (#RRGGBBAA)
    if (!function_exists('getSecondaryColorLanding')) {
        function getSecondaryColorLanding($bgHexColor) {
            if (empty($bgHexColor)) return 'rgba(255,255,255,0.7)';
            $hexColor = ltrim($bgHexColor, '#');
            if (!in_array(strlen($hexColor), [6, 8])) return 'rgba(255,255,255,0.7)';
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            return $luminance > 0.5 ? '#4a4a4a' : 'rgba(255,255,255,0.7)';
        }
    }

    // Helper to convert hex color to rgba string
    // Supports both 6-char hex (#RRGGBB) and 8-char hex with alpha (#RRGGBBAA)
    // Returns rgba(r, g, b, a) format for use in CSS
    if (!function_exists('hexToRgbaLanding')) {
        function hexToRgbaLanding($hexColor) {
            if (empty($hexColor)) return 'transparent';
            $hexColor = ltrim($hexColor, '#');
            if (!in_array(strlen($hexColor), [6, 8])) return 'transparent';
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $a = strlen($hexColor) === 8 ? round(hexdec(substr($hexColor, 6, 2)) / 255, 2) : 1;
            return "rgba($r, $g, $b, $a)";
        }
    }

    // Helper to format a color value for CSS output
    // If the color has alpha (8-char hex), convert to rgba()
    // Otherwise return as-is (6-char hex with # prefix)
    if (!function_exists('formatColorForCssLanding')) {
        function formatColorForCssLanding($color) {
            if (empty($color)) return null;
            $hex = ltrim($color, '#');
            // If 8-char hex with alpha, convert to rgba
            if (strlen($hex) === 8) {
                return hexToRgbaLanding($color);
            }
            // Otherwise return as-is (6-char hex)
            return $color;
        }
    }

    // Helper to darken a color by a percentage
    // Supports both 6-char hex (#RRGGBB) and 8-char hex with alpha (#RRGGBBAA)
    if (!function_exists('darkenColorLanding')) {
        function darkenColorLanding($hexColor, $percent = 15) {
            if (empty($hexColor)) return $hexColor;
            $hexColor = ltrim($hexColor, '#');
            if (!in_array(strlen($hexColor), [6, 8])) return $hexColor;
            $r = hexdec(substr($hexColor, 0, 2));
            $g = hexdec(substr($hexColor, 2, 2));
            $b = hexdec(substr($hexColor, 4, 2));
            $hasAlpha = strlen($hexColor) === 8;
            $alpha = $hasAlpha ? substr($hexColor, 6, 2) : null;
            $amt = round(2.55 * $percent);
            $r = max(0, $r - $amt);
            $g = max(0, $g - $amt);
            $b = max(0, $b - $amt);
            return $hasAlpha 
                ? sprintf('#%02x%02x%02x%s', $r, $g, $b, $alpha)
                : sprintf('#%02x%02x%02x', $r, $g, $b);
        }
    }
@endphp
