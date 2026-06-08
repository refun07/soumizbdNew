@extends('layouts.seller')

@section('title')
    {{ translate('Orders') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Order List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.orders.index') }}" class="font-bold ">{{ translate('Orders') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Order List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
            <div class="flex justify-end">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="w-full"
                    placeholder="Type name or order code & hit enter">

                    <x-backend.inputs.select name="deliveryStatus" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Orders') }}" value=""
                            selected="{{ Request::get('deliveryStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('New Orders') }}" value="order_placed"
                            selected="{{ Request::get('deliveryStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Confirmed') }}" value="confirmed"
                            selected="{{ Request::get('deliveryStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Processing') }}" value="processing"
                            selected="{{ Request::get('deliveryStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Shipped') }}" value="shipped"
                            selected="{{ Request::get('deliveryStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Delivered') }}" value="delivered"
                            selected="{{ Request::get('deliveryStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Cancelled') }}" value="cancelled"
                            selected="{{ Request::get('deliveryStatus') }}" />
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="paymentStatus" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('Paid & Unpaid') }}" value=""
                            selected="{{ Request::get('paymentStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Paid Orders') }}" value="paid"
                            selected="{{ Request::get('paymentStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Unpaid Orders') }}" value="unpaid"
                            selected="{{ Request::get('paymentStatus') }}" />

                    </x-backend.inputs.select>
                </x-backend.forms.search-form>
            </div>
        </div>

        <table class="product-list-table footable w-full">
            <thead class="">
                <tr>
                    <th>
                        {{ translate('Customer') }}
                    </th>

                    <th>
                        {{ translate('Invoice No') }}
                    </th>
                    <th data-breakpoints="xs sm md">
                        {{ translate('Date') }}
                    </th>
                    <th data-breakpoints="xs sm md">
                        {{ translate('QTY') }}
                    </th>

                    <th data-breakpoints="xs sm">
                        {{ translate('Value') }}
                    </th>

                    <th data-breakpoints="xs sm">{{ translate('Delivery Status') }}</th>
                    <th data-breakpoints="xs sm">{{ translate('Payment') }}</th>
                    <th data-breakpoints="xs sm" class="w-[130px] text-right">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="">
                            <div class="inline-flex items-center gap-4">
                                <div class="max-xs:hidden">
                                    <img src="{{ uploadedAsset($order->user?->avatar) }}" alt=""
                                        class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                        onerror="this.onerror=null;this.src='{{ asset('backend/assets/img/placeholder-thumb.png') }}';" />
                                </div>
                                <div class=" line-clamp-2">
                                    {{ $order->user?->name }}
                                    <div class=" line-clamp-2">
                                        {{ $order->user?->phone }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="text-ceter">
                            <a href="{{ route('seller.orders.downloadInvoice', $order->id) }}" target="_blank"
                                title="Download Invoice">
                                <i data-feather="download" width="18"></i>
                                {{ getSetting('orderCodePrefix') }}{{ $order->order_code }}
                                <span class="text-lg text-theme-orange">
                                    <i class="fa-solid fa-file-arrow-down"></i>
                                </span>
                            </a>


                            @if ($order->pos_order)
                                <a title="Download Challan" href="{{ route('seller.orders.downloadChallan', $order->id) }}"
                                    target="_blank" title="Download Challan">
                                    <span class="text-lg text-theme-orange pl-1">
                                        <i class="fa-light fa-file-contract"></i>
                                    </span>
                                </a>
                            @endif
                        </td>

                        <td>
                            {{ date('d M, Y', strtotime($order->created_at)) }}
                        </td>

                        <td>
                            {{ $order->orderItems()->count() < 10 ? '0' . $order->orderItems()->count() : $order->orderItems()->count() }}
                        </td>

                        <td>
                            <span class="font-bold text-sky-500">{{ formatPrice($order->total_amount) }}</span>
                        </td>


                        <td>
                            <div>
                                <span
                                    class="px-2 py-1 text-xs {{ getOrderBgClass($order->delivery_status) }} text-white rounded-md leading-none">
                                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                                </span>
                                @if ($order->latestDeliveryStatusUpdate)
                                    <div class="text-[11px] text-muted mt-2 leading-4">
                                        <div class="font-medium text-foreground">
                                            {{ $order->latestDeliveryStatusUpdate->getActorName() ?? translate('System') }}
                                            @if ($order->latestDeliveryStatusUpdate->getActorRole())
                                                ({{ $order->latestDeliveryStatusUpdate->getActorRole() }})
                                            @endif
                                        </div>
                                        <div>
                                            {{ $order->latestDeliveryStatusUpdate->status }} ·
                                            {{ date('d M, Y', strtotime($order->latestDeliveryStatusUpdate->created_at)) }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>


                        <td>
                            <div class="inline-flex items-center capitalize gap-1.5">
                                <span
                                    class="{{ $order->payment_status == 'paid' ? 'text-teal-600' : 'text-neutral-300 dark:text-background-hover' }} text-2xl">
                                    <i class="fa-solid fa-circle-check"></i>
                                </span>
                                <span>{{ ucfirst($order->payment_status) }}</span>
                            </div>
                        </td>


                        <td class="text-right">
                            <a href="{{ route('seller.orders.show', $order->order_code) }}" class="text-secondary text-lg">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="card__footer">
            {{ $orders->links() }}
        </div>
    </div>
    </div>
@endsection
