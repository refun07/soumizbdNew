<?php

namespace App\Http\Controllers\Backend\Seller;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            # currency settings
            'currency'      => [
                'code'      => getSetting('currencyCode') ?? 'usd',
                'symbol'    => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1  : 0] : 'symbol_first',

                    'show'  => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals'     => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator'  => getSetting('decimalSeparator') ?? '.',
            ],
        ];

        return view('backend.seller.dashboard.index', compact('settings'));
    }


    #
    public function notifications(Request $request)
    {
        $limit = $request->limit ?? 15;
        $notifications = Notification::where('for', 'shop')->where('shop_id', shopId())->latest()->paginate($limit);
        return view('backend.seller.dashboard.notifications', compact('notifications'));
    }

    #
    public function markRead(Request $request)
    {
        try {
            Notification::where('shop_id', shopId())->update([
                'is_read' => 1
            ]);
        } catch (\Throwable $th) {
        }

        flash(translate('Marked as read'))->success();
        return back();
    }


    # Show the form for editing the specified resource.
    public function getNavbarSearchData(Request $request)
    {
        $searchKey  =  $request->search;
        $products   = Product::shop()->where('name', 'like', '%' .  $searchKey . '%');

        $orders     = Order::shopOrders()->latest();

        $orders     = $orders->whereHas('user', function ($q) use ($searchKey) {
            $q->where('name', 'like', '%' .  $searchKey  . '%')->orWhere('email', 'like', '%' .  $searchKey  . '%');
        })->orWhere(function ($q) use ($searchKey) {
            $q->where('order_code',  'like', '%' .  $searchKey  . '%')->where('shop_id', shopId());
        });

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'products'      => $products->take(4)->get(),
                'orders'        => $orders->take(4)->get(),
                'searchKey'     =>  $searchKey,
            ],
        ];

        return view('components.backend.inc.navbar-search', $data['result'])->render();
    }
}
