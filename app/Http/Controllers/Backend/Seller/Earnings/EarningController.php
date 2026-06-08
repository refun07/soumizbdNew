<?php

namespace App\Http\Controllers\Backend\Seller\Earnings;

use App\Http\Controllers\Controller;
use App\Models\CommissionHistory;
use App\Models\Notification;
use App\Models\ShopPayment;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    # constructor
    public function __construct()
    {
        // 
    }

    # payouts
    public function payouts(Request $request)
    {
        try {
            Notification::where('type', 'payout')->where('shop_id', shopId())->update([
                'is_read' => 1
            ]);
        } catch (\Throwable $th) {
        }


        $limit     = $request->limit ?? 15;
        $requests = ShopPayment::shop()->paid()->latest()->paginate($limit);
        return view('backend.seller.earnings.payouts', compact('requests'));
    }

    # payout requests
    public function requests(Request $request)
    {
        $limit     = $request->limit ?? 15;
        $requests = ShopPayment::shop()->notPaid()->latest()->paginate($limit);
        return view('backend.seller.earnings.requests', compact('requests'));
    }

    # payout requests
    public function storeRequest(Request $request)
    {
        $shop = shop();
        if ($request->amount > $shop->current_balance) {
            $minPayout = getSetting('minWithdrawalAmount') ?? 0.001;
            if ($shop->current_balance < $minPayout) {
                flash(translate('Your can not withdraw less than ') . $minPayout)->success();
            } else {
                flash(translate('Your balance is low'))->success();
            }
        } else {
            $payoutRequest = new ShopPayment;
            $payoutRequest->shop_id = $shop->id;
            $payoutRequest->demanded_amount = $request->amount;
            $payoutRequest->additional_info = $request->note;
            $payoutRequest->save();

            $shop->current_balance -= $request->amount;
            $shop->save();

            // notification
            Notification::create([
                'shop_id'   => $shop->id,
                'for'       => 'admin',
                'type'      => 'payout-request',
                'text'      => $request->amount,
                'link_info' => $shop->id,
            ]);

            flash(translate('Payout request submitted successfully'))->success();
        }
        return back();
    }

    # earning histories
    public function histories(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;
        $earnings = CommissionHistory::shopCommissions()->whereHas('order', function ($q) {
            $q->where('delivery_status', '!=', 'cancelled');
        })->latest();

        if ($request->search != null) {
            $earnings = $earnings->whereHas('shop', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    });
            });
            $searchKey = $request->search;
        }

        $earnings = $earnings->paginate($limit);
        return view('backend.seller.earnings.histories', compact('earnings', 'searchKey'));
    }

    # payout setting
    public function payoutSettings()
    {
        $shop = shop();
        return view('backend.seller.earnings.payout-settings', compact('shop'));
    }

    # payout setting
    public function updatePayoutSettings(Request $request)
    {
        $shop = shop();
        $shop->is_cash_payout = $request->is_cash_payout ?? 0;
        $shop->is_bank_payout = $request->is_bank_payout ?? 0;
        $shop->bank_name = $request->bank_name;
        $shop->bank_acc_name = $request->bank_acc_name;
        $shop->bank_acc_no = $request->bank_acc_no;
        $shop->bank_routing_no = $request->bank_routing_no;
        $shop->save();
        flash(translate('Payout settings updated successfully'))->success();
        return back();
    }
}
