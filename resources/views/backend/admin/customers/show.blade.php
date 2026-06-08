@extends('layouts.admin')

@section('title')
    {{ translate('Customer Details') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Customer Details') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.customers.index') }}" class="font-bold ">{{ translate('Customers') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Customer Details') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12 gap-3">
        <div class="xl:col-span-3">
            <div class="card">
                <div class="card__content">
                    <div class="flex justify-center">
                        <div>
                            <img src="{{ uploadedAsset($customer->avatar) }}" alt="" class="rounded-full">

                            <h2 class="mt-3 text-lg text-center">{{ $customer->name }}</h2>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h2 class="mt-3 text-lg">{{ translate('Account Information') }}</h2>
                        <p class="mt-2"><b>{{ translate('Full Name') }}:</b> {{ $customer->name }}</p>
                        <p class="mt-1"><b>{{ translate('Email') }}:</b> {{ $customer->email ?? '-' }}</p>
                        <p class="mt-1"><b>{{ translate('Phone') }}:</b> {{ $customer->phone ?? '-' }}</p>
                        <p class="mt-1"><b>{{ translate('Registration Date') }}:</b>
                            {{ date('d M, Y', $customer->created_at) }}</p>
                    </div>

                    <div class="mt-5">
                        <h2 class="mt-3 text-lg">{{ translate('Other Information') }}</h2>
                        <p class="mt-2"><b>{{ translate('Number of Orders') }}:</b> {{ $customer->orders()->count() }}
                        </p>
                        <p class="mt-1"><b>{{ translate('Ordered Amount') }}:</b>
                            {{ formatPrice($customer->orders()->sum('total_amount')) }}</p>
                        <p class="mt-1"><b>{{ translate('Number of items in cart') }}:</b>
                            {{ $customer->allCarts()->count() }}</p>
                        <p class="mt-1"><b>{{ translate('Number of items in wishlist') }}:</b>
                            {{ $customer->wishlists()->count() }}</p>
                        <p class="mt-1"><b>{{ translate('Total reviewed products') }}:</b>
                            {{ $customer->productReviews()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="xl:col-span-9">
            <div class="card">
                <div class="card__content">
                    <div>
                        <table class="product-list-table footable w-full">
                            <thead class="">
                                <tr>
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
                                @php
                                    $orders = $customer->orders;
                                @endphp
                                @foreach ($orders as $order)
                                    <tr>


                                        <td class="text-ceter">
                                            <a href="{{ route('admin.orders.downloadInvoice', $order->id) }}">
                                                <i data-feather="download" width="18"></i>
                                                {{ getSetting('orderCodePrefix') }}{{ $order->order_code }}
                                                <span class="text-lg text-theme-orange">
                                                    <i class="fa-solid fa-file-arrow-down"></i>
                                                </span>
                                        </td>

                                        <td>
                                            {{ date('d M, Y', strtotime($order->created_at)) }}
                                        </td>

                                        <td>
                                            {{ $order->orderItems()->count() < 10 ? '0' . $order->orderItems()->count() : $order->orderItems()->count() }}
                                        </td>

                                        <td>
                                            <span
                                                class="font-bold text-sky-500">{{ formatPrice($order->total_amount) }}</span>
                                        </td>


                                        <td>
                                            <span
                                                class="px-2 py-1 text-xs {{ getOrderBgClass($order->delivery_status) }} text-white rounded-md leading-none">
                                                {{ ucfirst(str_replace('_', ' ', $order->delivery_status)) }}
                                            </span>
                                        </td>


                                        <td>
                                            <div class="inline-flex items-center capitalize gap-1.5">
                                                <span
                                                    class="{{ $order->payment_status == 'paid' ? 'text-teal-600' : 'text-muted' }} text-2xl">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </span>
                                                <span>{{ ucfirst($order->payment_status) }}</span>
                                            </div>
                                        </td>


                                        <td class="text-right">
                                            <a href="{{ route('admin.orders.show', $order->order_code) }}"
                                                class="text-secondary text-lg">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
