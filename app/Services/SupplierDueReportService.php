<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;

class SupplierDueReportService
{
    public static function index($searchKey = null, $minDue = null, $maxDue = null, $sortBy = 'supplier_name', $sortDir = 'asc', $limit = 15)
    {
        try {
            # Build the base query
            $query = self::buildBaseQuery($searchKey, $minDue, $maxDue);

            # Apply sorting
            $query = self::applySorting($query, $sortBy, $sortDir);

            # Get paginated data or all data
            if ($limit) {
                # For pagination, we need to use a subquery approach
                $supplierDueData = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
                $currentPage = $supplierDueData ?: 1;
                $perPage = $limit;

                $results = $query->get();
                $total = $results->count();

                # Create paginated collection
                $items = $results->forPage($currentPage, $perPage)->values();

                $supplierDueData = new \Illuminate\Pagination\LengthAwarePaginator(
                    $items,
                    $total,
                    $perPage,
                    $currentPage,
                    [
                        'path' => request()->url(),
                        'pageName' => 'page',
                    ]
                );
            } else {
                $supplierDueData = $query->get();
            }

            # Get summary statistics from the full result set
            $allResults = self::buildBaseQuery($searchKey, $minDue, $maxDue)->get();
            $totalSuppliersWithDue = $allResults->count();
            $totalDueAmount = $allResults->sum('total_due');

            return [
                'supplierDueData' => $supplierDueData,
                'totalSuppliersWithDue' => $totalSuppliersWithDue,
                'totalDueAmount' => $totalDueAmount,
                'filters' => [
                    'search' => $searchKey,
                    'min_due' => $minDue,
                    'max_due' => $maxDue,
                    'sort' => $sortBy,
                    'sort_dir' => $sortDir,
                ]
            ];

        } catch (\Exception $e) {
            \Log::error('SupplierDueReportService::index', ['error' => $e->getMessage()]);
            return [
                'supplierDueData' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15, 1),
                'totalSuppliersWithDue' => 0,
                'totalDueAmount' => 0,
                'filters' => []
            ];
        }
    }

    public static function getAllForPdf($searchKey = null, $minDue = null, $maxDue = null, $sortBy = 'supplier_name', $sortDir = 'asc')
    {
        try {
            # Build the base query
            $query = self::buildBaseQuery($searchKey, $minDue, $maxDue);

            # Apply sorting
            $query = self::applySorting($query, $sortBy, $sortDir);

            return $query->get();

        } catch (\Exception $e) {
            \Log::error('SupplierDueReportService::getAllForPdf', ['error' => $e->getMessage()]);
            return collect();
        }
    }

    private static function buildBaseQuery($searchKey = null, $minDue = null, $maxDue = null)
    {
        $query = DB::table('suppliers')
            ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->where('suppliers.deleted_at', null)
            ->where('purchase_orders.deleted_at', null)
            ->where('purchase_orders.shop_id', shopId())
            ->where('purchase_orders.due', '>', 0)
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.email', 'suppliers.phone_no')
            ->select([
                'suppliers.id as supplier_id',
                'suppliers.name as supplier_name',
                'suppliers.email as supplier_email',
                'suppliers.phone_no as supplier_phone',
                DB::raw('COUNT(purchase_orders.id) as total_orders'),
                DB::raw('SUM(purchase_orders.grand_total) as total_order_amount'),
                DB::raw('SUM(purchase_orders.paid) as total_paid'),
                DB::raw('SUM(purchase_orders.due) as total_due'),
                DB::raw('MAX(purchase_orders.created_at) as last_purchase_date')
            ]);

        # Apply search filter
        if (!empty($searchKey)) {
            $query->where(function ($q) use ($searchKey) {
                $searchPattern = '%' . addcslashes($searchKey, '%_\\') . '%';
                $q->where('suppliers.name', 'like', $searchPattern)
                  ->orWhere('suppliers.email', 'like', $searchPattern)
                  ->orWhere('suppliers.phone_no', 'like', $searchPattern);
            });
        }


        # Apply due amount range filters
        if (!empty($minDue)) {
            $query->havingRaw('SUM(purchase_orders.due) >= ?', [$minDue]);
        }

        if (!empty($maxDue)) {
            $query->havingRaw('SUM(purchase_orders.due) <= ?', [$maxDue]);
        }

        return $query;
    }

    private static function applySorting($query, $sortBy = 'supplier_name', $sortDir = 'asc')
    {
        $allowedSortFields = [
            'supplier_name' => 'suppliers.name',
            'supplier_email' => 'suppliers.email',
            'total_orders' => 'total_orders',
            'total_order_amount' => 'total_order_amount',
            'total_paid' => 'total_paid',
            'total_due' => 'total_due',
            'last_purchase_date' => 'last_purchase_date'
        ];

        $sortField = $allowedSortFields[$sortBy] ?? 'suppliers.name';

        return $query->orderBy($sortField, $sortDir);
    }

    public static function getSupplierPurchaseOrderDetails($supplierId)
    {
        try {
            return PurchaseOrder::with(['supplier', 'warehouse', 'payments'])
                ->where('supplier_id', $supplierId)
                ->where('shop_id', shopId())
                ->where('due', '>', 0)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            \Log::error('SupplierDueReportService::getSupplierPurchaseOrderDetails', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}