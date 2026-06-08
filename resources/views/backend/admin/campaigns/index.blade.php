@extends('layouts.admin')

@section('title')
    {{ translate('Campaigns') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Campaign List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.campaigns.index') }}" class="font-bold ">{{ translate('Campaigns') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Campaign List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title border-none card__title theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            @can('create_campaigns')
                <x-backend.inputs.link href="{{ route('admin.campaigns.create') }}"> <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span> {{ translate('Add New') }}</x-backend.inputs.link>
            @else
                <div></div>
            @endcan

            <x-backend.forms.search-form placeholder="Type name and hit enter" searchKey="{{ $searchKey }}"
                class="max-w-[380px]" />
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-primary/10">
                <tr>
                    <th data-breakpoints="xs sm">
                        #
                    </th>

                    <th>
                        {{ translate('Campaign Details') }}
                    </th>

                    <th>{{ translate('Start Date') }}</th>

                    <th>{{ translate('End Date') }}</th>

                    <th data-breakpoints="xs sm">{{ translate('Products') }}</th>

                    <th data-breakpoints="xs sm" class="w-[130px]">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaigns as $key => $campaign)
                    <tr>
                        <td>{{ $key + 1 + ($campaigns->currentPage() - 1) * $campaigns->perPage() }}</td>
                        <td>
                            <div class="inline-flex items-center gap-4">
                                <div class="max-xs:hidden">
                                    <img src="{{ uploadedAsset($campaign->thumbnail_image) }}" alt=""
                                        class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                </div>
                                <div class=" line-clamp-2">
                                    {{ $campaign->name }}
                                </div>
                            </div>
                        </td>

                        <td>{{ date('d M, Y', $campaign->start_date) }}</td>
                        <td>{{ date('d M, Y', $campaign->end_date) }}</td>
                        <td>{{ $campaign->campaignProducts()->count() }}</td>

                        <td>
                            <div class="option-dropdown" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        @can('edit_campaigns')
                                            <li>
                                                <a href="{{ route('admin.campaigns.edit', ['campaign' => $campaign->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                    class="option-dropdown__option">
                                                    {{ translate('Edit') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.campaigns.show', ['campaign' => $campaign->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                    class="option-dropdown__option">
                                                    {{ translate('Products') }}
                                                </a>
                                            </li>
                                        @endcan
                                        @can('delete_campaigns')
                                            <li>
                                                <x-backend.inputs.delete-link
                                                    href="{{ route('admin.campaigns.destroy', $campaign->id) }}" />
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
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection
