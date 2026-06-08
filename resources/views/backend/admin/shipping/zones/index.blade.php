@extends('layouts.admin')

@section('title')
    {{ translate('All Zones') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('All Zones') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="" class="font-bold ">{{ translate('Shipping') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Zones') }}</p>
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
                        <th data-breakpoints="xs sm">
                            #
                        </th>
                        <th>
                            {{ translate('Name') }}
                        </th>
                        <th>{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($zones as $key => $zone)
                        <tr>

                            <td>{{ $key + 1 + ($zones->currentPage() - 1) * $zones->perPage() }}</td>

                            <td>
                                <div class="inline-flex items-center gap-4">
                                    <div class="hidden">
                                        <img src="{{ uploadedAsset($zone->banner) }}" alt=""
                                            class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                    </div>
                                    <div class=" line-clamp-2">
                                        {{ $zone->name }}
                                    </div>
                                </div>

                            </td>
                            <td>
                                <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.zones.status') }}"
                                    name="isActiveCheckbox" value="{{ $zone->id }}"
                                    data-status="{{ $zone->is_active }}" isChecked="{{ (int) $zone->is_active == 1 }}" />
                            </td>
                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_zones')
                                                <li>
                                                    <a href="{{ route('admin.zones.edit', ['zone' => $zone->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('delete_zones')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.zones.destroy', $zone->id) }}" />
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
                {{ $zones->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_zones')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Zone') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.zone-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
