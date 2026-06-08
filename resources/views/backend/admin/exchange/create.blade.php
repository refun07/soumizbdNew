@extends('layouts.' . $route)

@section('title')
    {{ translate('Create Exchange') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Create Exchange') }}
            </span>
        </div>
        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route($route . '.exchanges.index') }}" class="font-bold">{{ translate('Exchanges') }}</a>
            <span class="text-primary dark:text-muted"><i class="fa-solid fa-chevron-right"></i></span>
            <p class="text-muted">{{ translate('Create Exchange') }}</p>
        </div>
    </div>

    <div class="mb-6">
        <div class="group relative inline-block w-full max-w-md" tabindex="0">
            <x-backend.inputs.text label="{{ translate('Search Invoice') }}" class="exchange-order-search-input"
                name="search" value="{{ $searchKey }}" autocomplete="off"
                placeholder="{{ translate('Search with Invoice ID') }}" :isRequired="false" />
            <div class="exchange-order-search absolute z-[1] bg-background rounded-md right-0 top-[calc(100%)] w-full min-w-[300px] min-h-[150px] shadow max-h-[350px] overflow-y-auto p-[2px] divide-y divide-[#78787829] hidden group-focus-within:block transition-all duration-300">
                <div class="block py-4 px-6 bg-primary/[.03] text-muted font-bold">
                    <span class="uppercase">{{ translate('Search Results') }}</span>
                </div>
                <div class="divide-y divide-[#78787829] exchange-order-search-results"></div>
            </div>
        </div>
    </div>

    @if ($order)
        <form id="exchange-form">
            {{ csrf_field() }}
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="requested_by" value="0">
            <input type="hidden" name="shipping_charge" id="shipping-charge-hidden" value="{{ $order->shipping_charge_amount }}">

            <div class="grid md:grid-cols-12 gap-6">
                <div class="md:col-span-7">
                    <div class="card">
                        <div class="card__title border-b pb-3 mb-0">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-sm">
                                    <i class="fa-solid fa-arrow-right-arrow-left mr-1"></i>
                                    {{ translate('Items Returned') }}
                                </h3>
                                <span class="text-xs text-muted">{{ $order->orderItems->count() }} {{ translate('items') }}</span>
                            </div>
                        </div>
                        <div class="card__content p-0">
                            <div class="theme-table">
                                <table class="footable w-full [&_td]:">
                                    <thead class="uppercase text-left bg-primary/10 text-xs">
                                        <tr>
                                            <th data-breakpoints="xs" class="w-10">
                                                <x-backend.inputs.checkbox name="select_all" class="select-all-original-items" />
                                            </th>
                                            <th>{{ translate('Product') }}</th>
                                            <th data-breakpoints="xs sm">{{ translate('Variation') }}</th>
                                            <th data-breakpoints="xs sm" class="text-left">{{ translate('QTY') }}</th>
                                            <th data-breakpoints="xs sm" class="text-right">{{ translate('Price / Subtotal') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $index => $item)
                                            @php
                                                $variation = $item->productVariation;
                                                $product = $variation?->product;
                                                if (!$variation || !$product) continue;
                                                $itemPrice = variationDiscountedPrice($product, $variation, false);
                                            @endphp
                                            <tr class="original-item-row" data-order-item-id="{{ $item->id }}"
                                                data-unit-price="{{ $itemPrice }}" data-max-qty="{{ $item->qty }}">
                                                <td>
                                                    <x-backend.inputs.checkbox
                                                        name="items[{{ $index }}][original_order_item_id]"
                                                        value="{{ $item->id }}"
                                                        class="original-item-checkbox"
                                                        data-unit-price="{{ $itemPrice }}"
                                                        data-max-qty="{{ $item->qty }}" />
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-3">
                                                        <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt=""
                                                            class="w-10 h-10 rounded-md object-cover"
                                                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                                        <div>
                                                            <p class="text-sm font-medium line-clamp-2">{{ $product->name }}</p>
                                                            <p class="text-xs text-muted">{{ $product->sku }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-xs text-muted">{{ $variation->name }}</span>
                                                </td>
                                                <td>
                                                    <x-backend.inputs.number class="original-qty-input w-16"
                                                        name="items[{{ $index }}][quantity]"
                                                        value="1" max="{{ $item->qty }}" min="1" :isRequired="false" />
                                                </td>
                                                <td class="text-right">
                                                    <div class="price-subtotal-cell">
                                                        <span class="text-sm">{{ formatPrice($itemPrice) }}</span>
                                                        <span class="text-sm font-medium original-item-subtotal">{{ formatPrice($itemPrice) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-5">
                    <div class="card">
                        <div class="card__title border-b pb-3 mb-4">
                            <h3 class="font-bold text-sm">
                                <i class="fa-solid fa-arrow-left-arrow-right mr-1"></i>
                                {{ translate('Items to Receive') }}
                            </h3>
                        </div>
                        <div class="card__content">
                            <div class="mb-4 relative">
                                <x-backend.inputs.text label="{{ translate('Search Products') }}"
                                    class="exchange-product-search-input"
                                    name="product_search" autocomplete="off"
                                    placeholder="{{ translate('Search by name or SKU') }}" :isRequired="false" />
                                <div class="exchange-product-search-results absolute z-[1] bg-background rounded-md w-full min-w-[300px] shadow max-h-[300px] overflow-y-auto hidden divide-y divide-[#78787829]"></div>
                            </div>

                            <div id="exchange-items-container" class="space-y-3 min-h-[80px]">
                                <div class="text-center text-muted py-8 empty-exchange-cart">
                                    <i class="fa-solid fa-box-open text-3xl mb-2 text-primary/40"></i>
                                    <p class="text-xs">{{ translate('Search and add products to receive') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card__title border-b pb-3 mb-4">
                            <h3 class="font-bold text-sm">
                                <i class="fa-solid fa-calculator mr-1"></i>
                                {{ translate('Price Summary') }}
                            </h3>
                        </div>
                        <div class="card__content">
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted">{{ translate('Returned Items Total') }}</span>
                                    <span class="font-medium" id="original-total">{{ formatPrice(0) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-muted">{{ translate('Exchange Items Total') }}</span>
                                    <span class="font-medium" id="exchange-total">{{ formatPrice(0) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-muted">{{ translate('Shipping Charge') }}</span>
                                    <div class="flex items-center gap-2">
                                        <x-backend.inputs.number class="w-24" id="shipping-charge-input"
                                            name="shipping_charge_display" value="{{ $order->shipping_charge_amount }}" min="0" :isRequired="false" />
                                    </div>
                                </div>
                                <hr class="border-border">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-sm">{{ translate('Difference') }}</span>
                                    <span id="price-difference" class="font-bold text-base">{{ formatPrice(0) }}</span>
                                </div>
                                <div id="difference-label" class="text-center text-xs py-1.5 rounded bg-primary/5 hidden">
                                    <span id="difference-label-text"></span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-backend.inputs.button buttonText="{{ translate('Create Exchange') }}"
                                    type="button" class="submit-exchange-btn w-full" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div id="variation-modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/50" id="variation-modal-overlay"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-background rounded-lg shadow-xl">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="font-bold text-sm">{{ translate('Select Variation') }}</h3>
                    <button type="button" id="close-variation-modal" class="text-muted hover:text-primary transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-4 max-h-[400px] overflow-y-auto">
                    <div id="variation-modal-content" class="space-y-2"></div>
                </div>
                <div class="p-4 border-t flex justify-end gap-3">
                    <x-backend.inputs.button buttonText="{{ translate('Cancel') }}"
                        type="button" class="bg-neutral-200 hover:bg-neutral-300 text-neutral-700" id="cancel-variation-modal" />
                    <x-backend.inputs.button buttonText="{{ translate('Add to List') }}"
                        type="button" class="confirm-variation-btn" id="confirm-variation-modal" />
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="p-12 text-center text-muted">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass text-2xl text-primary/60"></i>
                </div>
                <p class="font-medium">{{ translate('Search for an invoice to start an exchange') }}</p>
                <p class="text-xs mt-1 text-primary/60">{{ translate('Enter an invoice ID above to begin') }}</p>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        "use strict";

        const exchangeRoutePrefix = '{{ routePrefix() }}';
        const exchangeCatalog = {};
        let exchangeItemCounter = 0;
        let pendingProduct = null;
        let selectedVariationKey = null;

        @if ($order)
        const orderItemsData = {!! $order->orderItems->filter(function ($item) { return $item->productVariation && $item->product; })->mapWithKeys(function ($item) { return [$item->id => ['variationId' => $item->product_variation_id, 'unitPrice' => variationDiscountedPrice($item->product, $item->productVariation, false)]]; })->toJson() !!};
        @else
        const orderItemsData = {};
        @endif

        document.addEventListener('DOMContentLoaded', function() {
            initOrderSearch();
            initProductSearch();
            initFormHandlers();
            initVariationModal();
        });

        function initOrderSearch() {
            const searchInput = document.querySelector('.exchange-order-search-input');
            const resultsContainer = document.querySelector('.exchange-order-search-results');
            if (!searchInput || !resultsContainer) return;

            let debounceTimer;
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                if (query.length < 2) {
                    resultsContainer.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`/${exchangeRoutePrefix}/exchanges/order-search`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value,
                        },
                        body: JSON.stringify({ searchKey: query }),
                    })
                    .then(r => r.text())
                    .then(html => {
                        resultsContainer.innerHTML = html;
                    });
                }, 300);
            });

            resultsContainer.addEventListener('click', function(e) {
                const resultItem = e.target.closest('.exchange-order-result');
                if (resultItem) {
                    const code = resultItem.dataset.code;
                    window.location.href = `/${exchangeRoutePrefix}/exchanges/create?search=${encodeURIComponent(code)}`;
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.innerHTML = '';
                }
            });
        }

        function initProductSearch() {
            const searchInput = document.querySelector('.exchange-product-search-input');
            const resultsContainer = document.querySelector('.exchange-product-search-results');
            if (!searchInput || !resultsContainer) return;

            let debounceTimer;
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') e.preventDefault();
            });
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.value.trim();
                    if (query.length < 2) {
                        resultsContainer.classList.add('hidden');
                        return;
                    }

                    fetch(`/${exchangeRoutePrefix}/exchanges/search-products?q=${encodeURIComponent(query)}`)
                        .then(r => r.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            if (data.result && data.result.length > 0) {
                                data.result.forEach(product => {
                                    const div = document.createElement('div');
                                    div.className = 'flex items-center gap-3 p-3 cursor-pointer hover:bg-primary/10 transition-colors';
                                    div.innerHTML = `
                                        <img src="${product.thumbnail}" class="w-10 h-10 rounded object-cover" onerror="this.src='{{ asset('images/image-error.png') }}'">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium line-clamp-1">${product.name}</p>
                                            <p class="text-xs text-muted">${product.sku || ''}</p>
                                        </div>
                                        ${product.has_variations ? '<span class="text-xs text-primary bg-primary/10 px-2 py-0.5 rounded">' + '{{ translate("Has Variations") }}' + '</span>' : ''}
                                    `;
                                    div.addEventListener('click', (e) => {
                                        e.stopPropagation();
                                        resultsContainer.classList.add('hidden');
                                        if (product.has_variations) {
                                            loadProductVariations(product.id, product.name, product.thumbnail, true);
                                        } else {
                                            loadProductVariations(product.id, product.name, product.thumbnail, false);
                                        }
                                    });
                                    resultsContainer.appendChild(div);
                                });
                                resultsContainer.classList.remove('hidden');
                            } else {
                                resultsContainer.innerHTML = '<div class="p-4 text-center text-muted text-sm">{{ translate("No products found") }}</div>';
                                resultsContainer.classList.remove('hidden');
                            }
                        });
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.classList.add('hidden');
                }
            });
        }

        async function loadProductVariations(productId, productName, productThumbnail, hasVariations) {
            try {
                const url = `/${exchangeRoutePrefix}/exchanges/product-variations/${productId}`;
                const response = await fetch(url);
                if (!response.ok) return;
                const data = await response.json();

                const variations = data.result || [];
                if (variations.length === 0) return;

                if (hasVariations && variations.length > 1) {
                    showVariationModal(productId, productName, productThumbnail, variations);
                    return;
                }

                const variation = variations[0];
                addExchangeItem(productId, productName, productThumbnail, variation);
            } catch (err) {
                console.error('loadProductVariations error:', err);
            }
        }

        function showVariationModal(productId, productName, productThumbnail, variations) {
            pendingProduct = { productId, productName, productThumbnail };
            selectedVariationKey = null;

            const modal = document.getElementById('variation-modal');
            const content = document.getElementById('variation-modal-content');

            content.innerHTML = '';
            variations.forEach((variation, index) => {
                const div = document.createElement('div');
                div.className = 'variation-option flex items-center gap-3 p-3 border rounded cursor-pointer hover:bg-primary/10 transition-colors';
                div.dataset.key = `pending_${index}`;
                div.dataset.variationId = variation.id;
                div.dataset.variationPrice = variation.price;
                div.dataset.variationLabel = variation.label;

                div.innerHTML = `
                    <img src="${productThumbnail}" class="w-10 h-10 rounded object-cover" onerror="this.src='{{ asset('images/image-error.png') }}'">
                    <div class="flex-1">
                        <p class="text-sm font-medium">${variation.label}</p>
                        <p class="text-xs text-muted">${formatCurrency(variation.price)}</p>
                    </div>
                `;

                div.addEventListener('click', () => {
                    content.querySelectorAll('.variation-option').forEach(el => el.classList.remove('border-primary', 'bg-primary/10'));
                    div.classList.add('border-primary', 'bg-primary/10');
                    selectedVariationKey = `pending_${index}`;
                });

                content.appendChild(div);
            });

            modal.classList.remove('hidden');
        }

        function initVariationModal() {
            const modal = document.getElementById('variation-modal');
            const overlay = document.getElementById('variation-modal-overlay');
            const closeBtn = document.getElementById('close-variation-modal');
            const cancelBtn = document.getElementById('cancel-variation-modal');
            const confirmBtn = document.getElementById('confirm-variation-modal');

            if (!modal) return;

            const closeModal = () => {
                modal.classList.add('hidden');
                pendingProduct = null;
                selectedVariationKey = null;
            };

            overlay?.addEventListener('click', closeModal);
            closeBtn?.addEventListener('click', closeModal);
            cancelBtn?.addEventListener('click', closeModal);

            confirmBtn?.addEventListener('click', () => {
                if (!selectedVariationKey || !pendingProduct) return;

                const content = document.getElementById('variation-modal-content');
                const selectedEl = content.querySelector(`[data-key="${selectedVariationKey}"]`);
                if (!selectedEl) return;

                const variation = {
                    id: selectedEl.dataset.variationId,
                    price: selectedEl.dataset.variationPrice,
                    label: selectedEl.dataset.variationLabel,
                };

                addExchangeItem(pendingProduct.productId, pendingProduct.productName, pendingProduct.productThumbnail, variation);
                closeModal();
            });
        }

        function addExchangeItem(productId, productName, productThumbnail, variation) {
            const container = document.getElementById('exchange-items-container');
            const emptyCart = container.querySelector('.empty-exchange-cart');

            if (emptyCart) emptyCart.remove();

            exchangeItemCounter++;
            const key = `ex_${exchangeItemCounter}`;

            const div = document.createElement('div');
            div.className = 'exchange-item-entry flex items-start gap-3 p-3 bg-primary/5 rounded-md hover:bg-primary/10 transition-colors border border-transparent';
            div.dataset.exchangeKey = key;
            div.dataset.exchangeVariationId = variation.id;
            div.dataset.exchangeUnitPrice = variation.price;
            div.innerHTML = `
                <div class="flex-shrink-0">
                    <img src="${productThumbnail}" class="w-12 h-12 rounded object-cover" onerror="this.src='{{ asset('images/image-error.png') }}'">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium line-clamp-1">${productName}</p>
                    <p class="text-xs text-muted mt-0.5">${variation.label}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <label class="text-xs text-muted">{{ translate('Qty') }}</label>
                        <input type="number" value="1" min="1" class="exchange-item-qty w-16 border rounded px-2 py-1 bg-white dark:bg-neutral-800 dark:border-neutral-600 text-sm text-center" data-key="${key}">
                        <span class="text-sm font-bold text-primary ml-auto exchange-item-price">${formatCurrency(variation.price)}</span>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <span class="text-xs font-medium text-muted exchange-item-subtotal">${formatCurrency(variation.price)}</span>
                    <button type="button" class="text-muted hover:text-red-500 transition-colors remove-exchange-item" data-key="${key}">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);

            exchangeCatalog[key] = {
                variationId: variation.id,
                unitPrice: parseFloat(variation.price) || 0,
                label: variation.label,
                productName: productName,
                thumbnail: productThumbnail,
            };

            document.querySelector('.exchange-product-search-input').value = '';
            document.querySelector('.exchange-product-search-results').classList.add('hidden');

            recalculateTotals();
        }

        function initFormHandlers() {
            const selectAllCheckbox = document.querySelector('.select-all-original-items');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.original-item-checkbox');
                    checkboxes.forEach(cb => {
                        if (!cb.disabled) cb.checked = this.checked;
                    });
                    recalculateTotals();
                });
            }

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('original-item-checkbox')) {
                    recalculateTotals();
                }
                if (e.target.classList.contains('original-qty-input')) {
                    updateOriginalItemSubtotal(e.target);
                    recalculateTotals();
                }
                if (e.target.classList.contains('exchange-item-qty')) {
                    updateExchangeItemSubtotal(e.target);
                    recalculateTotals();
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-exchange-item')) {
                    const key = e.target.closest('.remove-exchange-item').dataset.key;
                    delete exchangeCatalog[key];
                    e.target.closest('.exchange-item-entry').remove();

                    const container = document.getElementById('exchange-items-container');
                    if (container.children.length === 0) {
                        container.innerHTML = `
                            <div class="text-center text-muted py-8 empty-exchange-cart">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-primary/40"></i>
                                <p class="text-xs">{{ translate('Search and add products to receive') }}</p>
                            </div>
                        `;
                    }

                    recalculateTotals();
                }
            });

            const shippingInput = document.getElementById('shipping-charge-input');
            if (shippingInput) {
                shippingInput.addEventListener('input', function() {
                    document.getElementById('shipping-charge-hidden').value = this.value || 0;
                    recalculateTotals();
                });
            }

            const submitBtn = document.querySelector('.submit-exchange-btn');
            if (submitBtn) {
                submitBtn.addEventListener('click', submitExchange);
            }
        }

        function updateOriginalItemSubtotal(qtyInput) {
            const row = qtyInput.closest('.original-item-row');
            if (!row) return;
            const unitPrice = parseFloat(row.dataset.unitPrice) || 0;
            const qty = parseInt(qtyInput.value) || 1;
            const subtotalEl = row.querySelector('.original-item-subtotal');
            if (subtotalEl) {
                subtotalEl.textContent = formatCurrency(unitPrice * qty);
            }
        }

        function updateExchangeItemSubtotal(qtyInput) {
            const key = qtyInput.dataset.key;
            if (!exchangeCatalog[key]) return;
            const unitPrice = exchangeCatalog[key].unitPrice;
            const qty = parseInt(qtyInput.value) || 1;
            const entry = document.querySelector(`.exchange-item-entry[data-exchange-key="${key}"]`);
            if (entry) {
                const subtotalEl = entry.querySelector('.exchange-item-subtotal');
                if (subtotalEl) {
                    subtotalEl.textContent = formatCurrency(unitPrice * qty);
                }
            }
        }

        function recalculateTotals() {
            let originalTotal = 0;
            let exchangeTotal = 0;

            document.querySelectorAll('.original-item-row').forEach(row => {
                const checkbox = row.querySelector('.original-item-checkbox');
                if (checkbox && checkbox.checked) {
                    const unitPrice = parseFloat(row.dataset.unitPrice) || 0;
                    const qtyInput = row.querySelector('.original-qty-input');
                    const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                    originalTotal += unitPrice * qty;
                }
            });

            document.querySelectorAll('.exchange-item-entry').forEach(entry => {
                const key = entry.dataset.exchangeKey;
                if (!exchangeCatalog[key]) return;
                const unitPrice = exchangeCatalog[key].unitPrice;
                const qtyInput = entry.querySelector('.exchange-item-qty');
                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                exchangeTotal += unitPrice * qty;
            });

            const shipping = parseFloat(document.getElementById('shipping-charge-hidden')?.value || 0);
            const priceDiff = exchangeTotal - (originalTotal + shipping);

            const originalEl = document.getElementById('original-total');
            const exchangeEl = document.getElementById('exchange-total');
            const diffEl = document.getElementById('price-difference');
            const diffLabel = document.getElementById('difference-label');
            const diffLabelText = document.getElementById('difference-label-text');

            if (originalEl) originalEl.textContent = formatCurrency(originalTotal);
            if (exchangeEl) exchangeEl.textContent = formatCurrency(exchangeTotal);
            if (diffEl) {
                const sign = priceDiff > 0 ? '+' : '';
                diffEl.textContent = sign + formatCurrency(priceDiff);
                if (priceDiff > 0) {
                    diffEl.className = 'font-bold text-base text-orange-600';
                    if (diffLabel) {
                        diffLabel.className = 'text-center text-xs py-1.5 rounded bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400';
                        diffLabelText.textContent = '{{ translate("Customer Pays") }}';
                        diffLabel.classList.remove('hidden');
                    }
                } else if (priceDiff < 0) {
                    diffEl.className = 'font-bold text-base text-green-600';
                    if (diffLabel) {
                        diffLabel.className = 'text-center text-xs py-1.5 rounded bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                        diffLabelText.textContent = '{{ translate("Refund to Customer") }}';
                        diffLabel.classList.remove('hidden');
                    }
                } else {
                    diffEl.className = 'font-bold text-base text-primary';
                    if (diffLabel) {
                        diffLabel.className = 'text-center text-xs py-1.5 rounded bg-primary/5 text-primary dark:bg-primary/20 dark:text-primary';
                        diffLabelText.textContent = '{{ translate("Even - No Payment Required") }}';
                        diffLabel.classList.remove('hidden');
                    }
                }
            }
        }

        function formatCurrency(amount) {
            const symbol = '{{ getSetting("currencySymbol") }}' || '$';
            return symbol + parseFloat(amount).toFixed(2);
        }

        function submitExchange() {
            const selectedItems = [];
            let hasError = false;

            document.querySelectorAll('.original-item-row').forEach(row => {
                const checkbox = row.querySelector('.original-item-checkbox');
                if (!checkbox || !checkbox.checked) return;

                const orderItemId = row.dataset.orderItemId || row.querySelector('.original-item-checkbox')?.value;
                const qtyInput = row.querySelector('.original-qty-input');
                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;

                selectedItems.push({
                    original_order_item_id: orderItemId,
                    quantity: qty,
                    reason: '',
                    note: '',
                });
            });

            if (selectedItems.length === 0) {
                alert('{{ translate("Please select at least one item to exchange") }}');
                return;
            }

            const exchangeItems = [];
            document.querySelectorAll('.exchange-item-entry').forEach(entry => {
                const key = entry.dataset.exchangeKey;
                if (!exchangeCatalog[key]) return;
                const qtyInput = entry.querySelector('.exchange-item-qty');
                const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                exchangeItems.push({
                    exchange_product_variation_id: exchangeCatalog[key].variationId,
                    exchange_quantity: qty,
                });
            });

            if (exchangeItems.length === 0) {
                alert('{{ translate("Please add at least one product to receive") }}');
                return;
            }

            const payload = {
                _token: document.querySelector('input[name="_token"]').value,
                order_id: document.querySelector('input[name="order_id"]').value,
                requested_by: document.querySelector('input[name="requested_by"]').value,
                shipping_charge: document.getElementById('shipping-charge-hidden').value,
                items: selectedItems,
                exchange_items: exchangeItems,
            };

            fetch(`/${exchangeRoutePrefix}/exchanges`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': payload._token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 200) {
                    flash(data.message, 'success');
                    window.location.href = `/${exchangeRoutePrefix}/exchanges`;
                } else {
                    alert(data.message || '{{ translate("Something went wrong") }}');
                }
            })
            .catch(err => {
                alert('{{ translate("Something went wrong") }}');
            });
        }

        function flash(message, type) {
            const flashDiv = document.createElement('div');
            flashDiv.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded shadow-lg ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}`;
            flashDiv.textContent = message;
            document.body.appendChild(flashDiv);
            setTimeout(() => flashDiv.remove(), 3000);
        }
    </script>
@endsection