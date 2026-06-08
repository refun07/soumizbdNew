<?php

namespace App\Http\Controllers\Backend\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IpBlockerController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:create_blocked_ips'])->only(['store']);
        $this->middleware(['permission:view_blocked_ips'])->only(['index']);
        $this->middleware(['permission:edit_blocked_ips'])->only(['update', 'toggleStatus']);
        $this->middleware(['permission:delete_blocked_ips'])->only(['destroy']);
    }

    # Display a listing of blocked IPs
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $blockedIps = BlockedIp::with(['createdBy:id,name'])
            ->withTrashed()
            ->orderBy('created_at', 'desc');

        if ($request->search != null) {
            $blockedIps = $blockedIps->where('ip_address', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }

        $blockedIps = $blockedIps->paginate($limit);

        return response()->json([
            'success' => true,
            'status' => 200,
            'blocked_ips' => $blockedIps,
            'searchKey' => $searchKey,
        ]);
    }

    # Store a newly blocked IP
    public function store(Request $request)
    {
        // Validation
        $validator = \Validator::make($request->all(), [
            'ip_address' => 'required|ip|unique:blocked_ips,ip_address',
            'reason' => 'nullable|string|max:500',
            'blocked_until' => 'nullable|date|after:now',
        ], [
            'ip_address.required' => translate('IP address is required'),
            'ip_address.ip' => translate('Please enter a valid IP address'),
            'ip_address.unique' => translate('This IP address is already blocked'),
            'reason.max' => translate('Reason cannot exceed 500 characters'),
            'blocked_until.after' => translate('Block expiry must be in the future'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->getMessages()
            ], 422);
        }

        $blockedIp = new BlockedIp();
        $blockedIp->ip_address = $request->ip_address;
        $blockedIp->reason = $request->reason;
        $blockedIp->blocked_until = $request->blocked_until ? Carbon::parse($request->blocked_until)->setTimezone('UTC') : null;
        $blockedIp->is_active = 1;
        $blockedIp->created_by = auth()->id();
        $blockedIp->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('IP address has been blocked successfully'),
            'blocked_ip' => $blockedIp
        ]);
    }

    # Update blocked IP details
    public function update(Request $request, $id)
    {
        $blockedIp = BlockedIp::withTrashed()->findOrFail($id);

        // Validation
        $validator = \Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
            'blocked_until' => 'nullable|date',
        ], [
            'reason.max' => translate('Reason cannot exceed 500 characters'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->getMessages()
            ], 422);
        }

        $blockedIp->reason = $request->reason;
        $blockedIp->blocked_until = $request->blocked_until ? Carbon::parse($request->blocked_until)->setTimezone('UTC') : null;
        $blockedIp->updated_by = auth()->id();
        $blockedIp->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('IP block has been updated successfully'),
            'blocked_ip' => $blockedIp
        ]);
    }

    # Toggle block status (active/inactive)
    public function toggleStatus(Request $request, $id)
    {
        $blockedIp = BlockedIp::withTrashed()->findOrFail($id);

        // Restore if soft deleted
        if ($blockedIp->trashed()) {
            $blockedIp->restore();
            $blockedIp->is_active = 1;
        } else {
            $blockedIp->is_active = !$blockedIp->is_active;
        }

        $blockedIp->updated_by = auth()->id();
        $blockedIp->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Status updated successfully'),
            'blocked_ip' => $blockedIp
        ]);
    }

    # Soft delete blocked IP (unblock)
    public function destroy($id)
    {
        $blockedIp = BlockedIp::findOrFail($id);
        $blockedIp->deleted_by = auth()->id();
        $blockedIp->save();
        $blockedIp->delete();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('IP address has been unblocked successfully'),
        ]);
    }

    # Display admin view
    public function indexView()
    {
        $blockedIps = BlockedIp::with(['createdBy:id,name'])
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.admin.configurations.security.ip-blocker', [
            'blockedIps' => $blockedIps,
            'settings' => getSetting([]),
        ]);
    }
}