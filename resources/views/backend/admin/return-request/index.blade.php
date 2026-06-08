@extends('layouts.' . $route)

@section('title')
    {{ translate('Return Request') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Return Request List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route($route . '.return-requests.index') }}" class="font-bold ">{{ translate('Return Request') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Return Request List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col lg:flex-row xl:items-center justify-between gap-3">
                <div class="flex items-center justify-between gap-4">
                    <p>Search Invoice</p>
                    <x-backend.forms.search-form searchKey="{{ $searchKey }}"
                        placeholder="Search With Invoice ID/ Customer Name" class="w-[330px]">

                    </x-backend.forms.search-form>
                </div>
                <x-backend.inputs.link href="{{ route($route . '.return-requests.create') }}">
                    <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span>
                    {{ translate('ADD RETURN REQUEST') }}
                </x-backend.inputs.link>



            </div>


            <table class="product-list-table footable w-full [&_td]:">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            {{ translate('Customer') }}
                        </th>
                        <th>
                            {{ translate('Invoice No') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Type') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Request') }}
                        </th>
                        <th>
                            {{ translate('Product') }}
                        </th>

                        <th data-breakpoints="xs sm">{{ translate('REASON') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('PAYMENT') }}</th>
                        <th data-breakpoints="xs sm md">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($returnRequests as $item)
                        <tr>
                            <td class="">
                                <div class="inline-flex items-center gap-4">

                                    <div class=" line-clamp-2">
                                        {{ $item->user?->name }}
                                        <div class=" line-clamp-2">
                                            {{ $item->user?->phone }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-ceter">
                                <a href="{{ route('admin.orders.downloadInvoice', $item->order_id) }}">
                                    <i data-feather="download" width="18"></i>
                                    {{ getSetting('orderCodePrefix') }}{{ $item->invoice_no }}
                                    <span class="text-lg text-theme-orange">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                    </span>
                                </a>
                            </td>
                            <td>
                                <span class="capitalize px-2 py-1 rounded text-xs font-medium {{ $item->request_type === 'refund' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300' }}">
                                    {{ $item->request_type ?? 'return' }}
                                </span>
                            </td>
                            <td>
                                @if ($item->requested_by == 0)
                                    <span class="text-gray-600">Admin</span>
                                @elseif ($item->requested_by == 1)
                                    <span class="text-gray-600">Customer</span>
                                @else
                                    <span class="text-gray-600">Delivery Partner</span>
                                @endif
                            </td>
                            <td>
                                @foreach ($item->items as $ritem)
                                    <p>
                                        <a href="{{ url('/products/') . ($ritem->productVariation->product->slug ?? '') }}"
                                            target="_blank">{{ Str::limit($ritem->productVariation->product->name ?? '', 40) }}</a>
                                    </p>
                                @endforeach
                            </td>
                            <td>
                                <p class="text-gray-600">{!! $item->items->pluck('reason')->implode('<br>') !!}</p>
                            </td>
                            <td>
                                <div class="inline-flex items-center capitalize gap-1.5">
                                    <span
                                        class="{{ $item->order->payment_status == 'paid' ? 'text-teal-600' : 'text-neutral-300 dark:text-neutral-800' }} text-2xl">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </span>
                                    <span>{{ ucfirst($item->order->payment_status) }}</span>
                                </div>
                            </td>

                            <td class="min-w-[180px]">
                                @if ($item->order->payment_status == 'paid')
                                    @php
                                        $isDisabled = $item->status == 1 || $item->status == 4;
                                    @endphp

                                    <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2 gap-2">
                                        {{-- Button 1: Process Return --}}
                                        <x-backend.inputs.link href="javascript:void(0);"
                                            data-href="{{ route($route . '.return-requests.update', $item->id) }}"
                                            data-title="{{ translate('Are you sure want to update this item?') }}"
                                            data-method="PUT" data-button-text="{{ translate('Process') }}"
                                            data-button-style="primary" data-micromodal-trigger="confirm-modal"
                                            class="text-sm confirm-modal w-full sm:w-auto {{ $isDisabled ? 'bg-gray-400 pointer-events-none opacity-80' : '' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                            {{ $isDisabled ? translate('Processed') : translate('Process Return') }}
                                        </x-backend.inputs.link>

                                        {{-- Button 2: Refund --}}
                                        @if ($item->status == 4 && $item->refund_amount != null)
                                            <div class="text-sm text-teal-600 flex items-center gap-1 w-full sm:w-auto">
                                                <i class="fa-solid fa-circle-check"></i>
                                                {{ formatPrice($item->refund_amount) }} {{ translate('Refunded') }}
                                            </div>
                                        @else
                                            <x-backend.inputs.link href="javascript:void(0);"
                                                data-micromodal-trigger="refund-payment-modal"
                                                data-id="{{ $item->id }}" data-invoice="{{ $item->invoice_no }}"
                                                data-order-amount="{{ formatPrice($item->order->total_amount) }}"
                                                class="button--success refund-payment-modal text-sm w-full sm:w-auto">
                                                <i class="fa-light fa-circle-dollar"></i>
                                                {{ translate('Process Refund') }}
                                            </x-backend.inputs.link>
                                        @endif
                                    </div>
                                @endif
                            </td>



                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $returnRequests->links() }}
            </div>
        </div>
    </div>
@endsection
