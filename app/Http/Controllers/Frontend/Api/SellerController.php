<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Notification;
use App\Models\Shop;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\SellerRegistration;
use Hash;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    # store seller req
    public function store(Request $request)
    {

        if ($request->phone) {
            if (User::where('phone', $request->phone)->first() != null) {
                return response()->json([
                    'success' => false,
                    'message' => translate('User already exists'),
                    'data' => null,
                    'authStatus' => 'user_exists_phone'
                ], 409);
            }
        }

        if ($request->email) {
            if (User::where('email', $request->email)->first() != null) {
                return response()->json([
                    'success' => false,
                    'message' => translate('User already exists'),
                    'data' => null,
                    'authStatus' => 'user_exists_email'
                ], 409);
            }
        }

        $seller             = new User;
        $seller->name       = $request->name;
        $seller->email      = $request->email;
        $seller->phone      = $request->phone;
        $seller->user_type  = 'seller';
        $seller->password   = Hash::make($request->password);
        $seller->save();

        // shop
        $shop = new Shop;
        $shop->user_id                      = $seller->id;
        $shop->name                         = $request->shopName;
        $shop->slug                         = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name));
        $shop->admin_commission_percentage  = (float) getSetting('adminCommissionPercentage', 0.00);
        $shop->manage_stock_by              = 'inventory';
        $shop->save();

        // seller
        $seller->shop_id = $shop->id;
        $seller->save();

        // warehouse
        $warehouse = new Warehouse;
        $warehouse->name                 = 'Default Warehouse';
        $warehouse->shop_id              = $shop->id;
        $warehouse->is_default           = 1;
        $warehouse->save();


        // notification
        Notification::create([
            'shop_id'   => $shop->id,
            'for'       => 'admin',
            'type'      => 'seller-registration',
            'text'      => 'Seller Registration',
            'link_info' => $seller->email ? $seller->email :  $seller->phone,
        ]);

        // email notification  
        try {
            $seller->notify(new SellerRegistration());
        } catch (\Throwable $th) {
            //throw $th;
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Your registration has been successful, please wait for approval'),
            'result'    => new ShopResource($seller->shop)
        ];
    }
}
