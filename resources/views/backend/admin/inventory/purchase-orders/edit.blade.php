@extends('layouts.admin')

@section('title')
    @if (Route::is('admin.purchase-return.create'))
        {{ translate('Return Purchase Order') }}
    @else
        {{ translate('Update Purchase Order') }}
    @endif | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                @if (Route::is('admin.purchase-return.create'))
                    {{ translate('Return Purchase Order') }}
                @else
                    {{ translate('Update Purchase Order') }}
                @endif
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.purchase-orders.index') }}" class="font-bold ">{{ translate('Purchase Orders') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">
                @if (Route::is('admin.purchase-return.create'))
                    {{ translate('Return Purchase Order') }}
                @else
                    {{ translate('Update Purchase Order') }}
                @endif
            </p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    @php
        $isReturn = Route::is('admin.purchase-return.create');
        $returnOrder = $purchaseOrder->return;

        $warehousesData = [];
        foreach ($warehouses as $warehouse) {
            $warehousesData[] = ['id' => $warehouse->id, 'name' => $warehouse->name];
        }
        
        $suppliersData = [];
        foreach ($suppliers as $supplier) {
            $suppliersData[] = ['id' => $supplier->id, 'name' => $supplier->name];
        }
        
        // Prepare existing order items
        $existingOrderItems = [];
        $sourceOrders = $isReturn && $returnOrder ? $returnOrder->orders : $purchaseOrder->orders;
        if (isset($purchaseOrder) && $sourceOrders) {
            foreach ($sourceOrders as $order) {
                $product = $order->product;
                $variation = $order->productVariation;
                
                $stockQty = 0;
                $productVariationStock = $variation->productVariationStocks()
                    ->where('warehouse_id', $purchaseOrder->warehouse_id)
                    ->first();
                if (!is_null($productVariationStock)) {
                    $stockQty = $productVariationStock->stock_qty;
                }

                $purchasedQty = $isReturn
                    ? ($purchaseOrder->orders->firstWhere('product_variation_id', $order->product_variation_id)?->qty ?? 0)
                    : ($order->qty ?? 0);
                
                $existingOrderItems[] = [
                    'variationId' => $order->product_variation_id,
                    'productId' => $product->id,
                    'productName' => $product->collectTranslation('name'),
                    'variationName' => generateVariationName($variation->code),
                    'sku' => $variation->sku,
                    'unitPrice' => $order->unit_price,
                    'quantity' => $isReturn ? ($returnOrder ? $order->returned_qty : 0) : $order->qty,
                    'purchasedQty' => $purchasedQty,
                    'currentStock' => $stockQty,
                    'unitName' => $product->unit?->collectTranslation('name') ?? 'N/A',
                    'discount' => ($isReturn
                        ? ($returnOrder
                            ? ($order->discount / ($order->returned_qty > 0 ? $order->returned_qty : 1))
                            : ($purchasedQty > 0 ? ($order->discount / $purchasedQty) : 0))
                        : ($order->qty > 0 ? ($order->discount / $order->qty) : 0)),
                    'tax' => ($isReturn
                        ? ($returnOrder
                            ? ($order->tax / ($order->returned_qty > 0 ? $order->returned_qty : 1))
                            : ($purchasedQty > 0 ? ($order->tax / $purchasedQty) : 0))
                        : ($order->qty > 0 ? ($order->tax / $order->qty) : 0)),
                ];
            }
        }
        
        $initialFormData = [
            'warehouseId' => $purchaseOrder->warehouse_id ?? null,
            'supplierId' => $purchaseOrder->supplier_id ?? null,
            'date' => $isReturn
                ? ($returnOrder ? date('Y-m-d', $returnOrder->date) : date('Y-m-d'))
                : (isset($purchaseOrder->date) ? date('Y-m-d', $purchaseOrder->date) : date('Y-m-d')),
            'status' => $isReturn ? ($returnOrder->status ?? 'pending') : ($purchaseOrder->status ?? 'pending'),
            'tax' => $isReturn ? ($returnOrder->tax_percentage ?? 0) : ($purchaseOrder->tax_percentage ?? 0),
            'discount' => $isReturn ? ($returnOrder->discount ?? 0) : ($purchaseOrder->discount ?? 0),
            'shipping' => $isReturn ? ($returnOrder->shipping ?? 0) : ($purchaseOrder->shipping ?? 0),
            'note' => $isReturn ? ($returnOrder->note ?? '') : ($purchaseOrder->note ?? ''),
        ];
    @endphp

    <div id="purchase-order-root"></div>

    @section('scripts')
        <script>
            window.purchaseOrderData = {
                warehouses: {!! json_encode($warehousesData) !!},
                suppliers: {!! json_encode($suppliersData) !!},
                existingOrderItems: {!! json_encode($existingOrderItems) !!},
                initialFormData: {!! json_encode($initialFormData) !!},
                useInventory: {{ useInventory() ? 'true' : 'false' }},
                isEdit: true,
                isReturn: {{ $isReturn ? 'true' : 'false' }},
                submitUrl: '{{ $isReturn ? route('admin.purchase-return.store', $purchaseOrder->id) : route('admin.purchase-orders.update', $purchaseOrder->id) }}',
                method: '{{ $isReturn ? 'POST' : 'PUT' }}',
            };
        </script>
        
        @viteReactRefresh
        @vite(['resources/backend/purchase-order/index.tsx'])
    @endsection
@endsection
