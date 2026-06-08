@extends('layouts.seller')

@section('title')
    {{ translate('All Warehouses') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All Warehouses') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Warehouses') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card theme-table">
                <div
                    class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                    <div></div>

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10 px-3">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th>
                                {{ translate('Name') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Zones') }}
                            </th>
                            <th>{{ translate('Show/Hide') }}</th>
                            <th>{{ translate('Default') }}</th>
                            <th data-breakpoints="xs sm" class="w-[130px]">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warehouses as $key => $warehouse)
                            <tr>

                                <td>{{ $key + 1 + ($warehouses->currentPage() - 1) * $warehouses->perPage() }}
                                </td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="hidden">
                                            <img src="{{ uploadedAsset($warehouse->thumbnail_image) }}" alt=""
                                                class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div class=" line-clamp-2">
                                            {{ $warehouse?->name ?? translate('N/A') }}
                                        </div>
                                    </div>

                                </td>

                                <td>
                                    @foreach ($warehouse->zones as $zone)
                                        <span
                                            class="me-1 px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none">{{ $zone?->name ?? translate('N/A') }}
                                        </span>
                                    @endforeach
                                </td>

                                <td>
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('seller.warehouses.status') }}" name="isActiveCheckbox"
                                        value="{{ $warehouse->id }}" data-status="{{ $warehouse->is_active }}"
                                        isChecked="{{ (int) $warehouse->is_active == 1 }}" />
                                </td>
                                <td>
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('seller.warehouses.default') }}" name="isDefaultCheckbox"
                                        value="{{ $warehouse->id }}" data-status="{{ $warehouse->is_default }}"
                                        isChecked="{{ (int) $warehouse->is_default == 1 }}"
                                        isDisabled="{{ (int) $warehouse->is_default }}" />
                                </td>
                                <td>
                                    <div class="option-dropdown" tabindex="0">
                                        <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                            <span>{{ translate('Actions') }}</span>
                                        </div>

                                        <div class="option-dropdown__options">
                                            <ul>
                                                <li>
                                                    <a href="{{ route('seller.warehouses.edit', ['warehouse' => $warehouse->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('seller.warehouses.destroy', $warehouse->id) }}" />
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
                    {{ $warehouses->links() }}
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            <div class="card">
                <h4 class="card__title">{{ translate('Add New Warehouse') }}</h4>
                <div class="card__content">
                    <x-backend.forms.warehouse-form :zones="$zones" />
                </div>
            </div>
        </div>
    </div>
@endsection
