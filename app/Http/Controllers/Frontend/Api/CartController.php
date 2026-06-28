<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use App\Services\FacebookConversionService;
use App\Facades\Gtag;

class CartController extends Controller
{
    protected $facebook;


    public function __construct(FacebookConversionService $facebook)
    {
        $this->facebook = $facebook;
    }
    # get all resources with pagination
    public function index(Request $request, $message = "")
    {
        return $this->__getCartsInfo($message, $request->guestUserId);
    }

    # add new resource
    public function store(Request $request)
    {
        $message            = "";
        $user               = null;
        $productVariation   = ProductVariation::find((int) $request->productVariationId);

        if (!is_null($productVariation)) {
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
                $cart = Cart::where('user_id', $user->id)->where('warehouse_id', $request->warehouseId)->where('product_variation_id', $productVariation->id)->first();
            } else {
                $cart = Cart::where('guest_user_id', (int) $request->guestUserId)->where('warehouse_id', $request->warehouseId)->where('product_variation_id', $productVariation->id)->first();
            }

            if (!is_null($cart)) {
                $cart->qty      += (int) $request->qty;
                $message        =  translate('Quantity has been increased');
            } else {
                $cart                           = new Cart;
                $cart->warehouse_id             = $request->warehouseId;
                $cart->product_variation_id     = $productVariation->id;
                $cart->qty                      = (int) $request->qty;

                if (!is_null($user)) {
                    $cart->user_id              = $user->id;
                } else {
                    $cart->guest_user_id        = (int) $request->guestUserId;
                }

                $message =  translate('Product has been added to your cart');
            }

            $cart->save();

            // Send Facebook AddToCart event
            if ($user && config('app.FACEBOOK_PIXEL_ACTIVATION') == '1') {
                try {

                    $variantId = "{$productVariation->product_id}-{$productVariation->id}"; // <-- FIXED

                    $this->facebook->sendEvent('AddToCart', [
                        'email' => $user->email,
                        'phone' => $user->phone
                    ], [
                        'value' => $productVariation->price * $cart->qty,
                        'currency' => getSetting('currencyCode') ?? 'USD',
                        'content_ids' => [$variantId],       // <-- FIXED
                        'content_type' => 'product',
                    ], $request->eventId);
                    // $this->facebook->sendEvent('AddToCart', [
                    //     'email' => $user->email,
                    //     'phone' => $user->phone
                    // ], [
                    //     'value' => $productVariation->price * $cart->qty,
                    //     'currency' => getSetting('currencyCode') ?? 'USD',
                    //     'content_ids' => [$productVariation->sku],
                    //     'content_type' => 'product',
                    // ]);

                } catch (\Exception $e) {

                    dd($e);
                }
            }

            // gtag event
            try {
                if (config('app.GOOGLE_ANALYTICS_ACTIVATION') == '1') {
                    Gtag::sendEvent('add_to_cart', [
                        'currency' => getSetting('currencyCode') ?? 'USD',
                        'value' => $productVariation->price * $cart->qty,
                        'items' => [[
                            'item_id' => $productVariation->sku,
                            'item_name' => $productVariation->product->name,
                            'price' => $productVariation->price * $cart->qty,
                            'quantity' => $cart->qty,
                        ]],
                    ]);
                }
            } catch (\Exception $e) {

                dd($e);
            }

            return $this->__getCartsInfo($message, $request->guestUserId);
        }

        return [
            'success'   => false,
            'status'    => 404,
            'message'   => translate('Something went wrong, please try again'),
            'result'    => null
        ];
    }

    # update cart
    public function update(Request $request)
    {
        try {
            $cart = Cart::where('id', $request->id)->first();
            if ($request->action == "increase") {
                $product = $cart->productVariation->product;
                $maxPurchaseQty = getSetting('productSettingMaxPurchaseQtyEnabled', '1') !== '0'
                    ? max(1, (int) ($product->max_purchase_qty ?? 100))
                    : 100;

                if ($maxPurchaseQty > $cart->qty) {
                    $allowBackorder = getSetting('allowBackorder') == '1';
                    
                    if ($allowBackorder) {
                        // Backorder enabled: skip stock check
                        $cart->qty += 1;
                        $cart->save();
                    } else {
                        // Backorder disabled: check stock availability
                        $hasEnoughStock = $cart->productVariation
                            ->productVariationStocks()
                            ->where('stock_qty', '>', $cart->qty)
                            ->exists();

                        if ($hasEnoughStock) {
                            $cart->qty += 1;
                            $cart->save();
                        }
                    }
                } else {
                    $message = translate('You have reached maximum order quantity at a time for this product');
                    return $this->__getCartsInfo($message, $request->guestUserId, 'danger', 400);
                }
            } elseif ($request->action == "decrease") {
                if ($cart->qty > 1) {
                    $cart->qty -= 1;
                    $cart->save();
                }
            } else {
                $cart->delete();
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $this->__getCartsInfo('', $request->guestUserId);
    }

    # delete resource
    public function destroy(Request $request)
    {
        $cart = Cart::where('id', (int) $request->id);

        if (auth('sanctum')->check()) {
            $cart  = $cart->where('user_id', auth('sanctum')->user()->id)->first();
        } else {
            $cart  = $cart->where('guest_user_id', (int) $request->guestUserId)->first();
        }
        if (!is_null($cart)) {
            $cart->delete();
        }

        return $this->__getCartsInfo('', $request->guestUserId);
    }

    # check if user has abandoned cart
    public function checkAbandonedCart(Request $request)
    {
        $userId = auth('sanctum')->check() ? apiUserId() : null;
        $guestUserId = (int) $request->guestUserId;
        $threshold = (int) getSetting('abandonedCartThreshold', 60);

        $isAbandoned = Cart::isAbandonedCart($userId, $guestUserId, $threshold);

        $carts = [];
        if ($isAbandoned) {
            if ($userId) {
                $carts = Cart::whereHas('productVariation')->where('user_id', $userId)->whereIn('warehouse_id', session('WarehouseIds'))->get();
            } elseif ($guestUserId) {
                $carts = Cart::whereHas('productVariation')->where('guest_user_id', $guestUserId)->whereIn('warehouse_id', session('WarehouseIds'))->get();
            }
        }

        return response()->json([
            'success' => true,
            'result' => [
                'is_abandoned' => $isAbandoned,
                'carts' => CartResource::collection($carts)
            ]
        ]);
    }

    # get carts information
    private function __getCartsInfo($message = '', $guestUserId = null, $toastVariant = 'success', $status = 200)
    {
        if (auth('sanctum')->check()) {
            $carts  = Cart::whereHas('productVariation')->where('user_id', apiUserId())->whereIn('warehouse_id', session('WarehouseIds'))->get();
        } else {
            $carts  = Cart::whereHas('productVariation')->where('guest_user_id', (int) $guestUserId)->whereIn('warehouse_id', session('WarehouseIds'))->get();
        }

        return response()->json([
            'success'           => true,
            'status'            => $status,
            'message'           => $message,
            'toastVariant'      => $toastVariant,
            'result'            => [
                'carts'             => CartResource::collection($carts),
            ]
        ], $status);
    }
}
