@extends('layouts.' . $route)

@section('title')
    {{ translate('Exchange Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Exchange Details') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route($route . '.exchanges.index') }}" class="font-bold">{{ translate('Exchanges') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Exchange Details') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card__title border-b pb-3 mb-4">
                <h3 class="font-bold text-sm">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    {{ translate('Exchange Information') }}
                </h3>
            </div>
            <div class="card__content space-y-4 text-sm">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
                        'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
                        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    ];
                    $statusIcons = [
                        'pending' => 'fa-clock',
                        'processing' => 'fa-spinner fa-spin',
                        'completed' => 'fa-circle-check',
                        'cancelled' => 'fa-circle-xmark',
                    ];
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Status') }}</span>
                    <span class="capitalize px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusColors[$exchange->status] ?? '' }}">
                        <i class="fa-solid {{ $statusIcons[$exchange->status] ?? '' }} mr-1"></i>
                        {{ $exchange->status }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Requested By') }}</span>
                    <span class="font-medium">{{ $exchange->requested_by == 0 ? 'Admin' : ($exchange->requested_by == 1 ? 'Customer' : 'Delivery Partner') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Customer') }}</span>
                    <span class="font-medium">{{ $exchange->customer?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Original Invoice') }}</span>
                    @if ($exchange->originalOrder)
                        <a href="{{ route($route . '.orders.show', $exchange->originalOrder->order_code) }}" class="text-primary font-medium hover:underline">
                            <i class="fa-solid fa-file-invoice mr-1"></i>
                            {{ getSetting('orderCodePrefix') }}{{ $exchange->originalOrder->order_code }}
                        </a>
                    @else
                        <span>-</span>
                    @endif
                </div>
                @if ($exchange->exchangeOrder)
                    <div class="flex justify-between items-center">
                        <span class="text-muted">{{ translate('Exchange Invoice') }}</span>
                        <a href="{{ route($route . '.orders.show', $exchange->exchangeOrder->order_code) }}" class="text-blue-500 font-medium hover:underline">
                            <i class="fa-solid fa-file-invoice mr-1"></i>
                            {{ getSetting('orderCodePrefix') }}{{ $exchange->exchangeOrder->order_code }}
                        </a>
                    </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Created') }}</span>
                    <span class="font-medium">{{ $exchange->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card__title border-b pb-3 mb-4">
                <h3 class="font-bold text-sm">
                    <i class="fa-solid fa-calculator mr-1"></i>
                    {{ translate('Price Summary') }}
                </h3>
            </div>
            <div class="card__content space-y-4 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Returned Items Total') }}</span>
                    <span class="font-medium">{{ formatPrice($exchange->total_original_price) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Exchange Items Total') }}</span>
                    <span class="font-medium">{{ formatPrice($exchange->total_exchange_price) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-muted">{{ translate('Shipping') }}</span>
                    <span class="font-medium">{{ formatPrice($exchange->shipping_charge) }}</span>
                </div>
                <hr class="border-border">
                <div class="flex justify-between items-center">
                    <span class="font-bold">{{ translate('Price Difference') }}</span>
                    @if ($exchange->price_difference > 0)
                        <span class="font-bold text-orange-600">+{{ formatPrice($exchange->price_difference) }}</span>
                    @elseif ($exchange->price_difference < 0)
                        <span class="font-bold text-green-600">{{ formatPrice($exchange->price_difference) }}</span>
                    @else
                        <span class="font-bold">{{ formatPrice(0) }}</span>
                    @endif
                </div>
                <div class="text-center text-xs py-2 rounded-md {{ $exchange->price_difference > 0 ? 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : ($exchange->price_difference < 0 ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-primary/5 text-primary dark:bg-primary/20') }}">
                    @if ($exchange->price_difference > 0)
                        <i class="fa-solid fa-arrow-up mr-1"></i> {{ translate('Customer Pays') }}
                    @elseif ($exchange->price_difference < 0)
                        <i class="fa-solid fa-arrow-down mr-1"></i> {{ translate('Refund to Customer') }}
                    @else
                        <i class="fa-solid fa-equals mr-1"></i> {{ translate('Even Exchange') }}
                    @endif
                </div>
                @if ($exchange->refund_amount)
                    <div class="flex justify-between items-center">
                        <span class="text-muted">{{ translate('Refund Amount') }}</span>
                        <span class="font-bold text-green-600">{{ formatPrice($exchange->refund_amount) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="card">
            <div class="card__title border-b pb-3 mb-4">
                <h3 class="font-bold text-sm">
                    <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i>
                    {{ translate('Items Returned') }}
                    <span class="text-xs text-muted ml-2">({{ $exchange->items->count() }} {{ translate('items') }})</span>
                </h3>
            </div>
            <div class="space-y-3">
                @foreach ($exchange->items as $item)
                    @php
                        $originalProduct = $item->originalVariation?->product;
                        $originalThumb = $originalProduct ? uploadedAsset($originalProduct->thumbnail_image) : asset('images/image-error.png');
                        $originalVariationLabel = $item->originalVariation?->combinations?->map(fn($c) => $c->variationValue?->name)?->filter()?->implode(' / ') ?? '';
                    @endphp
                    <div class="flex items-center gap-3 p-3 bg-primary/5 rounded-lg">
                        <img src="{{ $originalThumb }}" alt="" class="w-12 h-12 rounded-md object-cover flex-shrink-0"
                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium line-clamp-1">{{ $originalProduct?->name ?? '-' }}</p>
                            @if ($originalVariationLabel)
                                <span class="inline-block text-xs text-muted bg-background px-2 py-0.5 rounded mt-1">{{ $originalVariationLabel }}</span>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-block text-xs bg-background px-2 py-0.5 rounded">x{{ $item->original_quantity }}</span>
                            <p class="text-sm font-medium mt-1">{{ formatPrice($item->original_unit_price) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card__title border-b pb-3 mb-4">
                <h3 class="font-bold text-sm">
                    <i class="fa-solid fa-arrow-left-arrow-right mr-1"></i>
                    {{ translate('Items to Receive') }}
                    <span class="text-xs text-muted ml-2">({{ $exchange->items->count() }} {{ translate('items') }})</span>
                </h3>
            </div>
            <div class="space-y-3">
                @foreach ($exchange->items as $item)
                    @php
                        $exchangeProduct = $item->exchangeVariation?->product;
                        $exchangeThumb = $exchangeProduct ? uploadedAsset($exchangeProduct->thumbnail_image) : asset('images/image-error.png');
                        $exchangeVariationLabel = $item->exchangeVariation?->combinations?->map(fn($c) => $c->variationValue?->name)?->filter()?->implode(' / ') ?? '';
                        $itemPriceDiff = $item->exchange_unit_price * $item->quantity - $item->original_unit_price * $item->original_quantity;
                    @endphp
                    <div class="flex items-center gap-3 p-3 bg-primary/5 rounded-lg">
                        <img src="{{ $exchangeThumb }}" alt="" class="w-12 h-12 rounded-md object-cover flex-shrink-0"
                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium line-clamp-1">{{ $exchangeProduct?->name ?? '-' }}</p>
                            @if ($exchangeVariationLabel)
                                <span class="inline-block text-xs text-muted bg-background px-2 py-0.5 rounded mt-1">{{ $exchangeVariationLabel }}</span>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-block text-xs bg-background px-2 py-0.5 rounded">x{{ $item->quantity }}</span>
                            <p class="text-sm font-medium mt-1">{{ formatPrice($item->exchange_unit_price) }}</p>
                            @if ($itemPriceDiff != 0)
                                <p class="text-xs mt-0.5 {{ $itemPriceDiff > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    {{ $itemPriceDiff > 0 ? '+' : '' }}{{ formatPrice($itemPriceDiff) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($exchange->status === 'pending')
        <div class="flex justify-end gap-3 mt-6">
            <x-backend.inputs.link href="javascript:void(0);"
                data-href="{{ route($route . '.exchanges.cancel', $exchange->id) }}"
                data-title="{{ translate('Are you sure you want to cancel this exchange?') }}"
                data-method="POST" data-button-text="{{ translate('Cancel') }}"
                data-button-style="danger" data-micromodal-trigger="confirm-modal"
                class="button--danger confirm-modal">
                <i class="fa-solid fa-xmark"></i>
                {{ translate('Cancel Exchange') }}
            </x-backend.inputs.link>

            <x-backend.inputs.link href="javascript:void(0);"
                data-href="{{ route($route . '.exchanges.process', $exchange->id) }}"
                data-title="{{ translate('Are you sure you want to process this exchange? This will create a new order and adjust stock.') }}"
                data-method="POST" data-button-text="{{ translate('Process') }}"
                data-button-style="primary" data-micromodal-trigger="confirm-modal"
                class="confirm-modal">
                <i class="fa-solid fa-rotate"></i>
                {{ translate('Process Exchange') }}
            </x-backend.inputs.link>
        </div>
    @endif
@endsection