<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $latestDeliveryStatusUpdate = $this->relationLoaded('latestDeliveryStatusUpdate')
            ? $this->latestDeliveryStatusUpdate
            : null;

        $data = [
            'id' => $this->id,
            'orderGroupId' => $this->order_group_id,
            'code' => $this->order_code,
            'codeToShow' => getSetting('orderCodePrefix').($this->orderGroup?->code ?? $this->order_code),
            'customerOrderCode' => $this->orderGroup?->code ?? $this->order_code,
            'customerOrderCodeToShow' => getSetting('orderCodePrefix').($this->orderGroup?->code ?? $this->order_code),
            'createdDate' => $this->created_at->toIso8601String(),
            'createdTime' => date('h:i A', strtotime($this->created_at)),
            'updatedDate' => date('d M, Y', strtotime($this->updated_at)),
            'updatedTime' => date('h:i A', strtotime($this->updated_at)),
            'subtotalAmount' => $this->amount - $this->discount_amount,
            'taxAmount' => $this->tax_amount,
            'shippingChargeAmount' => $this->shipping_charge_amount,
            'discountAmount' => $this->discount_amount,
            'couponDiscountAmount' => $this->coupon_discount_amount,
            'adminDiscountAmount' => $this->admin_discount_amount,
            'totalAmount' => $this->total_amount,
            'deliveryStatus' => $this->delivery_status,
            'deliveryStatusToShow' => ucfirst(str_replace('_', ' ', $this->delivery_status)),
            'paymentStatus' => $this->payment_status,
            'isPosOrder' => $this->pos_order,
            'isExchangeOrder' => $this->is_exchange_order,
            'originalOrderId' => $this->original_order_id,
            'source' => $this->source,
            'ipAddress' => $this->ip_address,
            'latestDeliveryUpdate' => $latestDeliveryStatusUpdate ? [
                'status' => $latestDeliveryStatusUpdate->status,
                'note' => $latestDeliveryStatusUpdate->note,
                'actorName' => $latestDeliveryStatusUpdate->getActorName() ?? 'System',
                'actorRole' => $latestDeliveryStatusUpdate->getActorRole(),
                'createdDate' => date('d M, Y', strtotime($latestDeliveryStatusUpdate->created_at)),
                'createdTime' => date('h:i A', strtotime($latestDeliveryStatusUpdate->created_at)),
            ] : null,
        ];

        $additionalData = [];
        if (Route::currentRouteName() == 'orders.show' || Route::currentRouteName() == 'orders.success') {
            $orderGroup = $this->orderGroup;

            $additionalData = [
                'paymentMethod' => ucfirst(str_replace('_', ' ', $orderGroup->transaction->payment_method)),
                'deliveryAddress' => [
                    'type' => $orderGroup->shipping_address_type,
                    'address' => $orderGroup->shipping_address,
                    'direction' => $orderGroup->direction,
                    'phone' => $orderGroup->phone,
                    'alternativePhone' => $orderGroup->alternative_phone,
                ],
                'items' => OrderItemResource::collection($this->orderItems),
                'shop' => new ShopResource($this->shop),
                'orderTimeline' => OrderUpdateResource::collection($this->orderUpdates),
            ];
        }
        if (Route::currentRouteName() == 'admin.allorders') {
            $orderGroup = $this->orderGroup;
            $firstOrderItem = $this->relationLoaded('orderItems') ? $this->orderItems->first() : null;
            $productVariation = $firstOrderItem?->productVariation;
            $product = $productVariation?->product;
            $productImage = $productVariation?->image ?: $product?->thumbnail_image;

            $additionalData['courier_partner'] = $orderGroup?->courier_partner ?: ($this->courier_name ?? '');
            $additionalData['courier_devlivery_status'] = $orderGroup?->delivery_status ?? '';
            $additionalData['tracking_number'] = $orderGroup?->tracking_number ?: ($this->tracking_number ?? '');
            $additionalData['customerName'] = $orderGroup?->name ?? $this->user?->name;
            $additionalData['customerPhone'] = $orderGroup?->phone ?? $this->user?->phone;
            $additionalData['address'] = $orderGroup?->shipping_address ?? '';
            $additionalData['shipping_address'] = $orderGroup?->shipping_address ?? '';
            $additionalData['orderCount'] = (int) ($this->order_items_count ?? $this->orderItems->count());
            $additionalData['totalItemQuantity'] = (int) ($this->total_item_quantity ?? $this->orderItems->sum('qty'));
            $additionalData['firstItem'] = $firstOrderItem ? [
                'name' => $product?->collectTranslation('name') ?? $product?->name ?? 'Product unavailable',
                'variationCode' => $productVariation?->code,
                'quantity' => (int) $firstOrderItem->qty,
                'image' => $productImage ? uploadedAsset($productImage) : null,
            ] : null;
            $additionalData['orderPrefix'] = getSetting('orderCodePrefix');
            $additionalData['customerOrderCode'] = $orderGroup?->code ?? $this->order_code;
            $additionalData['customerOrderCodeToShow'] = getSetting('orderCodePrefix').($orderGroup?->code ?? $this->order_code);
            $additionalData['agentId'] = $this->agent_id;
            $additionalData['agentName'] = $this->agent?->name;

            if ($this->is_exchange_order && $this->originalOrder) {
                $additionalData['originalOrderCode'] = getSetting('orderCodePrefix').$this->originalOrder->order_code;
                $additionalData['originalOrderRawCode'] = $this->originalOrder->order_code;
                $additionalData['originalOrderId'] = $this->originalOrder->id;
            }

            $exchangeAsOriginal = $this->relationLoaded('exchangeAsOriginal') ? $this->exchangeAsOriginal : null;
            if ($exchangeAsOriginal) {
                $additionalData['hasExchange'] = true;
                $additionalData['exchangeStatus'] = $exchangeAsOriginal->status;
                if ($exchangeAsOriginal->exchangeOrder) {
                    $additionalData['exchangeOrderCode'] = getSetting('orderCodePrefix').$exchangeAsOriginal->exchangeOrder->order_code;
                    $additionalData['exchangeOrderRawCode'] = $exchangeAsOriginal->exchangeOrder->order_code;
                    $additionalData['exchangeOrderId'] = $exchangeAsOriginal->exchangeOrder->id;
                }
            } else {
                $additionalData['hasExchange'] = false;
            }
        }

        if (Route::currentRouteName() == routePrefix().'.dashboard.recentOrders') {
            $additionalData['customerName'] = $this->user->name;
        }

        $data = array_merge($data, $additionalData);

        return $data;
    }
}
