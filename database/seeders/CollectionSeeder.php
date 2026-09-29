<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CollectionSeeder extends Seeder
{
    // Development data: collections shared across the dev brands, and some products put in them
    public function run()
    {
        // collection => brands (by slug) that carry it
        $collections = [
            'Smartphones'       => ['samsung', 'apple'],
            'Laptops & Tablets' => ['samsung', 'apple'],
            'Wearables'         => ['samsung', 'apple'],
            'Audio'             => ['apple', 'sony'],
            'TVs & Displays'    => ['samsung', 'sony', 'lg'],
            'Gaming'            => ['sony'],
            'Footwear'          => ['nike', 'adidas'],
        ];

        // product name => collection; products left out stay without a collection
        $productCollections = [
            'Samsung Galaxy S20 Ultra'     => 'Smartphones',
            'Samsung Galaxy S23 Ultra'     => 'Smartphones',
            'iPhone 14 Pro Max'            => 'Smartphones',
            'MacBook Air M2'               => 'Laptops & Tablets',
            'iPad Pro 12.9"'               => 'Laptops & Tablets',
            'Samsung Galaxy Tab S9'        => 'Laptops & Tablets',
            'Apple Watch Series 8'         => 'Wearables',
            'Samsung Galaxy Watch 5'       => 'Wearables',
            'AirPods Pro (2nd Generation)' => 'Audio',
            'Sony WH-1000XM5 Headphones'   => 'Audio',
            'Samsung 55" QLED TV'          => 'TVs & Displays',
            'LG 65" OLED TV'               => 'TVs & Displays',
            'Sony PlayStation VR2'         => 'Gaming',
            'Nike Air Max 270'             => 'Footwear',
            'Adidas Ultraboost 22'         => 'Footwear',
        ];

        $collectionIds = [];
        $sortOrder     = 0;
        foreach ($collections as $name => $brandSlugs) {
            $collection = ProductCollection::create([
                'name'       => $name,
                'slug'       => Str::slug($name),
                'sort_order' => $sortOrder++,
            ]);
            $collection->brands()->sync(Brand::whereIn('slug', $brandSlugs)->pluck('id'));

            $collectionIds[$name] = $collection->id;
        }

        foreach ($productCollections as $productName => $collectionName) {
            $product = Product::where('name', $productName)->first();
            if (!$product) {
                continue;
            }

            // only a collection the product's brand carries
            $isBrandCollection = ProductCollection::whereKey($collectionIds[$collectionName])
                ->whereHas('brands', fn ($query) => $query->where('brands.id', $product->brand_id))
                ->exists();

            if ($isBrandCollection) {
                $product->collection_id = $collectionIds[$collectionName];
                $product->saveQuietly();
            }
        }
    }
}
