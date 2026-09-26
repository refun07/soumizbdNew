@extends('layouts.admin')

@section('title')
    {{ translate('Brand Groups') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Brand Groups') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.brands.index') }}" class="font-bold ">{{ translate('Brands') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Brand Groups') }}</p>
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
                        <th>#</th>
                        <th>{{ translate('Group') }}</th>
                        <th>{{ translate('Brands') }}</th>
                        <th>{{ translate('Sort Order') }}</th>
                        <th class="w-[130px]">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brandGroups as $key => $brandGroup)
                        <tr>
                            <td>{{ $key + 1 + ($brandGroups->currentPage() - 1) * $brandGroups->perPage() }}</td>
                            <td>
                                <div class="inline-flex items-center gap-3">
                                    <img src="{{ uploadedAsset($brandGroup->image) }}" alt=""
                                        class="w-10 h-10 rounded-md object-cover"
                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">
                                    <div class="line-clamp-2">{{ $brandGroup->name }}</div>
                                </div>
                            </td>
                            <td>{{ $brandGroup->brands_count }}</td>
                            <td>{{ $brandGroup->sort_order }}</td>
                            <td>
                                <div class="option-dropdown" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_brands')
                                                <li>
                                                    <a href="{{ route('admin.brand-groups.edit', $brandGroup->id) }}"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('delete_brands')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.brand-groups.destroy', $brandGroup->id) }}"
                                                        text="Brands in this group will become ungrouped." />
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">{{ translate('No brand groups yet') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="card__footer">
                {{ $brandGroups->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_brands')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Brand Group') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.brand-group-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
