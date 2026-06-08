@extends('layouts.admin')

@section('title')
    {{ translate('Coupons') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Coupon List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.coupons.index') }}" class="font-bold ">{{ translate('Coupons') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Coupon List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card theme-table">
        <div
            class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
            @can('create_coupons')
                <x-backend.inputs.link href="{{ route('admin.coupons.create') }}"> <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span> {{ translate('Add New') }}</x-backend.inputs.link>
            @else
                <div></div>
            @endcan

            <x-backend.forms.search-form placeholder="Type code and hit enter" searchKey="{{ $searchKey }}"
                class="max-w-[380px]" />
        </div>

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-primary/10">
                <tr>
                    <th data-breakpoints="xs sm">
                        #
                    </th>

                    <th>
                        {{ translate('Coupon Details') }}
                    </th>

                    <th data-breakpoints="xs sm">
                        {{ translate('Discount Amount') }}
                    </th>

                    <th>{{ translate('Start Date') }}</th>

                    <th>{{ translate('End Date') }}</th>

                    <th data-breakpoints="xs sm" class="w-[130px]">
                        {{ translate('Options') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupons as $key => $coupon)
                    <tr>
                        <td>{{ $key + 1 + ($coupons->currentPage() - 1) * $coupons->perPage() }}</td>
                        <td>
                            <div class="inline-flex items-center gap-4">
                                <div class="max-xs:hidden">
                                    <img src="{{ uploadedAsset($coupon->banner) }}" alt=""
                                        class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                </div>
                                <div class=" line-clamp-2">
                                    {{ $coupon->code }}
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $coupon->discount_value }} ({{ $coupon->discount_type }})
                        </td>
                        <td>{{ date('d-m-Y', $coupon->start_date) }}</td>
                        <td>{{ date('d-m-Y', $coupon->end_date) }}</td>

                        <td>
                            <div class="option-dropdown" tabindex="0">
                                <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                    <span>{{ translate('Actions') }}</span>
                                </div>

                                <div class="option-dropdown__options">
                                    <ul>
                                        @can('edit_coupons')
                                            <li>
                                                <a href="{{ route('admin.coupons.edit', ['coupon' => $coupon->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                    class="option-dropdown__option">
                                                    {{ translate('Edit') }}
                                                </a>
                                            </li>
                                        @endcan

                                        @can('delete_coupons')
                                            <li>
                                                <x-backend.inputs.delete-link
                                                    href="{{ route('admin.coupons.destroy', $coupon->id) }}" />
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
            {{ $coupons->links() }}
        </div>
    </div>
@endsection
