@extends('layouts.admin')

@section('title')
    {{ translate('Stock Adjustment') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Adjustment List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Adjustment List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            @can('create_stock_adjustment')
                <x-backend.inputs.link href="{{ route('admin.stockAdjustments.create') }}"> <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span> {{ translate('Add New') }}</x-backend.inputs.link>
            @else
                <div></div>
            @endcan

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
                        {{ translate('Warehouse') }}
                    </th>
                    <th>
                        {{ translate('Total Products') }}
                    </th>

                    <th data-breakpoints="xs sm" class="w-[180px]">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody class="po-order-tbody">
                @foreach ($stockAdjustments as $key => $stockAdjustment)
                    <tr>
                        <td>{{ $key + 1 + ($stockAdjustments->currentPage() - 1) * $stockAdjustments->perPage() }}
                        </td>
                        <td>
                            <div class=" line-clamp-2">
                                {{ date('d M, Y', strtotime($stockAdjustment->created_at)) }}
                            </div>
                        </td>
                        <td>
                            {{ $stockAdjustment->warehouse?->name ?? translate('N/A') }}
                        </td>

                        <td>
                            <div class=" line-clamp-2">
                                {{ $stockAdjustment->productVariations()->count() }}
                            </div>
                        </td>

                        <td>
                            <div class="option-dropdown w-full" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        @can('show_adjustment_details')
                                            <li>
                                                <a href="{{ route('admin.stockAdjustments.show', ['adjustment' => $stockAdjustment->id]) }}"
                                                    class="option-dropdown__option">
                                                    {{ translate('Details') }}
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
            {{ $stockAdjustments->links() }}
        </div>
    </div>
@endsection
