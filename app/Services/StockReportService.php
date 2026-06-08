<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class StockReportService
{
    # get stock report data
    public static function index($searchKey = null, $categoryId = null, $brandId = null, $warehouseId = null, $sortBy = null, $sortDir = 'asc', $limit = 15)
    {
        # Build query for stock report with variation data
        $query = Product::with(['variations.productVariationStocks', 'productTranslations', 'variations.combinations.variationValue.variationValueTranslations'])
            ->select([
                'products.id',
                'products.name',
                'products.thumbnail_image',
                'products.alert_qty',
                'products.created_at',
                DB::raw('COALESCE((SELECT SUM(pvs.stock_qty)
                    FROM product_variations pv
                    LEFT JOIN product_variation_stocks pvs ON pv.id = pvs.product_variation_id
                    WHERE pv.product_id = products.id
                    AND pv.deleted_at IS NULL
                    AND pvs.deleted_at IS NULL
                    ' . (!empty($warehouseId) ? 'AND pvs.warehouse_id = ' . (int)$warehouseId : '') . '
                ), 0) as total_stock'),
                DB::raw('COALESCE((SELECT SUM(pvs.stock_qty * pv.price)
                    FROM product_variations pv
                    LEFT JOIN product_variation_stocks pvs ON pv.id = pvs.product_variation_id
                    WHERE pv.product_id = products.id
                    AND pv.deleted_at IS NULL
                    AND pvs.deleted_at IS NULL
                    ' . (!empty($warehouseId) ? 'AND pvs.warehouse_id = ' . (int)$warehouseId : '') . '
                ), 0) as total_stock_value')
            ])
            ->whereHas('variations.productVariationStocks', function($q) use ($warehouseId) {
                if (!empty($warehouseId)) {
                    $q->where('warehouse_id', $warehouseId);
                }
            });

        # Apply search filter
        if (!empty($searchKey)) {
            # Sanitize search input to prevent LIKE injection
            $sanitizedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $searchKey);
            # Search in both product translations and product name
            $query->where(function($q) use ($sanitizedSearch) {
                $q->whereHas('productTranslations', function($subQ) use ($sanitizedSearch) {
                    $subQ->where('name', 'LIKE', "%{$sanitizedSearch}%");
                })
                ->orWhere('products.name', 'LIKE', "%{$sanitizedSearch}%");
            });
        }

        # Apply category filter
        if (!empty($categoryId)) {
            $query->whereHas('productCategories', function($q) use ($categoryId) {
                return $q->where('category_id', $categoryId);
            });
        }

        # Apply brand filter
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        # Apply sorting
        switch ($sortBy) {
            case 'stock_amount':
                $query->orderBy('total_stock', $sortDir);
                break;
            case 'stock_value':
                $query->orderBy('total_stock_value', $sortDir);
                break;
            case 'product_name':
                $defaultLang = config('app.default_language');
                $direction = ($sortDir === 'desc') ? 'DESC' : 'ASC';
                $query->orderByRaw("(SELECT name FROM product_translations WHERE product_id = products.id AND lang_key = ? LIMIT 1) {$direction}", [$defaultLang]);
                break;
            default:
                $query->orderBy('total_stock', 'desc')
                      ->latest(); # Add latest() for default sort
                break;
        }

        $stockData = $query->paginate($limit);

        # Process each product to add variation data
        $stockData->getCollection()->transform(function($product) use ($warehouseId) {
            $product->variation_stocks = self::getVariationStocks($product, $warehouseId);
            return $product;
        });

        # Fetch categories, brands and warehouses for dropdowns
        $categories = Category::where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->with('childrenCategories')
            ->get();

        $brands = Brand::isActive()->orderBy('name', 'ASC')->get();

        $warehouses = Warehouse::shop()->orderBy('name', 'ASC')->get();

        return [
            'stockData' => $stockData,
            'searchKey' => $searchKey,
            'categories' => $categories,
            'brands' => $brands,
            'warehouses' => $warehouses,
        ];
    }

    # Get variation-wise stock data for a product
    private static function getVariationStocks($product, $warehouseId = null)
    {
        $variationStocks = [];

        # Return empty array if no variations
        if (!$product->variations || $product->variations->isEmpty()) {
            return $variationStocks;
        }

        foreach ($product->variations as $variation) {
            if (!empty($warehouseId)) {
                $stockQty = $variation->productVariationStocks->where('warehouse_id', $warehouseId)->sum('stock_qty') ?? 0;
            } else {
                $stockQty = $variation->productVariationStocks->sum('stock_qty') ?? 0;
            }

            # Get variation name from combinations
            $variationNames = [];
            foreach ($variation->combinations as $combination) {
                if ($combination->variationValue) {
                    $variationNames[] = $combination->variationValue->collectTranslation('name');
                }
            }

            if (!empty($variationNames)) {
                $variationKey = implode(' ', $variationNames);
                if (isset($variationStocks[$variationKey])) {
                    $variationStocks[$variationKey] += $stockQty;
                } else {
                    $variationStocks[$variationKey] = $stockQty;
                }
            }
        }

        return $variationStocks;
    }

    # Get all stock report data for PDF export (no pagination)
    public static function getAllForPdf($searchKey = null, $categoryId = null, $brandId = null, $warehouseId = null, $sortBy = null, $sortDir = 'asc')
    {
        # Build query for stock report with variation data
        $query = Product::with(['variations.productVariationStocks', 'productTranslations', 'variations.combinations.variationValue.variationValueTranslations'])
            ->select([
                'products.id',
                'products.name',
                'products.thumbnail_image',
                'products.alert_qty',
                'products.created_at',
                DB::raw('COALESCE((SELECT SUM(pvs.stock_qty)
                    FROM product_variations pv
                    LEFT JOIN product_variation_stocks pvs ON pv.id = pvs.product_variation_id
                    WHERE pv.product_id = products.id
                    AND pv.deleted_at IS NULL
                    AND pvs.deleted_at IS NULL
                    ' . (!empty($warehouseId) ? 'AND pvs.warehouse_id = ' . (int)$warehouseId : '') . '
                ), 0) as total_stock'),
                DB::raw('COALESCE((SELECT SUM(pvs.stock_qty * pv.price)
                    FROM product_variations pv
                    LEFT JOIN product_variation_stocks pvs ON pv.id = pvs.product_variation_id
                    WHERE pv.product_id = products.id
                    AND pv.deleted_at IS NULL
                    AND pvs.deleted_at IS NULL
                    ' . (!empty($warehouseId) ? 'AND pvs.warehouse_id = ' . (int)$warehouseId : '') . '
                ), 0) as total_stock_value')
            ])
            ->whereHas('variations.productVariationStocks', function($q) use ($warehouseId) {
                if (!empty($warehouseId)) {
                    $q->where('warehouse_id', $warehouseId);
                }
            });

        # Apply search filter
        if (!empty($searchKey)) {
            # Sanitize search input to prevent LIKE injection
            $sanitizedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $searchKey);
            # Search in both product translations and product name
            $query->where(function($q) use ($sanitizedSearch) {
                $q->whereHas('productTranslations', function($subQ) use ($sanitizedSearch) {
                    $subQ->where('name', 'LIKE', "%{$sanitizedSearch}%");
                })
                ->orWhere('products.name', 'LIKE', "%{$sanitizedSearch}%");
            });
        }

        # Apply category filter
        if (!empty($categoryId)) {
            $query->whereHas('productCategories', function($q) use ($categoryId) {
                return $q->where('category_id', $categoryId);
            });
        }

        # Apply brand filter
        if (!empty($brandId)) {
            $query->where('brand_id', $brandId);
        }

        # Apply sorting
        switch ($sortBy) {
            case 'stock_amount':
                $query->orderBy('total_stock', $sortDir);
                break;
            case 'stock_value':
                $query->orderBy('total_stock_value', $sortDir);
                break;
            case 'product_name':
                $defaultLang = config('app.default_language');
                $direction = ($sortDir === 'desc') ? 'DESC' : 'ASC';
                $query->orderByRaw("(SELECT name FROM product_translations WHERE product_id = products.id AND lang_key = ? LIMIT 1) {$direction}", [$defaultLang]);
                break;
            default:
                $query->orderBy('total_stock', 'desc')
                      ->latest(); # Add latest() for default sort
                break;
        }

        # Get all records (no pagination)
        $stockData = $query->get();

        # Process each product to add variation data
        $stockData->transform(function($product) use ($warehouseId) {
            $product->variation_stocks = self::getVariationStocks($product, $warehouseId);
            return $product;
        });

        return $stockData;
    }
}