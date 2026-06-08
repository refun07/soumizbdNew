@extends('layouts.admin')

@section('title')
    {{ translate('All Areas') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All Areas') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Areas') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]">
                    <x-backend.inputs.select name="zoneId" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Zones') }}" value="" selected="" />
                        @foreach ($zones as $zone)
                            <x-backend.inputs.select-option name="{{ $zone->name }}" value="{{ $zone->id }}"
                                selected="{{ Request::get('zoneId') ? Request::get('zoneId') : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>
                </x-backend.forms.search-form>
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
                        <th>{{ translate('City') }}</th>
                        <th>{{ translate('Zone') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($areas as $key => $area)
                        <tr>

                            <td>{{ $key + 1 + ($areas->currentPage() - 1) * $areas->perPage() }}</td>

                            <td>{{ $area->name }}</td>
                            <td>
                                <span
                                    class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none">{{ $area->city?->name }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="px-2 py-1 text-xs bg-primary text-white rounded-md leading-none">{{ $area->zone?->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.areas.status') }}"
                                    name="isActiveCheckbox" value="{{ $area->id }}"
                                    data-status="{{ $area->is_active }}" isChecked="{{ (int) $area->is_active == 1 }}" />
                            </td>

                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_areas')
                                                <li>
                                                    <a href="{{ route('admin.areas.edit', ['area' => $area->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete_areas')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.areas.destroy', $area->id) }}" />
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
                {{ $areas->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_areas')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Area') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.area-form :cities="$cities" :zones="$zones" />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
