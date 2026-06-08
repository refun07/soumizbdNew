@extends('layouts.admin')

@section('title')
    {{ translate('Seller List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Seller List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Seller List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[500px]"
                    placeholder="Type name/email/phone & hit enter">

                    <x-backend.inputs.select name="isApproved" class="filterSelect" :isRequired="false" data-search="false">
                        <x-backend.inputs.select-option name="{{ translate('All Sellers') }}" value=""
                            selected="{{ Request::get('isApproved') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Approved') }}" value="approved"
                            selected="{{ Request::get('isApproved') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Non - Approved') }}" value="non-approved"
                            selected="{{ Request::get('isApproved') }}" />
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="balance" class="filterSelect" :isRequired="false" data-search="false">
                        <x-backend.inputs.select-option name="{{ translate('Current Balance') }}" value=""
                            selected="{{ Request::get('balance') }}" />
                        <x-backend.inputs.select-option name="{{ translate('$ - High to Low') }}" value="highToLow"
                            selected="{{ Request::get('balance') }}" />
                        <x-backend.inputs.select-option name="{{ translate('$ - Low to High') }}" value="lowToHigh"
                            selected="{{ Request::get('balance') }}" />
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
                            {{ translate('Seller Info') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Shop Info') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Admin Commission Rate') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Current Balance') }}
                        </th>
                        <th data-breakpoints="xs sm">
                            {{ translate('Banned') }}
                        </th>
                        <th data-breakpoints="xs sm md">
                            {{ translate('Shop Approval') }}
                        </th>
                        <th data-breakpoints="xs sm md">
                            {{ translate('Shop Published') }}
                        </th>
                        <th data-breakpoints="xs sm" class="text-end">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sellers as $key => $seller)
                        @php
                            $shop = $seller->shop;
                        @endphp
                        <tr>
                            <td>
                                <span>{{ $key + 1 + ($sellers->currentPage() - 1) * $sellers->perPage() }}</span>
                            </td>

                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="max-xs:hidden">
                                        <img src="{{ uploadedAsset($seller->avatar) }}" alt=""
                                            class="w-[70px] h-[80px] rounded-md max-md:hidden"
                                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                    </div>
                                    <div>
                                        <div class=" line-clamp-2">
                                            {{ $seller->name }}
                                        </div>
                                        <div>
                                            {{ translate('Phone') }}: {{ $seller->phone ?? 'N/A' }}
                                        </div>
                                        <div>
                                            {{ translate('Email') }}: {{ $seller->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="flex items-center gap-4">
                                    <div class="max-xs:hidden">
                                        <img src="{{ uploadedAsset($shop->logo) }}" alt=""
                                            class="w-[70px] h-[80px] rounded-md"
                                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                    </div>
                                    <div>
                                        <div class=" line-clamp-2">
                                            {{ $shop->name }}
                                        </div>
                                        <div>
                                            {{ translate('Total Products') }}:
                                            {{ $shop->products()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ $shop->admin_commission_percentage }}%
                                </div>
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ formatPrice($shop->current_balance) }}
                                </div>
                            </td>

                            <td>
                                @can('edit_sellers')
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('admin.sellers.toggleBan') }}" variant="danger"
                                        name="isActiveCheckbox" value="{{ $seller->id }}"
                                        data-status="{{ $seller->is_banned }}"
                                        isChecked="{{ (int) $seller->is_banned == 1 }}" />
                                @endcan
                            </td>

                            <td>

                                @can('edit_sellers')
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('admin.sellers.toggleApproval') }}" variant="warning"
                                        name="isActiveCheckbox" value="{{ $seller->id }}"
                                        data-status="{{ $shop->is_approved }}"
                                        isChecked="{{ (int) $shop->is_approved == 1 }}" />
                                @endcan
                            </td>

                            <td>

                                @can('edit_sellers')
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('admin.sellers.togglePublished') }}" variant="success"
                                        name="isActiveCheckbox" value="{{ $seller->id }}"
                                        data-status="{{ $shop->is_published }}"
                                        isChecked="{{ (int) $shop->is_published == 1 }}" />
                                @endcan
                            </td>

                            <td class="flex justify-end">
                                <div class="option-dropdown w-[140px]" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>

                                            @can('edit_sellers')
                                                <li>
                                                    <a href="{{ route('admin.sellers.edit', ['seller' => $seller->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('pay_sellers')
                                                <li>
                                                    <a href="javascript:void(0);" data-micromodal-trigger="seller-payout-modal"
                                                        data-due="{{ $shop->current_balance }}"
                                                        data-shop-id="{{ $shop->id }}"
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

            <div class="my-5 px-8">
                {{ $sellers->links() }}
            </div>
        </div>
    </div>
@endsection
