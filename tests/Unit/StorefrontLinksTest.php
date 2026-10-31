<?php

namespace Tests\Unit;

use App\Services\StorefrontLinks;
use Tests\TestCase;

class StorefrontLinksTest extends TestCase
{
    public function test_saved_product_links_keep_filters_and_fragments(): void
    {
        config(['app.url' => 'https://store.example']);
        $this->assertSame(['menu' => ['/product?query=cream#results', 'https://store.example/product/cream']],
            StorefrontLinks::normalize(['menu' => ['/products?query=cream#results', 'https://store.example/products/cream']]));
    }

    public function test_other_routes_and_external_links_are_preserved(): void
    {
        config(['app.url' => 'https://store.example']);
        foreach (['/api/v1/products', '/shops/example/products', '/products-other', 'https://other.example/products/cream'] as $url) {
            $this->assertSame($url, StorefrontLinks::normalize($url));
        }
    }

    public function test_imported_article_links_use_the_singular_path(): void
    {
        $this->assertSame('<p><a href="/product/cream">Cream</a></p>',
            StorefrontLinks::html('<p><a href="/products/cream">Cream</a></p>'));
    }
}
