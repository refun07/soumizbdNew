<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$manifest = json_decode(file_get_contents(base_path('database/imports/soumis-blogs/blogs.json')), true, 512, JSON_THROW_ON_ERROR);
$issues = [];
foreach ($manifest['articles'] as $source) {
    $blog = App\Models\Blog::where('slug', $source['slug'])->first();
    if (!$blog) { $issues[] = 'Missing blog: ' . $source['slug']; continue; }
    foreach (['title', 'short_description', 'description', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
        if ($blog->{$field} !== $source[$field]) $issues[] = $source['slug'] . ': mismatch ' . $field;
    }
    if ($blog->blogCategory?->name !== $source['category']) $issues[] = $source['slug'] . ': category mismatch';
    if (!$blog->is_published) $issues[] = $source['slug'] . ': unpublished';
    if (!$source['published_at'] && $blog->created_at !== null) $issues[] = $source['slug'] . ': invented publication date';
    $translation = $blog->blogTranslations->where('lang_key', 'en-US')->first();
    foreach (['title', 'short_description', 'description'] as $field) {
        if ($translation?->{$field} !== $source[$field]) $issues[] = $source['slug'] . ': translation mismatch ' . $field;
    }
    foreach (['thumbnail_image' => 'local_thumbnail', 'banner' => 'local_banner', 'meta_image' => 'local_meta_image'] as $field => $sourceField) {
        $media = App\Models\MediaFile::find($blog->{$field});
        if ($media?->media_file !== $source[$sourceField] || !is_file(public_path($source[$sourceField]))) $issues[] = $source['slug'] . ': media mismatch ' . $field;
    }
}
$report = ['checked_at' => now()->toIso8601String(), 'verified_blogs' => count($manifest['articles']), 'database_blogs' => App\Models\Blog::count(), 'categories' => App\Models\BlogCategory::count(), 'images' => count($manifest['images']), 'products' => App\Models\Product::count(), 'orders' => App\Models\Order::count(), 'issues' => $issues];
file_put_contents(base_path('database/imports/soumis-blogs/database-verification.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit($issues ? 1 : 0);
