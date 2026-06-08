@php
    $customerMenuEnabled = getSetting('headerCustomerMenuEnabled', '1') !== '0';
    $socialLinks = json_decode(getSetting('headerSocialLinks', '[]'), true);
    $socialLinks = is_array($socialLinks) ? array_values($socialLinks) : [];
    $platforms = [
        'facebook' => ['name' => 'Facebook', 'icon' => 'fa-brands fa-facebook-f'],
        'instagram' => ['name' => 'Instagram', 'icon' => 'fa-brands fa-instagram'],
        'tiktok' => ['name' => 'TikTok', 'icon' => 'fa-brands fa-tiktok'],
        'youtube' => ['name' => 'YouTube', 'icon' => 'fa-brands fa-youtube'],
        'x' => ['name' => 'X / Twitter', 'icon' => 'fa-brands fa-x-twitter'],
        'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fa-brands fa-linkedin-in'],
        'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp'],
        'pinterest' => ['name' => 'Pinterest', 'icon' => 'fa-brands fa-pinterest-p'],
        'telegram' => ['name' => 'Telegram', 'icon' => 'fa-brands fa-telegram'],
        'snapchat' => ['name' => 'Snapchat', 'icon' => 'fa-brands fa-snapchat'],
    ];

    $platformOptions = function ($selected = '') use ($platforms) {
        return collect($platforms)
            ->map(function ($platform, $value) use ($selected) {
                $isSelected = $selected === $value ? ' selected' : '';
                return '<option value="' . $value . '" data-icon-class="' . $platform['icon'] . '"' . $isSelected . '>' . $platform['name'] . '</option>';
            })
            ->implode('');
    };
@endphp

{{-- header customer menu settings --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Header Customer Menu') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST">
                @csrf

                <input type="hidden" name="types[]" value="headerCustomerMenuEnabled">
                <input type="hidden" name="headerCustomerMenuEnabled" value="0">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Customer Login Menu') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="headerCustomerMenuEnabled" value="1"
                        isChecked="{{ $customerMenuEnabled }}" />
                </div>

                <input type="hidden" name="types[]" value="headerSocialLinks">
                <div class="mb-4">
                    <p class="font-medium mb-1">{{ translate('Social Icons When Customer Menu Is Hidden') }}</p>
                    <p class="text-xs text-muted">
                        {{ translate('Enable one or more social links to show icons in place of the customer login menu.') }}
                    </p>
                </div>

                <div class="header-social-links-wrapper">
                    @foreach ($socialLinks as $key => $socialLink)
                        @php
                            $platform = $socialLink['platform'] ?? 'facebook';
                            $url = $socialLink['url'] ?? '';
                            $enabled = ($socialLink['enabled'] ?? '1') !== '0';
                        @endphp
                        <div class="main-banner-item header-social-link-item border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium">{{ translate('Social Link') }} {{ $key + 1 }}</h4>
                                <button type="button" class="text-red-500 hover:text-red-700 text-sm"
                                    data-toggle="remove-parent" data-parent=".header-social-link-item">
                                    <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                                </button>
                            </div>

                            <input type="hidden" name="headerSocialLinks[{{ $key }}][enabled]" value="0">
                            <div class="flex items-center justify-between mb-4">
                                <p>{{ translate('Enable This Icon') }}</p>
                                <x-backend.inputs.checkbox toggler="true"
                                    name="headerSocialLinks[{{ $key }}][enabled]" value="1"
                                    isChecked="{{ $enabled }}" />
                            </div>

                            <div class="mb-4">
                                <label class="theme-input-label pt-0">{{ translate('Social Media') }}</label>
                                <div class="theme-input-wrapper">
                                    <select class="initSelect2 social-icon-select theme-input" data-search="false"
                                        data-no-add-new="true" name="headerSocialLinks[{{ $key }}][platform]">
                                        {!! $platformOptions($platform) !!}
                                    </select>
                                </div>
                            </div>

                            <x-backend.inputs.text :labelInline="false" label="Social Link"
                                name="headerSocialLinks[{{ $key }}][url]" value="{{ $url }}"
                                placeholder="https://www.example.com" :isRequired="false" />
                        </div>
                    @endforeach
                </div>

                <a href="javascript:void(0);" class="text-secondary-light" data-toggle="add-more"
                    data-content='<div class="main-banner-item header-social-link-item border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium">{{ translate('New Social Link') }}</h4>
                            <button type="button" class="text-red-500 hover:text-red-700 text-sm" data-toggle="remove-parent" data-parent=".header-social-link-item">
                                <i class="fa-solid fa-trash"></i> {{ translate('Remove') }}
                            </button>
                        </div>
                        <input type="hidden" name="headerSocialLinks[__INDEX__][enabled]" value="0">
                        <div class="flex items-center justify-between mb-4">
                            <p>{{ translate('Enable This Icon') }}</p>
                            <x-backend.inputs.checkbox toggler="true" name="headerSocialLinks[__INDEX__][enabled]" value="1" :isChecked="true" />
                        </div>
                        <div class="mb-4">
                            <label class="theme-input-label pt-0">{{ translate('Social Media') }}</label>
                            <div class="theme-input-wrapper">
                                <select class="initSelect2 social-icon-select theme-input" data-search="false" data-no-add-new="true" name="headerSocialLinks[__INDEX__][platform]">
                                    {!! $platformOptions('facebook') !!}
                                </select>
                            </div>
                        </div>
                        <x-backend.inputs.text :labelInline="false" label="Social Link" name="headerSocialLinks[__INDEX__][url]" value="" placeholder="https://www.example.com" :isRequired="false" />
                    </div>'
                    data-target=".header-social-links-wrapper">
                    {{ translate('Add Social Link') }}
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
