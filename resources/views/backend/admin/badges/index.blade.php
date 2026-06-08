@extends('layouts.admin')

@section('title')
    {{ translate('Badge List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Badge List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Badge List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-3">
            <div class="card">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                    <div></div>

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[380px]" />
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th>
                                {{ translate('Badge Details') }}
                            </th>
                            <th data-breakpoints="xs sm md">
                                {{ translate('Color') }}
                            </th>
                            <th data-breakpoints="xs sm md">
                                {{ translate('Background Color') }}
                            </th>
                            <th data-breakpoints="xs sm">{{ translate('Show/Hide') }}</th>
                            <th data-breakpoints="xs sm md lg" class="w-[130px]">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($badges as $key => $badge)
                            <tr>
                                <td>{{ $key + 1 + ($badges->currentPage() - 1) * $badges->perPage() }}</td>
                                <td>
                                    {{ $badge->name }}
                                </td>


                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 aspect-square rounded-full"
                                            style="background-color: {{ $badge->color }}"></span>
                                        <span class="text-muted">
                                            {{ $badge->color }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 aspect-square rounded-full"
                                            style="background-color: {{ $badge->bg_color }}"></span>
                                        <span class="text-muted">
                                            {{ $badge->bg_color }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    @can('edit_badges')
                                        <x-backend.inputs.checkbox toggler="true"
                                            data-route="{{ route('admin.badges.status') }}" name="isActiveCheckbox"
                                            value="{{ $badge->id }}" data-status="{{ $badge->is_active }}"
                                            isChecked="{{ (int) $badge->is_active == 1 }}" />
                                    @endcan
                                </td>


                                <td>
                                    <div class="option-dropdown" tabindex="0">
                                        <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                            <span>{{ translate('Actions') }}</span>
                                        </div>

                                        <div class="option-dropdown__options">
                                            <ul>

                                                @can('edit_badges')
                                                    <li>
                                                        <a href="{{ route('admin.badges.edit', ['badge' => $badge->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                            class="option-dropdown__option">
                                                            {{ translate('Edit') }}
                                                        </a>
                                                    </li>
                                                @endcan

                                                @can('delete_badges')
                                                    <li>
                                                        <x-backend.inputs.delete-link
                                                            href="{{ route('admin.badges.destroy', $badge->id) }}" />
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
                    {{ $badges->links() }}
                </div>
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_badges')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Badge') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.badge-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
