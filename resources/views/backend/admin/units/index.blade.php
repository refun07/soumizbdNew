@extends('layouts.admin')

@section('title')
    {{ translate('Unit List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Unit List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Unit List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            {{ translate('Unit Details') }}
                        </th>
                        <th class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $key => $unit)
                        <tr>
                            <td>{{ $key + 1 + ($units->currentPage() - 1) * $units->perPage() }}</td>
                            <td class="">
                                <div class="inline-flex items-center gap-4">
                                    <div class=" line-clamp-2">
                                        {{ $unit->collectTranslation('name') }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>

                                            @can('edit_units')
                                                <li>
                                                    <a href="{{ route('admin.units.edit', ['unit' => $unit->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('delete_units')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.units.destroy', $unit->id) }}" />
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
                {{ $units->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_units')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Unit') }} </h4>
                    <div class="card__content">
                        <x-backend.forms.unit-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
