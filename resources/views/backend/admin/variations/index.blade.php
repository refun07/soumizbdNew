@extends('layouts.admin')

@section('title')
    {{ translate('Variation List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Variation List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Variation List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-6 gap-3">
        <div class="md:col-span-4 card">
            <div class="card__title border-none font-normal grid sm:grid-cols-2 4xl:grid-cols-3 gap-4">
                @foreach ($variations as $key => $variation)
                    <div class="py-3 md:py-7 px-4 md:px-8 bg-background-primary-light rounded-md flex flex-col">
                        <div class="flex justify-between">
                            <p class="uppercase">{{ $variation->collectTranslation('name') }}</p>

                            <div class="flex gap-3">

                                @can('edit_variations')
                                    <a class="text-stone-300 hover:text-stone-500"
                                        href="{{ route('admin.variations.edit', ['variation' => $variation->id, 'lang_key' => config('app.default_language')]) }}&translate">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                @endcan
                                @php
                                    $defaultVariationIds = [1, 2];
                                @endphp
                                @if (!in_array($variation->id, $defaultVariationIds))
                                    @can('delete_variations')
                                        <a class="text-red-400 hover:text-rose-600 confirm-modal" href="javascript:void(0);"
                                            data-href="{{ route('admin.variations.destroy', $variation->id) }}"
                                            data-title="{{ translate('Are you sure you want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    @endcan
                                @endif

                            </div>
                        </div>

                        <div class="mt-1.5 mb-3 flex gap-1.5 flex-wrap max-w-[190px]">
                            @foreach ($variation->getLimitedVariationValues as $variationValue)
                                <span
                                    class="font-medium uppercase text-white bg-secondary-light px-2.5 py-1 rounded">{{ $variationValue->collectTranslation('name') }}</span>
                            @endforeach
                        </div>

                        <div class="mt-auto flex justify-between">
                            <p>
                                {{ translate('Total Variations') }}
                                <span class="text-muted">{{ $variation->variation_values_count }}</span>
                            </p>

                            @can('view_variation_values')
                                <a href="{{ route('admin.variation-values.index', ['variation_id' => $variation->id]) }}"
                                    class="text-secondary-light">{{ translate('Edit Values') }}</a>
                            @endcan
                        </div>

                        @can('edit_variations')
                            <div class="flex items-center justify-end gap-4 mt-3 border-t border-primary-14 pt-4">
                                <label for="isActiveCheckbox">{{ translate('Activate Variation') }}</label>
                                <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.variations.status') }}"
                                    name="isActiveCheckbox" value="{{ $variation->id }}"
                                    data-status="{{ $variation->is_active }}"
                                    isChecked="{{ (int) $variation->is_active == 1 }}" />
                            </div>
                        @endcan
                    </div>
                @endforeach
            </div>

            <div class="card__footer">
                {{ $variations->links() }}
            </div>
        </div>
        <div class="md:col-span-2">
            @can('create_variations')
                <div class="card">
                    <h4 class="card__title">{{ translate('Add New Variation') }}</h4>
                    <div class="card__content">
                        <x-backend.forms.variation-form />
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
