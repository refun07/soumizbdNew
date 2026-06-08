<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest\StoreRequest;
use App\Models\Product;
use App\Models\StockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockRequestApiController extends Controller
{
    public function store(StoreRequest $request)
    {
        try {
            $product = Product::with('variations')->findOrFail($request->product_id);
            $variation = $request->variation_id
                ? $product->variations()->find($request->variation_id)
                : null;

            $existingRequest = StockRequest::where('product_id', $request->product_id)
                ->where('status', 0);

            if ($request->email) {
                $existingRequest->where('email', $request->email);
            }

            if ($request->guest_user_id) {
                $existingRequest->orWhere('guest_user_id', $request->guest_user_id);
            } elseif ($cookieGuestUserId = $request->cookie('guestUserId')) {
                $existingRequest->orWhere('guest_user_id', $cookieGuestUserId);
            }

            if ($request->variation_id) {
                $existingRequest->where('product_variation_id', $request->variation_id);
            } else {
                $existingRequest->whereNull('product_variation_id');
            }

            if ($existingRequest->exists()) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'message' => 'You have already requested stock for this item',
                    'already_requested' => true,
                ], 400);
            }

            $productDetails = [
                'slug' => $product->slug,
                'price' => $variation?->price ?? $product->price,
                'variation_name' => $variation?->name ?? null,
                'variation_attributes' => $variation?->combinations ? $variation->combinations->pluck('variationValue', 'variation')->toArray() : [],
            ];

            $stockRequest = StockRequest::create([
                'user_id' => null,
                'guest_user_id' => $request->guest_user_id,
                'email' => $request->email,
                'phone' => $request->phone,
                'product_id' => $request->product_id,
                'product_variation_id' => $request->variation_id ?? null,
                'product_name' => $product->collectTranslation('name'),
                'product_sku' => $variation?->sku ?? $product->sku,
                'product_image' => $variation?->image ?? ($product->thumbnailImage ? $product->thumbnailImage->image : null),
                'product_details' => $productDetails,
                'quantity' => $request->quantity ?? 1,
                'customer_note' => $request->note,
                'status' => 0,
            ]);

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Stock request submitted successfully. We will notify you when stock is available.',
                'result' => $stockRequest,
            ]);
        } catch (\Exception $e) {
            Log::error('Stock request store error: '.$e->getMessage().' - '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong: '.$e->getMessage(),
            ], 500);
        }
    }

    public function check(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $email = $request->email;
        $guestUserId = $request->guest_user_id ?? $request->cookie('guestUserId');
        $variationId = $request->variation_id;

        $requested = false;

        if ($email) {
            $query = StockRequest::where('email', $email)
                ->where('product_id', $request->product_id)
                ->where('status', 0);

            if ($variationId) {
                $query->where('product_variation_id', $variationId);
            } else {
                $query->whereNull('product_variation_id');
            }

            $requested = $query->exists();
        }

        if (! $requested && $guestUserId) {
            $query = StockRequest::where('guest_user_id', $guestUserId)
                ->where('product_id', $request->product_id)
                ->where('status', 0);

            if ($variationId) {
                $query->where('product_variation_id', $variationId);
            } else {
                $query->whereNull('product_variation_id');
            }

            $requested = $query->exists();
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'requested' => $requested,
        ]);
    }
}
