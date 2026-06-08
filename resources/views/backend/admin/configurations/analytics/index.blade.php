@extends('layouts.admin')

@section('title')
    {{ translate('Pixel and Meta Analytics Setting') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Pixel and Meta Analytics') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Pixel and Meta Analytics') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-3">
        <div class="card">
            <h4 class="card__title">{{ translate('Google Analytics Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="GTM_ID">
                    <x-backend.inputs.text label="Google Tag Manager ID" name="GTM_ID" placeholder="Google Tag Manager ID"
                        value="{{ config('app.GTM_ID') }}" />

                    <input type="hidden" name="types[]" value="GA4_API_SECRET">
                    <x-backend.inputs.text label="Google Analytics API Secret" name="GA4_API_SECRET"
                        placeholder="Google Analytics API Secret" value="{{ config('app.GA4_API_SECRET') }}" />

                    <input type="hidden" name="types[]" value="GA4_MEASUREMENT_ID">
                    <x-backend.inputs.text label="Google Analytics Measurment ID" name="GA4_MEASUREMENT_ID"
                        placeholder="Google Analytics Measurment ID" value="{{ config('app.GA4_MEASUREMENT_ID') }}" />

                    <input type="hidden" name="types[]" value="GOOGLE_ANALYTICS_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Google Analytics Activation" name="GOOGLE_ANALYTICS_ACTIVATION"
                        placeholder="Google Analytics Activation" value="1"
                        isChecked="{{ config('app.GOOGLE_ANALYTICS_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Meta Pixel Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="META_PIXEL_ID">
                    <x-backend.inputs.text label="{{ translate('Meta Pixel ID') }}" name="META_PIXEL_ID"
                        placeholder="{{ translate('Meta Pixel ID') }}"
                        value="{{ config('app.META_PIXEL_ID') }}" />

                    <input type="hidden" name="types[]" value="META_CONVERSIONS_API_ACCESS_TOKEN">
                    <div class="theme-input-group w-full">
                        <label for="META_CONVERSIONS_API_ACCESS_TOKEN" class="theme-input-label input-required">
                            {{ translate('Meta Conversions API') }}<br>{{ translate('Access Token') }}
                        </label>

                        <div class="theme-input-wrapper flex gap-3">
                            <input type="text" id="META_CONVERSIONS_API_ACCESS_TOKEN" name="META_CONVERSIONS_API_ACCESS_TOKEN"
                                class="theme-input" placeholder="{{ translate('Meta Conversions API Access Token') }}"
                                value="{{ config('app.META_CONVERSIONS_API_ACCESS_TOKEN') }}" required />
                        </div>
                    </div>

                    <input type="hidden" name="types[]" value="FACEBOOK_AD_ACCOUNT_ID">
                    <div class="theme-input-group w-full">
                        <label for="FACEBOOK_AD_ACCOUNT_ID" class="theme-input-label input-required">
                            {{ translate('Facebook Ad') }}<br>{{ translate('Account ID') }}
                        </label>

                        <div class="theme-input-wrapper flex gap-3">
                            <input type="text" id="FACEBOOK_AD_ACCOUNT_ID" name="FACEBOOK_AD_ACCOUNT_ID"
                                class="theme-input" placeholder="{{ translate('Facebook Ad Account ID') }}"
                                value="{{ config('app.FACEBOOK_AD_ACCOUNT_ID') }}" required />
                        </div>
                    </div>

                    <input type="hidden" name="types[]" value="FB_TEST_EVENT_CODE">
                    <x-backend.inputs.text label="{{ translate('Facebook Test Event Code') }}" name="FB_TEST_EVENT_CODE"
                        placeholder="{{ translate('TEST12345 (optional)') }}"
                        value="{{ config('app.FB_TEST_EVENT_CODE') }}" />

                    <input type="hidden" name="types[]" value="FACEBOOK_PIXEL_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Meta Pixel Activation" name="FACEBOOK_PIXEL_ACTIVATION"
                        placeholder="{{ translate('Meta Pixel Activation') }}" value="1"
                        isChecked="{{ config('app.FACEBOOK_PIXEL_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
