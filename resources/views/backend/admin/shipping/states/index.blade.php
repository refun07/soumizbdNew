@extends('layouts.admin')

@section('title')
    {{ translate('All Districts') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All Districts') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Districts') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3 card theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-end gap-3">
                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]">
                    <x-backend.inputs.select name="countryId" class="filterOnSelect" data-route="{{ route('admin.states.index') }}" data-name="countryId" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Countries') }}" value=""
                            selected="" />
                        @foreach ($countries as $country)
                            <x-backend.inputs.select-option name="{{ $country->name }}" value="{{ $country->id }}"
                                selected="{{ Request::get('countryId') ? Request::get('countryId') : 0 }}" />
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
                            <a href="{{ route('admin.states.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_order' => $sortBy === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-primary transition-colors">
                                {{ translate('Name') }}
                                @if($sortBy === 'name')
                                    <i class="fa-solid fa-chevron-{{ $sortOrder === 'asc' ? 'up' : 'down' }} text-xs"></i>
                                @else
                                    <i class="fa-solid fa-sort text-xs text-muted"></i>
                                @endif
                            </a>
                        </th>
                        <th data-breakpoints="xs sm">{{ translate('Country') }}</th>
                        <th>{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($states as $key => $state)
                        <tr>

                            <td>{{ $key + 1 + ($states->currentPage() - 1) * $states->perPage() }}</td>

                            <td>{{ $state->name }}</td>
                            <td><span
                                    class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none">{{ $state->country?->name }}</span>
                            </td>
                            <td>
                                <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.states.status') }}"
                                    name="isActiveCheckbox" value="{{ $state->id }}"
                                    data-status="{{ $state->is_active }}"
                                    isChecked="{{ (int) $state->is_active == 1 }}" />
                            </td>

                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_states')
                                                <li>
                                                    <a href="{{ route('admin.states.edit', ['state' => $state->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete_states')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.states.destroy', $state->id) }}" />
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
                {{ $states->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_states')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New State') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.state-form :countries="$countries" />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
