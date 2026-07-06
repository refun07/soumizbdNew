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

    {{-- Invoice Settings starts --}}
    <div class="card max-w-[900px] mt-6">
        <h4 class="card__title">{{ translate('Invoice Settings') }}</h4>

        <div class="card__content">
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="invoiceQrCodeEnabled">
                <div class="flex items-center justify-between py-2">
                    <p>{{ translate('Show QR Code On Invoice PDF') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="invoiceQrCodeEnabled" value="1"
                        isChecked="{{ getSetting('invoiceQrCodeEnabled') != '0' }}" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>
        </div>
    </div>
    {{-- Invoice Settings ends --}}

    {{-- Order Success Page Settings starts --}}
    <div class="card max-w-[900px] mt-6">
        <h4 class="card__title">{{ translate('Order Success Page') }}</h4>

        <div class="card__content">
            <form action="{{ route('admin.general-settings.update') }}" class="space-y-4" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium">{{ translate('Enable Custom Success Page') }}</label>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="types[]" value="orderSuccessCustomEnabled">
                        <input type="hidden" name="orderSuccessCustomEnabled" value="0">
                        <x-backend.inputs.checkbox toggler="true" name="orderSuccessCustomEnabled" value="1"
                            isChecked="{{ getSetting('orderSuccessCustomEnabled', '1') !== '0' }}" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <input type="hidden" name="types[]" value="orderSuccessHeadline">
                        <x-backend.inputs.text label="Headline" name="orderSuccessHeadline"
                            placeholder="আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে।"
                            value="{{ getSetting('orderSuccessHeadline', 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে।') }}" />
                    </div>

                    <div class="space-y-3">
                        <input type="hidden" name="types[]" value="orderSuccessHeadlineColor">
                        <x-backend.inputs.color label="Headline Color" name="orderSuccessHeadlineColor"
                            value="{{ getSetting('orderSuccessHeadlineColor', '#1f9bad') }}" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <input type="hidden" name="types[]" value="orderSuccessSubtitle">
                        <x-backend.inputs.textarea label="Subtitle" name="orderSuccessSubtitle" rows="3"
                            placeholder="আমাদের কল সেন্টার থেকে এক্ষন করে আপনার অর্ডারটি কনফার্ম করা হবে"
                            value="{{ getSetting('orderSuccessSubtitle', 'আমাদের কল সেন্টার থেকে এক্ষন করে আপনার অর্ডারটি কনফার্ম করা হবে') }}" />
                    </div>

                    <div class="space-y-3">
                        <input type="hidden" name="types[]" value="orderSuccessSubtitleColor">
                        <x-backend.inputs.color label="Subtitle Color" name="orderSuccessSubtitleColor"
                            value="{{ getSetting('orderSuccessSubtitleColor', '#ef4444') }}" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <input type="hidden" name="types[]" value="orderSuccessCenterImage">
                        <x-backend.inputs.file label="Center Icon/Image" name="orderSuccessCenterImage"
                            value="{{ getSetting('orderSuccessCenterImage') }}" />
                    </div>

                    <div>
                        <input type="hidden" name="types[]" value="orderSuccessStepsImage">
                        <x-backend.inputs.file label="Steps Image" name="orderSuccessStepsImage"
                            value="{{ getSetting('orderSuccessStepsImage') }}" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <input type="hidden" name="types[]" value="orderSuccessOrderNumberTemplate">
                        <x-backend.inputs.text label="Order Number Template" name="orderSuccessOrderNumberTemplate"
                            placeholder="অর্ডার নম্বর : {order_code}"
                            value="{{ getSetting('orderSuccessOrderNumberTemplate', 'অর্ডার নম্বর : {order_code}') }}" />
                    </div>

                    <div>
                        <input type="hidden" name="types[]" value="orderSuccessOrderTotalTemplate">
                        <x-backend.inputs.text label="Order Total Template" name="orderSuccessOrderTotalTemplate"
                            placeholder="অর্ডারের মোট মূল্য {order_total}"
                            value="{{ getSetting('orderSuccessOrderTotalTemplate', 'অর্ডারের মোট মূল্য {order_total}') }}" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <input type="hidden" name="types[]" value="orderSuccessOrderTotalColor">
                        <x-backend.inputs.color label="Order Total Color" name="orderSuccessOrderTotalColor"
                            value="{{ getSetting('orderSuccessOrderTotalColor', '#ef4444') }}" />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between rounded border border-slate-200 px-4 py-3">
                        <label class="text-sm font-medium">{{ translate('Show Customer Details') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="types[]" value="orderSuccessShowCustomerDetails">
                            <input type="hidden" name="orderSuccessShowCustomerDetails" value="0">
                            <x-backend.inputs.checkbox toggler="true" name="orderSuccessShowCustomerDetails" value="1"
                                isChecked="{{ getSetting('orderSuccessShowCustomerDetails', '1') !== '0' }}" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between rounded border border-slate-200 px-4 py-3">
                        <label class="text-sm font-medium">{{ translate('Show Order Information') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="types[]" value="orderSuccessShowOrderInformation">
                            <input type="hidden" name="orderSuccessShowOrderInformation" value="0">
                            <x-backend.inputs.checkbox toggler="true" name="orderSuccessShowOrderInformation" value="1"
                                isChecked="{{ getSetting('orderSuccessShowOrderInformation', '1') !== '0' }}" />
                        </div>
                    </div>
                </div>

                <div class="rounded border border-slate-200 bg-slate-50 p-4 text-center">
                    <h2 class="text-2xl font-bold leading-tight"
                        style="color: {{ getSetting('orderSuccessHeadlineColor', '#1f9bad') }}">
                        {{ getSetting('orderSuccessHeadline', 'আপনার অর্ডারটি সফলভাবে সম্পন্ন হয়েছে।') }}
                    </h2>
                    <p class="mt-1 text-sm font-semibold"
                        style="color: {{ getSetting('orderSuccessSubtitleColor', '#ef4444') }}">
                        {{ getSetting('orderSuccessSubtitle', 'আমাদের কল সেন্টার থেকে এক্ষন করে আপনার অর্ডারটি কনফার্ম করা হবে') }}
                    </p>
                    <div class="mt-6 text-3xl">🎉</div>
                    <p class="mt-4 text-sm font-semibold">{{ str_replace('{order_code}', 'RM3101', getSetting('orderSuccessOrderNumberTemplate', 'অর্ডার নম্বর : {order_code}')) }}</p>
                    <p class="mt-2 text-sm font-semibold"
                        style="color: {{ getSetting('orderSuccessOrderTotalColor', '#ef4444') }}">
                        {{ str_replace('{order_total}', '1320 BDT', getSetting('orderSuccessOrderTotalTemplate', 'অর্ডারের মোট মূল্য {order_total}')) }}
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                </div>
            </form>
        </div>
    </div>
    {{-- Order Success Page Settings ends --}}

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
