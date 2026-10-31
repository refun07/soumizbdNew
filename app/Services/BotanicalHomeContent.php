<?php

namespace App\Services;

class BotanicalHomeContent
{
    public static function schema(): array
    {
        return json_decode(file_get_contents(resource_path('storefront/home-content.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function content(bool $resolveImages = true): array
    {
        $saved = json_decode(getSetting('botanicalHomeContent', '{}'), true) ?: [];
        $content = [];
        foreach (self::schema() as $section => $definition) {
            foreach ($definition['fields'] as $key => $field) {
                $value = $saved[$section][$key] ?? $field['default'];
                if ($resolveImages && $field['type'] === 'image' && is_numeric($value)) {
                    $value = uploadedAsset($value);
                }
                $content[$section][$key] = $value;
            }
        }

        // Preserve existing Theme One content until each field is saved in the dedicated editor.
        $banners = json_decode(getSetting('themeOneMainBanners', '[]'), true) ?: [];
        $legacyHero = $banners[0] ?? [];
        foreach (['title' => 'title', 'description' => 'subTitle', 'link' => 'link'] as $field => $legacy) {
            if (!array_key_exists($field, $saved['hero'] ?? []) && !empty($legacyHero[$legacy])) {
                $content['hero'][$field] = $legacyHero[$legacy];
            }
        }
        $legacyImage = ($legacyHero['showForegroundImage'] ?? '0') === '1'
            ? ($legacyHero['foregroundImage'] ?? null) : ($legacyHero['backgroundImage'] ?? null);
        if (!array_key_exists('image', $saved['hero'] ?? []) && $legacyImage) {
            $content['hero']['image'] = $resolveImages ? uploadedAsset($legacyImage) : $legacyImage;
        }
        foreach (['hero' => 'showThemeOneMainBanner', 'categories' => 'showThemeOneFeaturedCategories', 'promotions' => 'showThemeOneSingleImageBanner', 'reviews' => 'showThemeOneReviewSection'] as $section => $setting) {
            if ($section === 'reviews' || !array_key_exists('enabled', $saved[$section] ?? [])) {
                $content[$section]['enabled'] = (string) getSetting($setting, '1') === '1';
            }
        }
        if (!array_key_exists('selection', $saved['categories'] ?? [])) {
            $content['categories']['selection'] = json_decode(getSetting('themeOneFeaturedCategories', '[]'), true) ?: [];
        }
        if ($resolveImages && $content['flash']['ends_at']) {
            $content['flash']['ends_at'] = \Carbon\Carbon::parse($content['flash']['ends_at'], 'Asia/Dhaka')->toIso8601String();
        }
        return StorefrontLinks::normalize($content);
    }

    public static function saleHasEnded(array $section): bool
    {
        return !empty($section['ends_at']) && \Carbon\Carbon::parse($section['ends_at'], 'Asia/Dhaka')->lessThanOrEqualTo(\Carbon\Carbon::now());
    }

    public static function saleProductIds(): array
    {
        $section = self::content(false)['flash'];
        if (self::saleHasEnded($section)) return [];
        $ids = $section['selection'] ?: (json_decode(getSetting('themeOneOnSaleProducts', '[]'), true) ?: []);
        $query = \App\Models\Product::fromPublishedShops()->isPublished()->hideOnEcom();
        if ($ids) $query->whereIn('id', $ids);
        return $query->orderByDesc('total_sale_count')->orderBy('id')->take($section['limit'])->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
