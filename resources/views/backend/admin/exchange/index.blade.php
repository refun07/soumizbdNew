@extends('layouts.' . $route)

@section('title')
    {{ translate('Exchange List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Exchange List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route($route . '.exchanges.index') }}" class="font-bold">{{ translate('Exchanges') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Exchange List') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="theme-table">
            <div class="card__title border-none theme-table__filter flex flex-col lg:flex-row xl:items-center justify-between gap-3">
                <div class="flex items-center justify-between gap-4">
                    <p>{{ translate('Search') }}</p>
                    <x-backend.forms.search-form searchKey="{{ $searchKey }}"
                        placeholder="{{ translate('Search Invoice / Customer') }}" class="w-[330px]">
                    </x-backend.forms.search-form>
                </div>
                <x-backend.inputs.link href="{{ route($route . '.exchanges.create') }}">
                    <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span>
                    {{ translate('ADD EXCHANGE') }}
                </x-backend.inputs.link>
            </div>

            <table class="product-list-table footable w-full [&_td]:">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>{{ translate('Customer') }}</th>
                        <th>{{ translate('Original Invoice') }}</th>
                        <th>{{ translate('Exchange Invoice') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Products') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Price Diff') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Shipping') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th data-breakpoints="xs sm md">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exchanges as $exchange)
                        <tr>
                            <td>
                                <div class="inline-flex items-center gap-4">
                                    <div class="line-clamp-2">
                                        {{ $exchange->customer?->name }}
                                        <div class="line-clamp-2">
                                            {{ $exchange->customer?->phone }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($exchange->originalOrder)
                                    <a href="{{ route($route . '.orders.show', $exchange->originalOrder->order_code) }}">
                                        {{ getSetting('orderCodePrefix') }}{{ $exchange->originalOrder->order_code }}
                                        <span class="text-lg text-theme-orange">
                                            <i class="fa-solid fa-file-arrow-down"></i>
                                        </span>
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if ($exchange->exchangeOrder)
                                    <a href="{{ route($route . '.orders.show', $exchange->exchangeOrder->order_code) }}">
                                        {{ getSetting('orderCodePrefix') }}{{ $exchange->exchangeOrder->order_code }}
                                        <span class="text-lg text-blue-500">
                                            <i class="fa-solid fa-file-arrow-down"></i>
                                        </span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @foreach ($exchange->items as $item)
                                    <p class="text-sm">
                                        <span class="line-through text-muted">{{ Str::limit($item->originalVariation?->product?->name ?? '', 20) }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs mx-1 text-blue-500"></i>
                                        <span>{{ Str::limit($item->exchangeVariation?->product?->name ?? '', 20) }}</span>
                                        <span class="text-muted">x{{ $item->quantity }}</span>
                                    </p>
                                @endforeach
                            </td>
                            <td>
                                @if ($exchange->price_difference > 0)
                                    <span class="text-orange-600 font-medium">+{{ formatPrice($exchange->price_difference) }}</span>
                                @elseif ($exchange->price_difference < 0)
                                    <span class="text-green-600 font-medium">{{ formatPrice($exchange->price_difference) }}</span>
                                @else
                                    <span class="text-muted">{{ formatPrice(0) }}</span>
                                @endif
                            </td>
                            <td>
                                {{ formatPrice($exchange->shipping_charge) }}
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                        'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                        'completed' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                    ];
                                @endphp
                                <span class="capitalize px-2 py-1 rounded text-xs font-medium {{ $statusColors[$exchange->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $exchange->status }}
                                </span>
                            </td>
                            <td class="min-w-[180px]">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2 gap-2">
                                    <x-backend.inputs.link href="{{ route($route . '.exchanges.show', $exchange->id) }}"
                                        class="text-sm">
                                        <i class="fa-solid fa-eye"></i>
                                        {{ translate('View') }}
                                    </x-backend.inputs.link>

                                    @if ($exchange->status === 'pending')
                                        <x-backend.inputs.link href="javascript:void(0);"
                                            data-href="{{ route($route . '.exchanges.process', $exchange->id) }}"
                                            data-title="{{ translate('Are you sure you want to process this exchange?') }}"
                                            data-method="POST" data-button-text="{{ translate('Process') }}"
                                            data-button-style="primary" data-micromodal-trigger="confirm-modal"
                                            class="text-sm confirm-modal">
                                            <i class="fa-solid fa-rotate"></i>
                                            {{ translate('Process') }}
                                        </x-backend.inputs.link>

                                        <x-backend.inputs.link href="javascript:void(0);"
                                            data-href="{{ route($route . '.exchanges.cancel', $exchange->id) }}"
                                            data-title="{{ translate('Are you sure you want to cancel this exchange?') }}"
                                            data-method="POST" data-button-text="{{ translate('Cancel') }}"
                                            data-button-style="danger" data-micromodal-trigger="confirm-modal"
                                            class="text-sm confirm-modal button--danger">
                                            <i class="fa-solid fa-xmark"></i>
                                            {{ translate('Cancel') }}
                                        </x-backend.inputs.link>
                                    @endif

                                    @if ($exchange->status === 'processing' && $exchange->refund_amount)
                                        <div class="text-sm text-teal-600 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check"></i>
                                            {{ formatPrice($exchange->refund_amount) }} {{ translate('Refund') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $exchanges->links() }}
            </div>
        </div>
    </div>
@endsection
