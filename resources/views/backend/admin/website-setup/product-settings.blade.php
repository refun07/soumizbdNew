@extends('layouts.admin')

@section('title')
    {{ translate('Product Settings') }} | {{ getSetting('systemName') }}
@endsection

@can('homepage')
    @section('content')
        <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
            <div class="flex items-center">
                <span class="text-xl mr-3 text-secondary">
                    <i class="fa-solid fa-box"></i>
                </span>
                <span class="text-sm sm:text-base font-bold">{{ translate('Product Settings') }}</span>
            </div>

            <div class="max-sm:hidden flex items-center gap-2">
                <a href="{{ route('admin.theme.selection') }}" class="font-bold ">{{ translate('Website Setup') }}</a>
                <span class="text-primary dark:text-muted">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
                <p class="text-muted">{{ translate('Product Settings') }}</p>
            </div>
        </div>

        <div class="card max-w-[900px] mt-5">
            <h4 class="card__title">{{ translate('Product Operation Fields') }}</h4>

            <div class="card__content">
                <form action="{{ route('admin.general-settings.update') }}" class="space-y-5" method="POST">
                    @csrf

                    <input type="hidden" name="types[]" value="productSettingMinPurchaseQtyEnabled">
                    <input type="hidden" name="productSettingMinPurchaseQtyEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Min Purchase Quantity') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product save uses 1 automatically.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingMinPurchaseQtyEnabled"
                            value="1" isChecked="{{ getSetting('productSettingMinPurchaseQtyEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingMaxPurchaseQtyEnabled">
                    <input type="hidden" name="productSettingMaxPurchaseQtyEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Max Purchase Quantity') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product save uses 100 automatically.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingMaxPurchaseQtyEnabled"
                            value="1" isChecked="{{ getSetting('productSettingMaxPurchaseQtyEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingSizeChartEnabled">
                    <input type="hidden" name="productSettingSizeChartEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Enable Size Chart') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, size chart controls and storefront size charts are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingSizeChartEnabled"
                            value="1" isChecked="{{ getSetting('productSettingSizeChartEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingVideoLinkEnabled">
                    <input type="hidden" name="productSettingVideoLinkEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Video Link') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product video link controls and storefront videos are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingVideoLinkEnabled"
                            value="1" isChecked="{{ getSetting('productSettingVideoLinkEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingQrCodeEnabled">
                    <input type="hidden" name="productSettingQrCodeEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Product QR Code') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, the product QR code section and public QR pages are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingQrCodeEnabled"
                            value="1" isChecked="{{ getSetting('productSettingQrCodeEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingTagsEnabled">
                    <input type="hidden" name="productSettingTagsEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Tags') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product tag controls and storefront product tags are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingTagsEnabled"
                            value="1" isChecked="{{ getSetting('productSettingTagsEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingBadgesEnabled">
                    <input type="hidden" name="productSettingBadgesEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Badges') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product badge controls and storefront product badges are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingBadgesEnabled"
                            value="1" isChecked="{{ getSetting('productSettingBadgesEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingEstimatedDeliveryTimeEnabled">
                    <input type="hidden" name="productSettingEstimatedDeliveryTimeEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Estimated Delivery Time') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, estimated delivery controls and storefront delivery time are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingEstimatedDeliveryTimeEnabled"
                            value="1" isChecked="{{ getSetting('productSettingEstimatedDeliveryTimeEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingEmiEnabled">
                    <input type="hidden" name="productSettingEmiEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show EMI Available & EMI Info') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, EMI controls and storefront EMI information are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingEmiEnabled"
                            value="1" isChecked="{{ getSetting('productSettingEmiEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingWarrantyEnabled">
                    <input type="hidden" name="productSettingWarrantyEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Warranty Available & Warranty Info') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, warranty controls and storefront warranty information are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingWarrantyEnabled"
                            value="1" isChecked="{{ getSetting('productSettingWarrantyEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingAlertQtyEnabled">
                    <input type="hidden" name="productSettingAlertQtyEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Alert Quantity') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product save uses 0 automatically.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingAlertQtyEnabled"
                            value="1" isChecked="{{ getSetting('productSettingAlertQtyEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingTaxesEnabled">
                    <input type="hidden" name="productSettingTaxesEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Product Taxes') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, product tax controls are hidden and product taxes are treated as 0%.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingTaxesEnabled"
                            value="1" isChecked="{{ getSetting('productSettingTaxesEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingPreOrderEnabled">
                    <input type="hidden" name="productSettingPreOrderEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Pre-Order Settings') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, pre-order controls and storefront pre-order behavior are hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingPreOrderEnabled"
                            value="1" isChecked="{{ getSetting('productSettingPreOrderEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingRelatedProductsSidebarEnabled">
                    <input type="hidden" name="productSettingRelatedProductsSidebarEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Related Products in Sidebar') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, the related products list in the right sidebar is hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingRelatedProductsSidebarEnabled"
                            value="1" isChecked="{{ getSetting('productSettingRelatedProductsSidebarEnabled', '1') !== '0' }}" />
                    </div>

                    <input type="hidden" name="types[]" value="productSettingRelatedProductsBottomEnabled">
                    <input type="hidden" name="productSettingRelatedProductsBottomEnabled" value="0">
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p>{{ translate('Show Related Products Below Description') }}</p>
                            <p class="text-xs text-muted mt-1">
                                {{ translate('When disabled, the related products carousel below the product description is hidden.') }}
                            </p>
                        </div>
                        <x-backend.inputs.checkbox toggler="true" name="productSettingRelatedProductsBottomEnabled"
                            value="1" isChecked="{{ getSetting('productSettingRelatedProductsBottomEnabled', '1') !== '0' }}" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
                    </div>
                </form>
            </div>
        </div>
    @endsection
@endcan
