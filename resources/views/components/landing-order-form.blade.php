@props([
    'products' => [],
    'landingPageId' => 0,
    'paymentMethods' => [],
    'coupon' => null,
    'countryId' => 18
])

@php
    $subtotal = 0;
    $tax = 0; // Tax disabled - @Dev
    $shippingCharge = 0;
    $couponDiscount = 0;
    $customerLoginMethod = getSetting('customerLoginMethod') ?? 'email';
    $isPhoneLogin = $customerLoginMethod === 'phone';

    if (!empty($products)) {
        $groupedItems = $products->groupBy(function($item) {
            return $item['shop_name'] ?? 'Default Seller';
        });
        foreach ($products as $item) {
            $itemTotal = ($item['price'] ?? 0) * 1;
            $subtotal += $itemTotal;
            // $tax += $itemTotal * 0.1; // Tax disabled - @Dev
        }
    }

    // Calculate coupon discount
    if ($coupon && $subtotal > 0) {
        if ($coupon->discount_type == 'percentage') {
            $couponDiscount = $subtotal * ($coupon->discount_value / 100);
            if ($coupon->max_discount_value && $couponDiscount > $coupon->max_discount_value) {
                $couponDiscount = $coupon->max_discount_value;
            }
        } else {
            $couponDiscount = $coupon->discount_value;
        }

        // Ensure discount doesn't exceed subtotal
        if ($couponDiscount > $subtotal) {
            $couponDiscount = $subtotal;
        }
    }

    $total = $subtotal + $shippingCharge - $couponDiscount; // Tax removed - @Dev
@endphp

<div class="landing-order-form max-w-[90rem] mx-auto py-12" id="landing-page-order-form">
    <form action="{{ route('frontend.landing-page.order') }}" method="POST" id="landing-order-form-inner">
        @csrf
                        <input type="hidden" name="landing_page_id" value="{{ $landingPageId }}">
                        <input type="hidden" name="customer_details[shipping_charge]" id="shipping-charge-input" value="0">
                        <input type="hidden" name="customer_details[shipping_breakdown]" id="shipping-breakdown-input" value="[]">
                        <input type="hidden" name="coupon_code" id="coupon-code-input" value="">
                        <input type="hidden" name="coupon_discount" id="coupon-discount-input" value="0">
        
        <!-- Dynamic items container - will be populated by JavaScript -->
        <div id="items-container"></div>

        <div class="min-h-screen py-10 px-5" style="background-color: var(--landing-theme-color);">
            <div class="max-w-6xl mx-auto">
                <div class="text-left mb-8">
                    <h2 class="text-2xl font-bold text-gray-800">Fill Up The Form To Confirm Order</h2>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Customer Details (Desktop First, Mobile Second) -->
                    <div class="order-2 lg:order-1 lg:col-span-2">
                        <div class="bg-white p-8 rounded-lg shadow-sm">

                            <!-- Customer Details Section -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <h3 class="bg-gray-100 px-7 py-3.5 text-sm font-semibold uppercase text-gray-800 m-0">Customer Details</h3>
                                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="customer_details[name]" value="{{ old('customer_details.name') }}" required
                                            class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors"
                                            placeholder="Type Your Full Name">
                                    </div>
                                    @if($isPhoneLogin)
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Phone <span class="text-red-500">*</span></label>
                                            <input type="text" name="customer_details[phone]" value="{{ old('customer_details.phone') }}" required
                                                class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors"
                                                placeholder="880XXXXXXXXXXX or 01XXXXXXXXX">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Email (Optional)</label>
                                            <input type="email" name="customer_details[email]" value="{{ old('customer_details.email') }}"
                                                class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors"
                                                placeholder="Type Your Email">
                                        </div>
                                    @else
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Email</label>
                                            <input type="email" name="customer_details[email]" value="{{ old('customer_details.email') }}"
                                                class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors"
                                                placeholder="Type Your Email">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Phone <span class="text-red-500">*</span></label>
                                            <input type="text" name="customer_details[phone]" value="{{ old('customer_details.phone') }}" required
                                                class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors"
                                                placeholder="Valid Phone No.">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Shipping Address Section -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden mt-8">
                                <h3 class="bg-gray-100 px-7 py-3.5 text-sm font-semibold uppercase text-gray-800 m-0">Shipping Address</h3>
                                <div class="p-6">
                                    <!-- Hidden Country ID (Bangladesh) -->
                                    <input type="hidden" name="customer_details[country_id]" id="country-id" value="{{ $countryId }}">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">District <span class="text-red-500">*</span></label>
                                            <select name="customer_details[state_id]" id="district-select" required
                                                class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors bg-white cursor-pointer"
                                                onchange="handleDistrictChange(this)">
                                                <option value="">Select a district</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">City/Town <span class="text-red-500">*</span></label>
                                            <select name="customer_details[city_id]" id="city-select" required
                                                class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors bg-white cursor-pointer disabled:bg-gray-100 disabled:cursor-not-allowed"
                                                onchange="handleCityChange(this)" disabled>
                                                <option value="">Select a city/town</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Address <span class="text-red-500">*</span></label>
                                        <textarea name="customer_details[shipping_address]" rows="3" required
                                            class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors resize-none"
                                            placeholder="Please Enter Your Details Delivery Address In English (or Banglish eg - Amar Sonar Bangla). দয়াকরে আপনার ডেলিভারি এড্রেস ইংরেজিতে (ইংরেজি ফন্টে/বাংলিশ যেমন - Aamar Sonar Bangla) লিখুন।">{{ old('customer_details.shipping_address') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method Section -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden mt-8">
                                <h3 class="bg-gray-100 px-7 py-3.5 text-sm font-semibold uppercase text-gray-800 m-0">Select Payment Method</h3>
                                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @if(count($paymentMethods) === 0)
                                        <div class="md:col-span-4 text-center py-5 text-gray-500">No payment methods available</div>
                                    @else
                                        @foreach($paymentMethods as $method)
                                            @php
                                                $isSelected = old('customer_details.payment_method') === $method->value;
                                                $borderColor = $isSelected ? '#6EAEE8' : '#E5E5E5';
                                                $methodImage = '';
                                                switch($method->value) {
                                                    case 'cash_on_delivery':
                                                        $methodImage = '/images/payment/cash_on_delivery.png';
                                                        break;
                                                    case 'card_on_delivery':
                                                        $methodImage = '/images/payment/card_on_delivery.png';
                                                        break;
                                                    case 'sslcommerz':
                                                        $methodImage = '/images/payment/sslcommerz.png';
                                                        break;
                                                    case 'stripe':
                                                        $methodImage = '/images/payment/stripe.png';
                                                        break;
                                                    case 'paypal':
                                                        $methodImage = '/images/payment/paypal.png';
                                                        break;
                                                }
                                            @endphp
                                            <label class="relative flex items-center justify-center px-4 py-4 border rounded-lg cursor-pointer transition-all hover:border-gray-300" style="border-color: {{ $borderColor }};">
                                                <input type="radio" name="customer_details[payment_method]" value="{{ $method->value }}" {{ $isSelected ? 'checked' : '' }} class="hidden" data-payment-method="{{ $method->value }}">
                                                @if($isSelected)
                                                    <span class="payment-checkmark absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full flex items-center justify-center z-10" style="background-color: #6EAEE8;">
                                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                                    </span>
                                                @endif
                                                @if($methodImage)
                                                    <img src="{{ $methodImage }}" alt="{{ $method->name }}" class="h-8 object-contain">
                                                @else
                                                    <span class="font-semibold text-sm text-gray-700">{{ $method->name }}</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Create Account Section -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden mt-8">
                                <label class="flex items-center gap-3 px-7 py-4 rounded-t-lg cursor-pointer" style="background-color: var(--landing-theme-color);">
                                    <input type="checkbox" name="create_account" id="create-account-check" value="1"
                                        class="w-5 h-5 rounded border-gray-300"
                                        style="color: var(--landing-theme-color);"
                                        onchange="togglePasswordFields(this)">
                                    <span class="text-sm font-semibold text-white uppercase">Open Account to Get Exclusive Offers</span>
                                </label>
                                <div class="p-6" id="password-fields" style="display: none;">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 uppercase mb-1.5">Type Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="customer_details[password]" id="account-password"
                                            class="w-full px-4 py-3 border border-gray-200 rounded text-sm outline-none focus:border-blue-400 transition-colors"
                                            placeholder="Enter password">
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Order Button -->
                            <div class="flex justify-end mt-8">
                                <button type="submit" id="confirm-order-btn"
                                    class="text-white px-8 py-3.5 border-none rounded font-semibold text-sm uppercase cursor-pointer transition-colors tracking-wide"
                                    style="background-color: var(--landing-primary-color);"
                                    onmouseover="this.style.backgroundColor='var(--landing-primary-dark)'"
                                    onmouseout="this.style.backgroundColor='var(--landing-primary-color)'">
                                    Confirm Order
                                </button>
                            </div>

                            <!-- Validation Errors -->
                            @if($errors->any())
                                <div class="mt-5 p-4 bg-red-50 border border-red-200 rounded text-red-600">
                                    <ul class="m-0 pl-5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>
                    </div>

                    <!-- Right Column: Order Summary (Mobile First, Desktop Second) -->
                    <div class="order-1 lg:order-2">
                        <div class="bg-white rounded-lg overflow-hidden shadow-sm">
                            <h3 class="px-8 py-3.5 text-sm font-semibold uppercase text-white m-0" style="background-color: var(--landing-theme-color);">Order Summary</h3>
                            <div id="order-summary-content">
                                @if(!empty($products) && isset($groupedItems))
                                    @foreach($groupedItems as $shopName => $items)
                                        <div>
                                            @foreach($items as $index => $item)
                                                <div class="px-4 py-3 border-b border-gray-100 product-item" data-product-id="{{ $item['id'] }}" data-price="{{ $item['price'] ?? 0 }}" data-base-price="{{ $item['base_price'] ?? $item['price'] ?? 0 }}" data-variation-id="{{ $item['variation_id'] ?? '' }}">
                                                    <div class="flex items-center gap-3">
                                                        <!-- Checkbox for selection -->
                                                        <div class="flex-shrink-0">
                                                            <input type="checkbox" 
                                                                class="product-checkbox w-5 h-5 rounded border-gray-300 cursor-pointer"
                                                                style="color: var(--landing-theme-color);"
                                                                checked
                                                                onchange="updateOrderSummary()">
                                                        </div>
                                                        <div class="flex-shrink-0 w-[70px] h-[70px] rounded overflow-hidden">
                                                            @if($item['image'])
                                                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                                            @else
                                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                                    <i class="fa-solid fa-image text-lg"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="m-0 text-sm font-medium text-gray-700 leading-relaxed">{{ $item['name'] }}</p>
                                                            
                                                            <!-- Selected Variation Display -->
                                                            <p class="selected-variation-name m-0 mt-1 text-xs text-gray-500 italic" data-product-id="{{ $item['id'] }}">
                                                                @if(!empty($item['variations']))
                                                                    @php
                                                                        $selectedVariation = collect($item['variations'])->firstWhere('id', $item['variation_id']);
                                                                    @endphp
                                                                    @if($selectedVariation)
                                                                        Variant: {{ $selectedVariation['name'] ?? 'Variant #' . $selectedVariation['id'] }}
                                                                    @endif
                                                                @endif
                                                            </p>
                                                            
                                                            @php
                                                                $itemBasePrice = $item['base_price'] ?? $item['price'] ?? 0;
                                                                $itemDiscountedPrice = $item['price'] ?? 0;
                                                                $itemHasDiscount = $itemBasePrice > $itemDiscountedPrice;
                                                                $itemDiscountPercent = $item['discount_percent'] ?? 0;
                                                            @endphp
                                                            <div class="product-price m-0 mt-1 flex items-center gap-2" data-product-id="{{ $item['id'] }}">
                                                                @if($itemHasDiscount)
                                                                    <span class="text-sm line-through text-gray-400">{{ formatPrice($itemBasePrice) }}</span>
                                                                    <span class="text-sm font-semibold" style="color: var(--landing-primary-color);">{{ formatPrice($itemDiscountedPrice) }}</span>
                                                                    <span class="text-xs font-bold px-1.5 py-0.5 rounded text-white" style="background-color: var(--landing-primary-color);">{{ $itemDiscountPercent }}% OFF</span>
                                                                @else
                                                                    <span class="text-sm text-blue-400">{{ formatPrice($itemDiscountedPrice) }}</span>
                                                                @endif
                                                            </div>
                                                            
                                                            <!-- Variation Selector Dropdown -->
                                                            @if(!empty($item['variations']) && count($item['variations']) > 1)
                                                                <div class="mt-2">
                                                                    <select class="variation-select w-full px-2 py-1.5 text-xs border border-gray-300 rounded focus:outline-none focus:border-blue-400 bg-white cursor-pointer" 
                                                                            data-product-id="{{ $item['id'] }}"
                                                                            onchange="handleVariationChange({{ $item['id'] }}, this.value)">
                                                                        @foreach($item['variations'] as $variation)
                                                                            <option value="{{ $variation['id'] }}" 
                                                                                    data-price="{{ $variation['price'] }}"
                                                                                    data-base-price="{{ $variation['base_price'] ?? $variation['price'] }}"
                                                                                    data-discount-percent="{{ $variation['discount_percent'] ?? 0 }}"
                                                                                    data-name="{{ $variation['name'] ?? 'Variant #' . $variation['id'] }}"
                                                                                    {{ $variation['id'] == $item['variation_id'] ? 'selected' : '' }}>
                                                                                {{ $variation['name'] ?? 'Variant #' . $variation['id'] }} - {{ formatPrice($variation['price']) }}
                                                                                @if(($variation['base_price'] ?? $variation['price']) > $variation['price'])
                                                                                    <span class="text-xs text-green-600">({{ $variation['discount_percent'] ?? 0 }}% OFF)</span>
                                                                                @endif
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @endif
                                                            
                                                            <!-- Quantity Controls -->
                                                            <div class="flex items-center gap-2 mt-2">
                                                                <button type="button" 
                                                                    class="quantity-btn w-7 h-7 flex items-center justify-center border border-gray-300 rounded text-xs hover:bg-gray-100"
                                                                    onclick="updateQuantity({{ $item['id'] }}, -1)">
                                                                    <i class="fa-solid fa-minus"></i>
                                                                </button>
                                                                <input type="number" 
                                                                    class="quantity-input w-12 h-7 text-center border border-gray-300 rounded text-sm"
                                                                    value="1" 
                                                                    min="1"
                                                                    data-product-id="{{ $item['id'] }}"
                                                                    onchange="updateQuantityFromInput({{ $item['id'] }}, this.value)">
                                                                <button type="button" 
                                                                    class="quantity-btn w-7 h-7 flex items-center justify-center border border-gray-300 rounded text-xs hover:bg-gray-100"
                                                                    onclick="updateQuantity({{ $item['id'] }}, 1)">
                                                                    <i class="fa-solid fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="text-right" style="display:none;">
                                                            @if($itemHasDiscount)
                                                                <p class="item-total m-0 text-sm font-semibold" style="color: var(--landing-primary-color);">{{ formatPrice($itemDiscountedPrice) }}</p>
                                                            @else
                                                                <p class="item-total m-0 text-sm font-semibold text-gray-800">{{ formatPrice($itemDiscountedPrice) }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-5 text-center text-gray-500">
                                        No products available
                                    </div>
                                @endif
                            </div>
                            <div class="px-8 pb-8">
                                <!-- Coupon Code Input -->
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Have a coupon code?</label>
                                    <div class="flex gap-2">
                                        <input type="text" 
                                            id="coupon-code-field" 
                                            placeholder="Enter coupon code"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-400 uppercase"
                                            style="text-transform: uppercase;"
                                        >
                                        <button type="button" 
                                            id="apply-coupon-btn"
                                            class="px-4 py-2 bg-primary text-white rounded text-sm font-semibold hover:bg-primary/90 transition-colors"
                                            style="background-color: var(--landing-primary-color);"
                                            onclick="applyCoupon()"
                                        >
                                            Apply
                                        </button>
                                    </div>
                                    <div id="coupon-message" class="mt-2 text-sm hidden"></div>
                                </div>

                                <div class="border border-gray-200 rounded overflow-hidden">
                                    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200">
                                        <span class="text-sm text-gray-700">Subtotal</span>
                                        <span class="text-sm text-gray-700" id="summary-subtotal">{{ formatPrice($subtotal) }}</span>
                                    </div>
                                    {{-- Tax disabled - @Dev
                                    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200">
                                        <span class="text-sm text-gray-700">Tax</span>
                                        <span class="text-sm text-gray-700" id="summary-tax">{{ formatPrice($tax) }}</span>
                                    </div>
                                    --}}
                                    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200">
                                        <span class="text-sm text-gray-700">Shipping Charge</span>
                                        <span class="text-sm text-gray-700" id="summary-shipping">{{ formatPrice($shippingCharge) }}</span>
                                    </div>
                                    <div id="shipping-breakdown-display" class="hidden px-6 pb-3 border-b border-gray-100">
                                        <!-- Per-shop shipping will be inserted here -->
                                    </div>
                                    <div id="coupon-discount-row" class="flex items-center justify-between px-6 py-3 border-b border-gray-200 bg-green-50 hidden">
                                        <span class="text-sm text-green-700">
                                            Coupon Discount (<span id="applied-coupon-code"></span>)
                                            <span id="coupon-discount-info"></span>
                                        </span>
                                        <span class="text-sm text-green-700" id="summary-coupon-discount">-$0.00</span>
                                    </div>
                                    <div class="flex items-center justify-between px-6 py-4 bg-gray-50">
                                        <span class="text-lg font-bold text-black">Total</span>
                                        <span class="text-lg font-bold text-black" id="summary-total">{{ formatPrice($total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const currencySymbol = '{{ getSetting("currencySymbol") ?? "$" }}';

// Store product data for JavaScript use
const productsData = {!! json_encode($products ?? []) !!};

// Coupon data - this is the coupon assigned to the landing page
const landingPageCoupon = {!! json_encode($coupon ? [
    'code' => $coupon->code,
    'discount_type' => $coupon->discount_type,
    'discount_value' => $coupon->discount_value,
    'max_discount_value' => $coupon->max_discount_value,
    'is_free_shipping' => $coupon->is_free_shipping
] : null) !!};

// Applied coupon state
let appliedCoupon = null;

// Track selected products and quantities
let selectedProducts = {};
let productQuantities = {};
let selectedDistrictId = '';
let selectedCityId = '';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeProductData();
    updateOrderSummary();
    
    // Load districts (states) for Bangladesh (country_id = 18)
    var districtSelect = document.getElementById('district-select');
    if (districtSelect) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/api/v1/get-states/{{ $countryId }}');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.result) {
                            selectedDistrictId = '{{ old('customer_details.state_id') }}';
                            response.result.forEach(function(district) {
                                var option = document.createElement('option');
                                option.value = district.id;
                                option.textContent = district.name;
                                if (district.id == selectedDistrictId) {
                                    option.selected = true;
                                }
                                districtSelect.appendChild(option);
                            });
                            
                            // If old value exists, trigger district change to load cities
                            if (selectedDistrictId) {
                                handleDistrictChange(districtSelect);
                            }
                            
                            console.log('Districts loaded:', response.result);
                        }
                    } catch (e) {
                        console.error('Error parsing districts:', e);
                    }
                } else {
                    console.error('Districts API error:', xhr.status, xhr.statusText);
                }
            }
        };
        xhr.onerror = function() {
            console.error('Districts API request failed');
        };
        xhr.send();
    }

    // Form validation
    var form = document.getElementById('landing-order-form-inner');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate at least one product is selected
            const selectedCount = Object.values(selectedProducts).filter(v => v).length;
            if (selectedCount === 0) {
                e.preventDefault();
                alert('Please select at least one product');
                return false;
            }
            
            // Update hidden inputs before submission
            updateHiddenInputs();
            
            var name = form.querySelector('[name="customer_details[name]"]').value.trim();
            var phone = form.querySelector('[name="customer_details[phone]"]').value.trim();
            var address = form.querySelector('[name="customer_details[shipping_address]"]').value.trim();
            var districtId = form.querySelector('[name="customer_details[state_id]"]').value;
            var cityId = form.querySelector('[name="customer_details[city_id]"]').value;
            var paymentMethod = form.querySelector('[name="customer_details[payment_method]"]:checked');

            var errors = [];
            if (!name) errors.push('Name is required');
            if (!phone) errors.push('Phone is required');
            if (!address) errors.push('Address is required');
            if (!districtId) errors.push('District is required');
            if (!cityId) errors.push('City is required');
            if (!paymentMethod) errors.push('Please select a payment method');

            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fill in all required fields:\n- ' + errors.join('\n- '));
                return false;
            }

            // Show loading state
            var btn = document.getElementById('confirm-order-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Placing Order...';
            }

            return true;
        });
    }

    // Payment method selection highlighting with event delegation
    document.addEventListener('click', function(e) {
        var radio = e.target.closest('input[name="customer_details[payment_method]"]');
        if (radio) {
            var allRadios = document.querySelectorAll('input[name="customer_details[payment_method]"]');
            allRadios.forEach(function(r) {
                var label = r.closest('label');
                if (r.checked) {
                    label.style.borderColor = '#6EAEE8';
                    if (!label.querySelector('.payment-checkmark')) {
                        var checkmark = document.createElement('span');
                        checkmark.className = 'payment-checkmark absolute -top-2.5 -right-2.5 w-5 h-5 rounded-full flex items-center justify-center';
                        checkmark.style.cssText = 'background-color: #6EAEE8;';
                        checkmark.innerHTML = '<i class="fa-solid fa-check text-white text-xs"></i>';
                        label.appendChild(checkmark);
                    }
                } else {
                    label.style.borderColor = '#E5E5E5';
                    var checkmark = label.querySelector('.payment-checkmark');
                    if (checkmark) checkmark.remove();
                }
            });
        }
    });

    // Initialize payment method selection on page load
    var checkedPayment = document.querySelector('input[name="customer_details[payment_method]"]:checked');
    if (checkedPayment) {
        checkedPayment.dispatchEvent(new Event('click'));
    }

    // Initialize create account checkbox state
    var createAccountCheck = document.getElementById('create-account-check');
    if (createAccountCheck && createAccountCheck.checked) {
        document.getElementById('password-fields').style.display = 'block';
    }
});

// Initialize product data - all selected by default
function initializeProductData() {
    productsData.forEach(product => {
        selectedProducts[product.id] = true;
        productQuantities[product.id] = 1;
    });
}

// Track current shipping state
let currentShippingCharge = 0;
let currentShippingBreakdown = [];

// Handle district change
function handleDistrictChange(selectElement) {
    const districtId = selectElement.value;
    selectedDistrictId = districtId;
    
    const citySelect = document.getElementById('city-select');
    
    // Reset city selection
    citySelect.innerHTML = '<option value="">Select a city/town</option>';
    citySelect.disabled = !districtId;
    selectedCityId = '';
    updateShippingCharge(0, []);
    
    if (!districtId) {
        return;
    }
    
    // Show loading state
    citySelect.innerHTML = '<option value="">Loading cities...</option>';
    citySelect.disabled = true;
    
    // Load cities for the selected district
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/api/v1/get-cities/' + districtId);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.result) {
                        citySelect.innerHTML = '<option value="">Select a city/town</option>';
                        citySelect.disabled = false;
                        
                        var oldCityId = '{{ old('customer_details.city_id') }}';
                        response.result.forEach(function(city) {
                            var option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            if (city.id == oldCityId && oldCityId) {
                                option.selected = true;
                                selectedCityId = city.id;
                                // Trigger shipping calculation for old value
                                calculateShippingCharge(city.id);
                            }
                            citySelect.appendChild(option);
                        });
                        console.log('Cities loaded for district ' + districtId + ':', response.result);
                    } else {
                        citySelect.innerHTML = '<option value="">No cities available</option>';
                        citySelect.disabled = true;
                    }
                } catch (e) {
                    console.error('Error parsing cities:', e);
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                    citySelect.disabled = true;
                }
            } else {
                console.error('Cities API error:', xhr.status, xhr.statusText);
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
                citySelect.disabled = true;
            }
        }
    };
    xhr.onerror = function() {
        console.error('Cities API request failed');
        citySelect.innerHTML = '<option value="">Error loading cities</option>';
        citySelect.disabled = true;
    };
    xhr.send();
}

// Handle city change
function handleCityChange(selectElement) {
    const cityId = selectElement.value;
    selectedCityId = cityId;
    if (cityId) {
        calculateShippingCharge(cityId);
    } else {
        updateShippingCharge(0, []);
    }
}

// Calculate shipping charge when city changes
async function calculateShippingCharge(cityId) {
    // Show loading state
    const shippingEl = document.getElementById('summary-shipping');
    if (shippingEl) {
        shippingEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i>';
    }

    // Build items array from selected products
    const items = [];
    const shopIds = [];

    productsData.forEach(product => {
        if (selectedProducts[product.id]) {
            const quantity = productQuantities[product.id] || 1;
            items.push({
                product_id: product.id,
                variation_id: product.variation_id,
                quantity: quantity,
                shop_id: product.shop_id
            });
            if (product.shop_id && !shopIds.includes(product.shop_id)) {
                shopIds.push(product.shop_id);
            }
        }
    });

    if (items.length === 0) {
        updateShippingCharge(0, []);
        return;
    }

    try {
        const response = await fetch('/api/v1/checkout/get-shipping-charge', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                cityId: parseInt(cityId),
                shopIds: shopIds,
                items: items
            })
        });

        const data = await response.json();

        if (data.success) {
            updateShippingCharge(data.result, data.breakdown || []);
        } else {
            throw new Error(data.message || 'Failed to calculate shipping');
        }
    } catch (error) {
        console.error('Shipping calculation error:', error);
        showShippingError();
    }
}

// Update shipping charge display
function updateShippingCharge(amount, breakdown) {
    currentShippingCharge = amount;
    currentShippingBreakdown = breakdown;

    // Update main shipping display
    const shippingEl = document.getElementById('summary-shipping');
    if (shippingEl) {
        shippingEl.textContent = currencySymbol + amount.toFixed(2);
    }

    // Update hidden inputs
    const shippingInput = document.getElementById('shipping-charge-input');
    const breakdownInput = document.getElementById('shipping-breakdown-input');
    if (shippingInput) {
        shippingInput.value = amount;
    }
    if (breakdownInput) {
        breakdownInput.value = JSON.stringify(breakdown);
    }

    // Show/hide breakdown for multi-vendor mode
    const breakdownDisplay = document.getElementById('shipping-breakdown-display');
    const isMultiVendor = window.config.generalSettings?.appMode === 'multiVendor';

    if (isMultiVendor && breakdown && breakdown.length > 0 && amount > 0) {
        breakdownDisplay.classList.remove('hidden');
        breakdownDisplay.innerHTML = breakdown.map(item =>
            `<div class="flex justify-between text-xs text-gray-500 py-1">
                <span>${item.shop_name}</span>
                <span>${currencySymbol}${parseFloat(item.charge || 0).toFixed(2)}</span>
            </div>`
        ).join('');
    } else {
        breakdownDisplay.classList.add('hidden');
    }

    // Recalculate total
    updateOrderSummary();
}

// Show shipping error state
function showShippingError() {
    const shippingEl = document.getElementById('summary-shipping');
    const breakdownDisplay = document.getElementById('shipping-breakdown-display');
    if (shippingEl) {
        shippingEl.textContent = 'Calculated at checkout';
    }
    if (breakdownDisplay) {
        breakdownDisplay.classList.add('hidden');
    }
    currentShippingCharge = 0;
    currentShippingBreakdown = [];
    updateOrderSummary();
}

// Update quantity for a product
function updateQuantity(productId, change) {
    const currentQty = productQuantities[productId] || 1;
    const newQty = Math.max(1, currentQty + change);
    productQuantities[productId] = newQty;
    
    // Update input value
    const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
    if (input) {
        input.value = newQty;
    }
    
    updateOrderSummary();
    if (selectedCityId) {
        calculateShippingCharge(selectedCityId);
    }
}

// Update quantity from input field
function updateQuantityFromInput(productId, value) {
    const qty = parseInt(value) || 1;
    productQuantities[productId] = Math.max(1, qty);
    updateOrderSummary();
    if (selectedCityId) {
        calculateShippingCharge(selectedCityId);
    }
}

// Update order summary calculations and hidden inputs
function updateOrderSummary() {
    let subtotal = 0;
    
    // Update selected products state from checkboxes
    document.querySelectorAll('.product-item').forEach(item => {
        const productId = parseInt(item.dataset.productId);
        const checkbox = item.querySelector('.product-checkbox');
        const quantity = productQuantities[productId] || 1;
        
        // Get price from productsData to reflect selected variation
        const product = productsData.find(p => p.id === productId);
        const price = product ? parseFloat(product.price) || 0 : 0;
        
        if (checkbox) {
            selectedProducts[productId] = checkbox.checked;
        }
        
        // Update item total display
        const itemTotalEl = item.querySelector('.item-total');
        if (itemTotalEl && selectedProducts[productId]) {
            const itemTotal = price * quantity;
            itemTotalEl.textContent = currencySymbol + itemTotal.toFixed(2);
            subtotal += itemTotal;
        } else if (itemTotalEl) {
            itemTotalEl.textContent = currencySymbol + '0.00';
        }
        
        // Show/hide quantity controls based on selection
        const quantityControls = item.querySelectorAll('.quantity-btn, .quantity-input');
        quantityControls.forEach(ctrl => {
            ctrl.style.opacity = selectedProducts[productId] ? '1' : '0.5';
            ctrl.style.pointerEvents = selectedProducts[productId] ? 'auto' : 'none';
        });
        
        // Show/hide variation selector based on selection
        const variationSelect = item.querySelector('.variation-select');
        if (variationSelect) {
            variationSelect.disabled = !selectedProducts[productId];
            variationSelect.style.opacity = selectedProducts[productId] ? '1' : '0.5';
        }
    });
    
    // Calculate totals
    const tax = 0; /* Tax disabled - @Dev subtotal * 0.1; */
    const shippingCharge = currentShippingCharge;
    
    // Calculate coupon discount
    let couponDiscount = 0;
    if (appliedCoupon && subtotal > 0) {
        if (appliedCoupon.discount_type === 'percentage') {
            couponDiscount = subtotal * (appliedCoupon.discount_value / 100);
            if (appliedCoupon.max_discount_value && couponDiscount > appliedCoupon.max_discount_value) {
                couponDiscount = appliedCoupon.max_discount_value;
            }
        } else {
            couponDiscount = appliedCoupon.discount_value;
        }
        // Ensure discount doesn't exceed subtotal
        if (couponDiscount > subtotal) {
            couponDiscount = subtotal;
        }
    }
    
    const total = subtotal + shippingCharge - couponDiscount; /* Tax removed - @Dev */
    
    // Update summary display
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryTax = document.getElementById('summary-tax');
    const summaryShipping = document.getElementById('summary-shipping');
    const summaryTotal = document.getElementById('summary-total');
    
    if (summarySubtotal) summarySubtotal.textContent = currencySymbol + subtotal.toFixed(2);
    if (summaryTax) summaryTax.textContent = currencySymbol + tax.toFixed(2);
    if (summaryShipping) summaryShipping.textContent = currencySymbol + shippingCharge.toFixed(2);
    if (summaryTotal) summaryTotal.textContent = currencySymbol + total.toFixed(2);
    
    // Update coupon discount display
    const couponDiscountRow = document.getElementById('coupon-discount-row');
    const couponDiscountEl = document.getElementById('summary-coupon-discount');
    const appliedCouponCode = document.getElementById('applied-coupon-code');
    const discountInfo = document.getElementById('coupon-discount-info');
    
    if (appliedCoupon && couponDiscount > 0) {
        if (couponDiscountRow) couponDiscountRow.classList.remove('hidden');
        if (couponDiscountEl) couponDiscountEl.textContent = '-' + currencySymbol + couponDiscount.toFixed(2);
        if (appliedCouponCode) appliedCouponCode.textContent = appliedCoupon.code;
        if (appliedCoupon.discount_type === 'percentage') {
            if (discountInfo) discountInfo.textContent = ' (' + appliedCoupon.discount_value + '% off)';
        } else {
            if (discountInfo) discountInfo.textContent = ' (' + currencySymbol + appliedCoupon.discount_value + ' off)';
        }
    } else {
        if (couponDiscountRow) couponDiscountRow.classList.add('hidden');
    }
    
    // Update hidden coupon inputs
    const couponCodeInput = document.getElementById('coupon-code-input');
    const couponDiscountInput = document.getElementById('coupon-discount-input');
    if (couponCodeInput) {
        couponCodeInput.value = appliedCoupon ? appliedCoupon.code : '';
    }
    if (couponDiscountInput) {
        couponDiscountInput.value = couponDiscount.toFixed(2);
    }
}

// Apply coupon function
function applyCoupon() {
    const couponInput = document.getElementById('coupon-code-field');
    const messageEl = document.getElementById('coupon-message');
    const enteredCode = couponInput.value.trim().toUpperCase();
    
    if (!enteredCode) {
        showCouponMessage('Please enter a coupon code', 'error');
        return;
    }
    
    if (!landingPageCoupon) {
        showCouponMessage('No coupon available for this landing page', 'error');
        return;
    }
    
    if (enteredCode !== landingPageCoupon.code.toUpperCase()) {
        showCouponMessage('Invalid coupon code', 'error');
        appliedCoupon = null;
        updateOrderSummary();
        return;
    }
    
    // Coupon matches - apply it
    appliedCoupon = landingPageCoupon;
    showCouponMessage('Coupon applied successfully!', 'success');
    updateOrderSummary();
}

// Show coupon message
function showCouponMessage(message, type) {
    const messageEl = document.getElementById('coupon-message');
    messageEl.textContent = message;
    messageEl.classList.remove('hidden', 'text-red-600', 'text-green-600');
    messageEl.classList.add(type === 'success' ? 'text-green-600' : 'text-red-600');
}

// Update hidden form inputs for submission
function updateHiddenInputs() {
    const container = document.getElementById('items-container');
    container.innerHTML = '';
    
    let index = 0;
    productsData.forEach(product => {
        if (selectedProducts[product.id]) {
            const quantity = productQuantities[product.id] || 1;
            
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = `items[${index}][product_id]`;
            productInput.value = product.id;
            container.appendChild(productInput);
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = quantity;
            container.appendChild(quantityInput);
            
            const variationInput = document.createElement('input');
            variationInput.type = 'hidden';
            variationInput.name = `items[${index}][variation_id]`;
            variationInput.value = product.variation_id || '';
            container.appendChild(variationInput);
            
            index++;
        }
    });
}

// Toggle password fields based on checkbox
function togglePasswordFields(checkbox) {
    var passwordFields = document.getElementById('password-fields');
    if (passwordFields) {
        passwordFields.style.display = checkbox.checked ? 'block' : 'none';
    }
}

// Handle variation change for a product
function handleVariationChange(productId, variationId) {
    const product = productsData.find(p => p.id === productId);
    if (!product || !product.variations) return;
    
    const variation = product.variations.find(v => v.id === parseInt(variationId));
    if (!variation) return;
    
    // Update product data with selected variation
    product.variation_id = parseInt(variationId);
    product.price = variation.price;
    product.base_price = variation.base_price ?? variation.price;
    product.discount_percent = variation.discount_percent ?? 0;
    
    // Update the product item's dataset for consistency
    const productItem = document.querySelector(`.product-item[data-product-id="${productId}"]`);
    if (productItem) {
        productItem.dataset.price = variation.price;
        productItem.dataset.basePrice = variation.base_price ?? variation.price;
        productItem.dataset.variationId = variationId;
    }
    
    // Update the selected variation name display
    const variationNameEl = document.querySelector(`.selected-variation-name[data-product-id="${productId}"]`);
    if (variationNameEl) {
        const displayName = variation.name || `Variant #${variation.id}`;
        variationNameEl.textContent = `Variant: ${displayName}`;
    }
    
    // Update the price display (with discount badge if applicable)
    const priceContainer = document.querySelector(`.product-price[data-product-id="${productId}"]`);
    if (priceContainer) {
        const basePrice = variation.base_price ?? variation.price;
        const discountedPrice = variation.price;
        const discountPercent = variation.discount_percent ?? 0;
        const hasDiscount = basePrice > discountedPrice;
        
        if (hasDiscount) {
            priceContainer.innerHTML = `
                <span class="text-sm line-through text-gray-400">${currencySymbol}${parseFloat(basePrice).toFixed(2)}</span>
                <span class="text-sm font-semibold" style="color: var(--landing-primary-color);">${currencySymbol}${parseFloat(discountedPrice).toFixed(2)}</span>
                <span class="text-xs font-bold px-1.5 py-0.5 rounded text-white" style="background-color: var(--landing-primary-color);">${discountPercent}% OFF</span>
            `;
        } else {
            priceContainer.innerHTML = `<span class="text-sm text-blue-400">${currencySymbol}${parseFloat(discountedPrice).toFixed(2)}</span>`;
        }
    }
    
    // Update order summary with new price
    updateOrderSummary();
    
    // Recalculate shipping if city is selected
    if (selectedCityId) {
        calculateShippingCharge(selectedCityId);
    }
}
</script>
