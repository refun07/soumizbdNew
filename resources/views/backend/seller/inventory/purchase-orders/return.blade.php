@extends('layouts.seller')

@section('title')
    {{ translate('Purchase Return') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Purchase Return List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-indigo">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Purchase Return List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                <div></div>
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]"
                    placeholder="Type reference code & hit enter" />
            </div>

            <div class="overflow-x-auto">
                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Date') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Reference') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Purchase Order') }}
                            </th>
                            <th data-breakpoints="xs sm md">
                                {{ translate('Supplier') }}
                            </th>
                            <th>
                                {{ translate('Warehouse') }}
                            </th>
                            <th>
                                {{ translate('Status') }}
                            </th>
                            <th>
                                {{ translate('Total') }}
                            </th>

                            <th data-breakpoints="xs sm">
                                {{ translate('Payment Status') }}
                            </th>

                            <th data-breakpoints="xs sm md">
                                {{ translate('Paid') }}
                            </th>
                            <th data-breakpoints="xs sm md">
                                {{ translate('Due') }}
                            </th>


                            <th data-breakpoints="xs sm" class="w-[180px]">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="po-order-tbody">
                        @foreach ($returnOrders as $key => $returnOrder)
                            <tr>
                                <td>{{ $key + 1 + ($returnOrders->currentPage() - 1) * $returnOrders->perPage() }}</td>
                                <td class="whitespace-nowrap">
                                    {{ date('d M, Y', $returnOrder->date) }}
                                </td>

                                <td>#PR-{{ $returnOrder->reference_code }}</td>
                                <td class="text-warning-500">
                                    <a class="text-blue-500"
                                        href="{{ route('seller.purchase-orders.index') }}?search=PO-{{ $returnOrder->purchaseOrder->reference_code }}">#PO-{{ $returnOrder->purchaseOrder->reference_code }}</a>
                                </td>
                                <td>{{ $returnOrder->supplier?->name ?? '-' }}</td>
                                <td>{{ $returnOrder->warehouse?->name ?? '-' }}</td>

                                <td>
                                    <span
                                        class="px-2 py-1 text-xs text-white rounded-md leading-none capitalize {{ poStatusBadge($returnOrder->status) }}">{{ $returnOrder->status }}
                                    </span>
                                </td>

                                <td>{{ formatPrice($returnOrder->grand_total) }}</td>

                                <td>
                                    <span
                                        class="px-2 py-1 text-xs {{ $returnOrder->payment_status == 'paid' ? 'bg-green-500 ' : 'bg-red-500' }} text-white rounded-md leading-none capitalize">{{ $returnOrder->payment_status }}
                                    </span>
                                </td>

                                <td>{{ formatPrice($returnOrder->paid) }}</td>
                                <td>{{ formatPrice($returnOrder->due) }}</td>

                                <td>
                                    <div class="option-dropdown w-[130px]" tabindex="0">
                                        <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                            <span>{{ translate('Actions') }}</span>
                                        </div>

                                        <div class="option-dropdown__options">
                                            <ul>
                                                <li>
                                                    <a href="{{ route('seller.purchase-return.create', ['id' => $returnOrder->purchase_order_id]) }}"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit Return') }}
                                                    </a>
                                                </li>

                                                @php
                                                    $payments = $returnOrder->payments;
                                                    $totalPaid = $payments->sum('paid_amount');
                                                    $due = $returnOrder->due;
                                                @endphp

                                                @if ($due != 0)
                                                    <li>
                                                        <a href="javascript:void(0);"
                                                            class="option-dropdown__option purchase-order-payment-modal"
                                                            data-micromodal-trigger="purchase-order-payment-modal"
                                                            data-payable-id="{{ $returnOrder->id }}"
                                                            data-payable-type="App\Models\PurchaseReturnOrder"
                                                            data-payable-amount="{{ $due }}">
                                                            {{ translate('Make Payment') }}
                                                        </a>
                                                    </li>
                                                @endif


                                                <li>
                                                    <a href="javascript:void(0);"
                                                        class="option-dropdown__option purchase-order-payment-histories-modal"
                                                        data-payable-id="{{ $returnOrder->id }}"
                                                        data-payable-type="App\Models\PurchaseReturnOrder"
                                                        data-micromodal-trigger="purchase-order-payment-histories-modal">
                                                        {{ translate('Show Payments') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
