@extends('layouts.seller')

@section('title')
    {{ translate('Stock Transfer') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Transfer List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-indigo">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Transfer List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            <x-backend.inputs.link href="{{ route('seller.stockTransfers.create') }}"> <span class="text-xl">
                    <i class="fal fa-plus"></i>
                </span> {{ translate('Add New') }}</x-backend.inputs.link>

            <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]"
                placeholder="Type warehouse name & hit enter" />
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-primary/10">
                <tr>
                    <th data-breakpoints="xs sm">
                        #
                    </th>
                    <th data-breakpoints="xs sm">
                        {{ translate('Date') }}
                    </th>
                    <th>
                        {{ translate('From Warehouse') }}
                    </th>
                    <th>
                        {{ translate('To Warehouse') }}
                    </th>

                    <th data-breakpoints="xs sm">
                        {{ translate('Total Products') }}
                    </th>

                    <th data-breakpoints="xs sm" class="w-[180px]">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody class="po-order-tbody">
                @foreach ($stockTransfers as $key => $stockTransfer)
                    <tr>
                        <td>{{ $key + 1 + ($stockTransfers->currentPage() - 1) * $stockTransfers->perPage() }}
                        </td>
                        <td>
                            <div class=" line-clamp-2">
                                {{ date('d M, Y', strtotime($stockTransfer->created_at)) }}
                            </div>
                        </td>
                        <td>
                            <span class="px-2 py-1 text-xs text-white rounded-md leading-none capitalize bg-red-500">
                                {{ $stockTransfer->fromWarehouse?->name ?? translate('N/A') }}
                            </span>
                        </td>

                        <td>
                            <span class="px-2 py-1 text-xs text-white rounded-md leading-none capitalize bg-green-500">
                                {{ $stockTransfer->toWarehouse?->name ?? translate('N/A') }}

                        </td>


                        <td>
                            <div class=" line-clamp-2">
                                {{ $stockTransfer->productVariations()->count() }}
                            </div>
                        </td>


                        <td>
                            <div class="option-dropdown w-full" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        <li>
                                            <a href="{{ route('seller.stockTransfers.show', ['transfer' => $stockTransfer->id]) }}"
                                                class="option-dropdown__option">
                                                {{ translate('Details') }}
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

        <div class="card__footer">
            {{ $stockTransfers->links() }}
        </div>
    </div>
@endsection
