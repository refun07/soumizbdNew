@extends('layouts.admin')

@section('title')
    {{ translate('Payout Requests') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Payout Requests') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Payout Requests') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->


    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                <div></div>


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
                            {{ translate('Date') }}
                        </th>

                        <th>
                            {{ translate('Requested Amount') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Total Due') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Status') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Message') }}
                        </th>
                        <th data-breakpoints="xs sm" class="text-end">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $key => $request)
                        @php
                            $shop = $request->shopInfo;
                            $due =
                                $shop->current_balance +
                                $shop->shopPayments()->where('status', '!=', 'paid')->sum('demanded_amount');
                        @endphp
                        <tr>
                            <td>{{ $key + 1 + ($requests->currentPage() - 1) * $requests->perPage() }}</td>
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
                                    {{ date('d M, Y', strtotime($request->created_at)) }}
                                </div>
                            </td>
                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($request->demanded_amount) }}
                                </div>
                            </td>
                            <td>
                                <div class="text-red-500 line-clamp-2">
                                    {{ formatPrice($due) }}
                                </div>
                            </td>
                            <td>
                                <span
                                    class="px-2 py-1 text-xs {{ $request->status == 'requested' ? 'bg-primary' : 'bg-red-500' }}  text-white rounded-md leading-none capitalize">
                                    {{ $request->status }}
                                </span>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ $request->additonal_info ?? '-' }}
                                </div>
                            </td>

                            <td class="text-end">
                                <div class="option-dropdown w-[140px]" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>

                                            @can('pay_sellers')
                                                <li>
                                                    <a href="javascript:void(0);" data-micromodal-trigger="seller-payout-modal"
                                                        data-shop-id="{{ $shop->id }}" data-id="{{ $request->id }}"
                                                        data-due="{{ $due }}"
                                                        data-demanded-amount="{{ $request->demanded_amount }}"
                                                        data-bank-name="{{ $shop->bank_name }}"
                                                        data-bank-acc-name="{{ $shop->bank_acc_name }}"
                                                        data-bank-acc-no="{{ $shop->bank_acc_no }}"
                                                        data-bank-routing-no="{{ $shop->bank_routing_no }}"
                                                        data-cash="{{ $shop->is_cash_payout }}"
                                                        data-bank="{{ $shop->is_bank_payout }}"
                                                        class="option-dropdown__option seller-payout-modal">
                                                        {{ translate('Make Payment') }}
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('show_payouts')
                                                <li>
                                                    <a href="{{ route('admin.sellers.payouts') }}?shopId={{ $shop->id }}"
                                                        class="option-dropdown__option">
                                                        {{ translate('Payment History') }}
                                                    </a>
                                                </li>
                                            @endcan

                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection
