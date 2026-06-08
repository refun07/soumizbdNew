<?php

namespace App\Http\Services;

class ProductService
{

    # specific columns
    public function getSpecificColumns()
    {
        return [
            'id',
            'shop_id',
            'name',
            'slug',
            'brand_id',
            'unit_id',
            'thumbnail_image',
            'min_price',
            'max_price',
            'discount_info',
            'discount_start_date',
            'discount_end_date',
            'stock_qty',
            'is_published',
            'hide_on_ecom',
            'min_purchase_qty',
            'max_purchase_qty',
            'has_variation',
            'has_warranty',
            'reward_points',
            'pre_order',
        ];
    }

}
