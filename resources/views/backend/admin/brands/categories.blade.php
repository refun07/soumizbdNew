@extends('layouts.admin')

@section('title')
    {{ translate('Brand Categories') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder-tree"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Brand Categories') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="{{ route('admin.brands.index') }}" class="font-bold">{{ translate('Brands') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Brand Categories') }}</p>
        </div>
    </div>

    <div class="grid xl:grid-cols-12 gap-3">
        <div class="xl:col-span-5">
            <div class="card">
                <h4 class="card__title">{{ translate('Select Brand') }}</h4>
                <div class="card__content">
                    <form action="{{ route('admin.brands.categories.index') }}" method="GET" class="space-y-4">
                        <x-backend.inputs.select label="Brand" name="brand_id"
                            data-selection-css-class="input-select2">
                            @foreach ($brands as $brand)
                                <x-backend.inputs.select-option
                                    name="{{ $brand->collectTranslation('name', config('app.default_language')) }}"
                                    value="{{ $brand->id }}"
                                    selected="{{ $selectedBrand?->id == $brand->id ? $brand->id : null }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Load Brand" type="submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="xl:col-span-7">
            <div class="card">
                <h4 class="card__title">{{ translate('Manage Brand Categories') }}</h4>
                <div class="card__content">
                    @if ($selectedBrand)
                        <div class="mb-5 rounded-md bg-background-primary-light p-4">
                            <p class="text-xs uppercase text-muted">{{ translate('Selected Brand') }}</p>
                            <h3 class="mt-1 font-bold text-lg">
                                {{ $selectedBrand->collectTranslation('name', config('app.default_language')) }}
                            </h3>
                            <p class="mt-1 text-sm text-muted">
                                {{ translate('Only selected categories will show on this brand page.') }}
                            </p>
                        </div>

                        <form action="{{ route('admin.brands.categories.update') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="brand_id" value="{{ $selectedBrand->id }}">

                            <x-backend.inputs.select label="Brand Categories" name="category_ids[]" multiple
                                data-selection-css-class="multi-select2"
                                data-placeholder="{{ translate('Select categories for this brand') }}" :isRequired="false">
                                @foreach ($categories as $category)
                                    <x-backend.inputs.select-option
                                        name="{{ $category->collectTranslation('name', config('app.default_language')) }}"
                                        value="{{ $category->id }}"
                                        selected="{{ in_array($category->id, $selectedCategories) ? $category->id : null }}" />

                                    @foreach ($category->childrenCategories as $childCategory)
                                        @include('backend.admin.categories.subCategory', [
                                            'subCategory' => $childCategory,
                                            'langKey' => config('app.default_language'),
                                            'category' => null,
                                            'selectedCategories' => $selectedCategories,
                                        ])
                                    @endforeach
                                @endforeach
                            </x-backend.inputs.select>

                            <div class="flex justify-end">
                                <x-backend.inputs.button buttonText="Save Categories" type="submit" />
                            </div>
                        </form>
                    @else
                        <div class="rounded-md bg-background-primary-light p-5 text-muted">
                            {{ translate('No active brands found. Please create a brand first.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
