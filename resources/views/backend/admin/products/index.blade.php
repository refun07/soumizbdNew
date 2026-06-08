@extends('layouts.admin')

@section('title')
    {{ translate('Products') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Product List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.products.index') }}" class="font-bold ">{{ translate('Products') }}</a>
            <span class="text-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Product List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="card">
        <div class="theme-table">
            <div
                class="card__title border-none theme-table__filter flex flex-col gap-3">

                <x-backend.admin.products.search-form searchKey="{{ $searchKey }}">

                    <x-slot name="buttonSlot">
                        @can('create_products')
                            <x-backend.inputs.link href="{{ route('admin.products.create') }}">
                                <span class="text-xl">
                                    <i class="fal fa-plus"></i>
                                </span>
                                {{ translate('Add New') }}
                            </x-backend.inputs.link>

                            <div class="inline-flex items-stretch">
                                <button type="button" class="button button--primary !rounded-r-none"
                                    data-micromodal-trigger="product-import-modal">
                                    <span class="text-lg">
                                        <i class="fa-solid fa-file-import"></i>
                                    </span>
                                    {{ translate('Import') }}
                                </button>

                                <div class="option-dropdown" tabindex="0">
                                    <div
                                        class="option-dropdown__toggler no-arrow !min-w-0 !w-10 !rounded-l-none !px-0 !justify-center border-l border-white/20">
                                        <span class="sr-only">{{ translate('Import Options') }}</span>
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>

                                    <div class="option-dropdown__options right-0 !w-[190px]">
                                        <ul>
                                            <li>
                                                <a href="{{ route('admin.products.sampleCsv') }}"
                                                    class="option-dropdown__option">
                                                    <i class="fa-solid fa-download"></i>
                                                    {{ translate('Sample CSV') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </x-slot>

                    <x-backend.inputs.select name="categoryId" class="filterSelect" :isRequired="false" groupClass="!mb-0 min-w-[140px]">
                        <x-backend.inputs.select-option name="{{ translate('All Categories') }}" value=""
                            selected="{{ Request::get('categoryId') }}" />
                        @foreach ($categories as $cat)
                            <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name') }}"
                                value="{{ $cat->id }}"
                                selected="{{ Request::get('categoryId') == $cat->id ? $cat->id : 0 }}" />

                            @foreach ($cat->childrenCategories as $childCategory)
                                @include('backend.admin.categories.subCategory', [
                                    'subCategory' => $childCategory,
                                    'langKey' => \Session::get('locale'),
                                    'category' => null,
                                ])
                            @endforeach
                        @endforeach
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="brandId" class="filterSelect" :isRequired="false" groupClass="!mb-0 min-w-[140px]">
                        <x-backend.inputs.select-option name="{{ translate('All Brands') }}" value=""
                            selected="{{ Request::get('brandId') }}" />
                        @foreach ($brands as $brand)
                            <x-backend.inputs.select-option name="{{ $brand->collectTranslation('name') }}"
                                value="{{ $brand->id }}"
                                selected="{{ Request::get('brandId') == $brand->id ? $brand->id : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="tagId" class="filterSelect" :isRequired="false" groupClass="!mb-0 min-w-[130px]">
                        <x-backend.inputs.select-option name="{{ translate('All Tags') }}" value=""
                            selected="{{ Request::get('tagId') }}" />
                        @foreach ($tags as $tag)
                            <x-backend.inputs.select-option name="{{ $tag->name }}" value="{{ $tag->id }}"
                                selected="{{ Request::get('tagId') == $tag->id ? $tag->id : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="stockStatus" class="filterSelect" :isRequired="false" groupClass="!mb-0 min-w-[140px]">
                        <x-backend.inputs.select-option name="{{ translate('All Stock') }}" value=""
                            selected="{{ Request::get('stockStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('In Stock') }}" value="in_stock"
                            selected="{{ Request::get('stockStatus') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Out of Stock') }}" value="out_of_stock"
                            selected="{{ Request::get('stockStatus') }}" />
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="isPublished" class="filterSelect" :isRequired="false" groupClass="!mb-0 min-w-[140px]">
                        <x-backend.inputs.select-option name="{{ translate('All Published') }}" value=""
                            selected="{{ Request::get('isPublished') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Published') }}" value="1"
                            selected="{{ Request::get('isPublished') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Unpublished') }}" value="0"
                            selected="{{ Request::get('isPublished') }}" />
                    </x-backend.inputs.select>

                    <x-backend.inputs.select name="hideOnEcom" class="filterSelect" :isRequired="false" groupClass="!mb-0 min-w-[160px]">
                        <x-backend.inputs.select-option name="{{ translate('All Ecom Visibility') }}" value=""
                            selected="{{ Request::get('hideOnEcom') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Showing on Ecom') }}" value="showing"
                            selected="{{ Request::get('hideOnEcom') }}" />
                        <x-backend.inputs.select-option name="{{ translate('Hidden from Ecom') }}" value="hidden"
                            selected="{{ Request::get('hideOnEcom') }}" />
                    </x-backend.inputs.select>

                    <label class="flex items-center gap-2 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" name="isPreOrder" value="1"
                            class="form-checkbox rounded text-primary focus:ring-primary"
                            {{ Request::get('isPreOrder') == 1 ? 'checked' : '' }}
                            onchange="this.form.submit()" />
                        <span class="text-sm font-medium">{{ translate('Pre Order Only') }}</span>
                    </label>

                </x-backend.admin.products.search-form>
            </div>


            <table class="product-list-table footable w-full [&_td]:">
                <thead class="uppercase text-left bg-primary/10">
                    <tr>
                        <th>
                            {{ translate('Product DETAILS') }}
                        </th>
                        <th data-breakpoints="xs sm md lg">
                            {{ translate('Categories') }}
                        </th>
                        <th data-breakpoints="xs sm" class="min-w-[150px]">
                            {{ translate('Stock') }}
                        </th>

                        <th data-breakpoints="xs sm">{{ translate('Show/Hide') }}</th>
                        <th data-breakpoints="xs sm">{{ translate('Price') }}</th>
                        <th data-breakpoints="xs sm md" class="w-[130px]">
                            {{ translate('Options') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td class="">
                                <div class="inline-flex items-center gap-4">
                                    <div class="max-xs:hidden">
                                        <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt=""
                                            class="w-12 h-12 lg:w-[70px] lg:h-[80px] rounded-md"
                                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                    </div>
                                    <div class=" line-clamp-2 max-w-[350px]">
                                        {{ $product->collectTranslation('name') }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="inline-flex items-center gap-2 flex-wrap">
                                    @forelse ($product->categories as $category)
                                        <a
                                            class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none cursor-default">{{ $category->collectTranslation('name') }}</a>
                                    @empty
                                        <a
                                            class="px-2 py-1 text-xs bg-secondary text-white rounded-md leading-none cursor-default">{{ translate('N/A') }}</a>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="inline-flex items-center gap-2">
                                    @php
                                        $stockQty = $product->getStockQuantity();
                                    @endphp

                                    <span>{{ $stockQty }}</span>

                                    @if ($stockQty <= $product->alert_qty)
                                        <span class="tooltip">
                                            <span class="tooltip__toggler text-orange-400 text-lg md:text-[26px]">
                                                <i class="far fa-exclamation-circle"></i>
                                            </span>
                                            <span
                                                class="tooltip__content">{{ translate('This shows your stock is low') }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td>
                                @can('edit_products')
                                    <x-backend.inputs.checkbox toggler="true" data-route="{{ route('admin.products.status') }}"
                                        name="isActiveCheckbox" value="{{ $product->id }}"
                                        data-status="{{ $product->is_published }}"
                                        isChecked="{{ (int) $product->is_published == 1 }}" />
                                @endcan
                            </td>

                            <td>
                                @if ($product->max_price != $product->min_price)
                                    {{ formatPrice($product->min_price) }}
                                    -
                                    {{ formatPrice($product->max_price) }}
                                @else
                                    {{ formatPrice($product->min_price) }}
                                @endif
                            </td>
                            <td>
                                <x-backend.inputs.action-dropdown label="{{ translate('Actions') }}" class="w-[130px]">
                                    <ul>
                                        <li>
                                            <a href="{{ url('/products\/') . $product->slug }}" target="_blank"
                                                class="ad-12b3e8cf__option">{{ translate('View Product') }}</a>
                                        </li>

                                        @can('edit_products')
                                            <li>
                                                <a href="{{ route('admin.products.duplicate', $product->id) }}"
                                                    class="ad-12b3e8cf__option">{{ translate('Duplicate') }}</a>
                                            </li>

                                            <li>
                                                <a href="#" class="ad-12b3e8cf__option quick-edit-btn"
                                                    data-product-id="{{ $product->id }}">
                                                    {{ translate('Quick Edit') }}
                                                </a>
                                            </li>

                                            <li>
                                                <a href="{{ route('admin.products.edit', ['product' => $product->id, 'lang_key' => config('app.default_language')]) }}&translate"
                                                    class="ad-12b3e8cf__option">
                                                    {{ translate('Edit') }}
                                                </a>
                                            </li>
                                        @endcan

                                        @can('delete_products')
                                            <li>
                                                <x-backend.inputs.delete-link
                                                    href="{{ route('admin.products.destroy', $product->id) }}"
                                                    class="ad-12b3e8cf__option confirm-modal" />
                                            </li>
                                        @endcan
                                    </ul>
                                </x-backend.inputs.action-dropdown>
                            </td>
                        </tr>
                    @endforeach

                    @if($products->isEmpty() && $searchKey)
                        <tr>
                            <td colspan="6" class="text-center py-8 text-muted">
                                {{ translate('No products found matching your search') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="card__footer">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    @can('create_products')
        <x-backend.modals.product-import modal-id="product-import-modal"
            form-action="{{ route('admin.products.import') }}" sample-url="{{ route('admin.products.sampleCsv') }}" />
    @endcan

    <!-- Quick Edit Modal -->
    <div class="modal micromodal-slide" id="quickEditModal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" onclick="closeQuickEditModal()"></div>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="quick-edit-modal-title">
            <header class="modal__header">
                <h2 class="modal__title" id="quick-edit-modal-title">
                    {{ translate('Quick Update Product') }}
                </h2>
                <button type="button" class="modal__close" aria-label="Close modal" onclick="closeQuickEditModal()"></button>
            </header>
            <main class="modal__content w-full">
                <form id="quickEditForm" class="space-y-4">
                    @csrf
                    <input type="hidden" id="product_id" name="product_id">
                    <input type="hidden" id="use_inventory" name="use_inventory">
                    <input type="hidden" id="has_variation" name="has_variation">
                    
                    <!-- Product Name -->
                    <div class="theme-input-group">
                        <label for="product_name" class="theme-input-label input-required">
                            {{ translate('Product Name') }}
                        </label>
                        <div class="theme-input-wrapper">
                            <input type="text" id="product_name" name="name" required
                                class="theme-input" placeholder="{{ translate('Enter product name') }}">
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="theme-input-group">
                        <label for="product_slug" class="theme-input-label input-required">
                            {{ translate('Slug') }}
                        </label>
                        <div class="theme-input-wrapper">
                            <input type="text" id="product_slug" name="slug" required
                                class="theme-input" placeholder="{{ translate('product-slug') }}">
                            <p class="text-xs text-gray-500 mt-1">{{ translate('Auto-generated from name, but you can edit it') }}</p>
                        </div>
                    </div>

                    <!-- Variation Selector (only for products with variations) -->
                    <div id="variation_selector_container" class="theme-input-group hidden">
                        <label for="variation_id" class="theme-input-label input-required">
                            {{ translate('Select Variation') }}
                        </label>
                        <div class="theme-input-wrapper input-select2">
                            <select id="variation_id" name="variation_id" class="theme-input">
                            </select>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="theme-input-group">
                        <label for="product_price" class="theme-input-label input-required">
                            {{ translate('Price') }}
                        </label>
                        <div class="theme-input-wrapper">
                            <input type="number" id="product_price" name="price" step="0.01" min="0" required
                                class="theme-input" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Stock - Editable when inventory disabled, Read-only when enabled -->
                    <div id="stock_container" class="theme-input-group">
                        <label for="product_stock" class="theme-input-label" id="stock_label">
                            {{ translate('Stock') }}
                        </label>
                        <div class="theme-input-wrapper">
                            <input type="number" id="product_stock" name="stock" 
                                class="theme-input" placeholder="0">
                            <p id="stock_help" class="text-xs text-gray-500 mt-1"></p>
                        </div>
                    </div>
                </form>
                
                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-border">
                    <button type="button" onclick="closeQuickEditModal()"
                        class="button button--light">
                        {{ translate('Cancel') }}
                    </button>
                    <button type="button" onclick="submitQuickEdit()"
                        class="button button--primary">
                        {{ translate('Save Changes') }}
                    </button>
                </div>
            </main>
        </div>
    </div>

    <script>
        let currentProductData = null;
        let slugManuallyEdited = false;
        
        // Define flash function fallback if not already defined
        if (typeof flash !== 'function') {
            window.flash = function(message, type) {
                console.log('Flash (' + type + '):', message);
                // Use toastr if available
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                }
            };
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Filter selects handling - use jQuery for Select2 compatibility
            $(document).on('change', '.product-search-form .filterSelect', function() {
                var form = document.querySelector('.product-search-form form');
                if (form) {
                    form.submit();
                }
            });

            // Quick Edit button handlers - using event delegation for better reliability
            document.addEventListener('click', function(e) {
                var btn = e.target.closest('.quick-edit-btn');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    var productId = btn.getAttribute('data-product-id');
                    if (productId && typeof openQuickEditModal === 'function') {
                        openQuickEditModal(productId);
                    }
                }
            });

            // Name to slug auto-generation
            var nameInput = document.getElementById('product_name');
            var slugInput = document.getElementById('product_slug');
            
            nameInput.addEventListener('input', function() {
                if (!slugManuallyEdited) {
                    slugInput.value = generateSlug(this.value);
                }
            });

            slugInput.addEventListener('input', function() {
                slugManuallyEdited = true;
            });

            // Variation selector change handler
            var variationSelect = document.getElementById('variation_id');
            variationSelect.addEventListener('change', function() {
                if (currentProductData && currentProductData.variations) {
                    var selectedVariation = currentProductData.variations.find(v => v.id == this.value);
                    if (selectedVariation) {
                        document.getElementById('product_price').value = selectedVariation.price;
                        updateStockField(selectedVariation.stock_qty);
                    }
                }
            });
        });

        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
        }

        function openQuickEditModal(productId) {
            var modal = document.getElementById('quickEditModal');
            // Show modal using Micromodal pattern
            modal.setAttribute('aria-hidden', 'false');
            modal.classList.add('is-open');
            
            // Fetch product data
            fetch(`{{ route('admin.api.products.quick-edit-data', ['id' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', productId))
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) {
                        currentProductData = data.data;
                        populateModal(data.data);
                    } else {
                        flash(data.message || '{{ translate("Failed to load product data") }}', 'error');
                        closeQuickEditModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    flash('{{ translate("Failed to load product data") }}', 'error');
                    closeQuickEditModal();
                });
        }

        function populateModal(data) {
            document.getElementById('product_id').value = data.id;
            document.getElementById('use_inventory').value = data.use_inventory ? '1' : '0';
            document.getElementById('has_variation').value = data.has_variation ? '1' : '0';
            document.getElementById('product_name').value = data.name;
            document.getElementById('product_slug').value = data.slug;
            document.getElementById('product_price').value = data.price;
            
            // Reset slug manual edit flag
            slugManuallyEdited = false;

            // Handle variation selector
            var variationContainer = document.getElementById('variation_selector_container');
            if (data.has_variation && data.variations) {
                variationContainer.classList.remove('hidden');
                var variationSelect = document.getElementById('variation_id');
                variationSelect.innerHTML = '';
                
                data.variations.forEach(function(variation) {
                    var option = document.createElement('option');
                    option.value = variation.id;
                    option.textContent = variation.code + ' - ' + variation.price;
                    if (variation.id == data.variation_id) {
                        option.selected = true;
                    }
                    variationSelect.appendChild(option);
                });
            } else {
                variationContainer.classList.add('hidden');
                document.getElementById('variation_id').value = '';
            }

            // Handle stock field based on inventory setting
            updateStockField(data.stock_qty, data.use_inventory);
        }

        function updateStockField(stockQty, useInventory = null) {
            if (useInventory === null && currentProductData) {
                useInventory = currentProductData.use_inventory;
            }
            
            var stockInput = document.getElementById('product_stock');
            var stockLabel = document.getElementById('stock_label');
            var stockHelp = document.getElementById('stock_help');
            
            stockInput.value = stockQty;
            
            if (useInventory) {
                // Inventory enabled - stock is read-only (sum across warehouses)
                stockInput.readOnly = true;
                stockInput.classList.add('bg-gray-100');
                stockLabel.innerHTML = '{{ translate("Stock") }}';
                stockHelp.textContent = '{{ translate("Read-only: Calculated from inventory across all warehouses") }}';
            } else {
                // Inventory disabled - stock is editable
                stockInput.readOnly = false;
                stockInput.classList.remove('bg-gray-100');
                stockLabel.innerHTML = '{{ translate("Stock") }} <span class="text-red-500">*</span>';
                
                var allowBackorder = '{{ getSetting("allowBackorder") }}' === '1';
                if (allowBackorder) {
                    stockHelp.textContent = '{{ translate("Backorder is enabled - negative values allowed") }}';
                    stockInput.min = '';
                } else {
                    stockHelp.textContent = '{{ translate("Minimum value is 0") }}';
                    stockInput.min = '0';
                }
            }
        }

        function closeQuickEditModal() {
            var modal = document.getElementById('quickEditModal');
            // Hide modal using Micromodal pattern
            modal.setAttribute('aria-hidden', 'true');
            modal.classList.remove('is-open');
            currentProductData = null;
            slugManuallyEdited = false;
            document.getElementById('quickEditForm').reset();
        }

        function submitQuickEdit() {
            var form = document.getElementById('quickEditForm');
            var formData = new FormData(form);
            var productId = document.getElementById('product_id').value;
            
            // Validation
            var name = document.getElementById('product_name').value.trim();
            var slug = document.getElementById('product_slug').value.trim();
            var price = document.getElementById('product_price').value;
            
            if (!name) {
                flash('{{ translate("Product name is required") }}', 'error');
                return;
            }
            
            if (!slug) {
                flash('{{ translate("Slug is required") }}', 'error');
                return;
            }
            
            if (!price || price < 0) {
                flash('{{ translate("Valid price is required") }}', 'error');
                return;
            }

            // Prepare data
            var data = {
                name: name,
                slug: slug,
                price: parseFloat(price),
            };

            // Only include stock if inventory is disabled
            var useInventory = document.getElementById('use_inventory').value === '1';
            if (!useInventory) {
                var stock = document.getElementById('product_stock').value;
                var allowBackorder = '{{ getSetting("allowBackorder") }}' === '1';
                
                if (!allowBackorder && stock < 0) {
                    flash('{{ translate("Stock cannot be negative when backorder is not allowed") }}', 'error');
                    return;
                }
                data.stock = parseInt(stock) || 0;
            }

            // Include variation_id if product has variations
            var hasVariation = document.getElementById('has_variation').value === '1';
            if (hasVariation) {
                var variationId = document.getElementById('variation_id').value;
                if (!variationId) {
                    flash('{{ translate("Please select a variation") }}', 'error');
                    return;
                }
                data.variation_id = variationId;
            }

            // Send request
            fetch(`{{ route('admin.api.products.quick-update', ['id' => 'PLACEHOLDER']) }}`.replace('PLACEHOLDER', productId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    flash(result.message || '{{ translate("Product updated successfully") }}', 'success');
                    closeQuickEditModal();
                    // Reload page to show updated data
                    setTimeout(function() {
                        window.location.reload();
                    }, 500);
                } else {
                    flash(result.message || '{{ translate("Failed to update product") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                flash('{{ translate("Failed to update product") }}', 'error');
            });
        }
    </script>
@endsection
