<?php
// Read-only deployment checks; temporary visibility changes roll back even on failure.
require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\{Blog, Product};
use App\Services\StorefrontSeo;
use App\Http\Controllers\Frontend\SeoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
$seo = app(StorefrontSeo::class); $controller = app(SeoController::class); $issues = [];
$check = function ($condition, $message) use (&$issues) { if (!$condition) $issues[] = $message; };
config(['seo.indexable' => true]);
$blog = Blog::isPublished()->firstOrFail(); $product = $seo->products()->firstOrFail();
$check(str_starts_with($seo->forRequest(Request::create('/blogs/' . $blog->slug))['robots'], 'index,'), 'Production article should be indexable');
$check(str_starts_with($seo->forRequest(Request::create('/checkout'))['robots'], 'noindex'), 'Checkout should not be indexed');
$check(str_starts_with($seo->forRequest(Request::create('/product?searchKey=cream'))['robots'], 'noindex'), 'Search should not be indexed');
$rules = $controller->robots($seo)->getContent();
$check(!str_contains($rules, "Disallow: /\n") && str_contains($rules, 'Disallow: /admin') && str_contains($rules, 'Sitemap:'), 'Production robots must allow public crawling and exclude private paths');
config(['seo.indexable' => false]);
$check(str_contains($controller->robots($seo)->getContent(), "Disallow: /\n"), 'Local copy must block indexing');
DB::beginTransaction();
try {
    $blog->is_published = 0; $blog->save();
    $product->hide_on_ecom = true; $product->save();
    $check($seo->forRequest(Request::create('/blogs/' . $blog->slug))['status'] === 404, 'Unpublished article should 404');
    $check($seo->forRequest(Request::create('/product/' . $product->slug))['status'] === 404, 'Hidden product should 404');
    $sitemap = $controller->sitemap($seo)->getContent();
    $check(!str_contains($sitemap, '/blogs/' . $blog->slug . '<'), 'Unpublished article leaked into sitemap');
    $check(!str_contains($sitemap, '/product/' . $product->slug . '<'), 'Hidden product leaked into sitemap');
} finally { DB::rollBack(); }
$report = ['production_indexing_rules' => 'checked', 'local_indexing_rules' => 'checked', 'hidden_content_exclusion' => 'checked', 'temporary_database_changes' => 'rolled back', 'issues' => $issues];
file_put_contents(base_path('artifacts/seo/visibility-audit.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode($report, JSON_PRETTY_PRINT) . PHP_EOL; exit($issues ? 1 : 0);
