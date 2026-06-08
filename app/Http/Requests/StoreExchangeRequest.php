<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExchangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|integer|exists:orders,id',
            'requested_by' => 'nullable|integer|in:0,1,2',
            'shipping_charge' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.original_order_item_id' => 'required|integer|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'nullable|string|max:255',
            'items.*.note' => 'nullable|string|max:1000',
            'exchange_items' => 'required|array|min:1',
            'exchange_items.*.exchange_product_variation_id' => 'required|integer|exists:product_variations,id',
            'exchange_items.*.exchange_quantity' => 'required|integer|min:1',
        ];
    }
}
