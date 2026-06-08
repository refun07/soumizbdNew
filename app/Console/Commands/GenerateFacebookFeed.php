<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Writer;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateFacebookFeed extends Command
{
    protected $signature = 'feed:facebook';
    protected $description = 'Generate Facebook Catalogue CSV feed';

    // public function handle()
    // {
    //     $path = storage_path('app/feed/products.csv');

    //     if (!file_exists(dirname($path))) {
    //         mkdir(dirname($path), 0755, true);
    //     }

    //     $csv = Writer::createFromString('');



    //     /**
    //      * ------------------------
    //      * CSV HEADER
    //      * ------------------------
    //      */
    //     $csv->insertOne([
    //         'id',
    //         'title',
    //         'description',
    //         'availability',
    //         'condition',
    //         'price',
    //         'link',
    //         'image_link',
    //         'brand',
    //         'google_product_category',
    //         'fb_product_category',
    //         'quantity_to_sell_on_facebook',
    //         'sale_price',
    //         'sale_price_effective_date',
    //         'item_group_id',
    //         'gender',
    //         'color',
    //         'size',
    //         'age_group',
    //         'material',
    //         'pattern',
    //         'shipping',
    //         'shipping_weight',
    //         'gtin',
    //         'identifier_exists',
    //         'video[0].url',
    //         'video[0].tag[0]',
    //         'product_tags[0]',
    //         'product_tags[1]',
    //         'style[0]'
    //     ]);

    //     /**
    //      * ------------------------
    //      * LOAD ALL PRODUCTS
    //      * ------------------------
    //      */
    //     $products = Product::fromPublishedShops()
    //         ->isPublished()
    //         ->with(['brand', 'categories', 'variations.combinations.variationValue', 'tags', 'shopInfo'])
    //         ->get();


    //     Log::info($products->toArray());
    //     /** fallback warehouse for CLI jobs */
    //     $warehouseIds = [1];




    //     foreach ($products as $product) {

    //         $data = (new \App\Http\Resources\ProductResource($product))->toArray(request());




    //         /** BASIC FIELDS */
    //         $parentId = $product->id;
    //         $title = $data['name'] ?? '';
    //         $description = strip_tags($product->collectTranslation('description') ?? '');
    //         $description = $description ?: $title;

    //         $title = strlen($title) > 65 ? substr($title, 0, 62) . '...' : $title;

    //         $priceValue = $data['max_price'] ?? 0;
    //         $price = number_format($priceValue, 2, '.', '') . ' BDT';



    //         $link = route('product', $data['slug']);
    //         $image = $data['thumbnailImg'] ?? '';

    //         $image = $image ?: url('/default-image.jpg');


    //         if (
    //             isset($data['brand']) &&
    //             is_array($data['brand']) &&
    //             !empty($data['brand']['name'])
    //         ) {
    //             $brand = $data['brand']['name'];
    //         } else {
    //             $brand = 'EpikCart';
    //         }


    //         /** FIX: must be standard */
    //         $googleCategory = "Animals & Pet Supplies";
    //         $fbCategory = "Animals & Pet Supplies > Pet Supplies";

    //         $availability = $data['stockQty'] > 0 ? 'in stock' : 'out of stock';

    //         /** FIX: GTIN / identifier_exists */
    //         $gtin = '';
    //         $identifierExists = 'no';

    //         /** Sale price handling */
    //         $saleEffective = '';
    //         if (!empty($product->discount_start_date) && !empty($product->discount_end_date)) {
    //             $saleEffective =
    //                 Carbon::createFromTimestamp($product->discount_start_date)->toIso8601String()
    //                 . '/' .
    //                 Carbon::createFromTimestamp($product->discount_end_date)->toIso8601String();
    //         }

    //         /** TAGS */
    //         $tag1 = $product->tags->get(0)->name ?? '';
    //         $tag2 = $product->tags->get(1)->name ?? '';

    //         /**
    //          * --------------------------------------
    //          * INSERT PARENT PRODUCT ROW
    //          * --------------------------------------
    //          */
    //         $csv->insertOne([
    //             $parentId,
    //             $title,
    //             $description,
    //             $availability,
    //             'new',
    //             $price,
    //             $link,
    //             $image,
    //             $brand,
    //             $googleCategory,
    //             $fbCategory,
    //             '',
    //             '',
    //             $saleEffective,
    //             $parentId,
    //             '',
    //             '',
    //             '',
    //             '',
    //             '',
    //             '',
    //             '',
    //             '',
    //             $gtin,
    //             $identifierExists,
    //             $data['videoLink'] ?? '',
    //             '',
    //             $tag1,
    //             $tag2,
    //             ''
    //         ]);

    //         /**
    //          * --------------------------------------
    //          * VARIATION ROWS
    //          * --------------------------------------
    //          */
    //         foreach ($product->variations as $variation) {

    //             /** Variant ID */
    //             $variantId = $product->id . '-' . $variation->id;

    //             /** Build variant title */
    //             $attrNames = $variation->combinations->map(function ($c) {
    //                 return $c->variationValue->collectTranslation('name');
    //             })->toArray();

    //             $variantTitle = $title . (count($attrNames) ? ' - ' . implode(', ', $attrNames) : '');
    //             $variantTitle = strlen($variantTitle) > 65 ? substr($variantTitle, 0, 62) . '...' : $variantTitle;

    //             /** Price & discount */
    //             $basePrice = (float) $variation->price;

    //             $discount = 0;
    //             if ($variation->discount_value > 0) {
    //                 $discount = $variation->discount_type === 'percent'
    //                     ? ($basePrice * $variation->discount_value) / 100
    //                     : $variation->discount_value;
    //             }

    //             $finalPrice = $basePrice - $discount;
    //             $finalPrice = max($finalPrice, 0);

    //             $formattedPrice = number_format($finalPrice, 2) . ' BDT';
    //             $formattedSalePrice = $discount > 0 ? $formattedPrice : '';

    //             /** Variation stock FIXED */
    //             $variationStock = $variation->productVariationStocks()
    //                 ->whereIn('warehouse_id', $warehouseIds)
    //                 ->sum('stock_qty');

    //             $variantAvailability = $variationStock > 0 ? 'in stock' : 'out of stock';

    //             /** Variation image */
    //             $variantImage = $variation->image
    //                 ? uploadedAsset($variation->image)
    //                 : $image;

    //             /** Extract color + size (optional) */
    //             $color = '';
    //             $size = '';

    //             foreach ($variation->combinations as $combo) {
    //                 $value = strtolower($combo->variationValue->collectTranslation('name'));

    //                 if (in_array($value, ['red', 'blue', 'green', 'black', 'white'])) {
    //                     $color = ucfirst($value);
    //                 }

    //                 if (in_array($value, ['s', 'm', 'l', 'xl'])) {
    //                     $size = strtoupper($value);
    //                 }
    //             }

    //             /** Tags */
    //             $tag1 = $product->tags->get(0)->name ?? '';
    //             $tag2 = $product->tags->get(1)->name ?? '';

    //             /**
    //              * -----------------------------------
    //              * INSERT VARIANT ROW
    //              * -----------------------------------
    //              */
    //             $csv->insertOne([
    //                 $variantId,
    //                 $variantTitle,
    //                 $description,
    //                 $variantAvailability,
    //                 'new',
    //                 $formattedPrice,
    //                 $link,
    //                 $variantImage,
    //                 $brand,
    //                 $googleCategory,
    //                 $fbCategory,
    //                 '',
    //                 $formattedSalePrice,
    //                 '',
    //                 $parentId,
    //                 '',
    //                 $color,
    //                 $size,
    //                 '',
    //                 '',
    //                 '',
    //                 '',
    //                 '',
    //                 '',
    //                 $gtin,
    //                 $identifierExists,
    //                 $data['videoLink'] ?? '',
    //                 '',
    //                 $tag1,
    //                 $tag2,
    //                 ''
    //             ]);
    //         }
    //     }

    //     file_put_contents($path, $csv->toString());
    //     $this->info("Facebook feed generated successfully → " . $path);
    // }

    public function handle()
    {
        $path = storage_path('app/feed/products.csv');

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $csv = Writer::createFromString('');

        /**
         * ------------------------
         * CSV HEADER
         * ------------------------
         */
        $csv->insertOne([
            'id',
            'title',
            'description',
            'availability',
            'condition',
            'price',
            'link',
            'image_link',
            'brand',
            'google_product_category',
            'fb_product_category',
            'quantity_to_sell_on_facebook',
            'sale_price',
            'sale_price_effective_date',
            'item_group_id',
            'gender',
            'color',
            'size',
            'age_group',
            'material',
            'pattern',
            'shipping',
            'shipping_weight',
            'gtin',
            'identifier_exists',
            'video[0].url',
            'video[0].tag[0]',
            'product_tags[0]',
            'product_tags[1]',
            'style[0]',
        ]);

        /**
         * ------------------------
         * LOAD PRODUCTS (MULTIVENDOR)
         * ------------------------
         */
        $products = Product::fromPublishedShops()
            ->isPublished()
            ->with([
                'brand',
                'shopInfo', // THIS IS THE CORRECT RELATION
                'categories',
                'variations.combinations.variationValue',
                'tags'
            ])
            ->get();

        /** fallback warehouse for CLI jobs */
        $warehouseIds = [1];




        foreach ($products as $product) {

            $data = (new \App\Http\Resources\ProductResource($product))
                ->toArray(request());

            /**
             * ------------------------
             * BASIC FIELDS
             * ------------------------
             */
            $parentId = $product->id;

            $title = $data['name'] ?? '';
            $title = strlen($title) > 65 ? substr($title, 0, 62) . '...' : $title;

            $description = strip_tags(
                $product->collectTranslation('description') ?? ''
            );
            $description = $description ?: $title;

            /**
             * ------------------------
             * PRICE (MAX PRICE ONLY)
             * ------------------------video
             */
            $priceValue = $data['max_price'] ?? 0;
            $price = number_format($priceValue, 2, '.', '') . ' BDT';

            /**
             * ------------------------
             * LINK & IMAGE
             * ------------------------
             */
            $link = route('product', $data['slug']);

            $image = $data['thumbnailImg'] ?? null;
            $image = $image ?: url('/default-image.jpg');

            /**
             * ------------------------
             * BRAND (CORRECT MULTIVENDOR LOGIC)
             * ------------------------
             */
            if ($product->brand && !empty($product->brand->name)) {
                $brand = $product->brand->name;
            } elseif ($product->shopInfo && !empty($product->shopInfo->name)) {
                $brand = $product->shopInfo->name;
            } else {
                $brand = 'EpikCart';
            }

            /**
             * ------------------------
             * CATEGORIES (TEMP DEFAULT)
             * ------------------------
             */
            $categoryMap = [

                //  Lighting
                'light and lamp'        => 'Home & Garden > Lighting',
                'table lamps'          => 'Home & Garden > Lighting > Lamps',

                // Furniture
                'drawing furniture'    => 'Furniture',
                'bookshelves'          => 'Furniture > Shelving',

                //  Home Decor
                'flower base'          => 'Home & Garden > Decor > Vases',
                'wall hanging decor'   => 'Home & Garden > Decor > Wall Decor',

                //  Fashion & Apparel
                'ladies'               => 'Apparel & Accessories > Clothing',
                'panjabi'              => 'Apparel & Accessories > Clothing > Traditional & Ceremonial Clothing',
                'shari'                => 'Apparel & Accessories > Clothing > Traditional & Ceremonial Clothing',
                'kids dress'           => 'Apparel & Accessories > Clothing > Children\'s Clothing',

                //  Accessories
                'watch'                => 'Apparel & Accessories > Jewelry > Watches',

                // 📱 Electronics
                'mobile and accecories' => 'Electronics > Communications > Telephony',
                'camera'               => 'Electronics > Cameras & Optics > Cameras',
                'gadget'               => 'Electronics > Electronics Accessories',

                //  Beauty & Personal Care
                'body spray'           => 'Health & Beauty > Personal Care > Cosmetics',

                //  Grocery / Food
                'big fish'             => 'Food, Beverages & Tobacco > Food Items > Seafood',
                'soft drinks'          => 'Food, Beverages & Tobacco > Beverages > Soft Drinks',
            ];


            $googleCategory = 'Retail > General Merchandise';
            $fbCategory = 'Retail > General Merchandise';

            if ($product->categories->isNotEmpty()) {

                foreach ($product->categories as $cat) {
                    $key = strtolower(trim($cat->name));

                    if (isset($categoryMap[$key])) {
                        $googleCategory = $categoryMap[$key];
                        $fbCategory = $categoryMap[$key];
                        break;
                    }
                }

                // fallback if category not mapped yet
                if ($googleCategory === 'Retail > General Merchandise') {
                    $googleCategory = $product->categories->first()->name;
                    $fbCategory = $product->categories->first()->name;
                }
            }



            /**
             * ------------------------
             * AVAILABILITY (FIXED)
             * ------------------------
             */
            $availability = $product->stock_qty > 0
                ? 'in stock'
                : 'out of stock';

            /**
             * ------------------------
             * IDENTIFIERS
             * ------------------------
             */
            $gtin = '';
            $identifierExists = 'no';

            /**
             * ------------------------
             * SALE DATE
             * ------------------------
             */
            $saleEffective = '';
            if (!empty($product->discount_start_date) && !empty($product->discount_end_date)) {
                $saleEffective =
                    Carbon::createFromTimestamp($product->discount_start_date)->toIso8601String()
                    . '/' .
                    Carbon::createFromTimestamp($product->discount_end_date)->toIso8601String();
            }

            /**
             * ------------------------
             * TAGS
             * ------------------------
             */
            $tag1 = $product->tags->get(0)->name ?? '';
            $tag2 = $product->tags->get(1)->name ?? '';

            /**
             * ------------------------
             * INSERT PARENT ROW
             * ------------------------
             */
            $csv->insertOne([
                $parentId,
                $title,
                $description,
                $availability,
                'new',
                $price,
                $link,
                $image,
                $brand,
                $googleCategory,
                $fbCategory,
                '',
                '',
                $saleEffective,
                $parentId,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $gtin,
                $identifierExists,
                $data['videoLink'] ?? '',
                '',
                $tag1,
                $tag2,
                '',
            ]);

            /**
             * ------------------------
             * VARIATIONS
             * ------------------------
             */
            foreach ($product->variations as $variation) {

                $variantId = $product->id . '-' . $variation->id;

                $attrNames = $variation->combinations->map(function ($c) {
                    return $c->variationValue->collectTranslation('name');
                })->toArray();

                $variantTitle = $title . (count($attrNames)
                    ? ' - ' . implode(', ', $attrNames)
                    : '');

                $variantTitle = strlen($variantTitle) > 65
                    ? substr($variantTitle, 0, 62) . '...'
                    : $variantTitle;

                $basePrice = (float) $variation->price;
                $discount = 0;

                if ($variation->discount_value > 0) {
                    $discount = $variation->discount_type === 'percent'
                        ? ($basePrice * $variation->discount_value) / 100
                        : $variation->discount_value;
                }

                $finalPrice = max($basePrice - $discount, 0);
                $formattedPrice = number_format($finalPrice, 2) . ' BDT';
                $formattedSalePrice = $discount > 0 ? $formattedPrice : '';

                $variationStock = $variation->productVariationStocks()
                    ->whereIn('warehouse_id', $warehouseIds)
                    ->sum('stock_qty');

                $variantAvailability = $variationStock > 0
                    ? 'in stock'
                    : 'out of stock';

                $variantImage = $variation->image
                    ? uploadedAsset($variation->image)
                    : $image;

                $color = '';
                $size = '';

                foreach ($variation->combinations as $combo) {
                    $value = strtolower(
                        $combo->variationValue->collectTranslation('name')
                    );

                    if (in_array($value, ['red', 'blue', 'green', 'black', 'white'])) {
                        $color = ucfirst($value);
                    }

                    if (in_array($value, ['s', 'm', 'l', 'xl'])) {
                        $size = strtoupper($value);
                    }
                }

                $csv->insertOne([
                    $variantId,
                    $variantTitle,
                    $description,
                    $variantAvailability,
                    'new',
                    $formattedPrice,
                    $link,
                    $variantImage,
                    $brand,
                    $googleCategory,
                    $fbCategory,
                    '',
                    $formattedSalePrice,
                    '',
                    $parentId,
                    '',
                    $color,
                    $size,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $gtin,
                    $identifierExists,
                    $data['videoLink'] ?? '',
                    '',
                    $tag1,
                    $tag2,
                    '',
                ]);
            }
        }

        file_put_contents($path, $csv->toString());

        $this->info("Facebook feed generated successfully → {$path}");
    }
}
