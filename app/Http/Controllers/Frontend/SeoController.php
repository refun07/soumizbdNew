<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Blog, Brand, Category, Page};
use App\Services\StorefrontSeo;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function metadata(Request $request, StorefrontSeo $seo)
    {
        // Resolve only a relative public URL; never fetch an arbitrary host.
        $target = $request->query('path', '/');
        abort_unless(is_string($target) && str_starts_with($target, '/') && !str_starts_with($target, '//') && !str_contains($target, '#'), 400);
        $data = $seo->forRequest(Request::create($target));
        unset($data['body'], $data['items']);
        return response()->json($data, $data['status'])->header('X-Robots-Tag', 'noindex');
    }

    public function sitemap(StorefrontSeo $seo)
    {
        $urls = array_map(fn ($path) => $seo->url($path), ['/', '/product', '/blogs', '/brands']);
        foreach (['product' => $seo->products(), 'categories' => Category::query(), 'blogs' => Blog::isPublished(), 'brands' => Brand::where('is_active', 1), 'pages' => Page::where('is_active', 1)] as $kind => $query) {
            foreach ($query->get() as $record) $urls[] = $seo->url('/' . $kind . '/' . $record->slug);
        }
        return response()->view('frontend.seo-sitemap', ['urls' => array_unique($urls)])->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(StorefrontSeo $seo)
    {
        $rules = ['User-agent: *'];
        if (!config('seo.indexable')) $rules[] = 'Disallow: /';
        else foreach (['/admin', '/api/', '/seo/', '/cart', '/checkout', '/dashboard', '/orders', '/wishlist', '/chats', '/settings', '/support', '/payment', '/seller'] as $path) $rules[] = 'Disallow: ' . $path;
        $rules[] = 'Sitemap: ' . $seo->url('/sitemap.xml');
        return response(implode("\n", $rules) . "\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function legacy(Request $request, string $kind, ?string $slug = null)
    {
        $target = match ($kind) { 'products', 'shop' => 'product', 'category' => 'categories', 'blog' => 'blogs' };
        // Unknown old slugs return 404 instead of redirecting every URL to the homepage.
        $path = '/' . $target . ($slug ? '/' . $slug : '');
        $seo = app(StorefrontSeo::class);
        abort_if($seo->forRequest(Request::create($path))['status'] === 404, 404);
        $query = $request->except(['accessToken']);
        // Source blog listings contain 12 articles per page; the new journal shows 20.
        if ($kind === 'blog' && !$slug && isset($query['page'])) {
            $page = intdiv((max(1, (int) $query['page']) - 1) * 12, 20) + 1;
            if ($page === 1) unset($query['page']);
            else $query['page'] = $page;
        }
        return redirect($path . ($query ? '?' . http_build_query($query) : ''), 301);
    }
}
