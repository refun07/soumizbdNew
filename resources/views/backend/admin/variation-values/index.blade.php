@extends('layouts.admin')

@section('title')
    {{ translate('Variation Value List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Variation Value List') }} ({{ $variation->collectTranslation('name') }})
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Variation Value List') }}</p>
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
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            #
                        </th>
                        <th>
                            {{ translate('Value Name') }}
                        </th>
                        @if ($variation->is_color)
                            <th data-breakpoints="xs sm">
                                {{ translate('Color Code') }}
                            </th>
                        @endif
                        <th>{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variationValues as $key => $variationValue)
                        <tr>
                            <td>{{ $key + 1 + ($variationValues->currentPage() - 1) * $variationValues->perPage() }}
                            </td>

                            <td>
                                <div class=" line-clamp-2">
                                    {{ $variationValue->collectTranslation('name') }}
                                </div>
                            </td>

                            @if ($variation->is_color)
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 aspect-square rounded-full"
                                            style="background-color: {{ $variationValue->color_code }}"></span>
                                        <span class="text-muted">
                                            {{ $variationValue->color_code }}
                                        </span>
                                    </div>
                                </td>
                            @endif

                            <td>
                                @can('edit_variation_values')
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('admin.variation-values.status') }}" name="isActiveCheckbox"
                                        value="{{ $variationValue->id }}" data-status="{{ $variationValue->is_active }}"
                                        isChecked="{{ (int) $variationValue->is_active == 1 }}" />
                                @endcan
                            </td>

                            <td>
                                <div class="option-dropdown w-[130px]" tabindex="0">
                                    <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                        <span>{{ translate('Actions') }}</span>
                                    </div>

                                    <div class="option-dropdown__options">
                                        <ul>
                                            @can('edit_variation_values')
                                                <li>
                                                    <a href="{{ route('admin.variation-values.edit', ['id' => $variationValue->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>
                                            @endcan

                                            @can('delete_variation_values')
                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('admin.variation-values.destroy', ['id' => $variationValue->id]) }}" />
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
                {{ $variationValues->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_variation_values')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Variation Value') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.variation-value-form :variation="$variation" />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
