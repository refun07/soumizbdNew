@extends('layouts.admin')

@section('title')
    {{ translate('All Countries') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All Countries') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Countries') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                <div></div>

                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />

            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10 px-3">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            {{ translate('Name') }}
                        </th>
                        <th>{{ translate('Show/Hide') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($countries as $key => $country)
                        <tr>

                            <td>{{ $key + 1 + ($countries->currentPage() - 1) * $countries->perPage() }}</td>

                            <td>{{ $country->name }}</td>
                            <td>
                                @can('edit_countries')
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('admin.countries.status') }}" name="isActiveCheckbox"
                                        value="{{ $country->id }}" data-status="{{ $country->is_active }}"
                                        isChecked="{{ (int) $country->is_active == 1 }}" />
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="card__footer">
                {{ $countries->links() }}
            </div>
        </div>
    </div>
@endsection
