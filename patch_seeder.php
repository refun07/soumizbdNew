<?php
$content = file_get_contents('database/seeders/DevDataSeeder.php');

$startCat = strpos($content, "DB::table('categories')->insert(");
$endCat = strpos($content, ");", $startCat) + 2;

$newCat = "DB::table('categories')->insert(
            [
                ['id' => 1, 'name' => \"Women's Jewelry\", 'slug' => 'womens-jewelry', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/0b5a1752-e0ef-4590-aa98-92dc3b858b68', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
                ['id' => 2, 'name' => \"Women's Dress\", 'slug' => 'womens-dress', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/125f2f06-7495-49a4-b4a8-864417f80a87', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
                ['id' => 3, 'name' => \"Women's Care\", 'slug' => 'womens-care', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/fff0285b-c857-48bc-994d-acfaa5c38b3e', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
                ['id' => 4, 'name' => \"Men's Fashion\", 'slug' => 'mens-fashion', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/a13fb685-bf50-4265-b77d-c298626d5e07', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
                ['id' => 5, 'name' => \"Fashion Accessories\", 'slug' => 'fashion-accessories', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/a1d84469-d28c-4fad-ad74-f718c6308b1b', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
                ['id' => 6, 'name' => \"Women's Shoes\", 'slug' => 'womens-shoes', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/164b48d1-2335-4285-8313-3922f858b14b', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
                ['id' => 7, 'name' => \"Make Up\", 'slug' => 'make-up', 'parent_id' => 0, 'level' => 0, 'sorting_order_level' => 0, 'thumbnail_image' => 'https://www.figma.com/api/mcp/asset/0b5a1752-e0ef-4590-aa98-92dc3b858b68', 'icon' => null, 'total_sale_count' => 0, 'meta_title' => null, 'meta_image' => null, 'meta_description' => null],
            ]
        );";

$content = substr_replace($content, $newCat, $startCat, $endCat - $startCat);

$startTrans = strpos($content, "DB::table('category_translations')->insert(");
$endTrans = strpos($content, ");", $startTrans) + 2;

$newTrans = "DB::table('category_translations')->insert(
            [
                ['id' => 1, 'category_id' => 1, 'lang_key' => 'en-US', 'name' => \"Women's Jewelry\"],
                ['id' => 2, 'category_id' => 2, 'lang_key' => 'en-US', 'name' => \"Women's Dress\"],
                ['id' => 3, 'category_id' => 3, 'lang_key' => 'en-US', 'name' => \"Women's Care\"],
                ['id' => 4, 'category_id' => 4, 'lang_key' => 'en-US', 'name' => \"Men's Fashion\"],
                ['id' => 5, 'category_id' => 5, 'lang_key' => 'en-US', 'name' => \"Fashion Accessories\"],
                ['id' => 6, 'category_id' => 6, 'lang_key' => 'en-US', 'name' => \"Women's Shoes\"],
                ['id' => 7, 'category_id' => 7, 'lang_key' => 'en-US', 'name' => \"Make Up\"],
            ]
        );";

$content = substr_replace($content, $newTrans, $startTrans, $endTrans - $startTrans);

file_put_contents('database/seeders/DevDataSeeder.php', $content);
echo "Seeder updated\n";
