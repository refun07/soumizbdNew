<?php

namespace App\Http\Requests\StockRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        try {
            $user = apiUser();
        } catch (\Throwable $e) {
            $user = null;
        }

        return [
            'product_id' => 'required|exists:products,id',
            'variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'nullable|integer|min:1|max:10',
            'note' => 'nullable|string|max:500',
            'guest_user_id' => 'nullable|integer',
            'email' => $user ? 'nullable' : 'required|string|email|max:255',
            'phone' => $user ? 'nullable' : 'nullable|string|max:20',
        ];
    }
}
