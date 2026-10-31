<?php

namespace App\Console\Commands;

use App\Models\{Brand, BrandTranslation, Category, CategoryTranslation, Language, MediaFile, Product, ProductTranslation, ProductVariation, ProductVariationCombination, ProductVariationStock, Role, Shop, SystemSetting, User, Variation, VariationTranslation, VariationValue, VariationValueTranslation, Warehouse};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Cache, DB};
use Illuminate\Support\Str;

class ImportSoumisCatalog extends Command
{
    protected $signature = 'catalog:import-soumis {--file=database/imports/soumis/catalog.json} {--shop=1} {--bootstrap-local : Initialize a new local shop, admin and storefront settings}';
    protected $description = 'Import the verified public Soumis catalog, images and variant stock without creating orders';

    public function handle(): int
    {
        $path = base_path($this->option('file'));
        if (!is_file($path)) {
            $this->error('Catalog manifest is missing. Run scripts/catalog/scrape_soumis.py first.');
            return self::FAILURE;
        }
        $catalog = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (($catalog['source'] ?? '') !== 'https://soumisbangladesh.com' || !empty($catalog['failures']) || empty($catalog['products'])) {
            $this->error('The source manifest is incomplete or has unresolved failures.');
            return self::FAILURE;
        }
        foreach ($catalog['images'] as $file) {
            if (!is_file(public_path($file))) {
                $this->error('Missing downloaded image: ' . $file);
                return self::FAILURE;
            }
        }
        if ($this->option('bootstrap-local') && (!app()->environment('local') || Product::exists() || User::exists() || Shop::exists())) {
            $this->error('Local initialization is only allowed in a new, empty local application.');
            return self::FAILURE;
        }
        $password = null;
        $report = DB::transaction(function () use ($catalog, &$password) {
            $shopId = (int) $this->option('shop');
            if ($this->option('bootstrap-local')) {
                $password = Str::random(24);
                $admin = User::create(['name' => 'Shomiz Admin', 'email' => 'admin@shomiz.test', 'password' => $password, 'user_type' => 'admin', 'role_id' => 1, 'shop_id' => $shopId, 'email_or_otp_verified' => 1, 'email_verified_at' => now()]);
                $role = Role::where('name', 'Super Admin')->firstOrFail();
                $admin->assignRole($role);
                Shop::create(['id' => $shopId, 'user_id' => $admin->id, 'name' => 'Shomiz', 'slug' => 'shomiz', 'manage_stock_by' => 'default', 'is_approved' => 1, 'is_verified_by_admin' => 1, 'is_published' => 1]);
                Language::firstOrCreate(['code' => 'en-US'], ['name' => 'English', 'flag' => 'en', 'is_rtl' => 0, 'is_active' => 1]);
            }
            $shop = Shop::findOrFail($shopId);
            $warehouse = Warehouse::firstOrCreate(['shop_id' => $shopId, 'is_default' => 1], ['name' => 'Default warehouse', 'is_active' => 1]);
            $media = [];
            foreach ($catalog['images'] as $url => $file) {
                $record = MediaFile::updateOrCreate(['media_file' => $file], ['media_size' => filesize(public_path($file)), 'media_type' => 'image', 'media_name' => basename(parse_url($url, PHP_URL_PATH)), 'media_extension' => pathinfo($file, PATHINFO_EXTENSION)]);
                $media[$file] = $record->id;
            }
            $categories = [];
            foreach ($catalog['categories'] as $index => $source) {
                $category = Category::updateOrCreate(['slug' => $source['slug'], 'shop_id' => $shopId], ['name' => $source['name'], 'level' => 0, 'sorting_order_level' => $index, 'thumbnail_image' => $media[$source['local_image']] ?? null]);
                CategoryTranslation::updateOrCreate(['category_id' => $category->id, 'lang_key' => 'en-US'], ['name' => $category->name, 'thumbnail_image' => $category->thumbnail_image]);
                $categories[$source['source_url']] = $category;
            }
            foreach ($catalog['categories'] as $source) {
                $category = $categories[$source['source_url']];
                $parent = $categories[$source['parent_url'] ?? ''] ?? null;
                $byUrl = array_column($catalog['categories'], null, 'source_url');
                $depth = 0; $next = $source['parent_url']; $seen = [];
                while ($next && isset($byUrl[$next])) {
                    if (isset($seen[$next])) throw new \RuntimeException('Cyclic source category hierarchy.');
                    $seen[$next] = true; $depth++; $next = $byUrl[$next]['parent_url'];
                }
                if ($parent && $parent->id !== $category->id) {
                    $category->update(['parent_id' => $parent->id, 'level' => $depth]);
                }
            }
            $created = 0; $updated = 0; $variantCount = 0; $productIds = [];
            foreach ($catalog['products'] as $source) {
                $brand = null;
                if (!empty($source['brand'])) {
                    $brand = Brand::firstOrCreate(['slug' => Str::slug($source['brand'])], ['name' => $source['brand'], 'is_active' => 1]);
                    BrandTranslation::updateOrCreate(['brand_id' => $brand->id, 'lang_key' => 'en-US'], ['name' => $brand->name]);
                }
                $gallery = array_map(fn ($file) => $media[$file], $source['local_gallery']);
                if (!$gallery) throw new \RuntimeException('Product has no local image: ' . $source['source_url']);
                $variants = $source['variants'];
                $product = Product::firstOrNew(['shop_id' => $shopId, 'slug' => $source['slug']]);
                $product->exists ? $updated++ : $created++;
                $product->fill(['name' => $source['name'], 'brand_id' => $brand?->id, 'thumbnail_image' => $gallery[0], 'gallery_images' => implode(',', $gallery), 'description' => $source['description'], 'meta_title' => $source['meta_title'], 'meta_description' => $source['meta_description'], 'meta_keywords' => $source['meta_keywords'], 'min_price' => min(array_column($variants, 'price')), 'max_price' => max(array_column($variants, 'price')), 'stock_qty' => array_sum(array_column($variants, 'stock_qty')), 'is_published' => 1, 'hide_on_ecom' => 0, 'min_purchase_qty' => 1, 'max_purchase_qty' => 0, 'has_variation' => !empty($source['attributes']), 'has_warranty' => 0, 'has_emi' => 0]);
                $product->save();
                ProductTranslation::updateOrCreate(['product_id' => $product->id, 'lang_key' => 'en-US'], ['name' => $source['name'], 'description' => $source['description']]);
                $product->categories()->sync(array_values(array_filter(array_map(fn ($url) => $categories[$url]->id ?? null, $source['category_urls']))));
                $attributes = [];
                foreach ($source['attributes'] as $attribute) {
                    $variation = Variation::firstOrCreate(['name' => $attribute['name']], ['is_active' => 1]);
                    VariationTranslation::updateOrCreate(['variation_id' => $variation->id, 'lang_key' => 'en-US'], ['name' => $variation->name]);
                    $attributes[$attribute['name']] = ['model' => $variation, 'values' => []];
                    foreach ($attribute['values'] as $value) {
                        $model = VariationValue::firstOrCreate(['variation_id' => $variation->id, 'name' => $value], ['is_active' => 1]);
                        VariationValueTranslation::updateOrCreate(['variation_value_id' => $model->id, 'lang_key' => 'en-US'], ['name' => $value]);
                        $attributes[$attribute['name']]['values'][$value] = $model;
                    }
                    foreach ($source['category_urls'] as $url) {
                        if (isset($categories[$url])) $categories[$url]->variations()->syncWithoutDetaching([$variation->id]);
                    }
                }
                foreach ($variants as $index => $sourceVariant) {
                    $combos = [];
                    foreach ($sourceVariant['options'] as $attribute => $value) {
                        $variation = $attributes[$attribute]['model'];
                        $variationValue = $attributes[$attribute]['values'][$value];
                        $combos[$variation->id] = $variationValue->id;
                    }
                    ksort($combos);
                    $code = $combos ? implode('/', array_map(fn ($key, $value) => $key . ':' . $value, array_keys($combos), $combos)) . '/' : null;
                    $variation = ProductVariation::firstOrNew(['product_id' => $product->id, 'code' => $code]);
                    $variation->fill(['price' => $sourceVariant['price'], 'image' => $gallery[0], 'sku' => mb_substr(($source['sku'] ?: 'SOUMIS-' . $source['source_id']) . (count($variants) > 1 ? '-' . $sourceVariant['source_code'] : ''), 0, 255), 'discount_value' => 0, 'discount_type' => 'flat']);
                    $variation->save();
                    foreach ($combos as $attributeId => $valueId) {
                        ProductVariationCombination::updateOrCreate(['product_id' => $product->id, 'product_variation_id' => $variation->id, 'variation_id' => $attributeId], ['variation_value_id' => $valueId]);
                    }
                    ProductVariationStock::updateOrCreate(['product_variation_id' => $variation->id, 'warehouse_id' => $warehouse->id], ['stock_qty' => $sourceVariant['stock_qty']]);
                    $variantCount++;
                }
                $productIds[$source['source_id']] = $product->id;
            }
            if ($this->option('bootstrap-local')) {
                $featured = array_values(array_filter(array_map(fn ($source) => empty($source['parent_url']) && count($source['product_urls']) > 0 ? $categories[$source['source_url']]->id : null, $catalog['categories'])));
                $settings = ['currencyCode' => 'BDT', 'currencySymbol' => '৳', 'currencySymbolAlignment' => '1', 'numOfDecimals' => '0', 'defaultTheme' => 'theme-one', 'defaultHeader' => 'header-one', 'defaultProductCard' => 'product-card-one', 'cartVisibilityToggle' => '0', 'showThemeOneMainBanner' => '1', 'showThemeOneFeaturedCategories' => '1', 'themeOneFeaturedCategories' => json_encode(array_slice($featured, 0, 6)), 'showThemeOneSingleImageBanner' => '1', 'showThemeOneReviewSection' => '0', 'showHomeFeaturedBrands' => '0', 'showHomepagePopup' => '0', 'hideProductSections' => '0', 'cod_activation' => '1', 'primaryColor' => '#087c3e', 'secondaryColor' => '#e9f3ed', 'allowBackorder' => '0'];
                foreach ($settings as $key => $value) SystemSetting::updateOrCreate(['type' => $key, 'lang_key' => 'en-US'], ['value' => $value]);
            }
            return ['source' => $catalog['source'], 'imported_at' => now()->toIso8601String(), 'database' => DB::connection()->getDatabaseName(), 'shop_id' => $shop->id, 'created' => $created, 'updated' => $updated, 'products' => count($productIds), 'variants' => $variantCount, 'categories' => count($categories), 'media_files' => count($media), 'product_id_map' => $productIds, 'unavailable_source_urls' => $catalog['unavailable']];
        });
        Cache::forget('settings'); Cache::forget('publishedShopsIds');
        if ($password !== null) {
            $credentials = storage_path('app/local-admin-credentials.txt');
            file_put_contents($credentials, "Local development only\nURL: " . config('app.url') . "/admin\nEmail: admin@shomiz.test\nPassword: " . $password . "\n");
            chmod($credentials, 0600);
            $this->info('Local admin details saved to storage/app/local-admin-credentials.txt');
        }
        file_put_contents(base_path('database/imports/soumis/import-report.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->info(json_encode(array_diff_key($report, ['product_id_map' => true, 'unavailable_source_urls' => true]), JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
