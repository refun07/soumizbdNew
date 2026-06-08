<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReturnRequestRequest;
use App\Models\ProductVariation;
use App\Models\ReturnRequest;
use App\Services\ReturnRequestService;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function __construct(protected ReturnRequestService $returnRequestService)
    {
        $this->middleware(['permission:view_orders|manage_assigned_orders|manage_orders'])->only(['index', 'show', 'getOrderInvoice', 'getProductVariations']);
        $this->middleware(['permission:manage_assigned_orders|manage_orders'])->only(['create', 'store', 'edit', 'update', 'destroy', 'refundPayment']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $response = $this->returnRequestService->index($request);
        if ($response['status'] == 200) {
            return view('backend.admin.return-request.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $response = $this->returnRequestService->create($request);

        return view('backend.admin.return-request.create', $response['result']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $response = $this->returnRequestService->store($request->all());

            if ($response['status'] == 200) {
                return response()->json([
                    'status' => 200,
                    'message' => $response['message'],
                ]);
            }

            return response()->json([
                'status' => $response['status'] ?? 500,
                'message' => $response['message'] ?? translate('Something went wrong'),
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ReturnRequest $returnRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReturnRequest $returnRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $response = $this->returnRequestService->update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route(routePrefix() . '.dashboard');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReturnRequest $returnRequest)
    {
        //
    }

    public function refundPayment(Request $request)
    {
        try {
            $response = $this->returnRequestService->refundPayment($request->all());
            if ($response['status'] == 200) {
                flash($response['message'])->success();
                return redirect()->back();
            }
            flash($response['message'] ?? translate('Something went wrong'))->error();
            return back();
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    public function getOrderInvoice(Request $request)
    {
        try {
            $response = $this->returnRequestService->getOrderInvoice($request);
            return view('backend.admin.return-request.order-search-results', $response['result']);
        } catch (\Exception $e) {
            flash('Something went wrong' . $e->getMessage())->error();
            return back();
        }
    }

    public function getProductVariations($productId)
    {
        $variations = ProductVariation::where('product_id', $productId)
            ->with('combinations.variation', 'combinations.variationValue')
            ->get();

        return response()->json([
            'success' => true,
            'result' => $variations->map(function ($variation) {
                $variationValues = $variation->combinations->map(function ($combination) {
                    return $combination->variationValue->name ?? '';
                })->implode(' / ');

                return [
                    'id' => $variation->id,
                    'sku' => $variation->sku,
                    'price' => $variation->price,
                    'label' => ($variationValues ? $variationValues . ' - ' : '') . ($variation->sku ? 'SKU: ' . $variation->sku : 'Variation #' . $variation->id),
                ];
            }),
        ]);
    }
}
