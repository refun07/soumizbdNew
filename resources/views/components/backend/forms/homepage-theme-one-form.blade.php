@php
    $categories = \App\Models\Category::where('parent_id', 0)
        ->orderBy('sorting_order_level', 'desc')
        ->with('childrenCategories')
        ->get();
    // $products = \App\Models\Product::shop()->isPublished()->get();
    $products = \App\Models\Product::isPublished()->latest()->get();
    $shops = \App\Models\Shop::isApproved()->isPublished()->get();
    $brands = \App\Models\Brand::isActive()->get();
    $campaigns = \App\Models\Campaign::isPublished()
        ->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))
        ->latest()
        ->get();
    $tags = \App\Models\Tag::get();
@endphp

{{-- cart visibility settings --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Cart Settings') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST">
                @csrf

                <input type="hidden" name="types[]" value="cartVisibilityToggle">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Fixed Cart Button') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="cartVisibilityToggle" value="1"
                        isChecked="{{ getSetting('cartVisibilityToggle') != '0' }}" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Cart Settings') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>


{{-- main banner carousel --}}
@php
    $mainBanners = getSetting('themeOneMainBanners') != null ? json_decode(getSetting('themeOneMainBanners'), true) : [];
    $mainSideBanners = getSetting('themeOneMainSideBanners') != null ? json_decode(getSetting('themeOneMainSideBanners'), true) : [];
    $bannerSettings = getSetting('themeOneMainBannerSettings') != null ? json_decode(getSetting('themeOneMainBannerSettings'), true) : [];
    $secondaryBanner = getSetting('themeOneSecondaryBanner') != null ? json_decode(getSetting('themeOneSecondaryBanner'), true) : [];
@endphp
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Main Banner Carousel') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeOneMainBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Main Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneMainBanner"
                        value="1" isChecked="{{ getSetting('showThemeOneMainBanner') ?? false }}" />
                </div>

                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-3">{{ translate('Carousel Settings') }}</h3>
                    <div class="flex items-center justify-between mb-4">
                        <input type="hidden" name="types[]" value="themeOneMainBannerSettings">
                        <input type="hidden" name="themeOneMainBannerSettings[autoplay]" value="0">
                        <p>{{ translate('Autoplay') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="themeOneMainBannerSettings[autoplay]"
                            value="1" isChecked="{{ $bannerSettings['autoplay'] ?? true }}" />
                    </div>
                    <x-backend.inputs.text :labelInline="false" label="Delay (milliseconds)"
                        name="themeOneMainBannerSettings[delay]"
                        value="{{ $bannerSettings['delay'] ?? 5000 }}" :isRequired="false" class="mb-4" />

                    <x-backend.inputs.select :labelInline="false" label="Content Alignment"
                        name="themeOneMainBannerSettings[contentAlignment]" :isRequired="false">
                        <x-backend.inputs.select-option name="Left (Default)" value="left"
                            selected="{{ ($bannerSettings['contentAlignment'] ?? 'left') === 'left' }}" />
                        <x-backend.inputs.select-option name="Center" value="center"
                            selected="{{ ($bannerSettings['contentAlignment'] ?? '') === 'center' }}" />
                        <x-backend.inputs.select-option name="Right" value="right"
                            selected="{{ ($bannerSettings['contentAlignment'] ?? '') === 'right' }}" />
                    </x-backend.inputs.select>
                </div>

                <input type="hidden" name="types[]" value="themeOneMainBanners">

                <div class="main-banner-wrapper">
                    @if (count($mainBanners) > 0)
                        @foreach ($mainBanners as $key => $banner)
                            <div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium">{{ translate('Banner') }} {{ $key + 1 }}</h4>
                                    <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-banner-item">
                                        <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                    </button>
                                </div>

                                <x-backend.inputs.file :labelInline="false" label="Foreground Image"
                                    name="themeOneMainBanners[{{ $key }}][foregroundImage]"
                                    value="{{ $banner['foregroundImage'] ?? '' }}" class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 600x400 pixels</p>
                                <div class="flex items-center justify-between mb-4">
                                    <input type="hidden" name="themeOneMainBanners[{{ $key }}][showForegroundImage]" value="0">
                                    <p>{{ translate('Show Foreground Image') }}</p>
                                    <x-backend.inputs.checkbox toggler="true"
                                        name="themeOneMainBanners[{{ $key }}][showForegroundImage]" value="1"
                                        isChecked="{{ ($banner['showForegroundImage'] ?? '1') == '1' }}" />
                                </div>

                                <div class="flex items-center justify-between mb-4">
                                    <input type="hidden" name="themeOneMainBanners[{{ $key }}][useBackgroundVideo]" value="0">
                                    <p>{{ translate('Use Background Video') }}</p>
                                    <x-backend.inputs.checkbox toggler="true"
                                        name="themeOneMainBanners[{{ $key }}][useBackgroundVideo]" value="1"
                                        class="main-banner-background-toggle"
                                        isChecked="{{ ($banner['useBackgroundVideo'] ?? '0') == '1' }}" />
                                </div>

                                <div
                                    class="main-banner-background-image-field {{ ($banner['useBackgroundVideo'] ?? '0') == '1' ? 'hidden' : '' }}">
                                    <x-backend.inputs.file :labelInline="false" label="Background Image"
                                        name="themeOneMainBanners[{{ $key }}][backgroundImage]"
                                        value="{{ $banner['backgroundImage'] ?? '' }}" class="mb-3" />
                                    <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 1212x644 pixels</p>
                                </div>

                                <div
                                    class="main-banner-background-video-field {{ ($banner['useBackgroundVideo'] ?? '0') == '1' ? '' : 'hidden' }}">
                                    <x-backend.inputs.text :labelInline="false" label="YouTube Video Link"
                                        name="themeOneMainBanners[{{ $key }}][backgroundVideoLink]"
                                        value="{{ $banner['backgroundVideoLink'] ?? '' }}"
                                        placeholder="https://www.youtube.com/watch?v=8UPPwMNxGvQ" :isRequired="false"
                                        class="mb-4" />
                                    <div class="flex items-center justify-between mb-4">
                                        <input type="hidden" name="themeOneMainBanners[{{ $key }}][backgroundVideoAudio]" value="0">
                                        <p>{{ translate('Play Video Audio') }}</p>
                                        <x-backend.inputs.checkbox toggler="true"
                                            name="themeOneMainBanners[{{ $key }}][backgroundVideoAudio]" value="1"
                                            isChecked="{{ ($banner['backgroundVideoAudio'] ?? '0') == '1' }}" />
                                    </div>
                                </div>

                                <x-backend.inputs.text :labelInline="false" label="Link"
                                    name="themeOneMainBanners[{{ $key }}][link]"
                                    value="{{ $banner['link'] ?? '' }}" placeholder="https://www.example.com" :isRequired="false" class="mb-4" />

                                <x-backend.inputs.text :labelInline="false" label="Badge Text"
                                    name="themeOneMainBanners[{{ $key }}][badgeText]"
                                    value="{{ $banner['badgeText'] ?? '' }}" :isRequired="false" class="mb-4" />

                                <x-backend.inputs.text :labelInline="false" label="Title"
                                    name="themeOneMainBanners[{{ $key }}][title]"
                                    value="{{ $banner['title'] ?? '' }}" :isRequired="false" class="mb-4" />

                                <x-backend.inputs.color :isRequired="false" label="Title Color"
                                    name="themeOneMainBanners[{{ $key }}][titleColor]"
                                    value="{{ $banner['titleColor'] ?? '' }}" class="mb-4" />

                                <x-backend.inputs.text :labelInline="false" label="Subtitle"
                                    name="themeOneMainBanners[{{ $key }}][subTitle]"
                                    value="{{ $banner['subTitle'] ?? '' }}" :isRequired="false" class="mb-4" />

                                <x-backend.inputs.color :isRequired="false" label="Subtitle Color"
                                    name="themeOneMainBanners[{{ $key }}][subTitleColor]"
                                    value="{{ $banner['subTitleColor'] ?? '' }}" />
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium">{{ translate('New Banner') }}</h4>
                                            <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-banner-item">
                                                <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                            </button>
                                        </div>
                                        <x-backend.inputs.file :labelInline="false"
                                            label="Foreground Image" name="themeOneMainBanners[__INDEX__][foregroundImage]" value="" class="mb-3" />
                                        <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 600x400 pixels</p>
                                        <div class="flex items-center justify-between mb-4">
                                            <input type="hidden" name="themeOneMainBanners[__INDEX__][showForegroundImage]" value="0">
                                            <p>{{ translate('Show Foreground Image') }}</p>
                                            <x-backend.inputs.checkbox toggler="true" name="themeOneMainBanners[__INDEX__][showForegroundImage]"
                                                value="1" :isChecked="true" />
                                        </div>
                                        <div class="flex items-center justify-between mb-4">
                                            <input type="hidden" name="themeOneMainBanners[__INDEX__][useBackgroundVideo]" value="0">
                                            <p>{{ translate('Use Background Video') }}</p>
                                            <x-backend.inputs.checkbox toggler="true" name="themeOneMainBanners[__INDEX__][useBackgroundVideo]"
                                                value="1" class="main-banner-background-toggle" :isChecked="false" />
                                        </div>
                                        <div class="main-banner-background-image-field">
                                            <x-backend.inputs.file :labelInline="false"
                                                label="Background Image" name="themeOneMainBanners[__INDEX__][backgroundImage]" value="" class="mb-3" />
                                            <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 1212x644 pixels</p>
                                        </div>
                                        <div class="main-banner-background-video-field hidden">
                                            <x-backend.inputs.text :labelInline="false" label="YouTube Video Link"
                                                name="themeOneMainBanners[__INDEX__][backgroundVideoLink]" value=""
                                                placeholder="https://www.youtube.com/watch?v=8UPPwMNxGvQ" :isRequired="false" class="mb-4" />
                                            <div class="flex items-center justify-between mb-4">
                                                <input type="hidden" name="themeOneMainBanners[__INDEX__][backgroundVideoAudio]" value="0">
                                                <p>{{ translate('Play Video Audio') }}</p>
                                                <x-backend.inputs.checkbox toggler="true" name="themeOneMainBanners[__INDEX__][backgroundVideoAudio]"
                                                    value="1" :isChecked="false" />
                                            </div>
                                        </div>
                                        <x-backend.inputs.text :labelInline="false" label="Link" name="themeOneMainBanners[__INDEX__][link]"
                                            value="" placeholder="https://www.example.com" :isRequired="false" class="mb-4" />
                                        <x-backend.inputs.text :labelInline="false" label="Badge Text" name="themeOneMainBanners[__INDEX__][badgeText]"
                                            value="" :isRequired="false" class="mb-4" />
                                        <x-backend.inputs.text :labelInline="false" label="Title" name="themeOneMainBanners[__INDEX__][title]"
                                            value="" :isRequired="false" class="mb-4" />
                                        <x-backend.inputs.color :isRequired="false" label="Title Color" name="themeOneMainBanners[__INDEX__][titleColor]"
                                            value="" class="mb-4" />
                                        <x-backend.inputs.text :labelInline="false" label="Subtitle" name="themeOneMainBanners[__INDEX__][subTitle]"
                                            value="" :isRequired="false" class="mb-4" />
                                        <x-backend.inputs.color :isRequired="false" label="Subtitle Color" name="themeOneMainBanners[__INDEX__][subTitleColor]"
                                            value="" />
                                    </div>'
                    data-target=".main-banner-wrapper">
                    {{ translate('Add Banner') }}
                    <span class="ml-2">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                </a>

                <input type="hidden" name="types[]" value="themeOneMainSideBanners">

                <div class="bg-gray-50 border border-gray-100 rounded-md p-3 mt-6 mb-4">
                    <h3 class="font-semibold text-base mb-1">{{ translate('Right Banner Slides') }}</h3>
                    <p class="text-xs text-gray-500">{{ translate('These slides appear in the right 40% banner on desktop and below the main banner on mobile.') }}</p>
                </div>

                <div class="main-banner-side-wrapper">
                    @if (count($mainSideBanners) > 0)
                        @foreach ($mainSideBanners as $key => $banner)
                            <div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium">{{ translate('Right Banner') }} {{ $key + 1 }}</h4>
                                    <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-banner-item">
                                        <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                    </button>
                                </div>

                                <x-backend.inputs.file :labelInline="false" label="Banner Image"
                                    name="themeOneMainSideBanners[{{ $key }}][backgroundImage]"
                                    value="{{ $banner['backgroundImage'] ?? '' }}" class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 800x600 pixels</p>

                                <x-backend.inputs.text :labelInline="false" label="Link"
                                    name="themeOneMainSideBanners[{{ $key }}][link]"
                                    value="{{ $banner['link'] ?? '' }}" placeholder="https://www.example.com" :isRequired="false" class="mb-4" />
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium">{{ translate('New Right Banner') }}</h4>
                                            <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-banner-item">
                                                <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                            </button>
                                        </div>
                                        <x-backend.inputs.file :labelInline="false"
                                            label="Banner Image" name="themeOneMainSideBanners[__INDEX__][backgroundImage]" value="" class="mb-3" />
                                        <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 800x600 pixels</p>
                                        <x-backend.inputs.text :labelInline="false" label="Link" name="themeOneMainSideBanners[__INDEX__][link]"
                                            value="" placeholder="https://www.example.com" :isRequired="false" class="mb-4" />
                                    </div>'
                    data-target=".main-banner-side-wrapper">
                    {{ translate('Add Right Banner') }}
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

{{-- promo cards --}}
@php
    $promoCardSettings = getSetting('themeOnePromoCardSettings') != null ? json_decode(getSetting('themeOnePromoCardSettings'), true) : [];
    $promoCards = getSetting('themeOnePromoCards') != null ? json_decode(getSetting('themeOnePromoCards'), true) : [];
@endphp
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Main Promo Cards') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeOnePromoCards">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Promo Cards') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOnePromoCards"
                        value="1" isChecked="{{ getSetting('showThemeOnePromoCards') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeOnePromoCardSettings">
                <div class="bg-gray-50 border border-gray-100 rounded-md p-3 mb-4">
                    <h3 class="font-semibold text-base mb-1">{{ translate('Card Settings') }}</h3>
                    <p class="text-xs text-gray-500">{{ translate('These colors apply to every promo card in this carousel.') }}</p>
                </div>

                <x-backend.inputs.color :isRequired="false" label="Full Banner Background Color"
                    name="themeOnePromoCardSettings[sectionBackgroundColor]"
                    value="{{ $promoCardSettings['sectionBackgroundColor'] ?? '#f7f7f7' }}" class="mb-4" />

                <x-backend.inputs.color :isRequired="false" label="Card Background Color"
                    name="themeOnePromoCardSettings[backgroundColor]"
                    value="{{ $promoCardSettings['backgroundColor'] ?? '#ffffff' }}" class="mb-4" />

                <x-backend.inputs.color :isRequired="false" label="Main Text Color"
                    name="themeOnePromoCardSettings[mainTextColor]"
                    value="{{ $promoCardSettings['mainTextColor'] ?? '#B3052A' }}" class="mb-4" />

                <x-backend.inputs.color :isRequired="false" label="Secondary Text Color"
                    name="themeOnePromoCardSettings[secondaryTextColor]"
                    value="{{ $promoCardSettings['secondaryTextColor'] ?? '#3f3f46' }}" class="mb-4" />

                <input type="hidden" name="types[]" value="themeOnePromoCards">

                <div class="promo-card-wrapper">
                    @if (count($promoCards) > 0)
                        @foreach ($promoCards as $key => $card)
                            <div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-medium">{{ translate('Promo Card') }} {{ $key + 1 }}</h4>
                                    <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-banner-item">
                                        <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                    </button>
                                </div>

                                <x-backend.inputs.file :labelInline="false" label="Card Image"
                                    name="themeOnePromoCards[{{ $key }}][image]"
                                    value="{{ $card['image'] ?? '' }}" class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 120x120 pixels</p>

                                <x-backend.inputs.text :labelInline="false" label="Main Text"
                                    name="themeOnePromoCards[{{ $key }}][mainText]"
                                    value="{{ $card['mainText'] ?? '' }}" :isRequired="false" class="mb-4" />

                                <x-backend.inputs.text :labelInline="false" label="Below Text"
                                    name="themeOnePromoCards[{{ $key }}][subText]"
                                    value="{{ $card['subText'] ?? '' }}" :isRequired="false" class="mb-4" />
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-medium">{{ translate('New Promo Card') }}</h4>
                                            <button type="button" class="text-red-500 hover:text-red-700 text-sm remove-banner-item">
                                                <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                            </button>
                                        </div>
                                        <x-backend.inputs.file :labelInline="false"
                                            label="Card Image" name="themeOnePromoCards[__INDEX__][image]" value="" class="mb-3" />
                                        <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 120x120 pixels</p>
                                        <x-backend.inputs.text :labelInline="false" label="Main Text" name="themeOnePromoCards[__INDEX__][mainText]"
                                            value="" :isRequired="false" class="mb-4" />
                                        <x-backend.inputs.text :labelInline="false" label="Below Text" name="themeOnePromoCards[__INDEX__][subText]"
                                            value="" :isRequired="false" class="mb-4" />
                                    </div>'
                    data-target=".promo-card-wrapper">
                    {{ translate('Add Promo Card') }}
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

{{-- secondary banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Secondary Banner') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeOneSecondaryBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Secondary Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneSecondaryBanner"
                        value="1" isChecked="{{ getSetting('showThemeOneSecondaryBanner') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeOneSecondaryBanner">

                <div class="main-banner-item border border-gray-200 rounded-lg p-4 mb-4">
                    <h4 class="font-medium mb-3">{{ translate('Secondary Banner Content') }}</h4>

                    <div class="flex items-center justify-between mb-4">
                        <input type="hidden" name="themeOneSecondaryBanner[useBackgroundVideo]" value="0">
                        <p>{{ translate('Use Background Video') }}</p>
                        <x-backend.inputs.checkbox toggler="true"
                            name="themeOneSecondaryBanner[useBackgroundVideo]" value="1"
                            class="main-banner-background-toggle"
                            isChecked="{{ ($secondaryBanner['useBackgroundVideo'] ?? '0') == '1' }}" />
                    </div>

                    <x-backend.inputs.select :labelInline="false" label="Content Alignment"
                        name="themeOneSecondaryBanner[contentAlignment]" :isRequired="false" class="mb-4">
                        <x-backend.inputs.select-option name="Left (Default)" value="left"
                            selected="{{ ($secondaryBanner['contentAlignment'] ?? 'left') === 'left' }}" />
                        <x-backend.inputs.select-option name="Center" value="center"
                            selected="{{ ($secondaryBanner['contentAlignment'] ?? '') === 'center' }}" />
                        <x-backend.inputs.select-option name="Right" value="right"
                            selected="{{ ($secondaryBanner['contentAlignment'] ?? '') === 'right' }}" />
                    </x-backend.inputs.select>

                    <div
                        class="main-banner-background-image-field {{ ($secondaryBanner['useBackgroundVideo'] ?? '0') == '1' ? 'hidden' : '' }}">
                        <x-backend.inputs.file :labelInline="false"
                            label="Background Image" name="themeOneSecondaryBanner[backgroundImage]"
                            value="{{ $secondaryBanner['backgroundImage'] ?? '' }}" class="mb-3" />
                        <p class="text-xs text-gray-500 mt-1 mb-4">Recommended: 1920x600 pixels</p>
                    </div>

                    <div
                        class="main-banner-background-video-field {{ ($secondaryBanner['useBackgroundVideo'] ?? '0') == '1' ? '' : 'hidden' }}">
                        <x-backend.inputs.text :labelInline="false" label="YouTube Video Link"
                            name="themeOneSecondaryBanner[backgroundVideoLink]"
                            value="{{ $secondaryBanner['backgroundVideoLink'] ?? '' }}"
                            placeholder="https://www.youtube.com/watch?v=8UPPwMNxGvQ" :isRequired="false"
                            class="mb-4" />
                        <div class="flex items-center justify-between mb-4">
                            <input type="hidden" name="themeOneSecondaryBanner[backgroundVideoAudio]" value="0">
                            <p>{{ translate('Play Video Audio') }}</p>
                            <x-backend.inputs.checkbox toggler="true"
                                name="themeOneSecondaryBanner[backgroundVideoAudio]" value="1"
                                isChecked="{{ ($secondaryBanner['backgroundVideoAudio'] ?? '0') == '1' }}" />
                        </div>
                    </div>

                    <x-backend.inputs.text :labelInline="false" label="Link" name="themeOneSecondaryBanner[link]"
                        value="{{ $secondaryBanner['link'] ?? '' }}" placeholder="https://www.example.com"
                        :isRequired="false" class="mb-4" />

                    <x-backend.inputs.text :labelInline="false" label="Badge Text"
                        name="themeOneSecondaryBanner[badgeText]" value="{{ $secondaryBanner['badgeText'] ?? '' }}"
                        :isRequired="false" class="mb-4" />

                    <x-backend.inputs.text :labelInline="false" label="Title" name="themeOneSecondaryBanner[title]"
                        value="{{ $secondaryBanner['title'] ?? '' }}" :isRequired="false" class="mb-4" />

                    <x-backend.inputs.color :isRequired="false" label="Title Color"
                        name="themeOneSecondaryBanner[titleColor]" value="{{ $secondaryBanner['titleColor'] ?? '' }}"
                        class="mb-4" />

                    <x-backend.inputs.text :labelInline="false" label="Subtitle"
                        name="themeOneSecondaryBanner[subTitle]" value="{{ $secondaryBanner['subTitle'] ?? '' }}"
                        :isRequired="false" class="mb-4" />

                    <x-backend.inputs.color :isRequired="false" label="Subtitle Color"
                        name="themeOneSecondaryBanner[subTitleColor]"
                        value="{{ $secondaryBanner['subTitleColor'] ?? '' }}" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- slider section --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Slider Section') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showThemeOneSliderSection">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Slider Section') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneSliderSection"
                        value="1" isChecked="{{ getSetting('showThemeOneSliderSection') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeOneSliders">
                <input type="hidden" name="types[]" value="themeOneSliderLinks">

                <div class="slider-input-wrapper">
                    @if (getSetting('themeOneSliders') != null)
                        @foreach (json_decode(getSetting('themeOneSliders'), true) as $key => $value)
                            <div class="slider-input">
                                <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                    label="Image" name="themeOneSliders[]" value="{{ $value }}"
                                    class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1">Recommended: 299.75x191.91 pixels</p>
                                <x-backend.inputs.text :labelInline="false" label="Link" name="themeOneSliderLinks[]"
                                    value="{{ json_decode(getSetting('themeOneSliderLinks'), true)[$key] }}"
                                    placeholder="https://www.example.com" :isRequired="false" />
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="themeOneSliders[]" value=""  class="mb-3" />
                                            <p class="text-xs text-gray-500 mt-1">Recommended: 299.75x191.91 pixels</p>
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="themeOneSliderLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                    </div>'
                    data-target=".slider-input-wrapper">
                    {{ translate('Add Banner') }}
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
                        getSetting('themeOneFeaturedCategories') != null
                            ? collect(json_decode(getSetting('themeOneFeaturedCategories')))
                            : collect();
                @endphp
                <div class="pt-2">

                    <input type="hidden" name="types[]" value="showThemeOneFeaturedCategories">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Featured Categories') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeOneFeaturedCategories"
                            value="1" isChecked="{{ getSetting('showThemeOneFeaturedCategories') ?? false }}" />
                    </div>


                    <input type="hidden" name="types[]" value="themeOneFeaturedCategories">
                    <x-backend.inputs.select :labelInline="false" label="Featured Categories"
                        name="themeOneFeaturedCategories[]" multiple data-selection-css-class="multi-select2"
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

{{-- top products --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Top Products') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $featuredProducts =
                        getSetting('themeOneTopProducts') != null
                            ? collect(json_decode(getSetting('themeOneTopProducts')))
                            : collect();
                @endphp
                <div class="pt-2">

                    <input type="hidden" name="types[]" value="showThemeOneTopProducts">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Top Products') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeOneTopProducts" value="1"
                            isChecked="{{ getSetting('showThemeOneTopProducts') ?? false }}" />
                    </div>


                    <input type="hidden" name="types[]" value="themeOneTopProducts">
                    <x-backend.inputs.select :labelInline="false" label="Featured Products" name="themeOneTopProducts[]"
                        multiple data-selection-css-class="multi-select2"
                        data-placeholder="{{ translate('Select products') }}" :isRequired="false">

                        @foreach ($products as $product)
                            <x-backend.inputs.select-option
                                name="{{ $product->collectTranslation('name', $langKey) }}"
                                value="{{ $product->id }}"
                                selected="{{ $featuredProducts->contains($product->id) ? $product->id : 0 }}" />
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

{{-- single image banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Single Image Banner') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="showThemeOneSingleImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Single Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneSingleImageBanner" value="1"
                        isChecked="{{ getSetting('showThemeOneSingleImageBanner') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerForegroundImage">
                <x-backend.inputs.file :labelInline="false" label="Foreground Image"
                    name="themeOneSingleImageBannerForegroundImage"
                    value="{{ getSetting('themeOneSingleImageBannerForegroundImage') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 1144x644 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerBackgroundImage">
                <x-backend.inputs.file :labelInline="false" label="Background Image"
                    name="themeOneSingleImageBannerBackgroundImage"
                    value="{{ getSetting('themeOneSingleImageBannerBackgroundImage') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 1144x644 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerLink">
                <x-backend.inputs.text :labelInline="false" label="Single Image Banner Link"
                    name="themeOneSingleImageBannerLink"
                    value="{{ getSetting('themeOneSingleImageBannerLink') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerBadgeText">
                <x-backend.inputs.text :labelInline="false" label="Single Image Banner Badge Text"
                    name="themeOneSingleImageBannerBadgeText"
                    value="{{ getSetting('themeOneSingleImageBannerBadgeText') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerHeadline">
                <x-backend.inputs.text :labelInline="false" label="Single Image Banner Headline"
                    name="themeOneSingleImageBannerHeadline"
                    value="{{ getSetting('themeOneSingleImageBannerHeadline') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerTitle">
                <x-backend.inputs.text :labelInline="false" label="Single Image Banner Title"
                    name="themeOneSingleImageBannerTitle"
                    value="{{ getSetting('themeOneSingleImageBannerTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Single Image Banner Subtitle"
                    name="themeOneSingleImageBannerSubTitle"
                    value="{{ getSetting('themeOneSingleImageBannerSubTitle') ?? '' }}" :isRequired="false" />

                <div class="mt-8 mb-3">
                    <h3 class="font-semibold text-lg">{{ translate('Colors') }}</h3>
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerBadgeTextColor">
                <x-backend.inputs.color :isRequired="false" label="Badge Text Color"
                    name="themeOneSingleImageBannerBadgeTextColor"
                    value="{{ getSetting('themeOneSingleImageBannerBadgeTextColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerBadgeBackgroundColor">
                <x-backend.inputs.color :isRequired="false" label="Badge Background Color"
                    name="themeOneSingleImageBannerBadgeBackgroundColor"
                    value="{{ getSetting('themeOneSingleImageBannerBadgeBackgroundColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerHeadlineColor">
                <x-backend.inputs.color :isRequired="false" label="Headline Color"
                    name="themeOneSingleImageBannerHeadlineColor"
                    value="{{ getSetting('themeOneSingleImageBannerHeadlineColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerTitleColor">
                <x-backend.inputs.color :isRequired="false" label="Title Color"
                    name="themeOneSingleImageBannerTitleColor"
                    value="{{ getSetting('themeOneSingleImageBannerTitleColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerSubTitleColor">
                <x-backend.inputs.color :isRequired="false" label="Subtitle Color"
                    name="themeOneSingleImageBannerSubTitleColor"
                    value="{{ getSetting('themeOneSingleImageBannerSubTitleColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerButtonTextColor">
                <x-backend.inputs.color :isRequired="false" label="Button Text Color"
                    name="themeOneSingleImageBannerButtonTextColor"
                    value="{{ getSetting('themeOneSingleImageBannerButtonTextColor') }}" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneSingleImageBannerButtonBackgroundColor">
                <x-backend.inputs.color :isRequired="false" label="Button Background Color"
                    name="themeOneSingleImageBannerButtonBackgroundColor"
                    value="{{ getSetting('themeOneSingleImageBannerButtonBackgroundColor') }}" />

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
                    <input type="hidden" name="types[]" value="showThemeOneFlashSale">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Flash Sale') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showThemeOneFlashSale" value="1"
                            isChecked="{{ getSetting('showThemeOneFlashSale') ?? false }}" />
                    </div>

                    <input type="hidden" name="types[]" value="themeOneFlashSale">
                    <x-backend.inputs.select :labelInline="false" label="Flash Sale" name="themeOneFlashSale"
                        data-placeholder="{{ translate('Select flash sale campaign') }}" :isRequired="false">

                        @foreach ($campaigns as $campaign)
                            <x-backend.inputs.select-option name="{{ $campaign->name }}" value="{{ $campaign->id }}"
                                selected="{{ getSetting('themeOneFlashSale') }}" />
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
            <span class="">{{ translate('4 Image Banner') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="showThemeOneFourImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide 4 Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneFourImageBanner" value="1"
                        isChecked="{{ getSetting('showThemeOneFourImageBanner') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One" name="themeOneFourImageBannerImageOne"
                    value="{{ getSetting('themeOneFourImageBannerImageOne') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 294x294 pixels</p>

                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageOneLink">
                <x-backend.inputs.text :labelInline="false" label="Image One Link"
                    name="themeOneFourImageBannerImageOneLink"
                    value="{{ getSetting('themeOneFourImageBannerImageOneLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two" name="themeOneFourImageBannerImageTwo"
                    value="{{ getSetting('themeOneFourImageBannerImageTwo') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 294x294 pixels</p>

                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Image Two Link"
                    name="themeOneFourImageBannerImageTwoLink"
                    value="{{ getSetting('themeOneFourImageBannerImageTwoLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageThree">
                <x-backend.inputs.file :labelInline="false" label="Image Three" name="themeOneFourImageBannerImageThree"
                    value="{{ getSetting('themeOneFourImageBannerImageThree') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 588x593.39 pixels</p>

                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageThreeLink">
                <x-backend.inputs.text :labelInline="false" label="Image Three Link"
                    name="themeOneFourImageBannerImageThreeLink"
                    value="{{ getSetting('themeOneFourImageBannerImageThreeLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageFour">
                <x-backend.inputs.file :labelInline="false" label="Image Four" name="themeOneFourImageBannerImageFour"
                    value="{{ getSetting('themeOneFourImageBannerImageFour') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 588x299.39 pixels</p>

                <input type="hidden" name="types[]" value="themeOneFourImageBannerImageFourLink">
                <x-backend.inputs.text :labelInline="false" label="Image Four Link"
                    name="themeOneFourImageBannerImageFourLink"
                    value="{{ getSetting('themeOneFourImageBannerImageFourLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>

{{-- category products --}}
@php
    $themeOneCategories = collect(json_decode(getSetting('themeOneCategories'), true));
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

                <input type="hidden" name="types[]" value="showThemeOneCategoryProducts">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Category Products') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneCategoryProducts"
                        value="1" isChecked="{{ getSetting('showThemeOneCategoryProducts') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeOneCategories">
                <input type="hidden" name="types[]" value="themeOneCategoryImages">
                <input type="hidden" name="types[]" value="themeOneCategoryLinks">

                <div class="slider-input-wrapper">
                    @if (getSetting('themeOneCategories') != null)

                        @foreach ($themeOneCategories as $key => $value)
                            <div class="slider-input">
                                <x-backend.inputs.file :labelInline="false" :trashBtn="true"
                                    trashParent=".slider-input" label="Image" name="themeOneCategoryImages[]"
                                    value="{{ json_decode(getSetting('themeOneCategoryImages'), true)[$key] }}"
                                    class="mb-3" />
                                <p class="text-xs text-gray-500 mt-1">Recommended: 382.08x416.17 pixels</p>
                                <x-backend.inputs.text :labelInline="false" label="Link"
                                    name="themeOneCategoryLinks[]"
                                    value="{{ json_decode(getSetting('themeOneCategoryLinks'), true)[$key] }}"
                                    placeholder="https://www.example.com" :isRequired="false" />

                                <div class="mt-5">
                                    <input type="hidden" name="themeOneCategories[]" value="{{ $value }}">
                                    @php
                                        $selectedCat = \App\Models\Category::where('id', $value)->first();
                                    @endphp
                                    <x-backend.inputs.text :labelInline="false" label="Category" name="tempCat[]"
                                        value="{{ $selectedCat->collectTranslation('name', $langKey) }}"
                                        :isRequired="false" :isDisabled="true" />
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="slider-input mt-3">
                                        <x-backend.inputs.file :labelInline="false" :trashBtn="true" trashParent=".slider-input"
                                            label="Image" name="themeOneCategoryImages[]" value=""  class="mb-3" />
                                            <p class="text-xs text-gray-500 mt-1">Recommended: 382.08x416.17 pixels</p>
                                            <x-backend.inputs.text :labelInline="false" label="Link" name="themeOneCategoryLinks[]"
                                            value=""
                                            placeholder="https://www.example.com" :isRequired="false" />
                                            <div class="mt-5">
<x-backend.inputs.select :labelInline="false" label="Select Category" name="themeOneCategories[]">
@foreach ($categories as $cat)
@if (!$themeOneCategories->contains($cat->id))
<x-backend.inputs.select-option
name="{{ $cat->collectTranslation('name', $langKey) }}"
value="{{ $cat->id }}"   />
@endif
@foreach ($cat->childrenCategories as $childCategory)
@include(' backend.admin.categories.subCategory', [
    'subCategory' => $childCategory,
    'langKey' => $langKey,
    'category' => null,
    'themeOneCategories' => $themeOneCategories,
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

{{-- product sections --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Product Sections') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="pt-2">
                    <input type="hidden" name="types[]" value="hideProductSections">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Hide Product Sections (New Arrivals, Best Sellers, Highest Rated)') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="hideProductSections" value="1"
                            isChecked="{{ getSetting('hideProductSections') ?? false }}" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn mt-3" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- bottom 4 image banner --}}
<div class="ac !mt-0 ">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Bottom 4 Image Banner') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="types[]" value="showThemeOneBottomFourImageBanner">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show/Hide Bottom 4 Image Banner') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneBottomFourImageBanner" value="1"
                        isChecked="{{ getSetting('showThemeOneBottomFourImageBanner') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerTitle">
                <x-backend.inputs.text :labelInline="false" label="Section Title"
                    name="themeOneBottomFourImageBannerTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerTitle') }}"
                    placeholder="Featured Collections" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Section SubTitle"
                    name="themeOneBottomFourImageBannerSubTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerSubTitle') }}"
                    placeholder="Handpicked" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageOne">
                <x-backend.inputs.file :labelInline="false" label="Image One"
                    name="themeOneBottomFourImageBannerImageOne"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageOne') }}" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 294x294 pixels</p>

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageOneBG">
                <x-backend.inputs.color :labelInline="false" label="Image One Background Color"
                    name="themeOneBottomFourImageBannerImageOneBG"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageOneBG') }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageOneSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Image One SubTitle"
                    name="themeOneBottomFourImageBannerImageOneSubTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageOneSubTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageOneTitle">
                <x-backend.inputs.text :labelInline="false" label="Image One Title"
                    name="themeOneBottomFourImageBannerImageOneTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageOneTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageOneDescription">
                <x-backend.inputs.textarea :labelInline="false" label="Image One Description"
                    name="themeOneBottomFourImageBannerImageOneDescription"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageOneDescription') ?? '' }}"
                    rows="3" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageOneLink">
                <x-backend.inputs.text :labelInline="false" label="Image One Link"
                    name="themeOneBottomFourImageBannerImageOneLink"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageOneLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageTwo">
                <x-backend.inputs.file :labelInline="false" label="Image Two"
                    name="themeOneBottomFourImageBannerImageTwo"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageTwo') }}" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 294x294 pixels</p>

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageTwoBG">
                <x-backend.inputs.color :labelInline="false" label="Image Two Background Color"
                    name="themeOneBottomFourImageBannerImageTwoBG"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageTwoBG') }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageTwoSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Two SubTitle"
                    name="themeOneBottomFourImageBannerImageTwoSubTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageTwoSubTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageTwoTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Two Title"
                    name="themeOneBottomFourImageBannerImageTwoTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageTwoTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageTwoDescription">
                <x-backend.inputs.textarea :labelInline="false" label="Image Two Description"
                    name="themeOneBottomFourImageBannerImageTwoDescription"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageTwoDescription') ?? '' }}"
                    rows="3" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageTwoLink">
                <x-backend.inputs.text :labelInline="false" label="Image Two Link"
                    name="themeOneBottomFourImageBannerImageTwoLink"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageTwoLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageThree">
                <x-backend.inputs.file :labelInline="false" label="Image Three"
                    name="themeOneBottomFourImageBannerImageThree"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageThree') }}" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 588x593.39 pixels</p>

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageThreeBG">
                <x-backend.inputs.color :labelInline="false" label="Image Three Background Color"
                    name="themeOneBottomFourImageBannerImageThreeBG"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageThreeBG') }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageThreeSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Three SubTitle"
                    name="themeOneBottomFourImageBannerImageThreeSubTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageThreeSubTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageThreeTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Three Title"
                    name="themeOneBottomFourImageBannerImageThreeTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageThreeTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageThreeDescription">
                <x-backend.inputs.textarea :labelInline="false" label="Image Three Description"
                    name="themeOneBottomFourImageBannerImageThreeDescription"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageThreeDescription') ?? '' }}"
                    rows="3" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageThreeLink">
                <x-backend.inputs.text :labelInline="false" label="Image Three Link"
                    name="themeOneBottomFourImageBannerImageThreeLink"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageThreeLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageFour">
                <x-backend.inputs.file :labelInline="false" label="Image Four"
                    name="themeOneBottomFourImageBannerImageFour"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageFour') }}" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 588x299.39 pixels</p>

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageFourBG">
                <x-backend.inputs.color :labelInline="false" label="Image Four Background Color"
                    name="themeOneBottomFourImageBannerImageFourBG"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageFourBG') }}" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageFourSubTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Four SubTitle"
                    name="themeOneBottomFourImageBannerImageFourSubTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageFourSubTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageFourTitle">
                <x-backend.inputs.text :labelInline="false" label="Image Four Title"
                    name="themeOneBottomFourImageBannerImageFourTitle"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageFourTitle') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageFourDescription">
                <x-backend.inputs.textarea :labelInline="false" label="Image Four Description"
                    name="themeOneBottomFourImageBannerImageFourDescription"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageFourDescription') ?? '' }}"
                    rows="3" :isRequired="false" />

                <input type="hidden" name="types[]" value="themeOneBottomFourImageBannerImageFourLink">
                <x-backend.inputs.text :labelInline="false" label="Image Four Link"
                    name="themeOneBottomFourImageBannerImageFourLink"
                    value="{{ getSetting('themeOneBottomFourImageBannerImageFourLink') }}"
                    placeholder="https://www.example.com" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>
