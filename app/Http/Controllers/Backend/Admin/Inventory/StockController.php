<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Services\ExpiryValidationService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_stocks'])->only(['index']);
        $this->middleware(['permission:view_reports'])->only(['expiryReport', 'expiringSoonReport']);
    }

    /**
     * Display a listing of stock entries with expiry information.
     */
    public function index(Request $request, $limit = 15)
    {
        $query = Stock::with(['productVariation.product', 'warehouse'])
            ->shop();

        // Filter by warehouse
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by expiry status
        if ($request->expiry_status) {
            switch ($request->expiry_status) {
                case 'expired':
                    $query->whereNotNull('expiry_date')
                          ->where('expiry_date', '<', strtotime(date('Y-m-d')));
                    break;
                case 'expiring-soon':
                    $futureDate = strtotime(date('Y-m-d', strtotime('+30 days')));
                    $query->whereNotNull('expiry_date')
                          ->where('expiry_date', '>=', strtotime(date('Y-m-d')))
                          ->where('expiry_date', '<=', $futureDate);
                    break;
                case 'no-expiry':
                    $query->whereNull('expiry_date');
                    break;
                case 'fresh':
                    $futureDate = strtotime(date('Y-m-d', strtotime('+30 days')));
                    $query->whereNotNull('expiry_date')
                          ->where('expiry_date', '>', $futureDate);
                    break;
            }
        }

        // Search by product name
        if ($request->search) {
            $query->whereHas('productVariation.product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $stocks = $query->latest()->paginate($limit);
        $warehouses = Warehouse::shop()->get();

        return view('backend.admin.inventory.stocks.index', compact('stocks', 'warehouses'));
    }

    /**
     * Display expired stock report
     */
    public function expiryReport(Request $request)
    {
        $report = ExpiryValidationService::getExpiredStockReport($request->warehouse_id);
        $warehouses = Warehouse::shop()->get();

        return view('backend.admin.inventory.stocks.expiry-report', compact('report', 'warehouses'));
    }

    /**
     * Display stock expiring soon report
     */
    public function expiringSoonReport(Request $request)
    {
        $days = $request->days ?? 30;
        $report = ExpiryValidationService::getExpiringSoonReport($days, $request->warehouse_id);
        $warehouses = Warehouse::shop()->get();

        return view('backend.admin.inventory.stocks.expiring-soon-report', compact('report', 'warehouses', 'days'));
    }

    /**
     * Get FIFO recommendations for a product variation
     */
    public function fifoRecommendations(Request $request)
    {
        $request->validate([
            'product_variation_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'quantity_needed' => 'required|integer|min:1'
        ]);

        $recommendations = ExpiryValidationService::getFifoRecommendations(
            $request->product_variation_id,
            $request->warehouse_id,
            $request->quantity_needed
        );

        return response()->json($recommendations);
    }
}