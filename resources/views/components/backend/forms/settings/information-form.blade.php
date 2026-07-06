<form action="{{ route('admin.general-settings.update') }}" class="space-y-3" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="hidden" name="types[]" value="systemName">
    <x-backend.inputs.text label="System Name" name="systemName" placeholder="Type system name"
        value="{{ getSetting('systemName') ?: 'EpikCart' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="systemTitle">
    <x-backend.inputs.text label="System Title" name="systemTitle" placeholder="Type system title"
        value="{{ getSetting('systemTitle') ?: 'EpikCart - E-commerce Platform' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="systemAddress">
    <x-backend.inputs.textarea label="System Address" name="systemAddress" placeholder="Type system address"
        value="{{ getSetting('systemAddress') ?: '123 Main Street, City, Country' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="systemPhone">
    <x-backend.inputs.text label="System Phone" name="systemPhone" placeholder="Type system phone"
        value="{{ getSetting('systemPhone') ?: '+1234567890' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="systemEmail">
    <x-backend.inputs.text label="System Email" name="systemEmail" placeholder="Type system email"
        value="{{ getSetting('systemEmail') ?: 'admin@example.com' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="currencySymbol">
    <x-backend.inputs.text label="Currency Symbol" name="currencySymbol" placeholder="Type surrency symbol"
        value="{{ getSetting('currencySymbol') ?: '$' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="currencyCode">
    <x-backend.inputs.text label="Currency Code" name="currencyCode" placeholder="Type surrency code"
        value="{{ getSetting('currencyCode') ?: 'USD' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="currencySymbolAlignment">
    <x-backend.inputs.select label="Currency Alignment" name="currencySymbolAlignment" :isRequired="false">
        <x-backend.inputs.select-option name="[symbol][amount] - $100" value="1"
            selected="{{ getSetting('currencySymbolAlignment') ?: '1' }}" />
        <x-backend.inputs.select-option name="[amount][symbol] - 100$" value="2"
            selected="{{ getSetting('currencySymbolAlignment') ?: '1' }}" />
        <x-backend.inputs.select-option name="[symbol] [amount] - Tk 100" value="3"
            selected="{{ getSetting('currencySymbolAlignment') ?: '1' }}" />
        <x-backend.inputs.select-option name="[amount] [symbol] - 100 Tk" value="4"
            selected="{{ getSetting('currencySymbolAlignment') ?: '1' }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="decimalSeparator">
    <x-backend.inputs.text label="Decimal Separator" name="decimalSeparator" placeholder="Add decimal separator"
        value="{{ getSetting('decimalSeparator') ?: '.' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="numOfDecimals">
    <x-backend.inputs.select label="Number Of Decimals" name="numOfDecimals" :isRequired="false">
        <x-backend.inputs.select-option name="Inactive - $100" value="0"
            selected="{{ getSetting('numOfDecimals') ?: '2' }}" />
        <x-backend.inputs.select-option name="1 Decimal - $100.0" value="1"
            selected="{{ getSetting('numOfDecimals') ?: '2' }}" />
        <x-backend.inputs.select-option name="2 Decimals - $100.00" value="2"
            selected="{{ getSetting('numOfDecimals') ?: '2' }}" />
        <x-backend.inputs.select-option name="3 Decimals - $100.000" value="3"
            selected="{{ getSetting('numOfDecimals') ?: '2' }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="thousandSeparator">
    <x-backend.inputs.text label="Thousand Separator" name="thousandSeparator" placeholder="Add thousand separator"
        value="{{ getSetting('thousandSeparator') ?: ',' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="emailVerification">
    <x-backend.inputs.select label="Email Verification" name="emailVerification" groupClass="s" :isRequired="false">
        <x-backend.inputs.select-option name="Active" value="1"
            selected="{{ getSetting('emailVerification') ?: '0' }}" />
        <x-backend.inputs.select-option name="Inactive" value="0"
            selected="{{ getSetting('emailVerification') ?: '0' }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="customerLoginMethod">
    <x-backend.inputs.select label="Customer Login Method" name="customerLoginMethod" :isRequired="false">
        <x-backend.inputs.select-option name="Email" value="email"
            selected="{{ getSetting('customerLoginMethod') ?: 'email' }}" />
        <x-backend.inputs.select-option name="Phone Number" value="phone"
            selected="{{ getSetting('customerLoginMethod') ?: 'email' }}" />
    </x-backend.inputs.select>

    {{-- <input type="hidden" name="types[]" value="manageOrderBy"> --}}
    <x-backend.inputs.select label="Manage Order By" name="manageOrderBy" groupClass="hidden" :isRequired="false">
        <x-backend.inputs.select-option name="Admin & Each Shop" value="both"
            selected="{{ getSetting('manageOrderBy') ?: 'both' }}" />
        <x-backend.inputs.select-option name="Only Admin" value="admin"
            selected="{{ getSetting('manageOrderBy') ?: 'both' }}" />
        <x-backend.inputs.select-option name="Each Shop" value="shop"
            selected="{{ getSetting('manageOrderBy') ?: 'both' }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="appMode">
    <x-backend.inputs.select label="App Mode" name="appMode" :isRequired="false">
        <x-backend.inputs.select-option name="Single Vendor" value="singleVendor"
            selected="{{ config('app.app_mode') }}" />
        <x-backend.inputs.select-option name="Multi Vendor" value="multiVendor"
            selected="{{ config('app.app_mode') }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="timezone">
    <x-backend.inputs.select label="Timezone" name="timezone" :isRequired="false">
        @foreach (timezones() as $key => $value)
            <x-backend.inputs.select-option name="{{ $key }}" value="{!! $value !!}"
                selected="{{ config('app.timezone') }}" />
        @endforeach
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="fraudCheckProvider">
    <x-backend.inputs.select label="{{ translate('fraud_check_provider') }}" name="fraudCheckProvider" :isRequired="false">
        <x-backend.inputs.select-option name="{{ translate('bd_courier_direct') }}" value="bdcourier"
            selected="{{ getSetting('fraudCheckProvider') ?: 'bdcourier' }}" />
        <x-backend.inputs.select-option name="{{ translate('fraudbd_api') }}" value="fraudbd"
            selected="{{ getSetting('fraudCheckProvider') ?: 'bdcourier' }}" />
    </x-backend.inputs.select>

    <input type="hidden" name="types[]" value="adminCommissionPercentage">
    <x-backend.inputs.text label="Admin Commission Rate From Shops" name="adminCommissionPercentage"
        placeholder="10.00" value="{{ getSetting('adminCommissionPercentage') ?: '10' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="minWithdrawalAmount">
    <x-backend.inputs.text label="Minimum Payout Amount" name="minWithdrawalAmount" placeholder="10.00"
        value="{{ getSetting('minWithdrawalAmount') ?: '10' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="showCouponOnCart">
    <div class="flex items-center justify-between py-2">
        <p>{{ translate('Show Coupon Section on Cart Page') }}</p>
        <x-backend.inputs.checkbox toggler="true" name="showCouponOnCart" value="1"
            isChecked="{{ getSetting('showCouponOnCart') != '0' }}" />
    </div>

    <input type="hidden" name="types[]" value="allowBackorder">
    <div class="flex items-center justify-between py-2">
        <p>{{ translate('Allow Backorders (Accept orders even when out of stock)') }}</p>
        <x-backend.inputs.checkbox toggler="true" name="allowBackorder" value="1"
            isChecked="{{ getSetting('allowBackorder') == '1' }}" />
    </div>

    <input type="hidden" name="types[]" value="skipCart">
    <div class="flex items-center justify-between py-2">
        <p>{{ translate('Skip Cart (Direct Checkout)') }}</p>
        <x-backend.inputs.checkbox toggler="true" name="skipCart" value="1"
            isChecked="{{ getSetting('skipCart') == '1' }}" />
    </div>

    <input type="hidden" name="types[]" value="abandonedCartThreshold">
    <x-backend.inputs.text label="Abandoned Cart Threshold (minutes)" name="abandonedCartThreshold"
        placeholder="60" value="{{ getSetting('abandonedCartThreshold') ?: '60' }}" :isRequired="false" />

    <input type="hidden" name="types[]" value="productInfiniteScroll">
    <div class="flex items-center justify-between py-2">
        <p>{{ translate('Enable Infinite Scroll for Product Listings') }}</p>
        <x-backend.inputs.checkbox toggler="true" name="productInfiniteScroll" value="1"
            isChecked="{{ getSetting('productInfiniteScroll') == '1' }}" />
    </div>

    <input type="hidden" name="types[]" value="imageToWebp">
    <div class="flex items-center justify-between py-2">
        <p>{{ translate('Convert Images to WebP (Reduces file size but may affect quality)') }}</p>
        <x-backend.inputs.checkbox toggler="true" name="imageToWebp" value="1"
            isChecked="{{ getSetting('imageToWebp') != '0' }}" />
    </div>

    {{-- <hr class="my-4 border-gray-200"> --}}

    {{-- Size Chart Settings --}}
    {{-- <input type="hidden" name="types[]" value="sizeChartEnabled">
    <div class="flex items-center justify-between py-2">
        <p>{{ translate('Enable Size Chart') }}</p>
        <x-backend.inputs.checkbox
            toggler="true"
            name="sizeChartEnabled"
            value="1"
            isChecked="{{ getSetting('sizeChartEnabled') == '1' }}"
        />
    </div>

    <input type="hidden" name="types[]" value="sizeChartImage">
    <x-backend.inputs.file
        label="Size Chart Image"
        name="sizeChartImage"
        value="{{ getSetting('sizeChartImage') }}"
        filesHint="Upload a global size chart image. This will be shown on all product detail pages when enabled. Recommended size: 800x600 or similar ratio."
        :isRequired="false"
    /> --}}

    <div class="flex justify-end pt-2">
        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" />
    </div>
</form>
