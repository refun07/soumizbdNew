<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\FacebookConversionService;
use App\Facades\Gtag;
use Route;

class ProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    protected $facebook;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->facebook = app(FacebookConversionService::class);

    }

    public function toArray($request)
    {
        $preOrderEnabled = getSetting('productSettingPreOrderEnabled', '1') !== '0';

        // Send Facebook Search event
        if(!Route::currentRouteName() == 'admin.allorders'){
            try{
                $user = apiUser();
                if($user && config('app.FACEBOOK_PIXEL_ACTIVATION') == '1'){
                    $this->facebook->sendEvent('ViewContent', [
                        'email' => $user->email,
                        'phone' => $user->phone
                    ], [
                        'content_ids' => [$this->sku],
                        'content_type' => 'product',
                        'value' => (float) variationPrice($this->product, $this, false),
                        'currency' => getSetting('currencyCode') ?? 'USD',
                    ]);

                }

            }catch (\Exception $e) {
                dd($e);
            }
            // Send Facebook Search event
            try{
                if(config('app.GOOGLE_ANALYTICS_ACTIVATION') == '1'){
                    Gtag::sendEvent('view_item', [
                        'value' => (float) variationPrice($this->product, $this, false),
                        'currency' => getSetting('currencyCode') ?? 'USD',
                        'items' => [[
                            'item_id' => $this->sku,
                            'item_name' => $this->product->name,
                            'price' => (float) variationPrice($this->product, $this, false),
                        ]],
                    ]);
                }

            }catch (\Exception $e) {
                dd($e);
            }
        }

        return [
            'id'                            => $this->id,
            'productId'                     => (int) $this->product_id,
            'name'                          => generateVariationName($this->code),
            'code'                          => $this->code,
            'sku'                           => $this->sku,
            'stocks'                        => ProductVariationStockResource::collection($this->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds') ?? [1])->get()),
            'image'                         => uploadedAsset($this->image),
            'basePrice'                     => (float) variationPrice($this->product, $this, false),
            'discountedBasePrice'           => (float) variationDiscountedPrice($this->product, $this, false),
            'basePriceWithTax'              => (float) variationPrice($this->product, $this),
            'discountedBasePriceWithTax'    => (float) variationDiscountedPrice($this->product, $this),
            'tax'                           => (float) variationDiscountedPrice($this->product, $this) - (float) variationDiscountedPrice($this->product, $this, false),
            'dealEnds'                      => $this->__getDealEnds($this),
            'isPreOrder'                    => $preOrderEnabled && $this->isPreOrderEnabled(),
            'isSoldOut'                     => $preOrderEnabled && $this->isSoldOut(),
            'currentOrders'                 => $preOrderEnabled ? $this->getCurrentOrders() : 0,
            'remainingOrders'               => $preOrderEnabled ? $this->remainingOrders() : PHP_INT_MAX,
            'preOrderData'                  => $preOrderEnabled ? $this->pre_order : null
        ];
    }

    # deal ends
    private function __getDealEnds($productVariation)
    {
        $endsAt             = null;
        $campaignProduct    = $productVariation->campaignProducts()->whereHas('campaign', function ($q) {
            $q->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')));
        })->first();

        if ($campaignProduct) {
            $campaign = $campaignProduct->campaign;
            $endsAt   = $campaign->end_date;
        }

        return $endsAt;
    }
}
