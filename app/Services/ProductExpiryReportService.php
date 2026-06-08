<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\ProductVariation;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ProductExpiryReportService
{
    /**
     * Get base query for product expiry report
     */
    private static function buildBaseQuery($searchKey = null, $statusFilter = null, $warehouseId = null, $supplierId = null, $categoryId = null)
    {
        $query = DB::table('stocks')
            ->join('product_variations', 'stocks.product_variation_id', '=', 'product_variations.id')
            ->join('products', 'product_variations.product_id', '=', 'products.id')
            ->leftJoin('suppliers', 'stocks.supplier_id', '=', 'suppliers.id')
            ->leftJoin('warehouses', 'stocks.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->leftJoin('categories', 'product_categories.category_id', '=', 'categories.id')
            ->where('stocks.expiry_date', '!=', null)
            ->where('stocks.qty', '>', 0)
            ->where('stocks.deleted_at', null)
            ->where('products.deleted_at', null)
            ->where('product_variations.deleted_at', null)
            ->where('products.shop_id', shopId())
            ->select([
                'stocks.id as stock_id',
                'stocks.expiry_date',
                'stocks.qty as current_stock',
                'stocks.purchase_cost',
                'stocks.selling_price',
                'product_variations.id as variation_id',
                'product_variations.sku',
                DB::raw('NULL as variation_title'),
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('NULL as product_type'),
                'suppliers.id as supplier_id',
                'suppliers.name as supplier_name',
                'suppliers.email as supplier_email',
                'suppliers.phone_no as supplier_phone',
                'warehouses.id as warehouse_id',
                'warehouses.name as warehouse_name',
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('
                    CASE
                        WHEN FROM_UNIXTIME(stocks.expiry_date) < NOW() THEN "expired"
                        WHEN FROM_UNIXTIME(stocks.expiry_date) <= DATE_ADD(NOW(), INTERVAL 7 DAY) THEN "expiring-soon"
                        ELSE "fresh"
                    END as expiry_status
                '),
                DB::raw('DATEDIFF(FROM_UNIXTIME(stocks.expiry_date), NOW()) as days_until_expiry')
            ]);

        // Apply search filter
        if (!empty($searchKey)) {
            $searchPattern = '%' . addcslashes($searchKey, '%_\\') . '%';
            $query->where(function ($q) use ($searchPattern) {
                $q->where('products.name', 'like', $searchPattern)
                  ->orWhere('product_variations.sku', 'like', $searchPattern)
                  ->orWhere('suppliers.name', 'like', $searchPattern)
                  ->orWhere('warehouses.name', 'like', $searchPattern);
            });
        }

        // Apply status filter
        if (!empty($statusFilter)) {
            switch ($statusFilter) {
                case 'expired':
                    $query->whereRaw('FROM_UNIXTIME(stocks.expiry_date) < NOW()');
                    break;
                case 'expiring-soon':
                    $query->whereRaw('FROM_UNIXTIME(stocks.expiry_date) >= NOW()')
                          ->whereRaw('FROM_UNIXTIME(stocks.expiry_date) <= DATE_ADD(NOW(), INTERVAL 7 DAY)');
                    break;
                case 'fresh':
                    $query->whereRaw('FROM_UNIXTIME(stocks.expiry_date) > DATE_ADD(NOW(), INTERVAL 7 DAY)');
                    break;
            }
        }

        // Apply warehouse filter
        if (!empty($warehouseId)) {
            $query->where('stocks.warehouse_id', $warehouseId);
        }

        // Apply supplier filter
        if (!empty($supplierId)) {
            $query->where('stocks.supplier_id', $supplierId);
        }

        // Apply category filter
        if (!empty($categoryId)) {
            $query->where('product_categories.category_id', $categoryId);
        }

        return $query;
    }

    /**
     * Get paginated results for product expiry report
     */
    public static function getPaginatedResults($searchKey = null, $statusFilter = null, $warehouseId = null, $supplierId = null, $categoryId = null, $sortBy = 'expiry_date', $sortDir = 'asc', $perPage = 15)
    {
        $query = self::buildBaseQuery($searchKey, $statusFilter, $warehouseId, $supplierId, $categoryId);

        // Apply sorting
        $allowedSortFields = [
            'expiry_date', 'product_name', 'supplier_name', 'warehouse_name',
            'current_stock', 'days_until_expiry', 'category_name'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('stocks.expiry_date', 'asc');
        }

        return $query->paginate($perPage, ['*'], 'page');
    }

    /**
     * Get all results for PDF export
     */
    public static function getAllForPdf($searchKey = null, $statusFilter = null, $warehouseId = null, $supplierId = null, $categoryId = null, $sortBy = 'expiry_date', $sortDir = 'asc')
    {
        $query = self::buildBaseQuery($searchKey, $statusFilter, $warehouseId, $supplierId, $categoryId);

        // Apply sorting
        $allowedSortFields = [
            'expiry_date', 'product_name', 'supplier_name', 'warehouse_name',
            'current_stock', 'days_until_expiry', 'category_name'
        ];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('stocks.expiry_date', 'asc');
        }

        return $query->get();
    }

    /**
     * Get summary statistics
     */
    public static function getSummaryStatistics($searchKey = null, $statusFilter = null, $warehouseId = null, $supplierId = null, $categoryId = null)
    {
        $baseQuery = DB::table('stocks')
            ->join('product_variations', 'stocks.product_variation_id', '=', 'product_variations.id')
            ->join('products', 'product_variations.product_id', '=', 'products.id')
            ->leftJoin('suppliers', 'stocks.supplier_id', '=', 'suppliers.id')
            ->leftJoin('warehouses', 'stocks.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->leftJoin('categories', 'product_categories.category_id', '=', 'categories.id')
            ->where('stocks.expiry_date', '!=', null)
            ->where('stocks.qty', '>', 0)
            ->where('stocks.deleted_at', null)
            ->where('products.deleted_at', null)
            ->where('product_variations.deleted_at', null)
            ->where('products.shop_id', shopId());

        // Apply search filter
        if (!empty($searchKey)) {
            $searchPattern = '%' . addcslashes($searchKey, '%_\\') . '%';
            $baseQuery->where(function ($q) use ($searchPattern) {
                $q->where('products.name', 'like', $searchPattern)
                    ->orWhere('product_variations.sku', 'like', $searchPattern)
                    ->orWhere('suppliers.name', 'like', $searchPattern)
                    ->orWhere('warehouses.name', 'like', $searchPattern);
            });
        }

        // Apply status filter
        if (!empty($statusFilter)) {
            switch ($statusFilter) {
                case 'expired':
                    $baseQuery->whereRaw('FROM_UNIXTIME(stocks.expiry_date) < NOW()');
                    break;
                case 'expiring-soon':
                    $baseQuery->whereRaw('FROM_UNIXTIME(stocks.expiry_date) >= NOW()')
                        ->whereRaw('FROM_UNIXTIME(stocks.expiry_date) <= DATE_ADD(NOW(), INTERVAL 7 DAY)');
                    break;
                case 'fresh':
                    $baseQuery->whereRaw('FROM_UNIXTIME(stocks.expiry_date) > DATE_ADD(NOW(), INTERVAL 7 DAY)');
                    break;
            }
        }

        // Apply filters to base query
        if (!empty($warehouseId)) {
            $baseQuery->where('stocks.warehouse_id', $warehouseId);
        }
        if (!empty($supplierId)) {
            $baseQuery->where('stocks.supplier_id', $supplierId);
        }
        if (!empty($categoryId)) {
            $baseQuery->where('product_categories.category_id', $categoryId);
        }

        $totalProducts = (clone $baseQuery)->count();

        $expiredCount = (clone $baseQuery)
            ->whereRaw('FROM_UNIXTIME(stocks.expiry_date) < NOW()')
            ->count();

        $expiringSoonCount = (clone $baseQuery)
            ->whereRaw('FROM_UNIXTIME(stocks.expiry_date) >= NOW()')
            ->whereRaw('FROM_UNIXTIME(stocks.expiry_date) <= DATE_ADD(NOW(), INTERVAL 7 DAY)')
            ->count();

        $freshCount = (clone $baseQuery)
            ->whereRaw('FROM_UNIXTIME(stocks.expiry_date) > DATE_ADD(NOW(), INTERVAL 7 DAY)')
            ->count();

        $totalStockValue = (clone $baseQuery)
            ->sum(DB::raw('stocks.qty * stocks.purchase_cost'));

        return [
            'total_products' => $totalProducts,
            'expired_count' => $expiredCount,
            'expiring_soon_count' => $expiringSoonCount,
            'fresh_count' => $freshCount,
            'total_stock_value' => $totalStockValue,
        ];
    }

    /**
     * Get filter options for dropdowns
     */
    public static function getFilterOptions()
    {
        $warehouses = DB::table('warehouses')
            ->where('shop_id', shopId())
            ->where('deleted_at', null)
            ->orderBy('name')
            ->pluck('name', 'id');

        $suppliers = DB::table('suppliers')
            ->where('shop_id', shopId())
            ->where('deleted_at', null)
            ->orderBy('name')
            ->pluck('name', 'id');

        $categories = DB::table('categories')
            ->where('shop_id', shopId())
            ->where('deleted_at', null)
            ->where('parent_id', null)
            ->orderBy('name')
            ->pluck('name', 'id');

        return [
            'warehouses' => $warehouses,
            'suppliers' => $suppliers,
            'categories' => $categories,
        ];
    }

    /**
     * Format expiry data for display
     */
    public static function formatExpiryData($stock)
    {
        // Convert to object if it's an array
        if (is_array($stock)) {
            $stock = (object) $stock;
        }

        $expiryDate = Carbon::createFromTimestamp($stock->expiry_date);
        $now = now();

        return [
            'stock_id' => $stock->stock_id,
            'product_name' => $stock->product_name ?: 'Unknown Product',
            'variation_title' => $stock->variation_title ?: null,
            'sku' => $stock->sku ?: 'N/A',
            'expiry_date' => $expiryDate->format('Y-m-d'),
            'expiry_date_formatted' => $expiryDate->format('M d, Y'),
            'days_until_expiry' => $stock->days_until_expiry,
            'status' => $stock->expiry_status,
            'current_stock' => $stock->current_stock,
            'purchase_cost' => $stock->purchase_cost,
            'selling_price' => $stock->selling_price,
            'total_value' => $stock->current_stock * $stock->purchase_cost,
            'supplier_name' => $stock->supplier_name ?: 'N/A',
            'warehouse_name' => $stock->warehouse_name ?: 'N/A',
            'category_name' => $stock->category_name ?: 'N/A',
            'product_type' => $stock->product_type ?: 'N/A',
        ];
    }

    /**
     * Get expiry status with badge information
     */
    public static function getExpiryBadgeInfo($status)
    {
        $badges = [
            'expired' => [
                'class' => 'badge-danger',
                'icon' => 'fas fa-times-circle',
                'text' => translate('Expired')
            ],
            'expiring-soon' => [
                'class' => 'badge-warning',
                'icon' => 'fas fa-exclamation-triangle',
                'text' => translate('Expiring Soon')
            ],
            'fresh' => [
                'class' => 'badge-success',
                'icon' => 'fas fa-check-circle',
                'text' => translate('Fresh')
            ]
        ];

        return $badges[$status] ?? $badges['fresh'];
    }
}
