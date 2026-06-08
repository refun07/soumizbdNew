@extends('layouts.admin')

@section('title')
    {{ translate('Social Media Login Setting') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Social Media Login') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Social Media Settings') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-3">
        <div class="card">
            <h4 class="card__title">{{ translate('Google Login Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="GOOGLE_CLIENT_ID">
                    <x-backend.inputs.text label="Google Client ID" name="GOOGLE_CLIENT_ID" placeholder="Google Client ID"
                        value="{{ config('app.GOOGLE_CLIENT_ID') }}" />

                    <input type="hidden" name="types[]" value="GOOGLE_CLIENT_SECRET">
                    <x-backend.inputs.text label="Google Client Secret" name="GOOGLE_CLIENT_SECRET"
                        placeholder="Google Client Secret" value="{{ config('app.GOOGLE_CLIENT_SECRET') }}" />

                    <input type="hidden" name="types[]" value="GOOGLE_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Google Activation" name="GOOGLE_ACTIVATION"
                        placeholder="Google Activation" value="1"
                        isChecked="{{ config('app.GOOGLE_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Facebook Login Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_ID">
                    <x-backend.inputs.text label="Facebook Client ID" name="FACEBOOK_CLIENT_ID"
                        placeholder="{{ translate('Facebook Client ID') }}"
                        value="{{ config('app.FACEBOOK_CLIENT_ID') }}" />

                    <input type="hidden" name="types[]" value="FACEBOOK_CLIENT_SECRET">
                    <x-backend.inputs.text label="Facebook Client Secret" name="FACEBOOK_CLIENT_SECRET"
                        placeholder="{{ translate('Facebook Client Secret') }}"
                        value="{{ config('app.FACEBOOK_CLIENT_SECRET') }}" />


                    <input type="hidden" name="types[]" value="FACEBOOK_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Facebook Activation" name="FACEBOOK_ACTIVATION"
                        placeholder="{{ translate('Facebook Activation') }}" value="1"
                        isChecked="{{ config('app.FACEBOOK_ACTIVATION') == 1 }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('WhatsApp Business API Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="WHATSAPP_APP_ID">
                    <x-backend.inputs.text label="WhatsApp App ID" name="WHATSAPP_APP_ID" placeholder="WhatsApp App ID"
                        value="{{ config('services.whatsapp.app_id') }}" />

                    <input type="hidden" name="types[]" value="WHATSAPP_APP_SECRET">
                    <x-backend.inputs.text label="WhatsApp App Secret" name="WHATSAPP_APP_SECRET"
                        placeholder="WhatsApp App Secret" value="{{ config('services.whatsapp.app_secret') }}" />

                    <input type="hidden" name="types[]" value="WHATSAPP_PHONE_NUMBER_ID">
                    <x-backend.inputs.text label="WhatsApp Phone Number ID" name="WHATSAPP_PHONE_NUMBER_ID"
                        placeholder="WhatsApp Phone Number ID" value="{{ config('services.whatsapp.phone_number_id') }}" />

                    <input type="hidden" name="types[]" value="WHATSAPP_ACCESS_TOKEN">
                    <x-backend.inputs.text label="WhatsApp Access Token" name="WHATSAPP_ACCESS_TOKEN"
                        placeholder="WhatsApp Access Token" value="{{ config('services.whatsapp.access_token') }}" />

                    <input type="hidden" name="types[]" value="WHATSAPP_WEBHOOK_VERIFY_TOKEN">
                    <x-backend.inputs.text label="WhatsApp Webhook Verify Token" name="WHATSAPP_WEBHOOK_VERIFY_TOKEN"
                        placeholder="WhatsApp Webhook Verify Token" value="{{ config('services.whatsapp.webhook_verify_token') }}" />

                    <input type="hidden" name="types[]" value="WHATSAPP_CHAT_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="WhatsApp Chat Activation" name="WHATSAPP_CHAT_ACTIVATION"
                        placeholder="WhatsApp Chat Activation" value="1"
                        isChecked="{{ config('services.whatsapp.chat_activation') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Facebook Messenger Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf

                    <input type="hidden" name="types[]" value="MESSENGER_APP_ID">
                    <x-backend.inputs.text label="Messenger App ID" name="MESSENGER_APP_ID"
                        placeholder="Messenger App ID" value="{{ config('services.messenger.app_id') }}" />

                    <input type="hidden" name="types[]" value="MESSENGER_APP_SECRET">
                    <x-backend.inputs.text label="Messenger App Secret" name="MESSENGER_APP_SECRET"
                        placeholder="Messenger App Secret" value="{{ config('services.messenger.app_secret') }}" />

                    <input type="hidden" name="types[]" value="MESSENGER_PAGE_ACCESS_TOKEN">
                    <x-backend.inputs.text label="Messenger Page Access Token" name="MESSENGER_PAGE_ACCESS_TOKEN"
                        placeholder="Messenger Page Access Token" value="{{ config('services.messenger.page_access_token') }}" />

                    <input type="hidden" name="types[]" value="MESSENGER_VERIFY_TOKEN">
                    <x-backend.inputs.text label="Messenger Verify Token" name="MESSENGER_VERIFY_TOKEN"
                        placeholder="Messenger Verify Token" value="{{ config('services.messenger.verify_token') }}" />

                    <input type="hidden" name="types[]" value="MESSENGER_CHAT_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Messenger Chat Activation" name="MESSENGER_CHAT_ACTIVATION"
                        placeholder="Messenger Chat Activation" value="1"
                        isChecked="{{ config('services.messenger.chat_activation') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
