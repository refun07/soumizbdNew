@extends('layouts.admin')

@section('title')
    {{ translate('Earning Histories') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Earning Histories') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Earning Histories') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                    placeholder="Type name/email/phone & hit enter">

                    <x-backend.inputs.select name="shopId" class="filterSelect" :isRequired="false" data-search="false">
                        <x-backend.inputs.select-option name="{{ translate('All Shops') }}" value=""
                            selected="{{ Request::get('shopId') }}" />
                        @foreach ($shops as $shop)
                            <x-backend.inputs.select-option name="{{ $shop->name }}" value="{{ $shop->id }}"
                                selected="{{ Request::get('shopId') }}" />
                        @endforeach
                    </x-backend.inputs.select>

                </x-backend.forms.search-form>
            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th data-breakpoints="xs sm">
                            #
                        </th>
                        <th>
                            {{ translate('Shop Info') }}
                        </th>

                        <th data-breakpoints="xs sm">
                            {{ translate('Order Code') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Order Amount') }}
                        </th>
                        <th>
                            {{ translate('Admin Earning') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Seller Earning') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Date') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($earnings as $key => $earning)
                        @php
                            $shop = $earning->shop;
                            $order = $earning->order;
                        @endphp

                        <tr>
                            <td>{{ $key + 1 + ($earnings->currentPage() - 1) * $earnings->perPage() }}</td>

                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="max-xs:hidden">
                                        <img src="{{ uploadedAsset($shop->logo) }}" alt=""
                                            class="w-[70px] h-[80px] rounded-md" />
                                    </div>
                                    <div>
                                        <div class=" line-clamp-2">
                                            {{ translate('Shop Name') }} : {{ $shop->name }}
                                        </div>
                                        <div class="line-clamp-2">
                                            {{ translate('Seller Name') }} : {{ $shop->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ getSetting('orderCodePrefix') }}{{ $order->order_code }}
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($order->total_amount) }}
                                </div>
                            </td>
                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($earning->admin_earning_amount) }}
                                </div>
                            </td>
                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($earning->shop_earning_amount) }}
                                </div>
                            </td>
                            <td>
                                <div class=" line-clamp-2">
                                    {{ date('d M, Y', strtotime($earning->created_at)) }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $earnings->links() }}
            </div>
        </div>
    </div>
@endsection
