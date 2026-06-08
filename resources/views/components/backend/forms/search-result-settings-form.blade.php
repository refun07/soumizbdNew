@php
    $showProducts = getSetting('searchResultShowProducts', '1') !== '0';
    $showBrands = getSetting('searchResultShowBrands', '1') !== '0';
    $showShops = getSetting('searchResultShowShops', '1') !== '0';
    $priceColor = getSetting('searchResultProductPriceColor', '#5D7261') ?: '#5D7261';
    $discountColor = getSetting('searchResultProductDiscountColor', '#EF4444') ?: '#EF4444';
@endphp

{{-- storefront search result settings --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Search Result Settings') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST">
                @csrf

                <input type="hidden" name="types[]" value="searchResultShowProducts">
                <input type="hidden" name="searchResultShowProducts" value="0">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Products In Search Results') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="searchResultShowProducts" value="1"
                        isChecked="{{ $showProducts }}" />
                </div>

                <input type="hidden" name="types[]" value="searchResultShowBrands">
                <input type="hidden" name="searchResultShowBrands" value="0">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Brands In Search Results') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="searchResultShowBrands" value="1"
                        isChecked="{{ $showBrands }}" />
                </div>

                <input type="hidden" name="types[]" value="searchResultShowShops">
                <input type="hidden" name="searchResultShowShops" value="0">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Shops In Search Results') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="searchResultShowShops" value="1"
                        isChecked="{{ $showShops }}" />
                </div>

                <input type="hidden" name="types[]" value="searchResultProductPriceColor">
                <x-backend.inputs.color :labelInline="false" label="Product Price Color"
                    name="searchResultProductPriceColor" value="{{ $priceColor }}" :isRequired="false" class="mb-4" />

                <input type="hidden" name="types[]" value="searchResultProductDiscountColor">
                <x-backend.inputs.color :labelInline="false" label="Product Discount Color"
                    name="searchResultProductDiscountColor" value="{{ $discountColor }}" :isRequired="false" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>
