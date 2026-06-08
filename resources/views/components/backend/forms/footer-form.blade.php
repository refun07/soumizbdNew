@php
    $tags = \App\Models\Tag::get();
@endphp

{{-- footer --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Footer') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="homeFooterLogo">
                <x-backend.inputs.file :labelInline="false" label="Logo" name="homeFooterLogo"
                    value="{{ getSetting('homeFooterLogo') }}" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 200x48 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterDescription">
                <x-backend.inputs.textarea :labelInline="false" label="Footer Description" name="homeFooterDescription"
                    value="{{ getSetting('homeFooterDescription') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterCustomerSupport">
                <x-backend.inputs.text :labelInline="false" label="Customer Support" name="homeFooterCustomerSupport"
                    value="{{ getSetting('homeFooterCustomerSupport') ?? '' }}" placeholder="(629) 555-0129"
                    :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterAddress">
                <x-backend.inputs.text :labelInline="false" label="Customer Address" name="homeFooterAddress"
                    value="{{ getSetting('homeFooterAddress') ?? '' }}"
                    placeholder="4517 Washington Ave. Manchester, Kentucky 39495" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterEmail">
                <x-backend.inputs.text :labelInline="false" label="Customer Email" name="homeFooterEmail"
                    value="{{ getSetting('homeFooterEmail') ?? '' }}" placeholder="info@epikcart.com"
                    :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeFacebookLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Facebook') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeFacebookLink" value="1"
                        isChecked="{{ getSetting('showHomeFacebookLink') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterFacebookLink">
                <x-backend.inputs.text :labelInline="false" label="Facebook Link" name="homeFooterFacebookLink"
                    value="{{ getSetting('homeFooterFacebookLink') ?? '' }}"
                    placeholder="https://www.facebook.com/epikcoders" :isRequired="false" />


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeTwitterLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Twitter') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeTwitterLink" value="1"
                        isChecked="{{ getSetting('showHomeTwitterLink') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterTwitterLink">
                <x-backend.inputs.text :labelInline="false" label="Twitter Link" name="homeFooterTwitterLink"
                    value="{{ getSetting('homeFooterTwitterLink') ?? '' }}"
                    placeholder="https://www.twitter.com/epikcoders" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeInstagramLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Instagram') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeInstagramLink" value="1"
                        isChecked="{{ getSetting('showHomeInstagramLink') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterInstagramLink">
                <x-backend.inputs.text :labelInline="false" label="Instagram Link" name="homeFooterInstagramLink"
                    value="{{ getSetting('homeFooterInstagramLink') ?? '' }}"
                    placeholder="https://www.instagram.com/epikcoders" :isRequired="false" />



                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeYoutubeLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Youtube') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeYoutubeLink" value="1"
                        isChecked="{{ getSetting('showHomeYoutubeLink') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterYoutubeLink">
                <x-backend.inputs.text :labelInline="false" label="Youtube Link" name="homeFooterYoutubeLink"
                    value="{{ getSetting('homeFooterYoutubeLink') ?? '' }}"
                    placeholder="https://www.youtube.com/epikcoders" :isRequired="false" />


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeLinkedInLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show LinkedIn') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeLinkedInLink" value="1"
                        isChecked="{{ getSetting('showHomeLinkedInLink') ?? false }}" />
                </div>


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterLinkedInLink">
                <x-backend.inputs.text :labelInline="false" label="LinkedIn Link" name="homeFooterLinkedInLink"
                    value="{{ getSetting('homeFooterLinkedInLink') ?? '' }}"
                    placeholder="https://www.linkedin.com/epikcoders" :isRequired="false" />


                <div class="mt-5"></div>
                <h5>{{ translate('Quick Links') }}</h5>
                <input type="hidden" name="types[]" value="homeFooterQuickLinkTitles">
                <input type="hidden" name="types[]" value="homeFooterQuickLinks">
                <div class="quick-link-input-wrapper">
                    @if (getSetting('homeFooterQuickLinks') != null)
                        @foreach (json_decode(getSetting('homeFooterQuickLinks'), true) as $key => $value)
                            <div class="quick-link-input relative" data-index="{{ $key }}">
                                <x-backend.inputs.text :labelInline="false" label="Title"
                                    name="homeFooterQuickLinkTitles[]"
                                    value="{{ json_decode(getSetting('homeFooterQuickLinkTitles'), true)[$key] }}"
                                    placeholder="Type title" :isRequired="false" />
                                <x-backend.inputs.text :labelInline="false" class="mt-3" label="Link"
                                    name="homeFooterQuickLinks[]" value="{{ $value }}"
                                    placeholder="https://www.example.com" :isRequired="false" />
                                <button type="button" class="delete-quick-link absolute top-0 right-0 text-red-500 hover:text-red-700 p-2"
                                        data-index="{{ $key }}" title="{{ translate('Delete Quick Link') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <a href="javascript:void(0);" class="text-secondary-light mt-3" data-toggle="add-more"
                    data-content='<div class="quick-link-input mt-3 relative">
                <x-backend.inputs.text :labelInline="false" label="Title" name="homeFooterQuickLinkTitles[]"  placeholder="Type title" :isRequired="false" />
                <div class="mt-3"></div>
                <x-backend.inputs.text :labelInline="false" label="Link" name="homeFooterQuickLinks[]"  placeholder="https://www.example.com" :isRequired="false" />
                <button type="button" class="delete-quick-link absolute top-0 right-0 text-red-500 hover:text-red-700 p-2"
                        title="{{ translate('Delete Quick Link') }}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>'
                    data-target=".quick-link-input-wrapper">
                    {{ translate('Add Quick Link') }}
                    <span class="ml-2">
                        <i class="fa-solid fa-plus"></i>
                    </span>
                </a>


                @php
                    $popularTags =
                        getSetting('homeFooterPopularTags') != null
                            ? collect(json_decode(getSetting('homeFooterPopularTags')))
                            : collect();
                @endphp
                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterPopularTags">
                <x-backend.inputs.select :labelInline="false" label="Popular Tags" name="homeFooterPopularTags[]"
                    multiple data-selection-css-class="multi-select2"
                    data-placeholder="{{ translate('Select tags') }}" :isRequired="false">
                    @foreach ($tags as $tag)
                        <x-backend.inputs.select-option name="{{ $tag->name }}" value="{{ $tag->id }}"
                            selected="{{ $popularTags->contains($tag->id) ? $tag->id : 0 }}" />
                    @endforeach
                </x-backend.inputs.select>


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeFooterPlayStoreLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Playstore') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeFooterPlayStoreLink" value="1"
                        isChecked="{{ getSetting('showHomeFooterPlayStoreLink') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterPlayStoreLink">
                <x-backend.inputs.text :labelInline="false" label="Playstore Link" name="homeFooterPlayStoreLink"
                    value="{{ getSetting('homeFooterPlayStoreLink') ?? '' }}" :isRequired="false" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="showHomeFooterAppStoreLink">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Appstore') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomeFooterAppStoreLink" value="1"
                        isChecked="{{ getSetting('showHomeFooterAppStoreLink') ?? false }}" />
                </div>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterAppStoreLink">
                <x-backend.inputs.text :labelInline="false" label="Appstore Link" name="homeFooterAppStoreLink"
                    value="{{ getSetting('homeFooterAppStoreLink') ?? '' }}" :isRequired="false" />


                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homeFooterCopyrightText">
                <x-backend.inputs.text :labelInline="false" label="Copyright Text" name="homeFooterCopyrightText"
                    value="{{ getSetting('homeFooterCopyrightText') ?? '' }}"
                    placeholder="Copyrights | All rights reserved" :isRequired="false" />

                <input type="hidden" name="types[]" value="homeFooterSecuredPayments">
                <x-backend.inputs.file :labelInline="false" label="Secured Payments" name="homeFooterSecuredPayments"
                    value="" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 400x40 pixels</p>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn mt-3" />
                </div>
        </div>
        </form>
    </div>
</div>
