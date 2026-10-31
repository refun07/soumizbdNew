<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$catalog = json_decode(file_get_contents(base_path('database/imports/soumis/catalog.json')), true, 512, JSON_THROW_ON_ERROR);
$issues = []; $stockOut = 0; $variants = 0;
foreach ($catalog['products'] as $source) {
    $product = App\Models\Product::where('slug', $source['slug'])->first();
    if (!$product) { $issues[] = 'Missing product: ' . $source['slug']; continue; }
    foreach (['name', 'description', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
        if ($product->{$field} !== $source[$field]) $issues[] = $source['slug'] . ': mismatch ' . $field;
    }
    $expectedStock = array_sum(array_column($source['variants'], 'stock_qty'));
    if ($product->stock_qty !== $expectedStock) $issues[] = $source['slug'] . ': product stock mismatch';
    if ($expectedStock === 0) $stockOut++;
    if ($product->variations->count() !== count($source['variants'])) $issues[] = $source['slug'] . ': variant count mismatch';
    foreach ($product->variations as $index => $variation) {
        $variants++;
        $sourceVariant = $source['variants'][$index];
        $expectedSku = mb_substr(($source['sku'] ?: 'SOUMIS-' . $source['source_id']) . (count($source['variants']) > 1 ? '-' . $sourceVariant['source_code'] : ''), 0, 255);
        if ($variation->sku !== $expectedSku) $issues[] = $source['slug'] . ': SKU mismatch';
        if ((float) $variation->price !== (float) $source['variants'][$index]['price']) $issues[] = $source['slug'] . ': price mismatch';
        if ((int) $variation->productVariationStocks->sum('stock_qty') !== $source['variants'][$index]['stock_qty']) $issues[] = $source['slug'] . ': variant stock mismatch';
    }
    $expectedCategories = array_column(array_filter($catalog['categories'], fn ($c) => in_array($c['source_url'], $source['category_urls'], true)), 'slug');
    $actualCategories = $product->categories->pluck('slug')->all(); sort($actualCategories); sort($expectedCategories);
    if ($actualCategories !== $expectedCategories) $issues[] = $source['slug'] . ': category membership mismatch';
    $actualGallery = array_filter(explode(',', $product->gallery_images));
    if (count($actualGallery) !== count($source['local_gallery'])) $issues[] = $source['slug'] . ': gallery mismatch';
    foreach ($actualGallery as $id) {
        $media = App\Models\MediaFile::find($id);
        if (!$media || !is_file(public_path($media->media_file))) $issues[] = $source['slug'] . ': image file missing';
    }
}
$report = ['checked_at' => now()->toIso8601String(), 'database_products' => App\Models\Product::count(), 'verified_products' => count($catalog['products']), 'verified_variants' => $variants, 'out_of_stock_products' => $stockOut, 'categories' => App\Models\Category::count(), 'root_categories' => App\Models\Category::whereNull('parent_id')->count(), 'media_files' => count($catalog['images']), 'orders' => App\Models\Order::count(), 'issues' => $issues];
file_put_contents(base_path('database/imports/soumis/database-verification.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit($issues ? 1 : 0);
