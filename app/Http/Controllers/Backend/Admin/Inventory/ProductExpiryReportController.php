<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Services\ProductExpiryReportService;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Validator;

class ProductExpiryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_reports']);
    }

    /**
     * Display the product expiry report page
     */
    public function index(Request $request)
    {
        $searchKey = $request->get('search');
        $statusFilter = $request->get('status');
        $warehouseId = $request->get('warehouse_id');
        $supplierId = $request->get('supplier_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort', 'expiry_date');
        $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc']) ? $request->get('sort_dir') : 'asc';

        // Get paginated results
        $stocks = ProductExpiryReportService::getPaginatedResults(
            $searchKey,
            $statusFilter,
            $warehouseId,
            $supplierId,
            $categoryId,
            $sortBy,
            $sortDir,
            15
        );

        // Get summary statistics
        $summary = ProductExpiryReportService::getSummaryStatistics(
            $searchKey,
            $statusFilter,
            $warehouseId,
            $supplierId,
            $categoryId
        );

        // Get filter options
        $filterOptions = ProductExpiryReportService::getFilterOptions();

        // Format stocks data
        $formattedStocks = $stocks->getCollection()->map(function ($stock) {
            return ProductExpiryReportService::formatExpiryData($stock);
        });

        // Replace collection with formatted data
        $stocks->setCollection($formattedStocks);

        return view('backend.admin.inventory.product-expiry-reports.index', [
            'stocks' => $stocks,
            'summary' => $summary,
            'filterOptions' => $filterOptions,
            'filters' => [
                'search' => $searchKey,
                'status' => $statusFilter,
                'warehouse_id' => $warehouseId,
                'supplier_id' => $supplierId,
                'category_id' => $categoryId,
                'sort' => $sortBy,
                'sort_dir' => $sortDir,
            ]
        ]);
    }

    /**
     * Export the product expiry report to PDF
     */
    public function export(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:expired,expiring-soon,fresh',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'sort' => 'nullable|in:expiry_date,product_name,supplier_name,warehouse_name,current_stock,days_until_expiry,category_name',
            'sort_dir' => 'nullable|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $searchKey = $request->get('search');
        $statusFilter = $request->get('status');
        $warehouseId = $request->get('warehouse_id');
        $supplierId = $request->get('supplier_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort', 'expiry_date');
        $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc']) ? $request->get('sort_dir') : 'asc';

        // Get all data for PDF
        $stocksData = ProductExpiryReportService::getAllForPdf(
            $searchKey,
            $statusFilter,
            $warehouseId,
            $supplierId,
            $categoryId,
            $sortBy,
            $sortDir
        );

        // Format stocks data
        $stocks = $stocksData->map(function ($stock) {
            return ProductExpiryReportService::formatExpiryData($stock);
        });

        // Get summary statistics
        $summary = ProductExpiryReportService::getSummaryStatistics(
            $searchKey,
            $statusFilter,
            $warehouseId,
            $supplierId,
            $categoryId
        );

        // Get filter details for display
        $filterDetails = [];
        if ($searchKey) {
            $filterDetails['Search'] = $searchKey;
        }
        if ($statusFilter) {
            $filterDetails['Status'] = ucfirst(str_replace('-', ' ', $statusFilter));
        }
        if ($warehouseId) {
            $warehouse = \App\Models\Warehouse::find($warehouseId);
            $filterDetails['Warehouse'] = $warehouse ? $warehouse->name : 'N/A';
        }
        if ($supplierId) {
            $supplier = \App\Models\Supplier::find($supplierId);
            $filterDetails['Supplier'] = $supplier ? $supplier->name : 'N/A';
        }
        if ($categoryId) {
            $category = \App\Models\Category::find($categoryId);
            $filterDetails['Category'] = $category ? $category->name : 'N/A';
        }

        $pdf = PDF::loadView('backend.admin.inventory.product-expiry-reports.pdf', [
            'stocks' => $stocks,
            'summary' => $summary,
            'filters' => $filterDetails,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);

        $filename = 'product_expiry_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * AJAX endpoint for dynamic data loading
     */
    public function ajaxData(Request $request)
    {
        $searchKey = $request->get('search');
        $statusFilter = $request->get('status');
        $warehouseId = $request->get('warehouse_id');
        $supplierId = $request->get('supplier_id');
        $categoryId = $request->get('category_id');
        $sortBy = $request->get('sort', 'expiry_date');
        $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc']) ? $request->get('sort_dir') : 'asc';

        // Get paginated results
        $stocks = ProductExpiryReportService::getPaginatedResults(
            $searchKey,
            $statusFilter,
            $warehouseId,
            $supplierId,
            $categoryId,
            $sortBy,
            $sortDir,
            $request->get('per_page', 15)
        );

        // Format stocks data
        $formattedStocks = $stocks->getCollection()->map(function ($stock) {
            $data = ProductExpiryReportService::formatExpiryData($stock);
            $badgeInfo = ProductExpiryReportService::getExpiryBadgeInfo($data['status']);
            $data['status_badge'] = $badgeInfo;
            return $data;
        });

        // Replace collection with formatted data
        $stocks->setCollection($formattedStocks);

        return response()->json([
            'data' => $formattedStocks,
            'pagination' => [
                'total' => $stocks->total(),
                'per_page' => $stocks->perPage(),
                'current_page' => $stocks->currentPage(),
                'last_page' => $stocks->lastPage(),
                'from' => $stocks->firstItem(),
                'to' => $stocks->lastItem(),
            ]
        ]);
    }
}
