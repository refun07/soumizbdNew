@php
    $categories = \App\Models\Category::where('parent_id', 0)
        ->orderBy('sorting_order_level', 'desc')
        ->with('childrenCategories')
        ->get();

    $campaigns = \App\Models\Campaign::isPublished()
        ->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))
        ->latest()
        ->get();

    $products = \App\Models\Product::isPublished()->latest()->get();
@endphp

{{-- slider section --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Main Slider Section') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeThreeSliderSection">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Main Slider Section') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeThreeSliderSection"
                        value="1" isChecked="{{ getSetting('showThemeThreeSliderSection') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeThreeSliders">
                <input type="hidden" name="types[]" value="themeThreeSliderHeadlines">
                <input type="hidden" name="types[]" value="themeThreeSliderTitles">
                <input type="hidden" name="types[]" value="themeThreeSliderSubtitles">
                <input type="hidden" name="types[]" value="themeThreeSliderLinks">

                <div class="slider-input-wrapper">
                    @if (getSetting('themeThreeSliders') != null)
                        @foreach (json_decode(getSetting('themeThreeSliders'), true) as $key => $value)
                            <div class="slider-input">
                                <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                    label="Image" name="themeThreeSliders[]" value="{{ $value }}"
                                    class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1">Recommended: 1920x600 pixels</p>

                                <x-backend.inputs.text :labelInline="false" label="Headline"
                                    name="themeThreeSliderHeadlines[]"
                                    value="{{ json_decode(getSetting('themeThreeSliderHeadlines'), true)[$key] }}"
                                    placeholder="Headline" :isRequired="false" />

                                <x-backend.inputs.text :labelInline="false" label="Title" name="themeThreeSliderTitles[]"
                                    value="{{ json_decode(getSetting('themeThreeSliderTitles'), true)[$key] }}"
                                    placeholder="Title" :isRequired="false" />

                                <x-backend.inputs.text :labelInline="false" label="Subtitle"
                                    name="themeThreeSliderSubtitles[]"
                                    value="{{ json_decode(getSetting('themeThreeSliderSubtitles'), true)[$key] }}"
                                    placeholder="Subtitle" :isRequired="false" />

                                <x-backend.inputs.text :labelInline="false" label="Link" name="themeThreeSliderLinks[]"
                                    value="{{ json_decode(getSetting('themeThreeSliderLinks'), true)[$key] }}"
                                    placeholder="https://www.example.com" :isRequired="false" />
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="themeThreeSliders[]" value=""  class="mb-3" />
                                            <p class="text-xs text-gray-500 mt-1">Recommended: 1920x600 pixels</p>

                                            <x-backend.inputs.text :labelInline="false" label="Headline" name="themeThreeSliderHeadlines[]"
                                            value=""
                                            placeholder="Headline" :isRequired="false" />

                                            <x-backend.inputs.text :labelInline="false" label="Title" name="themeThreeSliderTitles[]"
                                            value=""
                                            placeholder="Title" :isRequired="false" />

                                            <x-backend.inputs.text :labelInline="false" label="Subtitle" name="themeThreeSliderSubtitles[]"
                                            value=""
                                            placeholder="Subtitle" :isRequired="false" />

                                            <x-backend.inputs.text :labelInline="false" label="Link" name="themeThreeSliderLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                    data-target=".slider-input-wrapper">
                    {{ translate('Add Slider') }}
                    <span class="ml-2">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                </a>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- featured category --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Featured Categories') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $featuredCategories =
                        getSetting('themeThreeFeaturedCategories') != null
                            ? collect(json_decode(getSetting('themeThreeFeaturedCategories')))
                            : collect();
                @endphp

                <input type="hidden" name="types[]" value="showthemeThreeFeaturedCategories">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Featured Categories') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showthemeThreeFeaturedCategories" value="1"
                        isChecked="{{ getSetting('showthemeThreeFeaturedCategories') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeThreeFeaturedCategoriesHeadline">
                <x-backend.inputs.text :labelInline="false" label="Headline" name="themeThreeFeaturedCategoriesHeadline"
                    value="{{ getSetting('themeThreeFeaturedCategoriesHeadline') }}" placeholder="Headline"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeFeaturedCategoriesSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Subtitle" name="themeThreeFeaturedCategoriesSubtitle"
                    value="{{ getSetting('themeThreeFeaturedCategoriesSubtitle') }}" placeholder="Subtitle"
                    :isRequired="false" />


                <input type="hidden" name="types[]" value="themeThreeFeaturedCategories">
                @include('components.backend.forms.partials.ordered-category-picker', [
                    'categories' => $categories,
                    'selectedCategories' => $featuredCategories,
                    'inputName' => 'themeThreeFeaturedCategories',
                    'langKey' => $langKey,
                ])

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn mt-3" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- featured products --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Featured Products') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $featuredProducts =
                        getSetting('themeThreeFeaturedProducts') != null
                            ? collect(json_decode(getSetting('themeThreeFeaturedProducts')))
                            : collect();
                @endphp
                <div class="pt-2">

                    <input type="hidden" name="types[]" value="showthemeThreeFeaturedProducts">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Featured Products') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showthemeThreeFeaturedProducts"
                            value="1" isChecked="{{ getSetting('showthemeThreeFeaturedProducts') ?? false }}" />
                    </div>


                    <input type="hidden" name="types[]" value="themeThreeFeaturedProducts">
                    @php
                        $productsById = $products->keyBy('id');
                    @endphp
                    <div class="ordered-product-picker" data-input-name="themeThreeFeaturedProducts">
                        <x-backend.inputs.select :labelInline="false" label="Featured Products"
                            name="themeThreeFeaturedProductsPicker" data-selection-css-class="input-select2"
                            data-placeholder="{{ translate('Select products') }}" data-no-add-new="true"
                            :isRequired="false">
                            <option></option>
                            @foreach ($products as $product)
                                <x-backend.inputs.select-option
                                    name="{{ $product->collectTranslation('name', $langKey) }}"
                                    value="{{ $product->id }}"
                                    data-selected="{{ $featuredProducts->contains($product->id) ? '1' : '0' }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        <input type="hidden" name="themeThreeFeaturedProducts" value="[]"
                            class="ordered-product-picker-empty-input" disabled>

                        <div class="ordered-product-picker-list mt-3 space-y-2">
                            @foreach ($featuredProducts as $productId)
                                @php
                                    $product = $productsById->get((int) $productId);
                                @endphp
                                @if ($product)
                                    <div class="ordered-product-picker-item flex items-center gap-2 rounded border border-border bg-background p-2"
                                        draggable="true" data-value="{{ $product->id }}">
                                        <button type="button"
                                            class="ordered-product-picker-drag-handle h-8 w-8 shrink-0 cursor-move rounded border border-border text-muted-foreground"
                                            title="{{ translate('Drag to reorder') }}">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </button>
                                        <input type="hidden" name="themeThreeFeaturedProducts[]"
                                            value="{{ $product->id }}">
                                        <span class="min-w-0 flex-1 truncate text-sm">
                                            {{ $product->collectTranslation('name', $langKey) }}
                                        </span>
                                        <button type="button"
                                            class="ordered-product-picker-move-up h-8 w-8 shrink-0 rounded border border-border"
                                            title="{{ translate('Move up') }}">
                                            <i class="fa-solid fa-chevron-up"></i>
                                        </button>
                                        <button type="button"
                                            class="ordered-product-picker-move-down h-8 w-8 shrink-0 rounded border border-border"
                                            title="{{ translate('Move down') }}">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </button>
                                        <button type="button"
                                            class="ordered-product-picker-remove h-8 w-8 shrink-0 rounded border border-border text-red-500"
                                            title="{{ translate('Remove') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <input type="hidden" name="types[]" value="themeThreeFeaturedProductsViewMoreUrl">
                    <x-backend.inputs.text :labelInline="false" label="View More URL"
                        name="themeThreeFeaturedProductsViewMoreUrl"
                        value="{{ getSetting('themeThreeFeaturedProductsViewMoreUrl') }}"
                        placeholder="/products" :isRequired="false" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn mt-3" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- popular category --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Popular Categories') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $popularCategories =
                        getSetting('themeThreePopularCategories') != null
                            ? collect(json_decode(getSetting('themeThreePopularCategories')))
                            : collect();
                @endphp

                <input type="hidden" name="types[]" value="showthemeThreePopularCategories">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Popular Categories') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showthemeThreePopularCategories" value="1"
                        isChecked="{{ getSetting('showthemeThreePopularCategories') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeThreePopularCategoriesHeadline">
                <x-backend.inputs.text :labelInline="false" label="Headline" name="themeThreePopularCategoriesHeadline"
                    value="{{ getSetting('themeThreePopularCategoriesHeadline') }}" placeholder="Headline"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreePopularCategoriesSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Subtitle" name="themeThreePopularCategoriesSubtitle"
                    value="{{ getSetting('themeThreePopularCategoriesSubtitle') }}" placeholder="Subtitle"
                    :isRequired="false" />


                <input type="hidden" name="types[]" value="themeThreePopularCategories">
                <x-backend.inputs.select :labelInline="false" label="Popular Categories"
                    name="themeThreePopularCategories[]" multiple data-selection-css-class="multi-select2"
                    data-placeholder="{{ translate('Select categories') }}" :isRequired="false">

                    @foreach ($categories as $cat)
                        <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name', $langKey) }}"
                            value="{{ $cat->id }}"
                            selected="{{ $popularCategories->contains($cat->id) ? $cat->id : 0 }}" />

                        @foreach ($cat->childrenCategories as $childCategory)
                            @include('backend.admin.categories.subCategory', [
                                'subCategory' => $childCategory,
                                'langKey' => $langKey,
                                'category' => null,
                                'popularCategories' => $popularCategories,
                            ])
                        @endforeach
                    @endforeach
                </x-backend.inputs.select>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn mt-3" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2 image banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('2 Image Banner') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="showThemeThreeTwoImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide 2 Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeThreeTwoImageBanner" value="1"
                        isChecked="{{ getSetting('showThemeThreeTwoImageBanner') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One" name="themeThreeTwoImageBannerImageOne"
                    value="{{ getSetting('themeThreeTwoImageBannerImageOne') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x400 pixels</p>

                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageOneTitle">
                <x-backend.inputs.text :labelInline="false" label="Image One Title"
                    name="themeThreeTwoImageBannerImageOneTitle"
                    value="{{ getSetting('themeThreeTwoImageBannerImageOneTitle') }}" placeholder="Title"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageOneSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Image One Subtitle"
                    name="themeThreeTwoImageBannerImageOneSubtitle"
                    value="{{ getSetting('themeThreeTwoImageBannerImageOneSubtitle') }}" placeholder="Subtitle"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageOneLink">
                <x-backend.inputs.text :labelInline="false" label="Image One Link"
                    name="themeThreeTwoImageBannerImageOneLink"
                    value="{{ getSetting('themeThreeTwoImageBannerImageOneLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two" name="themeThreeTwoImageBannerImageTwo"
                    value="{{ getSetting('themeThreeTwoImageBannerImageTwo') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x400 pixels</p>

                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageTwoTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Two Title"
                    name="themeThreeTwoImageBannerImageTwoTitle"
                    value="{{ getSetting('themeThreeTwoImageBannerImageTwoTitle') }}" placeholder="Title"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageTwoSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Image Two Subtitle"
                    name="themeThreeTwoImageBannerImageTwoSubtitle"
                    value="{{ getSetting('themeThreeTwoImageBannerImageTwoSubtitle') }}" placeholder="Subtitle"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeTwoImageBannerImageTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Image Two Link"
                    name="themeThreeTwoImageBannerImageTwoLink"
                    value="{{ getSetting('themeThreeTwoImageBannerImageTwoLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- flash sale products --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Flash Sale') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="pt-2">
                    <input type="hidden" name="types[]" value="showThemeThreeFlashSale">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Flash Sale') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeThreeFlashSale" value="1"
                            isChecked="{{ getSetting('showThemeThreeFlashSale') ?? false }}" />
                    </div>

                    <input type="hidden" name="types[]" value="themeThreeFlashSale">
                    <x-backend.inputs.select :labelInline="false" label="Flash Sale" name="themeThreeFlashSale"
                        data-placeholder="{{ translate('Select flash sale campaign') }}" :isRequired="false">

                        @foreach ($campaigns as $campaign)
                            <x-backend.inputs.select-option name="{{ $campaign->name }}" value="{{ $campaign->id }}"
                                selected="{{ getSetting('themeThreeFlashSale') }}" />
                        @endforeach
                    </x-backend.inputs.select>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn mt-3" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- category products --}}
@php
    $themeThreeCategories = collect(json_decode(getSetting('themeThreeCategories'), true));
@endphp
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Category Products') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeThreeCategoryProducts">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Category Products') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeThreeCategoryProducts"
                        value="1" isChecked="{{ getSetting('showThemeThreeCategoryProducts') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeThreeCategories">

                <div class="slider-input-wrapper">
                    @if (getSetting('themeThreeCategories') != null)
                        @foreach ($themeThreeCategories as $key => $value)
                            <div class="slider-input">
                                <div class="mt-5">
                                    <input type="hidden" name="themeThreeCategories[]" value="{{ $value }}">
                                    @php
                                        $selectedCat = \App\Models\Category::where('id', $value)->first();
                                    @endphp
                                    <x-backend.inputs.text :labelInline="false" label="Category" name="tempCat[]"
                                        value="{{ $selectedCat->collectTranslation('name', $langKey) }}"
                                        :isRequired="false" :isDisabled="true" :trashBtn="true"
                                        trashParent=".slider-input" />
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="slider-input mt-3">
                                    <div class="mt-5"> 
<x-backend.inputs.select :labelInline="false" label="Select Category" name="themeThreeCategories[]" :trashBtn="true" trashParent=".slider-input"> 
@foreach ($categories as $cat)
@if (!$themeThreeCategories->contains($cat->id))
<x-backend.inputs.select-option
name="{{ $cat->collectTranslation('name', $langKey) }}"
value="{{ $cat->id }}"   />
@endif 
@foreach ($cat->childrenCategories as $childCategory)
@include(' backend.admin.categories.subCategory', [
    'subCategory' => $childCategory,
    'langKey' => $langKey,
    'category' => null,
    'themeThreeCategories' => $themeThreeCategories,
])
@endforeach
@endforeach
                        </x-backend.inputs.select>
            </div>
        </div>'
                    data-target=".slider-input-wrapper">
                    {{ translate('Add Category') }}
                    <span class="ml-2">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                </a>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- bottom 2 image banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Bottom 2 Image Banner') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="showThemeThreeBottomTwoImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Bottom 2 Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeThreeBottomTwoImageBanner"
                        value="1" isChecked="{{ getSetting('showThemeThreeBottomTwoImageBanner') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One"
                    name="themeThreeBottomTwoImageBannerImageOne"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageOne') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x400 pixels</p>

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageOneHeadline">
                <x-backend.inputs.text :labelInline="false" label="Image One Headline"
                    name="themeThreeBottomTwoImageBannerImageOneHeadline"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageOneHeadline') }}" placeholder="Headline"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageOneTitle">
                <x-backend.inputs.text :labelInline="false" label="Image One Title"
                    name="themeThreeBottomTwoImageBannerImageOneTitle"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageOneTitle') }}" placeholder="Title"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageOneSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Image One Subtitle"
                    name="themeThreeBottomTwoImageBannerImageOneSubtitle"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageOneSubtitle') }}" placeholder="Subtitle"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageOneLink">
                <x-backend.inputs.text :labelInline="false" label="Image One Link"
                    name="themeThreeBottomTwoImageBannerImageOneLink"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageOneLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two"
                    name="themeThreeBottomTwoImageBannerImageTwo"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageTwo') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x400 pixels</p>

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageTwoHeadline">
                <x-backend.inputs.text :labelInline="false" label="Image Two Headline"
                    name="themeThreeBottomTwoImageBannerImageTwoHeadline"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageTwoHeadline') }}" placeholder="Headline"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageTwoTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Two Title"
                    name="themeThreeBottomTwoImageBannerImageTwoTitle"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageTwoTitle') }}" placeholder="Title"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageTwoSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Image Two Subtitle"
                    name="themeThreeBottomTwoImageBannerImageTwoSubtitle"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageTwoSubtitle') }}" placeholder="Subtitle"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeBottomTwoImageBannerImageTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Image Two Link"
                    name="themeThreeBottomTwoImageBannerImageTwoLink"
                    value="{{ getSetting('themeThreeBottomTwoImageBannerImageTwoLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- social media section --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Social Media Section') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="showthemeThreeSocialMedia">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Social Media Section') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showthemeThreeSocialMedia" value="1"
                        isChecked="{{ getSetting('showthemeThreeSocialMedia') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeThreeSocialMediaHeadline">
                <x-backend.inputs.text :labelInline="false" label="Headline" name="themeThreeSocialMediaHeadline"
                    value="{{ getSetting('themeThreeSocialMediaHeadline') }}" placeholder="Headline"
                    :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeSocialMediaTitle">
                <x-backend.inputs.text :labelInline="false" label="Subtitle" name="themeThreeSocialMediaTitle"
                    value="{{ getSetting('themeThreeSocialMediaTitle') }}" placeholder="Title" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeThreeSocialMediaSubtitle">
                <x-backend.inputs.text :labelInline="false" label="Subtitle" name="themeThreeSocialMediaSubtitle"
                    value="{{ getSetting('themeThreeSocialMediaSubtitle') }}" placeholder="Subtiitle"
                    :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn mt-3" />
                </div>
            </form>
        </div>
    </div>
</div>
