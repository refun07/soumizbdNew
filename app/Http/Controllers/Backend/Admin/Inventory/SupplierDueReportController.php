<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\SupplierDueReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class SupplierDueReportController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_suppliers'])->only(['index', 'details', 'export']);
    }

    # Display supplier due report
    public function index(Request $request)
    {
        try {
            # Extract only the data needed by the service
            $searchKey = $request->get('search');
            $minDue = $request->get('min_due');
            $maxDue = $request->get('max_due');
            $sortBy = $request->get('sort', 'supplier_name');
            $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc'])
                ? $request->get('sort_dir')
                : 'asc';
            $limit = $request->get('limit', 15);

            $data = SupplierDueReportService::index($searchKey, $minDue, $maxDue, $sortBy, $sortDir, $limit);

            # Add query parameters to pagination links
            $data['supplierDueData']->appends($request->query());

            # Add searchKey for the search form (like stock report)
            $data['searchKey'] = $searchKey;

            return view('backend.admin.inventory.supplier-due-reports.index', $data);
        } catch (\Exception $e) {
            Log::error("showing supplier due report", ['panel' => 'admin', 'module' => 'inventory', 'error' => $e->getMessage()]);
            flash(translate('Error fetching supplier due report data'))->error();
            return redirect()->route('admin.dashboard');
        }
    }

    # Display supplier due details
    public function details(Request $request, Supplier $supplier)
    {
        try {
            $purchaseOrders = SupplierDueReportService::getSupplierPurchaseOrderDetails($supplier->id);

            return view('backend.admin.inventory.supplier-due-reports.details', compact('supplier', 'purchaseOrders'));
        } catch (\Exception $e) {
            Log::error("showing supplier due details", ['panel' => 'admin', 'module' => 'inventory', 'supplier_id' => $supplier->id, 'error' => $e->getMessage()]);
            flash(translate('Error fetching supplier due details'))->error();
            return redirect()->route('admin.supplierDueReports.index');
        }
    }

    # Export supplier due report as PDF
    public function export(Request $request)
    {
        try {
            # Get filters from request
            $searchKey = $request->get('search');
            $minDue = $request->get('min_due');
            $maxDue = $request->get('max_due');
            $sortBy = $request->get('sort', 'supplier_name');
            $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc'])
                ? $request->get('sort_dir')
                : 'asc';

            # Get data for PDF
            $data = SupplierDueReportService::index($searchKey, $minDue, $maxDue, $sortBy, $sortDir, null);

            # Get all supplier due data for PDF (not paginated)
            $supplierDueData = SupplierDueReportService::getAllForPdf($searchKey, $minDue, $maxDue, $sortBy, $sortDir);

            # Prepare filters for display
            $filters = [
                'search' => $searchKey,
                'min_due' => $minDue,
                'max_due' => $maxDue,
                'sort' => $sortBy,
                'sort_dir' => $sortDir,
            ];

            # Generate PDF
            $pdf = PDF::loadView('backend.admin.inventory.supplier-due-reports.pdf', [
                'supplierDueData' => $supplierDueData,
                'filters' => $filters,
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'totalSuppliersWithDue' => $data['totalSuppliersWithDue'] ?? 0,
                'totalDueAmount' => $data['totalDueAmount'] ?? 0,
            ]);

            # Generate filename
            $filename = 'supplier_due_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            # Return PDF for download
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("exporting supplier due report", ['panel' => 'admin', 'module' => 'inventory', 'error' => $e->getMessage()]);
            flash(translate('Error generating PDF export'))->error();
            return redirect()->back();
        }
    }

}