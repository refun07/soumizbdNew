<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockRequest;
use App\Models\User;
use App\Notifications\StockRequestFulfilledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StockRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_stock_requests'])->only(['index', 'show']);
        $this->middleware(['permission:edit_stock_requests'])->only(['updateStatus']);
        $this->middleware(['permission:delete_stock_requests'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = StockRequest::with(['user', 'product', 'productVariation'])
            ->when($request->status !== null && $request->status !== '', function($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->when($request->search, function($q) use ($request) {
                $search = $request->search;
                $q->where(function($subQ) use ($search) {
                    $subQ->where('product_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('product_sku', 'like', "%{$search}%")
                        ->orWhereHas('user', function($userQ) use ($search) {
                            $userQ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->date_from, function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            });

        $items = $query->latest()->paginate($request->limit ?? perPage());

        return view('backend.admin.inventory.stock-requests.index', compact('items'));
    }

    public function show(StockRequest $stockRequest)
    {
        return view('backend.admin.inventory.stock-requests.show', compact('stockRequest'));
    }

    public function updateStatus(Request $request, StockRequest $stockRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1,2',
            'admin_note' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $stockRequest->status;
        $stockRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note']
        ]);

        if ($oldStatus != 1 && $validated['status'] == 1) {
            try {
                $email = $stockRequest->email ?? $stockRequest->user?->email;
                if ($email) {
                    Notification::route('mail', $email)
                        ->notify(new StockRequestFulfilledNotification($stockRequest));
                }
            } catch (\Exception $e) {
                Log::error('Stock request fulfillment email failed: ' . $e->getMessage());
            }
        }

        flash(translate('Status updated successfully'))->success();
        return redirect()->back();
    }

    public function destroy(StockRequest $stockRequest)
    {
        $stockRequest->delete();
        flash(translate('Request deleted successfully'))->success();
        return redirect()->route('admin.stock-requests.index');
    }
}
