@extends('layouts.admin')

@section('title')
    {{ translate('Edit Order') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Edit Order') }}
            </span>
        </div>
        <div>
            <a href="{{ route('admin.orders.show', $order->order_code) }}" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-eye mr-2"></i>
                {{ translate('View Order') }}
            </a>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <form action="{{ route('admin.orders.updateOrder', $order->order_code) }}" method="POST">
        @csrf
        <div class="grid 4xl:grid-cols-4 gap-10">
            <div class="4xl:col-span-3 space-y-10">
                <div class="card">
                    <h4 class="card__title">{{ translate('Order Information') }}</h4>

                    <div class="grid lg:grid-cols-2 xl:grid-cols-10 max-lg:divide-y xl:divide-x divide-border">
                        <div class="xl:col-span-3 py-8 px-3 lg:px-6">
                            <p class="text-muted">{{ translate('INVOICE') }}</p>
                            <p class="text-xl ">{{ getSetting('orderCodePrefix') }}{{ $order->order_code }}</p>

                            <p class="mt-4">
                                <span class="mr-3 text-xs text-muted">{{ translate('Order Date') }}:</span>
                                {{ date('d M, Y', strtotime($order->created_at)) }}
                            </p>

                            <p class="mt-10 mb-5 text-muted">{{ translate('CUSTOMER INFORMATION') }}</p>
                            <table class="w-full [&_td]:py-2">
                                <tr>
                                    <td class="text-xs text-muted">{{ translate('Name') }}:</td>
                                    <td class="text-sm">
                                        <x-backend.inputs.text name="customer_name" value="{{ $orderGroup->name }}" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-xs text-muted">{{ translate('Phone') }}:</td>
                                    <td class="text-sm">
                                        <x-backend.inputs.text name="customer_phone" value="{{ $orderGroup->phone }}" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-xs text-muted">{{ translate('Email') }}:</td>
                                    <td class="text-sm">
                                        <x-backend.inputs.text name="customer_email" value="{{ $orderGroup->email }}" />
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="xl:col-span-4 py-8 px-3 lg:px-9">
                            <p class="text-muted mb-6">{{ translate('DELIVERY INFORMATION') }}</p>

                            <div class="space-y-7">
                                <div>
                                    <p class="text-muted text-xs mb-2">{{ translate('BILLING ADDRESS') }}</p>
                                    <x-backend.inputs.textarea name="billing_address"
                                        rows="3">{{ $orderGroup->billing_address }}</x-backend.inputs.textarea>
                                </div>
                                <div>
                                    <p class="text-muted text-xs mb-2">{{ translate('SHIPPING ADDRESS') }}</p>
                                    <x-backend.inputs.textarea name="shipping_address"
                                        rows="3">{{ $orderGroup->shipping_address }}</x-backend.inputs.textarea>
                                </div>
                                <div>
                                    <p class="text-muted text-xs mb-2">{{ translate('Direction') }}</p>
                                    <x-backend.inputs.text name="direction" value="{{ $orderGroup->direction }}" />
                                </div>

                                <div>
                                    <p class="text-muted text-xs mb-2">{{ translate('Payment Type') }}</p>
                                    <x-backend.inputs.select name="payment_method" groupClass="mb-0">
                                        <x-backend.inputs.select-option value="cash_on_delivery"
                                            selected="{{ $orderGroup->transaction->payment_method }}"
                                            name="Cash On Delivery" />
                                        <x-backend.inputs.select-option value="card_on_delivery"
                                            selected="{{ $orderGroup->transaction->payment_method }}"
                                            name="Card On Delivery" />
                                        <x-backend.inputs.select-option value="paypal"
                                            selected="{{ $orderGroup->transaction->payment_method }}" name="Paypal" />
                                        <x-backend.inputs.select-option value="stripe"
                                            selected="{{ $orderGroup->transaction->payment_method }}" name="Stripe" />
                                    </x-backend.inputs.select>
                                </div>
                            </div>
                        </div>
                        <div class="xl:col-span-3 py-8 px-3 lg:px-9 ">
                            <p class="text-muted mb-6">{{ translate('ORDER TRACKING') }}</p>

                            <div class="space-y-3">
                                <div>
                                    <x-backend.inputs.text label="Courier Name" :labelInline="false" name="courier_name"
                                        placeholder="Type courier name" value="{{ $order->courier_name }}" />
                                </div>
                                <div>
                                    <x-backend.inputs.text label="Tracking Number" :labelInline="false"
                                        name="tracking_number" placeholder="Type tracking number"
                                        value="{{ $order->tracking_number }}" />
                                </div>
                                <div>
                                    <x-backend.inputs.text label="Tracking URL" :labelInline="false" name="tracking_url"
                                        placeholder="Type tracking url" value="{{ $order->tracking_url }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="card__title border-b-0">{{ translate('Product Details') }}</h4>
                        <button type="button" onclick="addProductRow()" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-plus mr-2"></i>
                            {{ translate('Add Product') }}
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="footable w-full" id="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 35%">{{ translate('PRODUCT') }}</th>
                                    <th style="width: 12%">{{ translate('UNIT PRICE') }}</th>
                                    <th style="width: 10%">{{ translate('QTY') }}</th>
                                    <th style="width: 12%">{{ translate('DISCOUNT') }}</th>
                                    <th style="width: 12%">{{ translate('TAX %') }}</th>
                                    <th style="width: 12%" class="text-right">{{ translate('TOTAL') }}</th>
                                    <th style="width: 7%" class="text-center">{{ translate('ACTION') }}</th>
                                </tr>
                            </thead>

                            <tbody class="[&_td]:py-3" id="product-items">
                                @foreach ($orderItems as $index => $item)
                                    @php
                                        $variation = $item->productVariation;
                                        $product = $variation->product;
                                    @endphp
                                    <tr data-item-id="{{ $item->id }}" class="product-row">
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="hidden" name="items[{{ $index }}][product_variation_id]"
                                                value="{{ $variation->id }}">
                                            <div class="inline-flex items-center gap-4">
                                                <div class="max-xs:hidden border border-border rounded-md p-2 aspect-square">
                                                    <img src="{{ uploadedAsset($product->thumbnail_image) }}"
                                                        class="w-12 h-12 lg:w-[70px] lg:h-[70px] rounded-md"
                                                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">
                                                </div>
                                                <div class="line-clamp-2 max-w-[230px]">
                                                    {{ $product->collectTranslation('name') }}
                                                    @if ($product->has_variation)
                                                        - {{ generateVariationName($variation->code) }}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <x-backend.inputs.text type="number" step="0.01"
                                                name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}"
                                                class="w-24 item-unit-price" onchange="calculateRowTotal(this)" />
                                        </td>
                                        <td>
                                            <x-backend.inputs.text type="number" name="items[{{ $index }}][qty]"
                                                value="{{ $item->qty }}" class="w-20 item-qty"
                                                onchange="calculateRowTotal(this)" />
                                        </td>
                                        <td>
                                            <x-backend.inputs.text type="number" step="0.01"
                                                name="items[{{ $index }}][discount]" value="{{ $item->total_discount ?? 0 }}"
                                                class="w-24 item-discount" onchange="calculateRowTotal(this)" />
                                        </td>
                                        <td>
                                            @php
                                                $itemSubtotal = ($item->unit_price * $item->qty) - ($item->total_discount ?? 0);
                                                $calculatedTaxPercentage = $itemSubtotal > 0 ? ($item->total_tax / $itemSubtotal) * 100 : 0;
                                            @endphp
                                            <x-backend.inputs.text type="number" step="0.01"
                                                name="items[{{ $index }}][tax_percentage]"
                                                value="{{ round($calculatedTaxPercentage, 2) }}" class="w-20 item-tax"
                                                onchange="calculateRowTotal(this)" />
                                        </td>
                                        <td class="text-right font-medium">
                                            <span class="item-total">{{ formatPrice($item->total_price) }}</span>
                                            <input type="hidden" name="items[{{ $index }}][total_price]"
                                                value="{{ $item->total_price }}" class="item-total-input">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" onclick="removeProductRow(this)"
                                                class="text-red-600 hover:text-red-800 text-lg" title="Remove">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="max-w-[400px] space-y-4 md:space-y-6 ml-auto pr-4 lg:pr-6 xl:pr-12 mt-6">
                        <div class="flex justify-between uppercase">
                            <span>{{ translate('Subtotal') }}</span>
                            <span id="order-subtotal">{{ formatPrice($order->amount) }}</span>
                        </div>

                        <div class="flex justify-between uppercase">
                            <span>{{ translate('TAX') }}</span>
                            <span id="order-tax">{{ formatPrice($order->tax_amount) }}</span>
                        </div>
                        <div class="flex justify-between uppercase items-center">
                            <span>{{ translate('Delivery charge') }}</span>
                            <x-backend.inputs.text type="number" step="0.01" name="shipping_charge"
                                value="{{ $order->shipping_charge_amount }}" class="w-28" id="shipping-charge"
                                onchange="calculateOrderTotal()" />
                        </div>
                        <div class="flex justify-between uppercase items-center">
                            <span>{{ translate('Order Discount') }}</span>
                            <x-backend.inputs.text type="number" step="0.01" name="discount_amount"
                                value="{{ $order->discount_amount }}" class="w-28" id="order-discount"
                                onchange="calculateOrderTotal()" />
                        </div>
                        @if ($order->coupon_id)
                            <div class="flex justify-between uppercase">
                                <span>{{ translate('Coupon Discount') }}</span>
                                <span>{{ formatPrice($order->coupon_discount_amount) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between uppercase border-t pt-4">
                            <span class="font-bold">{{ translate('TOTAL') }}</span>
                            <span class="text-base font-bold text-secondary" id="order-total">
                                {{ formatPrice($order->total_amount) }}
                            </span>
                        </div>
                    </div>

                    <br>
                </div>
            </div>

            <div class="col-span-1">
                <div class="card">
                    <h4 class="card__title">{{ translate('Order Status') }}</h4>

                    <div class="card__content py-6">
                        <x-backend.inputs.select label="Payment Status" name="payment_status" wrapperClass="col-span-1"
                            groupClass="grid-cols-2 mb-2">
                            <x-backend.inputs.select-option value="paid" selected="{{ $order->payment_status }}"
                                name="Paid" />
                            <x-backend.inputs.select-option value="unpaid" selected="{{ $order->payment_status }}"
                                name="Unpaid" />
                        </x-backend.inputs.select>

                        <x-backend.inputs.select label="Delivery Status" name="delivery_status" wrapperClass="col-span-1"
                            groupClass="grid-cols-2 mb-2">
                            <x-backend.inputs.select-option value="order_placed" selected="{{ $order->delivery_status }}"
                                name="Order Placed" />
                            <x-backend.inputs.select-option value="processing" selected="{{ $order->delivery_status }}"
                                name="Processing" />
                            <x-backend.inputs.select-option value="confirmed" selected="{{ $order->delivery_status }}"
                                name="Confirmed" />
                            <x-backend.inputs.select-option value="shipped" selected="{{ $order->delivery_status }}"
                                name="Shipped" />
                            <x-backend.inputs.select-option value="delivered" selected="{{ $order->delivery_status }}"
                                name="Delivered" />
                            <x-backend.inputs.select-option value="cancelled" selected="{{ $order->delivery_status }}"
                                name="Cancelled" />
                        </x-backend.inputs.select>
                    </div>

                    <div class="card__content py-6 border-t border-border">
                        <x-backend.inputs.button type="submit" variant="primary" class="w-full">
                            <i class="fa-solid fa-save mr-2"></i>
                            {{ translate('UPDATE ORDER') }}
                        </x-backend.inputs.button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Product Search Modal -->
    <div id="product-search-modal" class="modal micromodal-slide" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container w-full max-w-4xl" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">{{ translate('Search Products') }}</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <div class="mb-4">
                        <input type="text" id="product-search-input" class="form-control"
                            placeholder="{{ translate('Search by product name...') }}" />
                    </div>
                    <div id="product-search-results" class="max-h-96 overflow-y-auto">
                        <!-- Results will be loaded here -->
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        "use strict";

        let rowIndex = {{ count($orderItems) }};
        const currencySymbol = '{{ getSetting('currencySymbol') ?? '$' }}';

        // Calculate row total
        function calculateRowTotal(element) {
            const row = element.closest('tr');
            const unitPrice = parseFloat(row.querySelector('.item-unit-price input')?.value || 0);
            const qty = parseFloat(row.querySelector('.item-qty input')?.value || 0);
            const discount = parseFloat(row.querySelector('.item-discount input')?.value || 0);
            const taxPercentage = parseFloat(row.querySelector('.item-tax input')?.value || 0);

            // Calculate subtotal after discount
            const subtotal = (unitPrice * qty) - discount;

            // Calculate tax
            const taxAmount = (subtotal * taxPercentage) / 100;

            // Calculate total
            const total = subtotal + taxAmount;

            // Update row total display
            row.querySelector('.item-total').textContent = formatPrice(total);
            row.querySelector('.item-total-input').value = total.toFixed(2);

            // Recalculate order total
            calculateOrderTotal();
        }

        // Calculate order total
        function calculateOrderTotal() {
            let subtotal = 0;
            let totalTax = 0;

            // Sum all product rows
            document.querySelectorAll('.product-row').forEach(row => {
                const unitPrice = parseFloat(row.querySelector('.item-unit-price input')?.value || 0);
                const qty = parseFloat(row.querySelector('.item-qty input')?.value || 0);
                const discount = parseFloat(row.querySelector('.item-discount input')?.value || 0);
                const taxPercentage = parseFloat(row.querySelector('.item-tax input')?.value || 0);

                const itemSubtotal = (unitPrice * qty) - discount;
                const itemTax = (itemSubtotal * taxPercentage) / 100;

                subtotal += itemSubtotal;
                totalTax += itemTax;
            });

            const shippingCharge = parseFloat(document.getElementById('shipping-charge')?.querySelector('input')?.value || 0);
            const orderDiscount = parseFloat(document.getElementById('order-discount')?.querySelector('input')?.value || 0);
            const couponDiscount = {{ $order->coupon_discount_amount ?? 0 }};

            const grandTotal = subtotal + totalTax + shippingCharge - orderDiscount - couponDiscount;

            // Update displays
            document.getElementById('order-subtotal').textContent = formatPrice(subtotal);
            document.getElementById('order-tax').textContent = formatPrice(totalTax);
            document.getElementById('order-total').textContent = formatPrice(grandTotal);
        }

        // Format price
        function formatPrice(amount) {
            return currencySymbol + parseFloat(amount).toFixed(2);
        }

        // Remove product row
        function removeProductRow(button) {
            if (confirm('{{ translate('Are you sure you want to remove this product?') }}')) {
                button.closest('tr').remove();
                calculateOrderTotal();
            }
        }

        // Add product row
        function addProductRow() {
            try {
                MicroModal.show('product-search-modal');
                searchProducts('');
            } catch (e) {
                console.error('Error opening modal:', e);
                // Try initializing if it failed
                if (typeof MicroModal !== 'undefined') {
                    MicroModal.init();
                    MicroModal.show('product-search-modal');
                }
            }
        }

        // Search products
        let searchTimeout;
        function searchProducts(query) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                console.log('Searching for:', query);
                fetch(`/admin/api/products/search?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        console.log('Search results:', data);
                        displaySearchResults(data.products || []);
                    })
                    .catch(error => {
                        console.error('Error searching products:', error);
                        document.getElementById('product-search-results').innerHTML = '<p class="text-center text-red-500 py-4">{{ translate('Error searching products') }}</p>';
                    });
            }, 300);
        }

        // Display search results
        function displaySearchResults(products) {
            const resultsContainer = document.getElementById('product-search-results');

            if (products.length === 0) {
                resultsContainer.innerHTML = '<p class="text-center text-gray-500 py-4">{{ translate('No products found') }}</p>';
                return;
            }

            let html = '<div class="space-y-2">';
            products.forEach(product => {
                product.variations.forEach(variation => {
                    html += `
                                <div class="flex items-center justify-between p-3 border rounded hover:bg-gray-50 cursor-pointer" 
                                     onclick="addProductToOrder(${variation.id}, '${product.name}', '${variation.name || ''}', ${variation.price}, '${product.image}')">
                                    <div class="flex items-center gap-3">
                                        <img src="${product.image}" class="w-12 h-12 rounded object-cover" onerror="this.src='{{ asset('images/image-error.png') }}'">
                                        <div>
                                            <div class="font-medium">${product.name}</div>
                                            ${variation.name ? `<div class="text-sm text-gray-500">${variation.name}</div>` : ''}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold">${formatPrice(variation.price)}</div>
                                        <div class="text-sm text-gray-500">Stock: ${variation.stock}</div>
                                    </div>
                                </div>
                            `;
                });
            });
            html += '</div>';

            resultsContainer.innerHTML = html;
        }

        // Add product to order
        function addProductToOrder(variationId, productName, variationName, price, image) {
            const tbody = document.getElementById('product-items');
            const newRow = document.createElement('tr');
            newRow.className = 'product-row';
            newRow.innerHTML = `
                        <td>
                            <input type="hidden" name="items[${rowIndex}][product_variation_id]" value="${variationId}">
                            <input type="hidden" name="items[${rowIndex}][is_new]" value="1">
                            <div class="inline-flex items-center gap-4">
                                <div class="border border-border rounded-md p-2 aspect-square">
                                    <img src="${image}" class="w-12 h-12 lg:w-[70px] lg:h-[70px] rounded-md" onerror="this.src='{{ asset('images/image-error.png') }}'">
                                </div>
                                <div class="line-clamp-2 max-w-[230px]">
                                    ${productName}${variationName ? ' - ' + variationName : ''}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="item-unit-price">
                                <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" value="${price}" 
                                       class="form-control w-24" onchange="calculateRowTotal(this)" />
                            </div>
                        </td>
                        <td>
                            <div class="item-qty">
                                <input type="number" name="items[${rowIndex}][qty]" value="1" 
                                       class="form-control w-20" onchange="calculateRowTotal(this)" />
                            </div>
                        </td>
                        <td>
                            <div class="item-discount">
                                <input type="number" step="0.01" name="items[${rowIndex}][discount]" value="0" 
                                       class="form-control w-24" onchange="calculateRowTotal(this)" />
                            </div>
                        </td>
                        <td>
                            <div class="item-tax">
                                <input type="number" step="0.01" name="items[${rowIndex}][tax_percentage]" value="0" 
                                       class="form-control w-20" onchange="calculateRowTotal(this)" />
                            </div>
                        </td>
                        <td class="text-right font-medium">
                            <span class="item-total">${formatPrice(price)}</span>
                            <input type="hidden" name="items[${rowIndex}][total_price]" value="${price}" class="item-total-input">
                        </td>
                        <td class="text-center">
                            <button type="button" onclick="removeProductRow(this)" class="text-red-600 hover:text-red-800 text-lg" title="Remove">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    `;

            tbody.appendChild(newRow);
            rowIndex++;

            calculateOrderTotal();
            MicroModal.close('product-search-modal');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            // Product search input
            const searchInput = document.getElementById('product-search-input');
            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    searchProducts(e.target.value);
                });
            }
        });
    </script>
@endsection