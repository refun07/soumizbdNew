<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Services\FacebookConversionService;
use App\Facades\Gtag;

class WishlistController extends Controller
{
    protected $facebook;

    public function __construct(FacebookConversionService $facebook)
    {
        $this->facebook = $facebook;
    }
    # get all resources with pagination
    public function index(Request $request, $message = '')
    {
        $wishlists = auth('sanctum')->user()->wishlists ?? [];
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => $message,
            'result'    => WishlistResource::collection($wishlists)
        ];
    }

    # add new resource
    public function store(Request $request)
    {
        $userId = auth('sanctum')->user()->id;
        $wishlist = Wishlist::with('product')->where('user_id', $userId)->where('product_id', $request->productId)->first();

        if (is_null($wishlist)) {
            $wishlist               = new Wishlist;
            $wishlist->user_id      = $userId;
            $wishlist->product_id   = $request->productId;
            $wishlist->save();
            // Send Facebook AddToWishlist event
            try {

                $user = auth('sanctum')->user();
                if ($user && config('app.FACEBOOK_PIXEL_ACTIVATION') == '1') {

                    // $this->facebook->sendEvent('AddToWishlist', [
                    //     'email' => $user->email,
                    //     'phone' => $user->phone
                    //     // 'order_id' => $order->id,
                    // ],[
                    //     'content_ids' => [$wishlist->product_id],
                    //     'content_type' => 'product'
                    // ]);

                    $this->facebook->sendEvent('AddToWishlist', [
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ], [
                        'content_ids'  => [(string) $wishlist->product_id],   // <-- FIXED
                        'content_type' => 'product_group',
                        'content_name' => $wishlist->product->name,
                        'value'        => (float) $wishlist->product->min_price,
                        'currency'     => getSetting('currencyCode') ?? 'USD',
                    ]);
                }
            } catch (\Exception $e) {

                dd($e);
            }
            try {
                if (config('app.GOOGLE_ANALYTICS_ACTIVATION') == '1') {
                    Gtag::sendEvent('add_to_wishlist', [
                        'currency' => getSetting('currencyCode') ?? 'USD',
                        'value' => $wishlist->product->min_price,
                        'items' => [[
                            'item_id' => $wishlist->product->id,
                            'item_name' => $wishlist->product->name,
                        ]],
                    ]);
                }
            } catch (\Exception $e) {

                dd($e);
            }
            return $this->index($request, translate('Product added to your wishlist'));
        } else {
            $wishlist->delete();
            return $this->index($request, translate('Product removed from your wishlist'));
        }
    }

    # delete resource
    public function destroy(Request $request)
    {
        auth('sanctum')->user()->wishlists()->where('product_id', $request->productId)->delete();
        return $this->index($request, translate('Product removed from your wishlist'));
    }
}
