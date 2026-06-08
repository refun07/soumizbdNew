@extends('layouts.seller')

@section('title')
    {{ translate('Suppliers') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Supplier List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-indigo">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Supplier List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            <x-backend.inputs.link href="{{ route('seller.suppliers.create') }}"> <span class="text-xl">
                    <i class="fal fa-plus"></i>
                </span> {{ translate('Add New') }}</x-backend.inputs.link>

            <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-primary/10">
                <tr>
                    <th data-breakpoints="xs sm">
                        #
                    </th>
                    <th>
                        {{ translate('Name') }}
                    </th>
                    <th data-breakpoints="xs sm">
                        {{ translate('Email') }}
                    </th>
                    <th>
                        {{ translate('Phone') }}
                    </th>
                    <th data-breakpoints="xs sm">
                        {{ translate('Address') }}
                    </th>

                    <th data-breakpoints="xs sm">{{ translate('Payment Details') }}</th>

                    <th data-breakpoints="xs sm" class="w-[130px]">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $key => $supplier)
                    <tr>
                        <td>{{ $key + 1 + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}</td>
                        <td>
                            <div class=" line-clamp-2">
                                {{ $supplier->name }}
                            </div>
                        </td>


                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ $supplier->phone_no ?? '-' }}</td>
                        <td>{{ $supplier->address ?? '-' }}</td>
                        <td>{{ $supplier->payment_details ?? '-' }}</td>

                        <td>
                            <div class="option-dropdown" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        <li>
                                            <a href="{{ route('seller.suppliers.edit', ['supplier' => $supplier->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                class="option-dropdown__option">
                                                {{ translate('Edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <x-backend.inputs.delete-link
                                                href="{{ route('seller.suppliers.destroy', $supplier->id) }}" />
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
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
