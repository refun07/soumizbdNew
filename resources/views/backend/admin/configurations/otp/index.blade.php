@extends('layouts.admin')

@section('title')
    {{ translate('OTP/SMS API Setting') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('OTP/SMS API') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('OTP/SMS API') }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols gap-3">
        <div class="card">
            <h4 class="card__title">{{ translate('Bulk SMS BD Credential') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="types[]" value="SMS_API_URL">
                    <x-backend.inputs.text label="Bulk SMS BD API URL" name="SMS_API_URL" placeholder="Bulk SMS BD API URL"
                        value="{{ config('app.SMS_API_URL') }}" />

                    <input type="hidden" name="types[]" value="SMS_API_KEY">
                    <x-backend.inputs.text label="Bulk SMS BD API Key" name="SMS_API_KEY"
                        placeholder="Bulk SMS BD API Key" value="{{ config('app.SMS_API_KEY') }}" />

                    <input type="hidden" name="types[]" value="SMS_SENDER_ID">
                    <x-backend.inputs.text label="Bulk SMS BD Sender ID" name="SMS_SENDER_ID"
                        placeholder="Bulk SMS BD Sender ID" value="{{ config('app.SMS_SENDER_ID') }}" />

                    <input type="hidden" name="types[]" value="OTP_ACTIVATION">
                    <x-backend.inputs.checkbox toggler="true" label="Google Activation" name="OTP_ACTIVATION"
                        placeholder="OTP Activation" value="1"
                        isChecked="{{ config('app.OTP_ACTIVATION') == '1' }}" />

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h4 class="card__title">{{ translate('Whats APP Credential') }} <a href="https://docs.rapiwa.com/api/device-key.html" title="{{ translate("Collect API key") }}" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-circle-info"></i></a> </h4>

            <div class="card__content">
                <form action="{{ route('admin.env-key.update') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="types[]" value="WHATSAPP_BASE_URL">
                    <x-backend.inputs.text label="WHATSAPP BASE URL" name="WHATSAPP_BASE_URL" placeholder="WHATSAPP BASE URL"
                        value="{{ config('app.WHATSAPP_BASE_URL') }}" /> 

                    <x-backend.inputs.text label="WHATSAPP DEVICE KEY" name="WHATSAPP_API_KEY" placeholder="WHATSAPP DEVICE KEY"
                        value="{{ config('app.WHATSAPP_API_KEY') }}" /> 

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
