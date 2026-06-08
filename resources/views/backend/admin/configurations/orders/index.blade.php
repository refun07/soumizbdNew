@extends('layouts.admin')

@section('title')
    {{ translate('Order Settings') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    {{-- breadcrumb starts --}}
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-shirt"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">{{ translate('Order Settings') }}</span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="#" class="font-bold ">{{ translate('Configurations') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Order Settings') }}</p>
        </div>
    </div>
    {{-- breadcrumb ends --}}

    {{-- information-form starts --}}
    <div class="card max-w-[900px]">
        <h4 class="card__title">{{ translate('Settings Informations') }}</h4>

        <div class="card__content">
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="orderCodePrefix">
                <x-backend.inputs.text label="Order Code Prefix" name="orderCodePrefix" placeholder="Type order code prefix"
                    value="{{ getSetting('orderCodePrefix') }}" />

                <input type="hidden" name="types[]" value="orderCodeStartsFrom">
                <x-backend.inputs.text label="Order Code Starts From" name="orderCodeStartsFrom" placeholder="10001"
                    value="{{ getSetting('orderCodeStartsFrom') }}" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>

        </div>
    </div>
    {{-- information-form ends --}}

    {{-- POS Memo Settings starts --}}
    <div class="card max-w-[900px] mt-6">
        <h4 class="card__title">{{ translate('POS Memo Settings') }}</h4>

        <div class="card__content">
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium">{{ translate('Memo Date') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="types[]" value="posMemoDateEnabled">
                        <input type="hidden" name="posMemoDateEnabled" value="0">
                        <x-backend.inputs.checkbox toggler="true" name="posMemoDateEnabled"
                            value="1" isChecked="{{ getSetting('posMemoDateEnabled', '1') !== '0' }}" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium">{{ translate('Memo No') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="types[]" value="posMemoNoEnabled">
                        <input type="hidden" name="posMemoNoEnabled" value="0">
                        <x-backend.inputs.checkbox toggler="true" name="posMemoNoEnabled"
                            value="1" isChecked="{{ getSetting('posMemoNoEnabled', '1') !== '0' }}" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium">{{ translate('Mushak Challan No') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="types[]" value="posMushakChallanNoEnabled">
                        <input type="hidden" name="posMushakChallanNoEnabled" value="0">
                        <x-backend.inputs.checkbox toggler="true" name="posMushakChallanNoEnabled"
                            value="1" isChecked="{{ getSetting('posMushakChallanNoEnabled', '1') !== '0' }}" />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>
        </div>
    </div>
    {{-- POS Memo Settings ends --}}
@endsection
