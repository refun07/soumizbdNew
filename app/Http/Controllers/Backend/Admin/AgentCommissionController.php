<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentCommissionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentCommissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_agent_commissions'])->only(['index', 'show']);
        $this->middleware(['permission:pay_agent_commissions'])->only(['pay', 'payBulk']);
        $this->middleware(['permission:edit_agent_commission_rate'])->only(['updateRate']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->user_type !== 'staff') {
                abort(403, 'Access denied. Staff only.');
            }
            return $next($request);
        })->only(['indexPage', 'myBalance', 'myCommissions']);
    }

    public function indexPage()
    {
        $agent = auth()->user();

        $balance = [
            'agent' => $agent->only(['id', 'name', 'email', 'agent_commission_percentage', 'agent_commission_fixed_amount', 'agent_commission_mode', 'agent_current_balance']),
            'pending_total' => (float) AgentCommissionHistory::where('agent_id', $agent->id)->where('status', 'pending')->sum('commission_amount'),
            'earned_total' => (float) AgentCommissionHistory::where('agent_id', $agent->id)->where('status', 'earned')->sum('commission_amount'),
            'paid_total' => (float) AgentCommissionHistory::where('agent_id', $agent->id)->where('status', 'paid')->sum('commission_amount'),
            'cancelled_total' => (float) AgentCommissionHistory::where('agent_id', $agent->id)->where('status', 'cancelled')->sum('commission_amount'),
        ];

        $settings = [
            'currency' => [
                'code' => getSetting('currencyCode') ?? 'usd',
                'symbol' => [
                    'position' => getSetting('currencySymbolAlignment') ? 'symbol_first' : 'amount_first',
                    'show' => getSetting('currencySymbol') ?? '$',
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals' => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator' => getSetting('decimalSeparator') ?? '.',
            ],
        ];

        return view('backend.admin.agent-commissions.index', compact('balance', 'settings'));
    }

    public function index(Request $request)
    {
        $query = AgentCommissionHistory::with(['order', 'agent']);

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $commissions,
        ]);
    }

    public function show($id)
    {
        $commission = AgentCommissionHistory::with(['order', 'agent'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $commission,
        ]);
    }

    public function pay($id)
    {
        return DB::transaction(function () use ($id) {
            $commission = AgentCommissionHistory::where('id', $id)
                ->where('status', 'earned')
                ->lockForUpdate()
                ->firstOrFail();

            $commission->status = 'paid';
            $commission->paid_at = now();
            $commission->save();

            $agent = User::lockForUpdate()->findOrFail($commission->agent_id);
            $agent->agent_current_balance -= $commission->commission_amount;
            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Commission marked as paid',
                'data' => $commission->fresh()->load(['order', 'agent']),
            ]);
        });
    }

    public function payBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:agent_commission_histories,id',
        ]);

        return DB::transaction(function () use ($request) {
            $commissions = AgentCommissionHistory::whereIn('id', $request->ids)
                ->where('status', 'earned')
                ->lockForUpdate()
                ->get();

            $totalPaid = 0;
            $agentIds = [];

            foreach ($commissions as $commission) {
                $commission->status = 'paid';
                $commission->paid_at = now();
                $commission->save();

                if (!isset($agentIds[$commission->agent_id])) {
                    $agentIds[$commission->agent_id] = 0;
                }
                $agentIds[$commission->agent_id] += $commission->commission_amount;
                $totalPaid++;
            }

            foreach ($agentIds as $agentId => $amount) {
                $agent = User::lockForUpdate()->find($agentId);
                if ($agent) {
                    $agent->agent_current_balance -= $amount;
                    $agent->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$totalPaid} commissions marked as paid",
            ]);
        });
    }

    public function agentCommissions(Request $request, $agentId)
    {
        $agent = User::agents()->findOrFail($agentId);

        $query = AgentCommissionHistory::where('agent_id', $agentId)->with('order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => $agent->only(['id', 'name', 'email', 'agent_commission_percentage', 'agent_commission_fixed_amount', 'agent_commission_mode', 'agent_current_balance']),
                'commissions' => $commissions,
            ],
        ]);
    }

    public function agentBalance($agentId)
    {
        $agent = User::agents()->findOrFail($agentId);

        $pendingTotal = AgentCommissionHistory::where('agent_id', $agentId)
            ->where('status', 'pending')
            ->sum('commission_amount');

        $earnedTotal = AgentCommissionHistory::where('agent_id', $agentId)
            ->where('status', 'earned')
            ->sum('commission_amount');

        $paidTotal = AgentCommissionHistory::where('agent_id', $agentId)
            ->where('status', 'paid')
            ->sum('commission_amount');

        $cancelledTotal = AgentCommissionHistory::where('agent_id', $agentId)
            ->where('status', 'cancelled')
            ->sum('commission_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => $agent->only(['id', 'name', 'email', 'agent_commission_percentage', 'agent_commission_fixed_amount', 'agent_commission_mode', 'agent_current_balance']),
                'pending_total' => (float) $pendingTotal,
                'earned_total' => (float) $earnedTotal,
                'paid_total' => (float) $paidTotal,
                'cancelled_total' => (float) $cancelledTotal,
            ],
        ]);
    }

    public function updateRate(Request $request, $agentId)
    {
        $agent = User::agents()->findOrFail($agentId);

        $request->validate([
            'agent_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'agent_commission_fixed_amount' => 'nullable|numeric|min:0',
            'agent_commission_mode' => 'nullable|in:percentage,fixed,both_highest,both_additive',
        ]);

        if ($request->has('agent_commission_percentage')) {
            $agent->agent_commission_percentage = $request->agent_commission_percentage;
        }
        if ($request->has('agent_commission_fixed_amount')) {
            $agent->agent_commission_fixed_amount = $request->agent_commission_fixed_amount;
        }
        if ($request->has('agent_commission_mode')) {
            $agent->agent_commission_mode = $request->agent_commission_mode;
        }
        $agent->save();

        return response()->json([
            'success' => true,
            'message' => 'Agent commission rate updated',
            'data' => $agent->only(['id', 'name', 'email', 'agent_commission_percentage', 'agent_commission_fixed_amount', 'agent_commission_mode', 'agent_current_balance']),
        ]);
    }

    public function myBalance()
    {
        $agent = auth()->user();

        $pendingTotal = AgentCommissionHistory::where('agent_id', $agent->id)
            ->where('status', 'pending')
            ->sum('commission_amount');

        $earnedTotal = AgentCommissionHistory::where('agent_id', $agent->id)
            ->where('status', 'earned')
            ->sum('commission_amount');

        $paidTotal = AgentCommissionHistory::where('agent_id', $agent->id)
            ->where('status', 'paid')
            ->sum('commission_amount');

        $cancelledTotal = AgentCommissionHistory::where('agent_id', $agent->id)
            ->where('status', 'cancelled')
            ->sum('commission_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => $agent->only(['id', 'name', 'email', 'agent_commission_percentage', 'agent_commission_fixed_amount', 'agent_commission_mode', 'agent_current_balance']),
                'pending_total' => (float) $pendingTotal,
                'earned_total' => (float) $earnedTotal,
                'paid_total' => (float) $paidTotal,
                'cancelled_total' => (float) $cancelledTotal,
            ],
        ]);
    }

    public function myCommissions(Request $request)
    {
        $query = AgentCommissionHistory::where('agent_id', auth()->id())->with('order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $commissions,
        ]);
    }
}
