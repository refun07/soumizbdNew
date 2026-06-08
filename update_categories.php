<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Categories
DB::table('categories')->delete();
DB::table('category_translations')->delete();

$categories = [
    ['id' => 1, 'name' => "Women's Jewelry", 'slug' => 'womens-jewelry', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/0b5a1752-e0ef-4590-aa98-92dc3b858b68'],
    ['id' => 2, 'name' => "Women's Dress", 'slug' => 'womens-dress', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/125f2f06-7495-49a4-b4a8-864417f80a87'],
    ['id' => 3, 'name' => "Women's Care", 'slug' => 'womens-care', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/fff0285b-c857-48bc-994d-acfaa5c38b3e'],
    ['id' => 4, 'name' => "Men's Fashion", 'slug' => 'mens-fashion', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/a13fb685-bf50-4265-b77d-c298626d5e07'],
    ['id' => 5, 'name' => "Fashion Accessories", 'slug' => 'fashion-accessories', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/a1d84469-d28c-4fad-ad74-f718c6308b1b'],
    ['id' => 6, 'name' => "Women's Shoes", 'slug' => 'womens-shoes', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/164b48d1-2335-4285-8313-3922f858b14b'],
    ['id' => 7, 'name' => "Make Up", 'slug' => 'make-up', 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/0b5a1752-e0ef-4590-aa98-92dc3b858b68'], // reusing first image as fallback
];

foreach ($categories as $cat) {
    DB::table('categories')->insert([
        'id' => $cat['id'],
        'name' => $cat['name'],
        'slug' => $cat['slug'],
        'parent_id' => 0,
        'level' => 0,
        'sorting_order_level' => 0,
        'thumbnail_image' => $cat['thumbnail_image'],
        'icon' => null,
        'total_sale_count' => 0,
    ]);

    DB::table('category_translations')->insert([
        'category_id' => $cat['id'],
        'lang_key' => 'en-US',
        'name' => $cat['name'],
    ]);
}

echo "Categories updated!\n";
