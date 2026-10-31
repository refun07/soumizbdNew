<?php

namespace App\Services;

use App\Models\{Blog, BlogCategory, Brand, Category, Page, Product, Shop};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorefrontSeo
{
    public function products()
    {
        return Product::fromPublishedShops()->isPublished()->hideOnEcom();
    }

    public function url(string $path = ''): string
    {
        return rtrim(config('app.url'), '/') . ($path === '' || $path === '/' ? '/' : '/' . ltrim($path, '/'));
    }

    private function plain(?string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($value ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    public function forRequest(Request $request): array
    {
        $path = '/' . trim($request->path(), '/');
        $name = config('app.name');
        $data = ['title' => getSetting('meta_title') ?: $name . ' | Skin, Hair & Beauty Products',
            'description' => getSetting('meta_description') ?: 'Shop skin, hair and beauty products at ' . $name . '. Explore our products and beauty journal.',
            'keywords' => getSetting('meta_keywords') ?: '', 'image' => uploadedAsset(getSetting('meta_image')) ?: $this->url('/images/storefront/botanical-hero.webp'),
            'canonical' => $this->url($path), 'type' => 'website', 'status' => 200,
            'heading' => $name, 'body' => '', 'items' => [], 'schemas' => [], 'indexable' => true];
        $record = null;
        if (preg_match('~^/concerns/([^/]+)$~', $path, $matches)) {
            $concern = StorefrontConcerns::find($matches[1]);
            if (!$concern) return $this->missing($data);
            $data['heading'] = $concern['label'];
            $data['title'] = $concern['label'] . ' | ' . $name;
            $data['description'] = 'Shop ' . $concern['label'] . ' products at ' . $name . '.';
            $data['image'] = $this->url($concern['image']);
            $this->listing($data, $this->products()->whereIn('slug', $concern['products']), $request, 'products');
        } elseif (preg_match('~^/(product|blogs|categories|brands|shops|pages)/([^/]+)$~u', $path, $matches)) {
            [$all, $kind, $slug] = $matches;
            $record = match ($kind) {
                'product' => $this->products()->where('slug', $slug)->first(),
                'blogs' => Blog::isPublished()->where('slug', $slug)->first(),
                'categories' => Category::where('slug', $slug)->first(),
                'brands' => Brand::where('is_active', 1)->where('slug', $slug)->first(),
                'shops' => Shop::isApproved()->isPublished()->where('slug', $slug)->first(),
                'pages' => Page::where('is_active', 1)->where('slug', $slug)->first(),
            };
            if (!$record) return $this->missing($data);
            $data['heading'] = $record->title ?: $record->name;
            $data['title'] = $record->meta_title ?: $data['heading'] . ' | ' . $name;
            $data['body'] = StorefrontLinks::html($record->description ?: $record->content ?: '');
            $data['description'] = $this->plain($record->meta_description ?: Str::limit($this->plain($record->short_description ?: $data['body']), 200)) ?: $data['description'];
            $data['keywords'] = $record->meta_keywords ?: '';
            $image = $record->meta_image ?: match ($kind) {
                'product', 'categories', 'brands' => $record->thumbnail_image,
                'blogs' => $record->banner ?: $record->thumbnail_image,
                'shops' => $record->logo,
                default => null,
            };
            if ($image) $data['image'] = uploadedAsset($image);
            $crumb = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $this->url()]];
            $crumb[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $data['heading'], 'item' => $data['canonical']];
            $data['schemas'][] = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumb];
            if ($kind === 'blogs') {
                $data['type'] = 'article';
                $article = ['@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $record->title,
                    'description' => $data['description'], 'image' => $data['image'], 'mainEntityOfPage' => $data['canonical'],
                    'publisher' => ['@type' => 'Organization', 'name' => $name, 'url' => $this->url()]];
                if ($record->created_at) $article['datePublished'] = $record->created_at->toIso8601String();
                $data['schemas'][] = $article;
            } elseif ($kind === 'product') {
                $offers = $record->variations->map(fn ($v) => ['@type' => 'Offer', 'url' => $data['canonical'],
                    'priceCurrency' => strtoupper(getSetting('currencyCode') ?: 'BDT'),
                    'price' => round(variationDiscountedPrice($record, $v, false), 2),
                    'availability' => 'https://schema.org/' . ($v->productVariationStocks->sum('stock_qty') > 0 ? 'InStock' : 'OutOfStock'),
                    'sku' => $v->sku, 'itemCondition' => 'https://schema.org/NewCondition'])->all();
                $product = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => $record->name,
                    'description' => $this->plain($data['body']) ?: $data['description'], 'image' => $data['image'], 'url' => $data['canonical']];
                if ($offers) $product['offers'] = $offers;
                if ($record->brand) $product['brand'] = ['@type' => 'Brand', 'name' => $record->brand->name];
                $data['schemas'][] = $product;
            } elseif ($kind === 'categories' || $kind === 'brands') {
                $products = $this->products();
                $kind === 'categories' ? $products->whereHas('categories', fn ($q) => $q->where('categories.id', $record->id)) : $products->where('brand_id', $record->id);
                $this->listing($data, $products, $request, 'products');
            }
        } elseif (in_array($path, ['/', '/product', '/blogs', '/brands', '/shops', '/featured-products', '/on-sale-products'], true)) {
            $labels = ['/' => $name, '/product' => 'All Products', '/blogs' => 'Beauty Journal', '/brands' => 'Our Brands', '/shops' => 'Our Shops', '/featured-products' => 'Featured Products', '/on-sale-products' => 'Products on Sale'];
            $data['heading'] = $labels[$path];
            if ($path !== '/') {
                $data['title'] = $data['heading'] . ' | ' . $name;
                $data['description'] = 'Explore ' . strtolower($data['heading']) . ' at ' . $name . '.';
            }
            if ($path === '/blogs') {
                $query = Blog::isPublished();
                if ($request->filled('blogCategoryId')) {
                    $category = BlogCategory::find($request->integer('blogCategoryId'));
                    if (!$category) return $this->missing($data);
                    $query->where('blog_category_id', $category->id);
                    $data['title'] = $category->name . ' | Beauty Journal | ' . $name;
                    $data['canonical'] .= '?blogCategoryId=' . $category->id;
                }
                $this->listing($data, $query, $request, 'blogs');
            } elseif ($path === '/brands' || $path === '/shops') {
                $this->listing($data, $path === '/brands' ? Brand::where('is_active', 1) : Shop::isApproved()->isPublished(), $request, trim($path, '/'));
            } else {
                $products = $this->products();
                if (in_array($path, ['/featured-products', '/on-sale-products'], true)) {
                    $key = $path === '/featured-products' ? 'themeOneTopProducts' : 'themeOneOnSaleProducts';
                    $ids = json_decode(getSetting($key) ?: '[]', true);
                    $products->whereIn('id', is_array($ids) ? array_map('intval', $ids) : []);
                }
                $this->listing($data, $products, $path === '/' ? Request::create('/') : $request, 'products');
                if ($path === '/') {
                    unset($data['next']);
                    $data['schemas'][] = ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => $name, 'url' => $this->url()];
                    $organization = ['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => $name, 'url' => $this->url()];
                    if (getSetting('homeFooterCustomerSupport')) $organization['telephone'] = preg_replace('/[^+\d]/', '', getSetting('homeFooterCustomerSupport'));
                    if (getSetting('homeFooterEmail')) $organization['email'] = getSetting('homeFooterEmail');
                    if (getSetting('homeFooterAddress')) $organization['address'] = getSetting('homeFooterAddress');
                    $data['schemas'][] = $organization;
                    foreach (Category::isRoot()->get() as $category) $data['items'][] = ['title' => $category->name, 'url' => $this->url('/categories/' . $category->slug)];
                    foreach (Blog::isPublished()->limit(6)->get() as $blog) $data['items'][] = ['title' => $blog->title, 'url' => $this->url('/blogs/' . $blog->slug)];
                }
            }
        } elseif (preg_match('~^/shops/([^/]+)/(products|offers|profile)$~', $path, $matches)) {
            if (!Shop::isApproved()->isPublished()->where('slug', $matches[1])->exists()) return $this->missing($data);
            $data['indexable'] = false;
        } elseif (preg_match('~^/(cart|checkout|dashboard|orders|wishlist|chats|reviews|settings|support|seller|payment|discounts|coupons|campaigns)(/|$)~', $path)) {
            $data['title'] = ucfirst(explode('/', trim($path, '/'))[0]) . ' | ' . $name;
            $data['indexable'] = false;
        } else return $this->missing($data);
        // Search/filter combinations are useful to customers, but aren't separate landing pages.
        if (array_diff(array_keys($request->query()), ['page', 'blogCategoryId', 'utm_source', 'utm_medium', 'utm_campaign', 'gclid', 'fbclid'])) $data['indexable'] = false;
        $data['description'] = $this->plain($data['description']);
        $data['robots'] = $data['indexable'] && config('seo.indexable') ? 'index, follow, max-image-preview:large' : 'noindex, follow';
        return $data;
    }

    private function listing(array &$data, $query, Request $request, string $kind): void
    {
        $page = max(1, $request->integer('page', 1));
        $limit = $kind === 'blogs' ? 20 : 24;
        $total = (clone $query)->count();
        if ($page > max(1, (int) ceil($total / $limit))) { $data = $this->missing($data); return; }
        foreach ($query->orderBy('id')->forPage($page, $limit)->get() as $item) {
            $data['items'][] = ['title' => $item->title ?: $item->name, 'url' => $this->url('/' . ($kind === 'products' ? 'product' : $kind) . '/' . $item->slug)];
        }
        if ($page > 1) {
            $data['canonical'] .= (str_contains($data['canonical'], '?') ? '&' : '?') . 'page=' . $page;
            $data['title'] .= ' - Page ' . $page;
        }
        if ($page < ceil($total / $limit)) $data['next'] = $this->url('/' . trim($request->path(), '/')) . '?' . http_build_query(array_merge($request->only('blogCategoryId'), ['page' => $page + 1]));
    }

    private function missing(array $data): array
    {
        return array_merge($data, ['status' => 404, 'indexable' => false, 'robots' => 'noindex, follow', 'title' => 'Page not found | ' . config('app.name'), 'heading' => 'Page not found', 'body' => '', 'schemas' => [], 'items' => []]);
    }
}
