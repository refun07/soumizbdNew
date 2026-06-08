<?php

namespace App\Services;

class EmailImageService
{
    public static function makeEmailSafeLogo(?string $relativePath): ?string
    {
        if (blank($relativePath)) {
            return null;
        }

        $absolutePath = public_path($relativePath);
        if (!file_exists($absolutePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg'])) {
            return $absolutePath;
        }

        if (!extension_loaded('gd')) {
            return $absolutePath;
        }

        $cacheDir = storage_path('app/mail-cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $hash = md5($absolutePath . '|' . @filemtime($absolutePath));
        $outputPath = $cacheDir . DIRECTORY_SEPARATOR . "logo_email_safe_{$hash}.png";

        if (file_exists($outputPath)) {
            return $outputPath;
        }

        $source = match ($ext) {
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : null,
            'png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($absolutePath) : null,
            'gif' => function_exists('imagecreatefromgif') ? @imagecreatefromgif($absolutePath) : null,
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($absolutePath) : null,
            default => null,
        };

        if (!$source) {
            return $absolutePath;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $width, $height, $white);

        imagealphablending($canvas, true);
        imagesavealpha($canvas, false);
        imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);

        imagepng($canvas, $outputPath, 9);

        imagedestroy($source);
        imagedestroy($canvas);

        return $outputPath;
    }
}
