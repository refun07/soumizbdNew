<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing product variations
        DB::table('product_variations')->delete();

        // Create variations for products with has_variation = 1
        // Products: 3, 4, 6, 7, 11, 12, 15

        // Product 3: iPhone 14 Pro Max - Color variations
        $product3Variations = [
            ['product_id' => 3, 'sku' => 'IP14PM-128-BLK', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'IP14PM128BLK', 'price' => 1099.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 3, 'sku' => 'IP14PM-128-SLV', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'IP14PM128SLV', 'price' => 1099.00, 'discount_value' => 50, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 3, 'sku' => 'IP14PM-128-GLD', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'IP14PM128GLD', 'price' => 1099.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Product 4: MacBook Air M2 - Storage variations
        $product4Variations = [
            ['product_id' => 4, 'sku' => 'MBA-M2-256', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'MBAM2256', 'price' => 1199.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 4, 'sku' => 'MBA-M2-512', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'MBAM2512', 'price' => 1399.00, 'discount_value' => 100, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Product 6: Nike Air Max 270 - Size variations
        $product6Variations = [
            ['product_id' => 6, 'sku' => 'NAM270-8', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'NAM2708', 'price' => 150.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 6, 'sku' => 'NAM270-9', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'NAM2709', 'price' => 150.00, 'discount_value' => 10, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 6, 'sku' => 'NAM270-10', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'NAM27010', 'price' => 150.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 6, 'sku' => 'NAM270-11', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'NAM27011', 'price' => 150.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Product 7: Adidas Ultraboost 22 - Size variations
        $product7Variations = [
            ['product_id' => 7, 'sku' => 'UB22-8', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'UB228', 'price' => 180.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 7, 'sku' => 'UB22-9', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'UB229', 'price' => 180.00, 'discount_value' => 15, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 7, 'sku' => 'UB22-10', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'UB2210', 'price' => 180.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Product 11: Apple Watch Series 8 - Size variations
        $product11Variations = [
            ['product_id' => 11, 'sku' => 'AWS8-41MM', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'AWS841', 'price' => 399.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 11, 'sku' => 'AWS8-45MM', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'AWS845', 'price' => 429.00, 'discount_value' => 30, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Product 12: Samsung Galaxy Watch 5 - Size variations
        $product12Variations = [
            ['product_id' => 12, 'sku' => 'SGW5-40MM', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'SGW540', 'price' => 279.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 12, 'sku' => 'SGW5-44MM', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'SGW544', 'price' => 309.00, 'discount_value' => 20, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Product 15: KitchenAid Stand Mixer - Color variations
        $product15Variations = [
            ['product_id' => 15, 'sku' => 'KAM-RED', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'KAMRED', 'price' => 379.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 15, 'sku' => 'KAM-BLU', 'image' => 'https://demo.epikcart.xyz/uploads/all/J1CPCWyU5sAGAcAH6COg0HdYfY87GP8nE2WFaFko.jpg', 'code' => 'KAMBLU', 'price' => 379.00, 'discount_value' => 25, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
            ['product_id' => 15, 'sku' => 'KAM-SLV', 'image' => 'https://demo.epikcart.xyz/uploads/all/o2MWhioAVzhj6Sj3HzxbYwgP1ZX5HQ6xPpjNHorV.jpg', 'code' => 'KAMSLV', 'price' => 399.00, 'discount_value' => 0, 'discount_type' => 'flat', 'created_by' => 1, 'created_at' => now(), 'updated_at' => now(), 'updated_by' => 1, 'deleted_at' => null, 'deleted_by' => null],
        ];

        // Combine all variations
        $allVariations = array_merge(
            $product3Variations,
            $product4Variations,
            $product6Variations,
            $product7Variations,
            $product11Variations,
            $product12Variations,
            $product15Variations
        );

        // Insert all variations
        DB::table('product_variations')->insert($allVariations);

        $this->command->info('Created product variations for ' . count($allVariations) . ' items.');
    }
}
