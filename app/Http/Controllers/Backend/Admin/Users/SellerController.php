<?php

namespace App\Http\Controllers\Backend\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\CommissionHistory;
use App\Models\Notification;
use App\Models\Shop;
use App\Models\ShopPayment;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class SellerController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_sellers'])->only(['index']);
        $this->middleware(['permission:edit_sellers'])->only([
            'toggleBan', 'toggleApproval', 'togglePublished', 'edit', 'update'
        ]);
        $this->middleware(['permission:pay_sellers'])->only(['makePayment']);
        $this->middleware(['permission:show_payouts'])->only(['payouts']);

        $this->middleware(['permission:show_payout_requests'])->only(['payoutRequests']);
        $this->middleware(['permission:show_earning_histories'])->only(['earnings']);
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $sellers = User::sellers();
        if ($request->search != null) {
            try {
                Notification::where('link_info', $request->search)->where('type', 'seller-registration')->update([
                    'is_read' => 1
                ]);
            } catch (\Throwable $th) {
            }
            $sellers = $sellers->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            })->orWhereHas('shop', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
            $searchKey = $request->search;
        }

        // isApproved
        if ($request->isApproved) {
            $isApproved = $request->isApproved == 'approved' ? '1' : '0';
            $sellers = $sellers->whereHas('shop', function ($q) use ($isApproved) {
                $q->where('is_approved', $isApproved);
            });
        }

        // sort by balance
        if ($request->balance) {
            switch ($request->balance) {
                case 'highToLow':
                    $sellers = $sellers->whereHas('shop', function ($q) {
                        $q->orderByDesc('current_balance');
                    });
                    break;
                case 'lowToHigh':
                    $sellers = $sellers->whereHas('shop', function ($q) {
                        $q->orderBy('current_balance');
                    });
                    break;
                default:
                    $sellers = $sellers->latest();
                    break;
            }
        }

        $sellers = $sellers->paginate($limit);
        return view('backend.admin.sellers.index', compact('sellers', 'searchKey'));
    }

    # toggle Ban 
    public function toggleBan(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $seller             = User::findOrFail($request->id);
        $seller->is_banned  = !$seller->is_banned;
        $seller->save();

        return $data;
    }

    # toggle Approval 
    public function toggleApproval(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $seller             = User::findOrFail($request->id);
        $shop               = $seller->shop;
        $shop->is_approved  = !$shop->is_approved;
        $shop->save();
        cacheClear();
        return $data;
    }

    # toggle Published 
    public function togglePublished(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $seller             = User::findOrFail($request->id);
        $shop               = $seller->shop;
        $shop->is_published = !$shop->is_published;
        $shop->save();
        cacheClear();
        return $data;
    }

    # edit
    public function edit($id)
    {
        $seller = User::sellers()->whereId($id)->first();
        return view('backend.admin.sellers.edit', compact('seller'));
    }

    # update
    public function update(Request $request, $id)
    {
        $seller         = User::sellers()->whereId($id)->first();
        $seller->name   = $request->name;
        $seller->email  = $request->email;
        if ($request->password) {
            $seller->password = Hash::make($request->password);
        }
        $seller->save();

        $shop =  $seller->shop;
        $shop->admin_commission_percentage = $request->admin_commission_percentage;
        $shop->save();

        flash(translate('Seller has been added successfully'))->success();
        return redirect()->route('admin.sellers.index');
    }

    # makePayment
    public function makePayment(Request $request)
    {
        if ($request->id) {
            $payment          = ShopPayment::whereId($request->id)->first();
        } else {
            $payment          = new ShopPayment;
            $payment->shop_id = $request->shop_id;

            $shop                   = Shop::whereId($request->shop_id)->first();
            $shop->current_balance -= $request->amount;
            $shop->save();
        }
        if ($payment) {
            $payment->status            = 'paid';
            $payment->given_amount      = $request->amount;
            $payment->payment_method    = $request->payment_method;
            $payment->payment_details   = $request->payment_details ?? null;
            $payment->save();
        }

        // notification
        Notification::create([
            'shop_id'   => $request->shop_id,
            'for'       => 'shop',
            'type'      => 'payout',
            'text'      => $request->amount,
            'link_info' => $request->shop_id,
        ]);

        flash(translate('Payment has been successful'))->success();
        return back();
    }

    # payouts
    public function payouts(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;
        $payouts   = ShopPayment::paid()->latest();

        if ($request->shopId) {
            $payouts = $payouts->where('shop_id', $request->shopId);
        }

        if ($request->search != null) {
            $payouts = $payouts->whereHas('shopInfo', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    });
            });
            $searchKey = $request->search;
        }

        $payouts = $payouts->paginate($limit);
        $shops   = Shop::latest()->whereNot('id', shopId())->get(['id', 'name']);
        return view('backend.admin.sellers.payouts', compact('payouts', 'shops', 'searchKey'));
    }

    # payout requests
    public function payoutRequests(Request $request)
    {
        if ($request->shopId) {
            try {
                Notification::where('type', 'payout-request')->where('shop_id', $request->shopId)->update([
                    'is_read' => 1
                ]);
            } catch (\Throwable $th) {
            }
        }

        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $requests = ShopPayment::notPaid()->latest();

        if ($request->shopId) {
            $requests = $requests->where('shop_id', $request->shopId);
        }

        if ($request->search != null) {
            $requests = $requests->whereHas('shopInfo', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('email', 'like', '%' . $request->search . '%')
                            ->orWhere('phone', 'like', '%' . $request->search . '%');
                    });
            });
            $searchKey = $request->search;
        }

        $requests = $requests->paginate($limit);

        $shops = Shop::latest()->whereNot('id', shopId())->get(['id', 'name']);
        return view('backend.admin.sellers.requests', compact('requests', 'searchKey', 'shops'));
    }

    # admin earnings
    public function earnings(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;
        $earnings  = CommissionHistory::whereHas('order', function ($q) {
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


        if ($request->shopId) {
            $earnings = $earnings->where('shop_id', $request->shopId);
        }

        $earnings   = $earnings->paginate($limit);
        $shops      = Shop::latest()->whereNot('id', shopId())->get(['id', 'name']);
        return view('backend.admin.sellers.earnings', compact('earnings', 'searchKey', 'shops'));
    }
}
