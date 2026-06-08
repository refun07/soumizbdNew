<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Services\StockReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class StockReportController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_stock_report'])->only(['index', 'export']);
    }

    # Display stock report
    public function index(Request $request)
    {
        try {
            # Extract only the data needed by the service
            $searchKey = $request->get('search');
            $categoryId = $request->get('categoryId');
            $brandId = $request->get('brandId');
            $warehouseId = $request->get('warehouseId');
            $sortBy = $request->get('sort');
            $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc'])
                ? $request->get('sort_dir')
                : 'asc';
            $limit = $request->get('limit', 15);

            $data = StockReportService::index($searchKey, $categoryId, $brandId, $warehouseId, $sortBy, $sortDir, $limit);

            # Add query parameters to pagination links
            $data['stockData']->appends($request->query());

            return view('backend.admin.inventory.stock-reports.index', $data);
        } catch (\Exception $e) {
            Log::error("showing stock report", ['panel' => 'admin', 'module' => 'inventory', 'error' => $e->getMessage()]);
            flash(translate('Error fetching stock report data'))->error();
            return redirect()->route('admin.dashboard');
        }
    }

    # Export stock report as PDF
    public function export(Request $request)
    {
        try {
            # Get filters from request
            $searchKey = $request->get('search');
            $categoryId = $request->get('categoryId');
            $brandId = $request->get('brandId');
            $warehouseId = $request->get('warehouseId');
            $sortBy = $request->get('sort');
            $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc'])
                ? $request->get('sort_dir')
                : 'asc';

            # Get data for PDF
            $data = StockReportService::index($searchKey, $categoryId, $brandId, $warehouseId, $sortBy, $sortDir, null);

            # Get all stock data for PDF (not paginated)
            $stockData = StockReportService::getAllForPdf($searchKey, $categoryId, $brandId, $warehouseId, $sortBy, $sortDir);

            # Prepare filters for display
            $filters = [
                'search' => $searchKey,
                'categoryId' => $categoryId,
                'brandId' => $brandId,
                'warehouseId' => $warehouseId,
                'sort' => $sortBy,
                'sort_dir' => $sortDir,
            ];

            # Generate PDF
            $pdf = PDF::loadView('backend.admin.inventory.stock-reports.pdf', [
                'stockData' => $stockData,
                'filters' => $filters,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'categories' => $data['categories'] ?? collect(),
                'brands' => $data['brands'] ?? collect(),
                'warehouses' => $data['warehouses'] ?? collect(),
            ]);

            # Generate filename
            $filename = 'stock_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            # Return PDF for download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("exporting stock report", ['panel' => 'admin', 'module' => 'inventory', 'error' => $e->getMessage()]);
            flash(translate('Error generating PDF export'))->error();
            return redirect()->back();
        }
    }

}
