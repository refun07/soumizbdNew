<?php

namespace App\Services;

use App\Models\DamageStock;
use App\Models\Order;
use App\Models\ProductVariationStock;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnRequestService
{
    # get data
    public function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey     = null;
        $limit         = $request->limit ?? perPage();

        $returnRequests = ReturnRequest::shop()->with('user', 'items.productVariation.product', 'order')->latest();

        if ($request->search != null) {
            $searchKey  = $request->search;
            $returnRequests   = $returnRequests->whereHas('user', function ($q) use ($searchKey) {
                $q->where('name', 'like', '%' . $searchKey . '%');
            })->orWhere('invoice_no', 'like', '%' . $request->search . '%');
        }


        $returnRequests   = $returnRequests->paginate($limit);


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'returnRequests'     => $returnRequests,
                'searchKey'    => $searchKey,
                'route'     => routePrefix()
            ],
        ];

        return $data;
    }

    public function create($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;

        $order = null;

        if ($request->search != null) {
            $searchKey = $request->search;
            $order = Order::where('order_code',  'like', '%' .  $searchKey  . '%')->with('orderItems')->first();
        }


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'searchKey'     => $searchKey,
                'order'         => $order,
                'route'         => routePrefix()
            ],
        ];

        return $data;
    }


    /**
     * Create a new return request
     *
     * @param array $data
     * @return ReturnRequest
     */
    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $selectedItems = array_filter($data['items'], function ($item) {
                return isset($item['product_variation_id']);
            });

            if (empty($selectedItems)) {
                throw ValidationException::withMessages(['items' => 'At least one product must be selected.']);
            }

            $requestType = $data['request_type'] ?? 'return';

            $returnRequest = ReturnRequest::create([
                'shop_id' => shop()->id ?? null,
                'order_id' => $data['order_id'],
                'customer_id' => $data['customer_id'],
                'invoice_no' => $data['invoice_no'],
                'status' => 0,
                'request_type' => $requestType,
                'requested_by' => $data['requested_by'] ?? 0,
                'created_by' => userId(),
            ]);
            $order = Order::findOrFail($data['order_id']);

            foreach ($selectedItems as $item) {
                ReturnRequestItem::create([
                    'return_request_id' => $returnRequest->id,
                    'product_variation_id' => $item['product_variation_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'reason' => $item['reason'],
                    'note' => $item['note'] ?? null,
                    'status' => 0,
                    'is_paid' => false,
                    'media_path' => $item['media_path'] ?? null,
                ]);

                $this->handleReturnStock($item, $order, $returnRequest);
            }


            DB::commit();

            $message = translate('Return request has been added successfully');

            $data = [
                'status'    => 200,
                'message'   => $message,
                'result'    => [],
            ];
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleReturnStock(array $item, Order $order, ReturnRequest $returnRequest): void
    {
        $damageReasons = ['expired', 'broken', 'damage', 'defected'];
        $reason = strtolower($item['reason']);
        $warehouseId = $order->warehouse_id ?: null;

        if (in_array($reason, $damageReasons)) {
            $damageTypeMap = [
                'expired' => 'expired',
                'broken' => 'warehouse',
                'damage' => 'warehouse',
                'defected' => 'manufacturing_defect',
            ];

            DamageStock::create([
                'shop_id' => shop()->id ?? null,
                'product_variation_id' => $item['product_variation_id'],
                'warehouse_id' => $warehouseId,
                'quantity' => $item['quantity'],
                'damage_type' => $damageTypeMap[$reason] ?? 'warehouse',
                'reason' => $item['reason'],
                'return_request_id' => $returnRequest->id,
                'return_date' => now(),
                'created_by' => auth()->id(),
            ]);
        } else {
            $product_variation_stock = new ProductVariationStock;
            $product_variation_stock->product_variation_id = $item['product_variation_id'];
            $product_variation_stock->warehouse_id = $warehouseId;
            $product_variation_stock->stock_qty = $item['quantity'];
            $product_variation_stock->save();
        }
    }

    public function getById($id)
    {
        $returnRequest = ReturnRequest::findOrFail($id);
        return $returnRequest;
    }

    public function update($attributes, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $returnRequest = $this->getById($id);
        $returnRequest->status = 1;
        $returnRequest->save();

        $message = translate('Return request has been updated successfully');

        $data = [
            'status'    => 200,
            'message'   => $message,
            'result'    => [],
        ];
        return $data;
    }

    public function refundPayment($attributes)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $returnRequest = $this->getById($attributes['id']);
        $returnRequest->status = 4;
        $returnRequest->refund_amount = $attributes['amount'];
        $returnRequest->save();

        $returnRequest->load('order');
        syncOrderProfitLog($returnRequest->order);

        $data = [
            'status'    => 200,
            'message'   => translate('Refund payment has been updated successfully'),
            'result'    => [],
        ];
        return $data;
    }

    public function getOrderInvoice($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];


        $searchKey = $request->searchKey;
        $orders = Order::shopOrders()->select('id', 'shop_id', 'order_code')->where('order_code', 'like', '%' . $searchKey . '%')->paginate(perPage(20));
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [
                'orders'     => $orders,
            ],
        ];

        return $data;
    }
}
