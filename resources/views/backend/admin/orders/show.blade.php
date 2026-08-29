@extends('layouts.admin')

@section('title')
    {{ translate('Orders Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <style>
        .order-show-page {
            color: rgb(var(--foreground) / 1);
        }

        .dark .order-show-page .bg-white {
            background-color: rgb(var(--background) / 1) !important;
            color: rgb(var(--foreground) / 1) !important;
        }

        .dark .order-show-page .bg-gray-50,
        .dark .order-show-page .bg-gray-100 {
            background-color: rgb(var(--background-hover) / 1) !important;
            color: rgb(var(--foreground) / 1) !important;
        }

        .dark .order-show-page .text-gray-900,
        .dark .order-show-page .text-gray-800,
        .dark .order-show-page .text-gray-700 {
            color: rgb(var(--foreground) / 1) !important;
        }

        .dark .order-show-page .text-gray-600,
        .dark .order-show-page .text-gray-500,
        .dark .order-show-page .text-gray-400 {
            color: rgb(var(--muted) / 1) !important;
        }

        .dark .order-show-page .border-gray-100,
        .dark .order-show-page .border-gray-200,
        .dark .order-show-page .border-gray-300,
        .dark .order-show-page .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: rgb(var(--border) / 1) !important;
        }

        .dark .order-show-page input,
        .dark .order-show-page textarea,
        .dark .order-show-page select {
            background-color: rgb(var(--background) / 1) !important;
            border-color: rgb(var(--border) / 1) !important;
            color: rgb(var(--foreground) / 1) !important;
        }

        .dark .order-show-page input::placeholder,
        .dark .order-show-page textarea::placeholder {
            color: rgb(var(--muted) / 1) !important;
        }

        .dark .order-show-page option {
            background-color: rgb(var(--background) / 1);
            color: rgb(var(--foreground) / 1);
        }
    </style>

    <div class="order-show-page">
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Order Details') }}
            </span>
            @if($order->is_exchange_order)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800">
                    Exchange Order
                </span>
            @elseif(isset($exchange) && $exchange)
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">
                    Has Exchange
                </span>
            @endif
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Left Column - Order Information & Products -->
        <div class="xl:col-span-3 space-y-6">
            <!-- Order Information Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-semibold mb-6 pb-4 border-b border-gray-200">{{ translate('Order Information') }}</h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">
                    <!-- Column 1: Invoice & Customer Info -->
                    <div class="p-4 lg:p-6">
                        <p class="text-xs text-gray-500 uppercase mb-2">{{ translate('INVOICE') }}</p>
                        <p class="text-2xl font-bold mb-4">{{ getSetting('orderCodePrefix') }}{{ $order->orderGroup?->code ?? $order->order_code }}</p>
                        
                        <button onclick="window.open('{{ route('admin.orders.downloadInvoice', $order->id) }}', '_blank')" 
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded text-sm font-medium uppercase mb-6">
                            {{ translate('Download Invoice') }}
                        </button>

                        <p class="mb-6">
                            <span class="text-xs text-gray-500 mr-2">{{ translate('Order Date') }}:</span>
                            <span class="text-sm">{{ date('d M, Y', strtotime($order->created_at)) }}</span>
                        </p>

                        <p class="text-xs text-gray-500 uppercase mb-4">{{ translate('CUSTOMER INFORMATION') }}</p>
                        <table class="w-full">
                            <tr>
                                <td class="text-xs text-gray-500 py-1">{{ translate('Name') }}:</td>
                                <td class="text-sm py-1">{{ $orderGroup->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-gray-500 py-1">{{ translate('Phone') }}:</td>
                                <td class="text-sm py-1">{{ $orderGroup->phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-xs text-gray-500 py-1">{{ translate('Email') }}:</td>
                                <td class="text-sm py-1">{{ $orderGroup->email }}</td>
                            </tr>
                            @if($order->ip_address)
                            <tr>
                                <td class="text-xs text-gray-500 py-1">{{ translate('IP Address') }}:</td>
                                <td class="text-sm py-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-mono">{{ $order->ip_address }}</span>
                                        <button type="button" 
                                                onclick="openBlockIpModal('{{ $order->ip_address }}')"
                                                class="text-red-500 hover:text-red-600 text-xs px-2 py-1 rounded hover:bg-red-50 transition-colors"
                                                title="{{ translate('Block this IP') }}">
                                            <i class="fa-solid fa-ban mr-1"></i>
                                            {{ translate('Block') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    <!-- Column 2: Delivery Information -->
                    <div class="p-4 lg:p-6">
                        <p class="text-xs text-gray-500 uppercase mb-6">{{ translate('DELIVERY INFORMATION') }}</p>

                        <div class="space-y-6">
                            <!-- Shipping Address -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-xs text-gray-500 uppercase">{{ translate('SHIPPING ADDRESS') }}</p>
                                    <button onclick="openAddressModal('{{ $orderGroup->shipping_address }}', {{ $order->id }}, '{{ $orderGroup->direction }}', '{{ $orderGroup->phone }}')"
                                        class="text-blue-500 hover:text-blue-600">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                </div>
                                <p class="text-sm mb-1">{{ $orderGroup->shipping_address }}</p>
                                @if($orderGroup->direction)
                                    <p class="text-xs text-gray-500">{{ $orderGroup->direction }}</p>
                                @endif
                                <p class="text-xs text-gray-500">{{ translate('Phone') }}: {{ $orderGroup->phone }}</p>
                            </div>

                            <!-- Delivery & Payment Type -->
                            <div class="pt-4">
                                <table class="w-full">
                                    <tr>
                                        <td class="text-xs text-gray-500 py-1">{{ translate('Delivery Type') }}:</td>
                                        <td class="text-sm py-1">{{ $orderGroup->shipping_type ?? 'Standard' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs text-gray-500 py-1">{{ translate('Payment Type') }}:</td>
                                        <td class="text-sm py-1">{{ $orderGroup->transaction ? ucfirst(str_replace('_', ' ', $orderGroup->transaction->payment_method)) : ucfirst(str_replace('_', ' ', $order->payment_type ?? 'N/A')) }}</td>
                                    </tr>
                                </table>
                            </div>

                            @if($orderGroup->courier_partner || $orderGroup->tracking_number)
                            <!-- Courier / Delivery Partner Info -->
                            <div class="pt-4 border-t border-gray-200 mt-4">
                                <p class="text-xs text-gray-500 uppercase mb-3">{{ translate('COURIER INFORMATION') }}</p>
                                <table class="w-full">
                                    @if($orderGroup->courier_partner)
                                    <tr>
                                        <td class="text-xs text-gray-500 py-1">{{ translate('Partner') }}:</td>
                                        <td class="text-sm py-1 font-medium capitalize">{{ $orderGroup->courier_partner }}</td>
                                    </tr>
                                    @endif
                                    @if($orderGroup->tracking_number)
                                    <tr>
                                        <td class="text-xs text-gray-500 py-1">{{ translate('Tracking No') }}:</td>
                                        <td class="text-sm py-1 font-medium">{{ $orderGroup->tracking_number }}</td>
                                    </tr>
                                    @endif
                                    @if($orderGroup->delivery_status)
                                    <tr>
                                        <td class="text-xs text-gray-500 py-1">{{ translate('Courier Status') }}:</td>
                                        <td class="text-sm py-1 font-medium capitalize">{{ $orderGroup->delivery_status }}</td>
                                    </tr>
                                    @endif
                                </table>
                                @if($orderGroup->courier_response)
                                    @php $courierData = is_string($orderGroup->courier_response) ? json_decode($orderGroup->courier_response, true) : $orderGroup->courier_response; @endphp
                                    @if($courierData)
                                    <div class="mt-3 bg-gray-50 rounded-lg p-3">
                                        <p class="text-xs text-gray-500 uppercase mb-2">{{ translate('COURIER DETAILS') }}</p>
                                        @if($orderGroup->courier_partner === 'pathao' && isset($courierData['data']))
                                        <table class="w-full text-sm">
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Consignment ID') }}:</td>
                                                <td class="text-sm py-0.5">{{ $courierData['data']['consignment_id'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Delivery Fee') }}:</td>
                                                <td class="text-sm py-0.5">{{ $courierData['data']['delivery_fee'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Order Status') }}:</td>
                                                <td class="text-sm py-0.5 capitalize">{{ $courierData['data']['order_status'] ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                        @elseif($orderGroup->courier_partner === 'steadfast' && isset($courierData['consignment']))
                                        <table class="w-full text-sm">
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Tracking Code') }}:</td>
                                                <td class="text-sm py-0.5">{{ $courierData['consignment']['tracking_code'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Status') }}:</td>
                                                <td class="text-sm py-0.5 capitalize">{{ $courierData['consignment']['status'] ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                        @elseif($orderGroup->courier_partner === 'redx')
                                        <table class="w-full text-sm">
                                            @if(isset($courierData['tracking_id']))
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Tracking ID') }}:</td>
                                                <td class="text-sm py-0.5">{{ $courierData['tracking_id'] }}</td>
                                            </tr>
                                            @endif
                                            @if(isset($courierData['status']))
                                            <tr>
                                                <td class="text-xs text-gray-500 py-0.5">{{ translate('Status') }}:</td>
                                                <td class="text-sm py-0.5 capitalize">{{ $courierData['status'] }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                        @endif
                                        @if(isset($courierData['message']))
                                        <p class="text-xs text-gray-400 mt-2 italic">{{ $courierData['message'] }}</p>
                                        @endif
                                    </div>
                                    @endif
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-semibold mb-6 pb-4 border-b border-gray-200">{{ translate('Product Details') }}</h4>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('PRODUCT') }}</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('UNIT PRICE') }}</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('UNIT TAX') }}</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('QTY') }}</th>
                                <th class="text-right py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('SUB TOTAL') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderItems as $item)
                                @php
                                    $variation = $item->productVariation;
                                    $product = $variation?->product;
                                @endphp
                                <tr class="border-b border-gray-100">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-4">
                                            @if ($product)
                                                <div class="w-16 h-16 flex-shrink-0 border border-gray-200 rounded p-1">
                                                    <img src="{{ uploadedAsset($product->thumbnail_image) }}" 
                                                        class="w-full h-full object-cover rounded"
                                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">
                                                </div>
                                            @endif
                                            <div class="text-sm">
                                                {{ $product ? $product->collectTranslation('name') : 'N/A' }}
                                                @if ($product && $product->has_variation && $variation?->code)
                                                    <span class="text-gray-500">- {{ generateVariationName($variation->code) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm">{{ formatPrice($item->unit_price) }}</td>
                                    <td class="py-4 px-4 text-sm">{{ formatPrice($item->total_tax / $item->qty) }}</td>
                                    <td class="py-4 px-4 text-sm">{{ str_pad($item->qty, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="py-4 px-4 text-sm text-right">{{ formatPrice($item->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Order Summary -->
                <div class="mt-8 flex justify-end">
                    <div class="w-full max-w-xs space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 uppercase">{{ translate('Subtotal') }}</span>
                            <span>{{ formatPrice($order->amount) }}</span>
                        </div>
                        @if(isset($exchange) && $exchange && $order->is_exchange_order)
                            <div class="flex justify-between text-sm text-orange-600">
                                <span class="uppercase">{{ translate('Original Product Value') }}</span>
                                <span>-{{ formatPrice($exchange->total_original_price) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 uppercase">{{ translate('TAX') }}</span>
                            <span>{{ formatPrice($order->tax_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 uppercase">{{ translate('Delivery charge') }}</span>
                            <span>{{ formatPrice($order->shipping_charge_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 uppercase">{{ translate('Discount') }}</span>
                            <span>{{ formatPrice($order->discount_amount) }}</span>
                        </div>
                        @if ($order->coupon_id)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 uppercase">{{ translate('Coupon Discount') }}</span>
                                <span>{{ formatPrice($order->coupon_discount_amount) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-bold pt-3 border-t border-gray-200">
                            <span class="text-gray-600 uppercase">{{ translate('TOTAL') }}</span>
                            <span class="text-blue-500">{{ formatPrice($order->total_amount) }}</span>
                        </div>
                        @if(isset($exchange) && $exchange && $order->is_exchange_order)
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-dashed border-orange-300">
                                <span class="text-gray-600 uppercase">{{ translate('EFFECTIVE TOTAL') }}</span>
                                <span class="text-orange-600">{{ formatPrice($order->total_amount - $exchange->total_original_price) }}</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1 text-right">
                                {{ translate('Total minus original product value being exchanged') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Return/Refund Details (if exists) -->
            @if ($returnRefunds && count($returnRefunds) > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h4 class="text-lg font-semibold mb-6 pb-4 border-b border-gray-200">{{ translate('RETURN/REFUND DETAILS') }}</h4>

                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('S/L') }}</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('PRODUCT') }}</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('QTY') }}</th>
                                <th class="text-right py-3 px-4 text-xs font-medium text-gray-600 uppercase">{{ translate('REFUND AMOUNT') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returnRefunds as $i => $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-4 text-sm">{{ $i + 1 }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        @foreach ($item->items as $ritem)
                                            <p>{{ Str::limit($ritem->product->name ?? '', 40) }}</p>
                                        @endforeach
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        {!! $item->items->pluck('quantity')->implode('<br>') !!}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-right">{{ formatPrice($item->refund_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Exchange Details (if exists) -->
            @if(isset($exchange) && $exchange)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <h4 class="text-lg font-semibold">{{ translate('EXCHANGE INFORMATION') }}</h4>
                        <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $exchange->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : ($exchange->status == 'processing' ? 'bg-blue-100 text-blue-800' : ($exchange->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($exchange->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            @if($order->is_exchange_order && $exchange->originalOrder)
                                <p class="text-xs text-gray-500 uppercase mb-1">{{ translate('ORIGINAL ORDER') }}</p>
                                <a href="{{ route('admin.orders.show', $exchange->originalOrder->order_code) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                    {{ getSetting('orderCodePrefix') }}{{ $exchange->originalOrder->order_code }}
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs ml-1"></i>
                                </a>
                            @elseif(!$order->is_exchange_order && $exchange->exchangeOrder)
                                <p class="text-xs text-gray-500 uppercase mb-1">{{ translate('EXCHANGE ORDER') }}</p>
                                <a href="{{ route('admin.orders.show', $exchange->exchangeOrder->order_code) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                    {{ getSetting('orderCodePrefix') }}{{ $exchange->exchangeOrder->order_code }}
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs ml-1"></i>
                                </a>
                            @else
                                <p class="text-xs text-gray-500 uppercase mb-1">{{ $order->is_exchange_order ? 'ORIGINAL ORDER' : 'EXCHANGE ORDER' }}</p>
                                <p class="text-sm text-gray-400">{{ translate('Not yet created') }}</p>
                            @endif

                            <div class="mt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ translate('Original Total') }}</span>
                                    <span>{{ formatPrice($exchange->total_original_price) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ translate('Exchange Total') }}</span>
                                    <span>{{ formatPrice($exchange->total_exchange_price) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ translate('Shipping') }}</span>
                                    <span>{{ formatPrice($exchange->shipping_charge) }}</span>
                                </div>
                                <div class="flex justify-between text-sm font-medium">
                                    <span class="text-gray-600">{{ translate('Price Difference') }}</span>
                                    <span class="{{ $exchange->price_difference > 0 ? 'text-red-500' : ($exchange->price_difference < 0 ? 'text-green-500' : '') }}">
                                        {{ $exchange->price_difference > 0 ? '+' : '' }}{{ formatPrice($exchange->price_difference) }}
                                    </span>
                                </div>
                                @if($exchange->refund_amount && $exchange->refund_amount > 0)
                                    <div class="flex justify-between text-sm text-green-500 font-medium">
                                        <span>{{ translate('Refund') }}</span>
                                        <span>-{{ formatPrice($exchange->refund_amount) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase mb-3">{{ translate('EXCHANGE ITEMS') }}</p>
                            <div class="space-y-3">
                                @foreach($exchange->items as $item)
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center gap-2 mb-1.5">
                                            <span class="text-xs text-gray-400 line-through">{{ $item->originalVariation?->product?->name ?? 'N/A' }}@if($item->originalVariation?->code) ({{ generateVariationName($item->originalVariation->code) }})@endif</span>
                                            <i class="fa-solid fa-arrow-right text-[10px] text-gray-400"></i>
                                            <span class="text-xs font-medium">{{ $item->exchangeVariation?->product?->name ?? 'N/A' }}@if($item->exchangeVariation?->code) ({{ generateVariationName($item->exchangeVariation->code) }})@endif</span>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>Qty: {{ $item->quantity }}</span>
                                            <span class="{{ $item->price_difference > 0 ? 'text-red-500' : ($item->price_difference < 0 ? 'text-green-500' : '') }}">
                                                Diff: {{ $item->price_difference > 0 ? '+' : '' }}{{ formatPrice($item->price_difference) }}
                                            </span>
                                        </div>
                                        @if($item->reason)
                                            <div class="text-xs text-gray-400 mt-1">{{ translate('Reason') }}: {{ $item->reason }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('admin.exchanges.show', $exchange->id) }}" class="text-sm text-blue-600 hover:underline">
                                    <i class="fa-solid fa-arrow-right mr-1"></i>
                                    {{ translate('View Full Exchange Details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Order Updates -->
        <div class="xl:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h4 class="text-lg font-semibold mb-6 pb-4 border-b border-gray-200">{{ translate('Order Updates') }}</h4>

                <!-- Status Dropdowns -->
                <div class="space-y-4 mb-8">
                    <div>
                        <label class="text-sm text-gray-600 block mb-2">{{ translate('Payment Status') }}</label>
                        <select id="update_payment_status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>{{ translate('Paid') }}</option>
                            <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>{{ translate('Unpaid') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600 block mb-2">{{ translate('Delivery Status') }}</label>
                        <select id="update_delivery_status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                            <option value="order_placed" {{ $order->delivery_status == 'order_placed' ? 'selected' : '' }}>{{ translate('Order Placed') }}</option>
                            <option value="confirmed" {{ $order->delivery_status == 'confirmed' ? 'selected' : '' }}>{{ translate('Confirmed') }}</option>
                            <option value="processing" {{ $order->delivery_status == 'processing' ? 'selected' : '' }}>{{ translate('Processing') }}</option>
                            <option value="shipped" {{ $order->delivery_status == 'shipped' ? 'selected' : '' }}>{{ translate('Shipped') }}</option>
                            <option value="delivered" {{ $order->delivery_status == 'delivered' ? 'selected' : '' }}>{{ translate('Delivered') }}</option>
                            <option value="cancelled" {{ $order->delivery_status == 'cancelled' ? 'selected' : '' }}>{{ translate('Cancelled') }}</option>
                        </select>
                    </div>
                </div>

                @if ($order->latestDeliveryStatusUpdate)
                    <div class="border-t border-gray-200 pt-6 mb-8">
                        <h5 class="text-sm font-medium mb-3">{{ translate('Last Delivery Update') }}</h5>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $order->latestDeliveryStatusUpdate->status }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $order->latestDeliveryStatusUpdate->note }}
                            </p>
                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                <span class="text-xs font-medium text-gray-700">
                                    {{ $order->latestDeliveryStatusUpdate->getActorName() ?? translate('System') }}
                                </span>
                                @if ($order->latestDeliveryStatusUpdate->getActorRole())
                                    <span class="text-xs px-2 py-0.5 rounded bg-white text-gray-600 border border-gray-200">
                                        {{ $order->latestDeliveryStatusUpdate->getActorRole() }}
                                    </span>
                                @endif
                                <time class="text-xs text-gray-400">
                                    {{ date('d M, Y', strtotime($order->latestDeliveryStatusUpdate->created_at)) }}
                                    <span class="ml-1">{{ date('h:i A', strtotime($order->latestDeliveryStatusUpdate->created_at)) }}</span>
                                </time>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Notes Form -->
                <div class="border-t border-gray-200 pt-6 mb-8">
                    <h5 class="text-sm font-medium mb-4">{{ translate('Order Notes') }}</h5>
                    
                    <form action="{{ route('admin.orders.storeOrderUpdates') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="id" value="{{ $order->id }}">
                        
                        <div>
                            <input type="text" name="status" placeholder="Title"
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        
                        <div>
                            <textarea name="note" placeholder="Details" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
                            {{ translate('PUBLISH NOTE') }}
                        </button>
                    </form>
                </div>

                <!-- Previous Notes Timeline -->
                <div class="border-t border-gray-200 pt-6">
                    <h5 class="text-sm font-medium text-gray-500 uppercase mb-6">{{ translate('PREVIOUS NOTES') }}</h5>
                    
                    <div class="space-y-6">
                        @foreach ($orderUpdates as $note)
                            <div class="relative flex gap-4">
                                <!-- Timeline line and dot -->
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full flex-shrink-0"></div>
                                    @if (!$loop->last)
                                        <div class="w-px h-full bg-gray-300 border-l border-dashed border-gray-300 absolute top-3 left-1.5 -translate-x-1/2" style="height: calc(100% + 24px);"></div>
                                    @endif
                                </div>
                                
                                <!-- Note Content -->
                                <div class="flex-1 pb-2">
                                    <div class="flex items-start justify-between mb-1">
                                        <h6 class="font-medium text-sm capitalize">{{ $note->status }}</h6>
                                        @if ($note->type != 'default')
                                            <button onclick="deleteNote({{ $note->id }})" class="text-red-500 hover:text-red-600 text-sm">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <p class="text-xs text-gray-500 mb-2">{{ $note->note }}</p>
                                    
                                    <div class="flex items-center gap-2">
                                        @if ($note->getActorName())
                                            <span class="text-xs font-medium text-gray-700">
                                                {{ $note->getActorName() }}
                                            </span>
                                        @endif
                                        @if ($note->getCreatorRole())
                                            <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-medium">
                                                {{ $note->getCreatorRole() }}
                                            </span>
                                        @endif
                                        <time class="text-xs text-gray-400">
                                            {{ date('d M, Y', strtotime($note->created_at)) }}
                                            <span class="ml-1">{{ date('h:i A', strtotime($note->created_at)) }}</span>
                                        </time>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div id="address-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4" id="modal-title">{{ translate('Edit Address') }}</h3>
            <form id="address-form" action="{{ route('admin.orders.updateOrderAddress') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="modal-order-id">
                <input type="hidden" name="type" id="modal-address-type">
                
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-2">{{ translate('Address') }}</label>
                    <textarea name="address" id="modal-address" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div id="shipping-extra-fields" class="hidden space-y-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">{{ translate('Direction') }}</label>
                        <textarea name="direction" id="modal-direction" rows="2" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">{{ translate('Phone') }}</label>
                        <input type="text" name="phone" id="modal-phone" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAddressModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                        {{ translate('Cancel') }}
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">
                        {{ translate('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Block IP Modal -->
    <div id="block-ip-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">{{ translate('Block IP Address') }}</h3>
            <form id="block-ip-form">
                @csrf
                
                <div class="bg-gray-50 p-3 rounded mb-4">
                    <span class="text-sm text-gray-600">{{ translate('IP Address:') }}</span>
                    <span id="block-ip-address" class="font-medium ml-2 font-mono"></span>
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-2">{{ translate('Reason (optional)') }}</label>
                    <textarea name="reason" id="block-reason" rows="2" 
                        placeholder="{{ translate('Enter reason for blocking...') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-2">{{ translate('Duration') }}</label>
                    <select name="duration" id="block-duration" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500">
                        <option value="permanent">{{ translate('Permanent') }}</option>
                        <option value="1_hour">{{ translate('1 Hour') }}</option>
                        <option value="24_hours">{{ translate('24 Hours') }}</option>
                        <option value="7_days">{{ translate('7 Days') }}</option>
                        <option value="30_days">{{ translate('30 Days') }}</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeBlockIpModal()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                        {{ translate('Cancel') }}
                    </button>
                    <button type="submit" id="block-ip-btn" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded">
                        {{ translate('Block IP') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Payment Status Update
        document.getElementById('update_payment_status').addEventListener('change', function() {
            const orderId = {{ $order->id }};
            const status = this.value;
            
            fetch('{{ route('admin.orders.updatePaymentStatus') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    alert('{{ translate('Payment status has been updated') }}');
                    window.location.reload();
                }
            });
        });

        // Delivery Status Update
        document.getElementById('update_delivery_status').addEventListener('change', function() {
            const orderId = {{ $order->id }};
            const status = this.value;
            
            fetch('{{ route('admin.orders.updateDeliveryStatus') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    alert('{{ translate('Delivery status has been updated') }}');
                    window.location.reload();
                }
            });
        });

        // Address Modal Functions
        function openAddressModal(address, orderId, direction = '', phone = '') {
            document.getElementById('modal-address-type').value = 'shipping_address';
            document.getElementById('modal-order-id').value = orderId;
            document.getElementById('modal-address').value = address;
            document.getElementById('modal-title').textContent = '{{ translate('Edit Shipping Address') }}';
            
            document.getElementById('modal-direction').value = direction;
            document.getElementById('modal-phone').value = phone;
            
            document.getElementById('address-modal').classList.remove('hidden');
        }

        function closeAddressModal() {
            document.getElementById('address-modal').classList.add('hidden');
        }

        // Delete Note
        function deleteNote(noteId) {
            if (confirm('{{ translate('Are you sure want to delete this item?') }}')) {
                fetch(`{{ route('admin.orders.deleteOrderUpdate', '') }}/${noteId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(() => window.location.reload());
            }
        }

        // Close modal when clicking outside
        document.getElementById('address-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddressModal();
            }
        });

        // Block IP Modal Functions
        function openBlockIpModal(ipAddress) {
            document.getElementById('block-ip-address').textContent = ipAddress;
            document.getElementById('block-reason').value = `Blocked from order #{{ getSetting('orderCodePrefix') }}{{ $order->order_code }}`;
            document.getElementById('block-duration').value = 'permanent';
            document.getElementById('block-ip-modal').classList.remove('hidden');
        }

        function closeBlockIpModal() {
            document.getElementById('block-ip-modal').classList.add('hidden');
            document.getElementById('block-ip-form').reset();
        }

        // Handle Block IP Form Submit
        document.getElementById('block-ip-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const ipAddress = document.getElementById('block-ip-address').textContent;
            const reason = document.getElementById('block-reason').value;
            const duration = document.getElementById('block-duration').value;
            
            let blockedUntil = null;
            if (duration === '1_hour') {
                blockedUntil = new Date(Date.now() + 60 * 60 * 1000).toISOString();
            } else if (duration === '24_hours') {
                blockedUntil = new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString();
            } else if (duration === '7_days') {
                blockedUntil = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString();
            } else if (duration === '30_days') {
                blockedUntil = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString();
            }
            
            const blockBtn = document.getElementById('block-ip-btn');
            blockBtn.disabled = true;
            blockBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> {{ translate('Blocking...') }}';
            
            fetch('/admin/api/blocked-ips', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ip_address: ipAddress,
                    reason: reason,
                    blocked_until: blockedUntil
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ translate('IP address blocked successfully') }}');
                    closeBlockIpModal();
                } else {
                    alert(data.message || '{{ translate('Failed to block IP') }}');
                }
            })
            .catch(error => {
                console.error('Error blocking IP:', error);
                alert('{{ translate('Failed to block IP address') }}');
            })
            .finally(() => {
                blockBtn.disabled = false;
                blockBtn.innerHTML = '{{ translate('Block IP') }}';
            });
        });

        // Close block IP modal when clicking outside
        document.getElementById('block-ip-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBlockIpModal();
            }
        });
    </script>
@endsection
