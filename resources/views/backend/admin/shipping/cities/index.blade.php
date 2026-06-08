@extends('layouts.admin')

@section('title')
    {{ translate('All Cities') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All Cities') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Cities') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
                <a href="javascript:void(0);"
                    class="btn btn-danger btn-sm confirm-modal"
                    data-micromodal-trigger="confirm-modal"
                    data-title="{{ translate('Reset Cities') }}"
                    data-text="{{ translate('Are you sure? This will delete all existing cities and recreate from Bangladesh districts.') }}"
                    data-href="{{ route('admin.cities.reset') }}"
                    data-method="POST">
                    <i class="fa fa-refresh"></i> {{ translate('Reset to Default') }}
                </a>
            </div>

            <table class="product-list-table footable w-full">
                <thead class="uppercase text-left bg-primary/10 px-3">
                    <tr>
                        <th data-breakpoints="xs sm">
                            #
                        </th>
                        <th>
                            <a href="{{ route('admin.cities.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_order' => $sortBy === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                {{ translate('Name') }}
                                @if($sortBy === 'name')
                                    <i class="fa-solid fa-chevron-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @else
                                    <i class="fa-solid fa-sort text-xs text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th data-breakpoints="xs sm">{{ translate('District') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Zone') }}</th>
                        <th>{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cities as $key => $city)
                        <tr>

                            <td>{{ $key + 1 + ($cities->currentPage() - 1) * $cities->perPage() }}</td>

                            <td>{{ $city->name }}</td>
                            <td><span
                                    class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none">{{ $city->state?->name }}</span>
                            </td>
                            <td>
                                @if ($city->zone?->name)
                                    <span
                                        class="px-2 py-1 text-xs bg-primary text-white rounded-md leading-none">{{ $city->zone?->name }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.cities.status') }}"
                                    name="isActiveCheckbox" value="{{ $city->id }}"
                                    data-status="{{ $city->is_active }}" isChecked="{{ (int) $city->is_active == 1 }}" />
                            </td>

                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_cities')
                                                <li>
                                                    <a href="{{ route('admin.cities.edit', ['city' => $city->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete_cities')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.cities.destroy', $city->id) }}" />
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
                {{ $cities->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_cities')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New City') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.city-form :states="$states" :zones="$zones" />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
