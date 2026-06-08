@extends('layouts.admin')

@section('title')
    {{ translate('Update Value') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <div class="text-sm sm:text-base font-bold flex items-center">
                {{ translate('Update Value') }} ({{ $variation->collectTranslation('name') }})
            </div>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.variation-values.index', ['variation_id' => $variation->id]) }}"
                class="font-bold ">{{ translate('Values List') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Update Value') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid xl:grid-cols-12">
        <div class="xl:col-span-4">
            <div class="card">
                <div class="card__content">
                    <div class="flex justify-end mb-3">
                        <x-backend.inputs.change-languages langKey="{{ $lang_key }}" />
                    </div>
                    <x-backend.forms.variation-value-form :variationValue="$variationValue" :variation="$variation"
                        langKey="{{ $lang_key }}" />
                </div>
            </div>
        </div>
    </div>
@endsection
