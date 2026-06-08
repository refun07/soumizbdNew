{{-- Product Order Section --}}
@php
    // Use section background color if set (and not empty), otherwise fall back to page theme color
    $sectionBg = $data['background_color'] ?? null;
    $bgColor = !empty($sectionBg) ? $sectionBg : ($themeColor ?? null);
@endphp

<section class="w-full py-16" style="position: relative; {{ $bgColor ? 'background-color: ' . $bgColor : 'background-color: #f9fafb;' }}">
    @include('frontend.landing.sections._background-layers', ['data' => $data])

    {{-- Main Content Container --}}
    <div class="container mx-auto px-4" style="position: relative; z-index: 10;">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $data['title'] ?? 'Order Our Products' }}
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ $data['subtitle'] ?? 'Select from our premium collection' }}
            </p>
        </div>

        <div id="landing-page-order-form"></div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('landing-page-order-form');
        if (!container) return;

        const landingPageId = {{ $landingPage->id ?? 0 }};
        const csrfToken = '{{ csrf_token() }}';
        const currencySymbol = '{{ getSetting("currencySymbol") ?? "$" }}';

        // Products are passed directly from controller
        let products = {!! json_encode($products ?? []) !!};
        let selectedProductId = null;
        let quantity = 1;
        let showOrderForm = false;

        const orderForm = {
            name: '',
            email: '',
            phone: '',
            alternativePhone: '',
            note: '',
            shippingAddress: '',
            paymentMethod: 'cash_on_delivery'
        };

        // Initialize products
        if (products.length > 0) {
            selectedProductId = products[0].id;
        }

        render();

        function handleProductChange(productId) {
            selectedProductId = parseInt(productId);
            quantity = 1;
            render();
        }

        function handleQuantityChange(e) {
            const qty = parseInt(e.target.value);
            if (qty > 0) {
                quantity = qty;
                updateTotalPrice();
            }
        }

        function handleInputChange(e) {
            const { name, value } = e.target;
            orderForm[name] = value;
        }

        async function handleSubmitOrder(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Placing Order...';
            }

            try {
                const response = await fetch('/api/v1/landing-page/order', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        landing_page_id: landingPageId,
                        product_id: selectedProductId,
                        quantity: quantity,
                        customer_details: {
                            name: orderForm.name,
                            email: orderForm.email,
                            phone: orderForm.phone,
                            alternative_phone: orderForm.alternativePhone,
                            note: orderForm.note,
                            shipping_address: orderForm.shippingAddress,
                            payment_method: orderForm.paymentMethod,
                        }
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to place order');
                }

                if (data.success) {
                    alert('Order placed successfully! Order Code: ' + data.orderCode);
                    window.location.reload();
                }
            } catch (err) {
                console.error('Error placing order:', err);
                alert(err.message || 'Failed to place order');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Place Order';
                }
            }
        }

        function getSelectedProduct() {
            return products.find(p => p.id === selectedProductId);
        }

        function getTotalPrice() {
            const product = getSelectedProduct();
            return product ? (product.price * quantity).toFixed(2) : '0.00';
        }

        function getBasePrice() {
            const product = getSelectedProduct();
            return product ? (product.base_price ?? product.price) : 0;
        }

        function getDiscountPercent() {
            const product = getSelectedProduct();
            return product ? (product.discount_percent ?? 0) : 0;
        }

        function hasDiscount() {
            const product = getSelectedProduct();
            if (!product) return false;
            const base = product.base_price ?? product.price;
            return base > product.price;
        }

        function formatPriceDisplay(price) {
            return currencySymbol + parseFloat(price).toFixed(2);
        }

        function renderPriceHTML(product) {
            const basePrice = product.base_price ?? product.price;
            const discountedPrice = product.price;
            const discountPercent = product.discount_percent ?? 0;
            const isDiscounted = basePrice > discountedPrice;

            if (isDiscounted) {
                return `
                    <span class="text-sm line-through text-gray-400">${formatPriceDisplay(basePrice)}</span>
                    <span class="text-xl font-bold" style="color: var(--landing-primary-color, #E68A3D);">${formatPriceDisplay(discountedPrice)}</span>
                    <span class="text-xs font-bold px-1.5 py-0.5 rounded text-white ml-1" style="background-color: var(--landing-primary-color, #E68A3D);">${discountPercent}% OFF</span>
                `;
            }
            return `<span class="text-xl font-bold text-primary">${formatPriceDisplay(discountedPrice)}</span>`;
        }

        function updateTotalPrice() {
            const totalElement = document.getElementById('total-price');
            if (totalElement) {
                totalElement.textContent = currencySymbol + getTotalPrice();
            }
            // Also update the strikethrough base price if it exists
            const baseTotalEl = document.getElementById('base-total-price');
            if (baseTotalEl && hasDiscount()) {
                baseTotalEl.textContent = formatPriceDisplay(getBasePrice() * quantity);
            }
        }

        function render() {
            if (products.length === 0) {
                container.innerHTML = '<div class="text-center py-12"><p class="text-gray-600">No products available for ordering.</p></div>';
                return;
            }

            const selectedProduct = getSelectedProduct();
            const totalPrice = getTotalPrice();

            let html = `
                <div class="max-w-4xl mx-auto">
                    <!-- Product Selection -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-xl font-semibold mb-4">Select a Product</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            `;

            products.forEach(product => {
                const isSelected = selectedProductId === product.id;
                html += `
                    <div class="border rounded-lg p-4 cursor-pointer transition-all ${isSelected ? 'border-primary bg-primary/5 ring-2 ring-primary/20' : 'border-gray-200 hover:border-gray-300'}" onclick="handleProductChange(${product.id})">
                        ${product.image ? `<img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover rounded mb-3">` : ''}
                        <h4 class="font-medium text-lg mb-2">${product.name}</h4>
                        <p class="text-sm text-gray-500 mb-2">SKU: ${product.sku}</p>
                        <div class="flex items-center gap-2 flex-wrap">${renderPriceHTML(product)}</div>
                        ${isSelected ? '<div class="mt-3 flex items-center gap-2 text-primary"><i class="fa-solid fa-check-circle"></i><span class="text-sm font-medium">Selected</span></div>' : ''}
                    </div>
                `;
            });

            html += `
                        </div>
                    </div>
            `;

            if (selectedProduct) {
                html += `
                    <!-- Quantity Selection -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-xl font-semibold mb-4">Quantity</h3>
                        <div class="flex items-center gap-4">
                            <button onclick="quantity = Math.max(1, quantity - 1); document.getElementById('quantity-input').value = quantity; updateTotalPrice();" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <input type="number" id="quantity-input" min="1" value="${quantity}" onchange="handleQuantityChange(event)" class="w-24 h-10 text-center border border-gray-300 rounded">
                            <button onclick="quantity++; document.getElementById('quantity-input').value = quantity; updateTotalPrice();" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded hover:bg-gray-50">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <div class="ml-auto text-right">
                                <p class="text-sm text-gray-600">Total Price</p>
                                ${hasDiscount() ? `<p class="text-xs line-through text-gray-400">${formatPriceDisplay(getBasePrice() * quantity)}</p>` : ''}
                                <p class="text-2xl font-bold text-primary" id="total-price">${currencySymbol}${getTotalPrice()}</p>
                                ${hasDiscount() ? `<span class="text-xs font-bold px-1.5 py-0.5 rounded text-white" style="background-color: var(--landing-primary-color, #E68A3D);">${getDiscountPercent()}% OFF</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }

            if (!showOrderForm && selectedProduct) {
                html += `
                    <div class="text-center">
                        <button onclick="showOrderForm = true; render();" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-primary/90 text-lg">
                            Proceed to Order
                        </button>
                    </div>
                `;
            }

            if (showOrderForm) {
                html += `
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-semibold">Customer Details</h3>
                            <button onclick="showOrderForm = false; render();" class="text-gray-500 hover:text-gray-700">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <form onsubmit="handleSubmitOrder(event)" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required value="${orderForm.name}" onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="${orderForm.email}" onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                                    <input type="tel" name="phone" required value="${orderForm.phone}" onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alternative Phone</label>
                                    <input type="tel" name="alternativePhone" value="${orderForm.alternativePhone}" onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address <span class="text-red-500">*</span></label>
                                <textarea name="shippingAddress" required rows="3" onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Please Enter Your Details Delivery Address In English (or Banglish eg - Amar Sonar Bangla). দয়াকরে আপনার ডেলিভারি এড্রেস ইংরেজিতে (ইংরেজি ফন্টে/বাংলিশ যেমন - Aamar Sonar Bangla) লিখুন।">${orderForm.shippingAddress}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                                <select name="paymentMethod" required onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="cash_on_delivery" ${orderForm.paymentMethod === 'cash_on_delivery' ? 'selected' : ''}>Cash on Delivery</option>
                                    <option value="card_on_delivery" ${orderForm.paymentMethod === 'card_on_delivery' ? 'selected' : ''}>Card on Delivery</option>
                                    <option value="stripe" ${orderForm.paymentMethod === 'stripe' ? 'selected' : ''}>Stripe</option>
                                    <option value="paypal" ${orderForm.paymentMethod === 'paypal' ? 'selected' : ''}>PayPal</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                                <textarea name="note" rows="2" onchange="handleInputChange(event)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">${orderForm.note}</textarea>
                            </div>

                            <div class="bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold mb-2">Order Summary</h4>
                                <div class="flex justify-between mb-2">
                                    <span>Product:</span>
                                    <span>${selectedProduct?.name || ''}</span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span>Quantity:</span>
                                    <span>${quantity}</span>
                                </div>
                                <div class="flex justify-between mb-2">
                                    <span>Price:</span>
                                    ${hasDiscount() ? `
                                        <span>
                                            <span class="text-sm line-through text-gray-400">${formatPriceDisplay(getBasePrice())}</span>
                                            <span class="font-semibold" style="color: var(--landing-primary-color, #E68A3D);">${formatPriceDisplay(selectedProduct?.price || 0)}</span>
                                            <span class="text-xs font-bold px-1 py-0.5 rounded text-white" style="background-color: var(--landing-primary-color, #E68A3D);">${getDiscountPercent()}% OFF</span>
                                        </span>
                                    ` : `
                                        <span>${formatPriceDisplay(selectedProduct?.price || 0)}</span>
                                    `}
                                </div>
                                <div class="flex justify-between font-bold text-lg border-t pt-2">
                                    <span>Total:</span>
                                    ${hasDiscount() ? `
                                        <span>
                                            <span style="color: var(--landing-primary-color, #E68A3D);">${currencySymbol}${getTotalPrice()}</span>
                                        </span>
                                    ` : `
                                        <span class="text-primary">${currencySymbol}${getTotalPrice()}</span>
                                    `}
                                </div>
                            </div>

                            <button type="submit" id="submit-btn" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary/90">
                                Place Order
                            </button>
                        </form>
                    </div>
                `;
            }

            html += '</div>';

            container.innerHTML = html;
        }

        // Expose functions to global scope
        window.handleProductChange = handleProductChange;
        window.handleQuantityChange = handleQuantityChange;
        window.handleInputChange = handleInputChange;
        window.handleSubmitOrder = handleSubmitOrder;
        window.updateTotalPrice = updateTotalPrice;
    });
</script>
@endpush
