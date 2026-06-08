<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExchangeRequest;
use App\Models\Exchange;
use App\Services\ExchangeService;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    public function __construct(protected ExchangeService $exchangeService)
    {
        $this->middleware(['permission:view_orders|manage_assigned_orders|manage_orders'])->only(['index', 'show', 'searchProducts', 'orderSearch', 'getProductVariations']);
        $this->middleware(['permission:manage_assigned_orders|manage_orders'])->only(['create', 'store', 'process', 'cancel']);
    }

    public function index(Request $request)
    {
        $response = $this->exchangeService->index($request);
        if ($response['status'] == 200) {
            return view('backend.admin.exchange.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route(routePrefix() . '.dashboard');
    }

    public function create(Request $request)
    {
        $response = $this->exchangeService->create($request);
        return view('backend.admin.exchange.create', $response['result']);
    }

    public function store(StoreExchangeRequest $request)
    {
        try {
            $response = $this->exchangeService->store($request->validated());

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

    public function show($id)
    {
        $exchange = $this->exchangeService->getById($id);
        return view('backend.admin.exchange.show', [
            'exchange' => $exchange,
            'route' => routePrefix(),
        ]);
    }

    public function process($id)
    {
        try {
            $response = $this->exchangeService->process($id);
            if ($response['status'] == 200) {
                flash($response['message'])->success();
                return redirect()->back();
            }

            flash($response['message'] ?? translate('Something went wrong'))->error();
            return redirect()->back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            flash(collect($e->errors())->flatten()->first())->error();
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back();
        }
    }

    public function cancel($id)
    {
        try {
            $response = $this->exchangeService->cancel($id);
            if ($response['status'] == 200) {
                flash($response['message'])->success();
                return redirect()->back();
            }

            flash($response['message'] ?? translate('Something went wrong'))->error();
            return redirect()->back();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back();
        }
    }

    public function searchProducts(Request $request)
    {
        $query = $request->q ?? '';
        $products = $this->exchangeService->searchProducts($query);

        return response()->json([
            'success' => true,
            'result' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'thumbnail' => uploadedAsset($product->thumbnail_image),
                    'sku' => $product->sku,
                    'has_variations' => $product->variations()->count() > 1,
                ];
            }),
        ]);
    }

    public function orderSearch(Request $request)
    {
        $searchKey = $request->searchKey;
        $orders = \App\Models\Order::select('id', 'shop_id', 'order_code')
            ->where('order_code', 'like', '%' . $searchKey . '%')
            ->paginate(perPage(20));

        return view('backend.admin.exchange.order-search-results', compact('orders'));
    }

    public function getProductVariations($productId)
    {
        $variations = $this->exchangeService->getProductVariations($productId);

        return response()->json([
            'success' => true,
            'result' => $variations,
        ]);
    }
}
