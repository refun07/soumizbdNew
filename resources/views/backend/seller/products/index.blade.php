@extends('layouts.seller')

@section('title')
    {{ translate('Products') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Product List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('seller.products.index') }}" class="font-bold ">{{ translate('Products') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Product List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <x-backend.inputs.link href="{{ route('seller.products.create') }}"> <span class="text-xl">
                            <i class="fal fa-plus"></i>
                        </span> {{ translate('Add New') }}</x-backend.inputs.link>

                    <div class="inline-flex items-stretch">
                        <button type="button" class="button button--primary !rounded-r-none"
                            data-micromodal-trigger="product-import-modal">
                            <span class="text-lg">
                                <i class="fa-solid fa-file-import"></i>
                            </span>
                            {{ translate('Import') }}
                        </button>

                        <div class="option-dropdown" tabindex="0">
                            <div
                                class="option-dropdown__toggler no-arrow !min-w-0 !w-10 !rounded-l-none !px-0 !justify-center border-l border-white/20">
                                <span class="sr-only">{{ translate('Import Options') }}</span>
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>

                            <div class="option-dropdown__options right-0 !w-[190px]">
                                <ul>
                                    <li>
                                        <a href="{{ route('seller.products.sampleCsv') }}"
                                            class="option-dropdown__option">
                                            <i class="fa-solid fa-download"></i>
                                            {{ translate('Sample CSV') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[1000px]">

                    <x-backend.inputs.select name="categoryId" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Categories') }}" value=""
                            selected="{{ Request::get('categoryId') }}" />
                        @foreach ($categories as $cat)
                            <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name') }}"
                                value="{{ $cat->id }}"
                                selected="{{ Request::get('categoryId') == $cat->id ? $cat->id : 0 }}" />

                            @foreach ($cat->childrenCategories as $childCategory)
                                @include('backend.admin.categories.subCategory', [
                                    'subCategory' => $childCategory,
                                    'langKey' => \Session::get('locale'),
                                    'category' => null,
                                ])
                            @endforeach
                        @endforeach
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="brandId" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Brands') }}" value=""
                            selected="{{ Request::get('brandId') }}" />
                        @foreach ($brands as $brand)
                            <x-backend.inputs.select-option name="{{ $brand->collectTranslation('name') }}"
                                value="{{ $brand->id }}"
                                selected="{{ Request::get('brandId') == $brand->id ? $brand->id : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="tagId" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Tags') }}" value=""
                            selected="{{ Request::get('tagId') }}" />
                        @foreach ($tags as $tag)
                            <x-backend.inputs.select-option name="{{ $tag->name }}" value="{{ $tag->id }}"
                                selected="{{ Request::get('tagId') == $tag->id ? $tag->id : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="isPublished" class="filterSelect" :isRequired="false">
                        <x-backend.inputs.select-option name="{{ translate('All Products') }}" value=""
                            selected="{{ Request::get('isPublished') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Published') }}" value="1"
                            selected="{{ Request::get('isPublished') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Unpublished') }}" value="0"
                            selected="{{ Request::get('isPublished') }}" />
                    </x-backend.inputs.select>
                </x-backend.forms.search-form>
            </div>

            <div>
                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th>
                                {{ translate('Product DETAILS') }}
                            </th>
                            <th data-breakpoints="xs sm md lg">
                                {{ translate('Categories') }}
                            </th>
                            <th data-breakpoints="xs sm" class="min-w-[150px]">
                                {{ translate('Stock') }}
                            </th>

                            <th data-breakpoints="xs sm">{{ translate('Show/Hide') }}</th>
                            <th data-breakpoints="xs sm">{{ translate('Price') }}</th>
                            <th data-breakpoints="xs sm md" class="w-[130px]">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="">
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt=""
                                                class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('backend/assets/img/placeholder-thumb.png') }}';" />
                                        </div>
                                        <div class=" line-clamp-2">
                                            {{ $product->collectTranslation('name') }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-flex items-center gap-2 flex-wrap">
                                        @forelse ($product->categories as $category)
                                            <a
                                                class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none cursor-default">{{ $category->collectTranslation('name') }}</a>
                                        @empty
                                            <a
                                                class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none cursor-default">{{ translate('N/A') }}</a>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <div class="inline-flex items-center gap-2">
                                        @php

                                            $productVariations = $product->variations;
                                            $stockQty = 0;
                                            foreach ($productVariations as $productVariation) {
                                                $stockQty += (int) $productVariation
                                                    ->productVariationStocks()
                                                    ->sum('stock_qty');
                                            }
                                        @endphp
                                        {{ $stockQty }}

                                        @if ($stockQty <= $product->alert_qty)
                                            <span class="tooltip">
                                                <span class="tooltip__toggler text-orange-400 text-lg md:text-[26px]">
                                                    <i class="far fa-exclamation-circle"></i>
                                                </span>
                                                <span
                                                    class="tooltip__content">{{ translate('This shows your stock is low') }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <x-backend.inputs.checkbox toggler="true"
                                        data-route="{{ route('seller.products.status') }}" name="isActiveCheckbox"
                                        value="{{ $product->id }}" data-status="{{ $product->is_published }}"
                                        isChecked="{{ (int) $product->is_published == 1 }}" />
                                </td>

                                <td>
                                    @if ($product->max_price != $product->min_price)
                                        {{ formatPrice($product->min_price) }}
                                        -
                                        {{ formatPrice($product->max_price) }}
                                    @else
                                        {{ formatPrice($product->min_price) }}
                                    @endif
                                </td>
                                <td>
                                    <div class="option-dropdown w-[130px]" tabindex="0">
                                        <div class="option-dropdown__toggler bg-secondary/10 text-secondary">
                                            <span>{{ translate('Actions') }}</span>
                                        </div>

                                        <div class="option-dropdown__options">
                                            <ul>
                                                <li>
                                                    <a href="{{ url('/products\/') . $product->slug }}" target="_blank"
                                                        class="option-dropdown__option">{{ translate('View Product') }}</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('seller.products.duplicate', $product->id) }}"
                                                        class="option-dropdown__option">{{ translate('Duplicate') }}</a>
                                                </li>

                                                <li>
                                                    <a href="{{ route('seller.products.edit', ['product' => $product->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                        class="option-dropdown__option">
                                                        {{ translate('Edit') }}
                                                    </a>
                                                </li>

                                                <li>
                                                    <x-backend.inputs.delete-link
                                                        href="{{ route('seller.products.destroy', $product->id) }}" />
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-backend.modals.product-import modal-id="product-import-modal"
        form-action="{{ route('seller.products.import') }}" sample-url="{{ route('seller.products.sampleCsv') }}" />
@endsection
