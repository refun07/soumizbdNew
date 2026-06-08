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


{{-- main banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Main Banners') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeTwoMainBanners">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Main Banners') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeTwoMainBanners"
                        value="1" isChecked="{{ getSetting('showThemeTwoMainBanners') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeTwoBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One" name="themeTwoBannerImageOne"
                    value="{{ getSetting('themeTwoBannerImageOne') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 1920x600 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneLink">
                <x-backend.inputs.text :labelInline="false" label="Banner One Link" name="themeTwoBannerOneLink"
                    value="{{ getSetting('themeTwoBannerOneLink') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneHeadline">
                <x-backend.inputs.text :labelInline="false" label="Banner One Headline" name="themeTwoBannerOneHeadline"
                    value="{{ getSetting('themeTwoBannerOneHeadline') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneHeadlineColor">
                <x-backend.inputs.color :isRequired="false" label="Banner One HeadLine Color" name="themeTwoBannerOneHeadlineColor" value="{{ getSetting('themeTwoBannerOneHeadlineColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneTitle">
                <x-backend.inputs.text :labelInline="false" label="Banner One Title" name="themeTwoBannerOneTitle"
                    value="{{ getSetting('themeTwoBannerOneTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneTitleColor">
                <x-backend.inputs.color :isRequired="false" label="Banner One Title Color" name="themeTwoBannerOneTitleColor" value="{{ getSetting('themeTwoBannerOneTitleColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Banner One Subtitle" name="themeTwoBannerOneSubTitle"
                    value="{{ getSetting('themeTwoBannerOneSubTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerOneSubTitleColor">
                <x-backend.inputs.color :isRequired="false" label="Banner One SubTitle Color" name="themeTwoBannerOneSubTitleColor" value="{{ getSetting('themeTwoBannerOneSubTitleColor') }}" />

                <input type="hidden" name="types[]" value="themeTwoBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two" name="themeTwoBannerImageTwo"
                    value="{{ getSetting('themeTwoBannerImageTwo') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 1920x600 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Banner Two Link" name="themeTwoBannerTwoLink"
                    value="{{ getSetting('themeTwoBannerTwoLink') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerTwoHeadline">
                <x-backend.inputs.text :labelInline="false" label="Banner Two Headline" name="themeTwoBannerTwoHeadline"
                    value="{{ getSetting('themeTwoBannerTwoHeadline') ?? '' }}" :isRequired="false" />


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerTwoHeadlineColor">
                <x-backend.inputs.color :isRequired="false" label="Banner Two HeadLine Color" name="themeTwoBannerTwoHeadlineColor" value="{{ getSetting('themeTwoBannerTwoHeadlineColor') }}" />


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerTwoTitle">
                <x-backend.inputs.text :labelInline="false" label="Banner Two Title" name="themeTwoBannerTwoTitle"
                    value="{{ getSetting('themeTwoBannerTwoTitle') ?? '' }}" :isRequired="false" />


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoBannerTwoTitleColor">
                <x-backend.inputs.color :isRequired="false" label="Banner Two Title Color" name="themeTwoBannerTwoTitleColor" value="{{ getSetting('themeTwoBannerTwoTitleColor') }}" />

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
                        getSetting('themeTwoFeaturedCategories') != null
                            ? collect(json_decode(getSetting('themeTwoFeaturedCategories')))
                            : collect();
                @endphp
                <div class="pt-2">

                    <input type="hidden" name="types[]" value="showThemeTwoFeaturedCategories">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Featured Categories') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeTwoFeaturedCategories" value="1"
                            isChecked="{{ getSetting('showThemeTwoFeaturedCategories') ?? false }}" />
                    </div>


                    <input type="hidden" name="types[]" value="themeTwoFeaturedCategories">
                    <x-backend.inputs.select :labelInline="false" label="Featured Categories"
                        name="themeTwoFeaturedCategories[]" multiple data-selection-css-class="multi-select2"
                        data-placeholder="{{ translate('Select categories') }}" :isRequired="false">

                        @foreach ($categories as $cat)
                            <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name', $langKey) }}"
                                value="{{ $cat->id }}"
                                selected="{{ $featuredCategories->contains($cat->id) ? $cat->id : 0 }}" />

                            @foreach ($cat->childrenCategories as $childCategory)
                                @include('backend.admin.categories.subCategory', [
                                    'subCategory' => $childCategory,
                                    'langKey' => $langKey,
                                    'category' => null,
                                    'featuredCategories' => $featuredCategories,
                                ])
                            @endforeach
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
    $themeTwoCategories = collect(json_decode(getSetting('themeTwoCategories'), true));
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

                <input type="hidden" name="types[]" value="showThemeTwoCategoryProducts">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Category Products') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeTwoCategoryProducts"
                        value="1" isChecked="{{ getSetting('showThemeTwoCategoryProducts') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeTwoCategories">

                <div class="slider-input-wrapper">
                    @if (getSetting('themeTwoCategories') != null)
                        @foreach ($themeTwoCategories as $key => $value)
                            <div class="slider-input">
                                <div class="mt-5">
                                    <input type="hidden" name="themeTwoCategories[]" value="{{ $value }}">
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
<x-backend.inputs.select :labelInline="false" label="Select Category" name="themeTwoCategories[]" :trashBtn="true" trashParent=".slider-input">
@foreach ($categories as $cat)
@if (!$themeTwoCategories->contains($cat->id))
<x-backend.inputs.select-option
name="{{ $cat->collectTranslation('name', $langKey) }}"
value="{{ $cat->id }}"   />
@endif
@foreach ($cat->childrenCategories as $childCategory)
@include(' backend.admin.categories.subCategory', [
    'subCategory' => $childCategory,
    'langKey' => $langKey,
    'category' => null,
    'themeTwoCategories' => $themeTwoCategories,
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


{{-- 3 banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Three Banners') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeTwoThreeImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Three Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeTwoThreeImageBanner" value="1"
                        isChecked="{{ getSetting('showThemeTwoThreeImageBanner') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeTwoThreeBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One" name="themeTwoThreeBannerImageOne"
                    value="{{ getSetting('themeTwoThreeBannerImageOne') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 600x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoThreeBannerOneLink">
                <x-backend.inputs.text :labelInline="false" label="Banner One Link" name="themeTwoThreeBannerOneLink"
                    value="{{ getSetting('themeTwoThreeBannerOneLink') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoThreeBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two" name="themeTwoThreeBannerImageTwo"
                    value="{{ getSetting('themeTwoThreeBannerImageTwo') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 600x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoThreeBannerTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Banner Two Link" name="themeTwoThreeBannerTwoLink"
                    value="{{ getSetting('themeTwoThreeBannerTwoLink') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoThreeBannerImageThree">
                <x-backend.inputs.file :labelInline="false" label="Image Three" name="themeTwoThreeBannerImageThree"
                    value="{{ getSetting('themeTwoThreeBannerImageThree') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 600x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoThreeBannerThreeLink">
                <x-backend.inputs.text :labelInline="false" label="Banner Three Link"
                    name="themeTwoThreeBannerThreeLink"
                    value="{{ getSetting('themeTwoThreeBannerThreeLink') ?? '' }}" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- on sale products --}}
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
                    <input type="hidden" name="types[]" value="showThemeTwoFlashSale">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Flash Sale') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeTwoFlashSale" value="1"
                            isChecked="{{ getSetting('showThemeTwoFlashSale') ?? false }}" />
                    </div>

                    <input type="hidden" name="types[]" value="themeTwoFlashSale">
                    <x-backend.inputs.select :labelInline="false" label="Flash Sale" name="themeTwoFlashSale"
                        data-placeholder="{{ translate('Select flash sale campaign') }}" :isRequired="false">

                        @foreach ($campaigns as $campaign)
                            <x-backend.inputs.select-option name="{{ $campaign->name }}" value="{{ $campaign->id }}"
                                selected="{{ getSetting('themeTwoFlashSale') }}" />
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

{{-- 4 image banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Four Image Banners') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeTwoFourImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Three Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeTwoFourImageBanner" value="1"
                        isChecked="{{ getSetting('showThemeTwoFourImageBanner') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeTwoFourBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One" name="themeTwoFourBannerImageOne"
                    value="{{ getSetting('themeTwoFourBannerImageOne') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 400x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoFourBannerOneLink">
                <x-backend.inputs.text :labelInline="false" label="Banner One Link" name="themeTwoFourBannerOneLink"
                    value="{{ getSetting('themeTwoFourBannerOneLink') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoFourBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two" name="themeTwoFourBannerImageTwo"
                    value="{{ getSetting('themeTwoFourBannerImageTwo') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 400x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoFourBannerTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Banner One Link" name="themeTwoFourBannerTwoLink"
                    value="{{ getSetting('themeTwoFourBannerTwoLink') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoFourBannerImageThree">
                <x-backend.inputs.file :labelInline="false" label="Image Three" name="themeTwoFourBannerImageThree"
                    value="{{ getSetting('themeTwoFourBannerImageThree') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 400x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoFourBannerThreeLink">
                <x-backend.inputs.text :labelInline="false" label="Banner One Link" name="themeTwoFourBannerThreeLink"
                    value="{{ getSetting('themeTwoFourBannerThreeLink') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoFourBannerImageFour">
                <x-backend.inputs.file :labelInline="false" label="Image Three" name="themeTwoFourBannerImageFour"
                    value="{{ getSetting('themeTwoFourBannerImageFour') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 400x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoFourBannerFourLink">
                <x-backend.inputs.text :labelInline="false" label="Banner One Link" name="themeTwoFourBannerFourLink"
                    value="{{ getSetting('themeTwoFourBannerFourLink') ?? '' }}" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- about-us --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('About Us Section') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeTwoAboutUs">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Three Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeTwoAboutUs" value="1"
                        isChecked="{{ getSetting('showThemeTwoAboutUs') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeTwoAboutUsImage">
                <x-backend.inputs.file :labelInline="false" label="About Us Image" name="themeTwoAboutUsImage"
                    value="{{ getSetting('themeTwoAboutUsImage') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 600x400 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsHeadline">
                <x-backend.inputs.text :labelInline="false" label="About Us Headline" name="themeTwoAboutUsHeadline"
                    value="{{ getSetting('themeTwoAboutUsHeadline') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Title" name="themeTwoAboutUsTitle"
                    value="{{ getSetting('themeTwoAboutUsTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSubTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us SubTitle" name="themeTwoAboutUsSubTitle"
                    value="{{ getSetting('themeTwoAboutUsSubTitle') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionOneIcon">
                <x-backend.inputs.file :labelInline="false" label="About Us Section One Icon"
                    name="themeTwoAboutUsSectionOneIcon" value="{{ getSetting('themeTwoAboutUsSectionOneIcon') }}"
                    class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 64x64 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionOneTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section One Title"
                    name="themeTwoAboutUsSectionOneTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionOneTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionOneSubTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section One SubTitle"
                    name="themeTwoAboutUsSectionOneSubTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionOneSubTitle') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionTwoIcon">
                <x-backend.inputs.file :labelInline="false" label="About Us Section Two Icon"
                    name="themeTwoAboutUsSectionTwoIcon" value="{{ getSetting('themeTwoAboutUsSectionTwoIcon') }}"
                    class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 64x64 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionTwoTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section Two Title"
                    name="themeTwoAboutUsSectionTwoTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionTwoTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionTwoSubTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section Two SubTitle"
                    name="themeTwoAboutUsSectionTwoSubTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionTwoSubTitle') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionThreeIcon">
                <x-backend.inputs.file :labelInline="false" label="About Us Section Three Icon"
                    name="themeTwoAboutUsSectionThreeIcon"
                    value="{{ getSetting('themeTwoAboutUsSectionThreeIcon') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 64x64 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionThreeTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section Three Title"
                    name="themeTwoAboutUsSectionThreeTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionThreeTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionThreeSubTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section Three SubTitle"
                    name="themeTwoAboutUsSectionThreeSubTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionThreeSubTitle') ?? '' }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionFourIcon">
                <x-backend.inputs.file :labelInline="false" label="About Us Section Four Icon"
                    name="themeTwoAboutUsSectionFourIcon" value="{{ getSetting('themeTwoAboutUsSectionFourIcon') }}"
                    class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 64x64 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionFourTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section Four Title"
                    name="themeTwoAboutUsSectionFourTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionFourTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeTwoAboutUsSectionFourSubTitle">
                <x-backend.inputs.text :labelInline="false" label="About Us Section Four SubTitle"
                    name="themeTwoAboutUsSectionFourSubTitle"
                    value="{{ getSetting('themeTwoAboutUsSectionFourSubTitle') ?? '' }}" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- trendy products --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Trendy Products') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $themeTwoTrendyProducts =
                        getSetting('themeTwoTrendyProducts') != null
                            ? collect(json_decode(getSetting('themeTwoTrendyProducts')))
                            : collect();
                @endphp
                <div class="pt-2">

                    <input type="hidden" name="types[]" value="showThemeTwoTrendyProducts">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Top Products') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeTwoTrendyProducts" value="1"
                            isChecked="{{ getSetting('showThemeTwoTrendyProducts') ?? false }}" />
                    </div>


                    <input type="hidden" name="types[]" value="themeTwoTrendyProducts">
                    <x-backend.inputs.select :labelInline="false" label="Trendy Products"
                        name="themeTwoTrendyProducts[]" multiple data-selection-css-class="multi-select2"
                        data-placeholder="{{ translate('Select products') }}" :isRequired="false">

                        @foreach ($products as $product)
                            <x-backend.inputs.select-option
                                name="{{ $product->collectTranslation('name', $langKey) }}"
                                value="{{ $product->id }}"
                                selected="{{ $themeTwoTrendyProducts->contains($product->id) ? $product->id : 0 }}" />
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

{{-- reviews --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Review Section') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="themeTwoReviewAvatars">
                <input type="hidden" name="types[]" value="themeTwoReviewNames">
                <input type="hidden" name="types[]" value="themeTwoReviewDesignations">
                <input type="hidden" name="types[]" value="themeTwoReviewTexts">
                <input type="hidden" name="types[]" value="themeTwoReviewRatings">


                <input type="hidden" name="types[]" value="showThemeTwoReviewSection">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Three Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeTwoReviewSection" value="1"
                        isChecked="{{ getSetting('showThemeTwoReviewSection') ?? false }}" />
                </div>

                <div class="reviews-input-wrapper">
                    @if (getSetting('themeTwoReviewAvatars') != null)
                        @foreach (json_decode(getSetting('themeTwoReviewAvatars'), true) as $key => $value)
                            <div class="reviews-input space-y-3">
                                <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                    trashParent=".reviews-input" label="Image" name="themeTwoReviewAvatars[]"
                                    value="{{ $value }}" class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1">Recommended: 100x100 pixels</p>

                                <x-backend.inputs.text :labelInline="false" label="Name" name="themeTwoReviewNames[]"
                                    value="{{ json_decode(getSetting('themeTwoReviewNames'), true)[$key] }}"
                                    placeholder="Type name" :isRequired="false" />

                                <x-backend.inputs.text :labelInline="false" label="Title"
                                    name="themeTwoReviewDesignations[]"
                                    value="{{ json_decode(getSetting('themeTwoReviewDesignations'), true)[$key] }}"
                                    placeholder="Type title" :isRequired="false" />

                                <x-backend.inputs.text :labelInline="false" label="Text" name="themeTwoReviewTexts[]"
                                    value="{{ json_decode(getSetting('themeTwoReviewTexts'), true)[$key] }}"
                                    placeholder="Type text" :isRequired="false" />

                                <x-backend.inputs.number min="1" max="5" :labelInline="false"
                                    label="Rating" name="themeTwoReviewRatings[]"
                                    value="{{ json_decode(getSetting('themeTwoReviewRatings'), true)[$key] }}"
                                    placeholder="Type rating. e.g. 5" :isRequired="false" />
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="reviews-input mt-3">
                                    <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".reviews-input"
                                        label="Image" name="themeTwoReviewAvatars[]" value=""  class="mb-3" />
                                    <p class="text-xs text-gray-500 mt-1">Recommended: 100x100 pixels</p>

                                    <x-backend.inputs.text :labelInline="false" label="Name" name="themeTwoReviewNames[]"
                                        value=""
                                        placeholder="Type name" :isRequired="false"  class="mb-3" />

                                    <x-backend.inputs.text :labelInline="false" label="Title" name="themeTwoReviewDesignations[]"
                                        value=""
                                        placeholder="Type title" :isRequired="false" class="mb-3"  />

                                    <x-backend.inputs.text :labelInline="false" label="Text" name="themeTwoReviewTexts[]"
                                        value=""
                                        placeholder="Type text" :isRequired="false"  class="mb-3" />

                                    <x-backend.inputs.number min="1" max="5" :labelInline="false" label="Rating" name="themeTwoReviewRatings[]"
                                        value=""
                                        placeholder="Type rating. e.g. 5" :isRequired="false" class="mb-3"  />
                                </div>'
                    data-target=".reviews-input-wrapper">
                    {{ translate('Add Review') }}
                    <span class="ml-2">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                </a>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn mt-3" />
                </div>
            </form>
        </div>
    </div>
</div>
