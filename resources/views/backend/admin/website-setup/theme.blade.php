@extends('layouts.admin')

@section('title')
    {{ translate('Theme Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    {{-- breadcrumb starts --}}
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Theme Settings') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Website Setup') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Settings') }}</p>
        </div>
    </div>
    {{-- breadcrumb ends --}}

    {{-- logo starts --}}
    <div class="card max-w-[900px] mt-5">
        <h4 class="card__title">{{ translate('Theme') }}</h4>

        <div class="card__content">
            <form class="space-y-5" action="{{ route('admin.general-settings.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="defaultTheme">
                <x-backend.inputs.radio label="{{ translate('Default Theme') }}" name="defaultTheme">
                    <ul class="space-y-2 max-h-[315px] overflow-y-auto brand-tree-data">
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultTheme"
                                    checkedValue="{{ getSetting('defaultTheme') }}" value="theme-one" label="Theme One" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultTheme"
                                    checkedValue="{{ getSetting('defaultTheme') }}" value="theme-two" label="Theme Two" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultTheme"
                                    checkedValue="{{ getSetting('defaultTheme') }}" value="theme-three"
                                    label="Theme Three" />
                            </label>
                        </li>
                    </ul>
                </x-backend.inputs.radio>

                <input type="hidden" name="types[]" value="defaultHeader">
                <x-backend.inputs.radio label="{{ translate('Default Header') }}" name="defaultHeader">
                    <ul class="space-y-2 max-h-[315px] overflow-y-auto brand-tree-data">
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultHeader"
                                    checkedValue="{{ getSetting('defaultHeader') }}" value="header-one"
                                    label="Header One" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultHeader"
                                    checkedValue="{{ getSetting('defaultHeader') }}" value="header-two"
                                    label="Header Two" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultHeader"
                                    checkedValue="{{ getSetting('defaultHeader') }}" value="header-three"
                                    label="Header Three" />
                            </label>
                        </li>
                    </ul>
                </x-backend.inputs.radio>

                <input type="hidden" name="types[]" value="defaultProductCard">
                <x-backend.inputs.radio label="{{ translate('Default Product Card') }}" name="defaultProductCard">
                    <ul class="space-y-2 max-h-[315px] overflow-y-auto brand-tree-data">
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultProductCard"
                                    checkedValue="{{ getSetting('defaultProductCard') }}" value="product-card-one"
                                    label="Product Card One" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultProductCard"
                                    checkedValue="{{ getSetting('defaultProductCard') }}" value="product-card-two"
                                    label="Product Card Two" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultProductCard"
                                    checkedValue="{{ getSetting('defaultProductCard') }}" value="product-card-three"
                                    label="Product Card Three" />
                            </label>
                        </li>
                    </ul>
                </x-backend.inputs.radio>

                <input type="hidden" name="types[]" value="defaultCategoryCard">
                <x-backend.inputs.radio label="{{ translate('Default Category Card') }}" name="defaultCategoryCard">
                    <ul class="space-y-2 max-h-[315px] overflow-y-auto brand-tree-data">
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultCategoryCard"
                                    checkedValue="{{ getSetting('defaultCategoryCard') }}" value="category-card-one"
                                    label="Category Card One" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultCategoryCard"
                                    checkedValue="{{ getSetting('defaultCategoryCard') }}" value="category-card-two"
                                    label="Category Card Two" />
                            </label>
                        </li>
                        <li>
                            <label class="inline-flex gap-3 items-center">
                                <x-backend.inputs.radio-option name="defaultCategoryCard"
                                    checkedValue="{{ getSetting('defaultCategoryCard') }}" value="category-card-three"
                                    label="Category Card Three" />
                            </label>
                        </li>
                    </ul>
                </x-backend.inputs.radio>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>
        </div>
    </div>
    {{-- logo ends --}}
@endsection
