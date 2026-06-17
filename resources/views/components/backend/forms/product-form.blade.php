<form
    action="{{ $product ? route(routePrefix() . '.products.update', $product->id) : route(routePrefix() . '.products.store') }}"
    method="POST" id="product-form">
    @csrf
    @if ($product)
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif
    @php
        $showMinPurchaseQtyField = getSetting('productSettingMinPurchaseQtyEnabled', '1') !== '0';
        $showMaxPurchaseQtyField = getSetting('productSettingMaxPurchaseQtyEnabled', '1') !== '0';
        $showSizeChartField = getSetting('productSettingSizeChartEnabled', '1') !== '0';
        $showVideoLinkField = getSetting('productSettingVideoLinkEnabled', '1') !== '0';
        $showQrCodeSection = getSetting('productSettingQrCodeEnabled', '1') !== '0';
        $showTagsField = getSetting('productSettingTagsEnabled', '1') !== '0';
        $showBadgesField = getSetting('productSettingBadgesEnabled', '1') !== '0';
        $showEstimatedDeliveryTimeField = getSetting('productSettingEstimatedDeliveryTimeEnabled', '1') !== '0';
        $showEmiField = getSetting('productSettingEmiEnabled', '1') !== '0';
        $showWarrantyField = getSetting('productSettingWarrantyEnabled', '1') !== '0';
        $showAlertQtyField = getSetting('productSettingAlertQtyEnabled', '1') !== '0';
        $showTaxesField = getSetting('productSettingTaxesEnabled', '1') !== '0';
        $showPreOrderField = getSetting('productSettingPreOrderEnabled', '1') !== '0';
        $showTaxesCard = $showTaxesField && $taxes->isNotEmpty();
        $showTagsBadgesCard = $showTagsField || $showBadgesField;
        $showOtherInfoCard = $showEstimatedDeliveryTimeField || $showEmiField || $showWarrantyField || $showAlertQtyField;
    @endphp

    @if ($simplifiedAdmin)
        <div class="card mb-3" id="admin-product-essentials">
            <div class="card__title flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if ($product)
                        <div id="admin-product-language-selector"></div>
                    @endif
                    <span>{{ translate('Required Product Information') }}</span>
                    <span class="ml-2 text-xs font-normal text-muted">{{ translate('Complete these fields first') }}</span>
                </div>
                <x-backend.inputs.button buttonText="Save Product" type="submit" form="product-form" />
            </div>
            <div class="card__content">
                <div id="admin-product-essentials-grid" class="grid md:grid-cols-2 gap-4"></div>
            </div>
        </div>

        <div class="card mb-3" id="admin-product-variations">
            <div class="card__title flex items-center justify-between">
                <span>{{ translate('Product Variations') }}</span>
                <div id="admin-product-variation-toggle"></div>
            </div>
            <div class="card__content" id="admin-product-variations-content"></div>
        </div>

        <div class="mb-3 mt-6">
            <h3 class="text-base font-semibold">{{ translate('Optional Product Information') }}</h3>
            <p class="text-sm text-muted mt-1">{{ translate('Open a section only when you need it.') }}</p>
        </div>
    @endif

    <div class="{{ $simplifiedAdmin ? 'admin-product-layout space-y-3' : 'grid md:grid-cols-12 gap-3' }}">
        <div class="{{ $simplifiedAdmin ? 'space-y-3' : 'md:col-span-8 md:row-span-2' }}">
            {{-- Name & Slug --}}
            <div class="card" data-product-section="general">
                <div class="card__title flex justify-between items-center">
                    {{ translate('General Informations') }}
                    @if ($product)
                        <div class="product-language-selector">
                            <x-backend.inputs.change-languages class="!text-sm !font-normal"
                                langKey="{{ $langKey }}" />
                        </div>
                    @endif
                </div>

                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.text label="Product Name" name="name" placeholder="Type product name"
                            value="{{ $product?->collectTranslation('name', $langKey) }}" />

                        <div class="unit-tree-data">
                            @include('components.backend.inc.products.unit-list', [
                                'product' => $product,
                                'units' => $units,
                                'selectedUnit' => null,
                            ])
                        </div>


                        @if (user()->user_type != 'seller')
                            @can('create_units')
                                <div class="text-end !my-3">
                                    <a href="javascript:void(0);" class="text-secondary-light unit-create-modal"
                                        data-micromodal-trigger="unit-create-modal">
                                        {{ translate('Add New Unit') }}
                                        <span class="ml-2">
                                            <i class="fa-solid fa-plus"></i>
                                        </span>
                                    </a>
                                </div>
                            @endcan
                        @endif

                        @if (!$product || ($product && $langKey == config('app.default_language')))
                            @php
                                $sizeChartEditorState = old('size_chart_state');
                                if (is_string($sizeChartEditorState)) {
                                    $decodedSizeChartEditorState = json_decode($sizeChartEditorState, true);
                                    $sizeChartEditorState = is_array($decodedSizeChartEditorState)
                                        ? $decodedSizeChartEditorState
                                        : null;
                                }

                                if (!is_array($sizeChartEditorState)) {
                                    $sizeChartEditorState = $product?->size_guides;
                                }

                                $sizeChartEditorState = is_array($sizeChartEditorState)
                                    ? $sizeChartEditorState
                                    : [
                                        'columns' => ['', ''],
                                        'rows' => [['', ''], ['', '']],
                                    ];
                            @endphp

                            @if ($showMinPurchaseQtyField)
                                <x-backend.inputs.number label="Min Purchase Quantity" name="min_purchase_qty"
                                    placeholder="Type minimum purchase quantity"
                                    value="{{ $product?->min_purchase_qty ?? 1 }}" min="1" />
                            @else
                                <input type="hidden" name="min_purchase_qty" value="1">
                            @endif

                            @if ($showMaxPurchaseQtyField)
                                <x-backend.inputs.number label="Max Purchase Quantity" name="max_purchase_qty"
                                    placeholder="Type maximum purchase quantity"
                                    value="{{ $product?->max_purchase_qty ?? 1 }}" min="1" />
                            @else
                                <input type="hidden" name="max_purchase_qty" value="100">
                            @endif

                            @if ($showSizeChartField)
                                <div class="flex items-center justify-between">
                                    <p>{{ translate('Enable Size Chart') }}</p>
                                    <x-backend.inputs.checkbox toggler="true" id="isSizeChart" name="isSizeChart"
                                        value="1" :isChecked="$product?->isSizeChart" />
                                </div>

                                <div id="size_chart_image_section"
                                    style="{{ $product?->isSizeChart ? '' : 'display:none;' }}">
                                    <div class="rounded-xl border border-slate-200 p-4 mb-4 bg-slate-50/60">
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <div>
                                                <h4 class="text-sm font-semibold text-slate-800">
                                                    {{ translate('Interactive Size Chart') }}
                                                </h4>
                                                <p class="text-xs text-muted mt-1">
                                                    {{ translate('Build a table-based size chart by adding columns, rows, and cell values. Leave it empty if you only want to use the image below.') }}
                                                </p>
                                            </div>
                                        </div>

                                        <input type="hidden" name="size_chart_state" id="size_chart_state"
                                            value="{{ json_encode($sizeChartEditorState) }}">
                                        <div id="size_chart_editor"></div>

                                        <p class="text-xs text-muted mt-3">
                                            {{ translate('Tip: use the blue plus buttons to insert a row or column, and the white minus buttons to remove one.') }}
                                        </p>
                                    </div>

                                    <x-backend.inputs.file label="Size Chart Image (Optional Fallback)" name="chart_image"
                                        value="{{ $product?->chart_image }}" :isRequired="false"
                                        filesHint="Optional fallback image for the size chart. If the interactive table has content, it will be used first." />
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            @if (!$product || ($product && $langKey == config('app.default_language')))
                {{-- Images --}}
                <div class="card mt-3" data-product-section="images">
                    <div class="card__title">
                        {{ translate('Product Images') }}
                    </div>
                    <div class="card__content">
                        <div class="space-y-3">
                            <x-backend.inputs.file label="Product Image" name="thumbnail_image"
                                value="{{ $product?->thumbnail_image }}"
                                filesHint="This image will be used as thumbnail image of product. Recommended size: 512*512" />

                            <x-backend.inputs.file :multiple="true" label="Gallery Images" name="gallery_images"
                                value="{{ $product?->gallery_images }}"
                                filesHint="These images will be used as slider images of product in details page. Recommended size: 512*512" />

                            @if ($showVideoLinkField)
                                <x-backend.inputs.text label="Video Link" name="video_link" placeholder="Enter video link"
                                    value="{{ $product?->video_link }}" :isRequired="false" />
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Product Price & Stock --}}
                <div class="card mt-3" data-product-section="price-stock">
                    <div class="card__title">
                        <div class="flex items-center justify-between">
                            <span>{{ translate('Product Price & Stock') }}</span>
                                    <span id="product-variation-toggle">
                                <span class="flex items-center">
                                    <label for="is_variant" class="cursor-pointer">
                                        {{ translate('Has Variations?') }}
                                    </label>
                                    <span class="ms-3">
                                        <x-backend.inputs.checkbox toggler="true" id="is_variant" name="is_variant"
                                            onchange="isVariantProduct(this)" :isChecked="$product?->has_variation"
                                            data-has-variations="{{ $product?->has_variation ? 'true' : 'false' }}"
                                            data-variation-count="{{ $product?->variations?->count() ?? 0 }}" />
                                    </span>
                                </span>

                            </span>
                        </div>
                    </div>

                    <div class="card__content">
                        {{-- no variation --}}
                        <div class="noVariation space-y-3"
                            @if ($product?->has_variation) style="display:none;" @endif>

                            @php
                                $price = 0;
                                $stock_qty = 0;
                                $sku = null;
                                $code = null;
                                $discount_value = 0;
                                $discount_type = 'amount';

                                if ($product) {
                                    $firstVariation = $product->variations()->first();
                                    if ($firstVariation) {
                                        $price = $firstVariation->price;
                                        $discount_value = $firstVariation->discount_value;
                                        $discount_type = $firstVariation->discount_type;

                                        $defaultWarehouse = shop()->warehouses()->where('is_default', 1)->first();
                                        if ($defaultWarehouse) {
                                            $stock = $firstVariation
                                                ->productVariationStocks()
                                                ->where('warehouse_id', $defaultWarehouse->id)
                                                ->first();
                                            if ($stock) {
                                                $stock_qty = $stock->stock_qty;
                                            }
                                        }

                                        $sku = $firstVariation->sku;
                                        $code = $firstVariation->code;
                                    }
                                }
                            @endphp

                            <x-backend.inputs.number label="Price" name="price" placeholder="Type price of product"
                                value="{{ $price }}" min="0" step="0.001" />

                            <x-backend.inputs.text label="Sku" name="sku" placeholder="Type sku of product"
                                value="{{ $sku }}" />

                            @if (!useInventory())
                                <x-backend.inputs.number label="Stock" name="stock_qty"
                                    placeholder="Type stock of product" value="{{ $stock_qty }}"
                                    min="0" />
                            @endif

                            <x-backend.inputs.number label="Discount Value" name="discount_value" min="0"
                                step="0.001" :isRequired="false" value="{{ $discount_value }}" />

                            <x-backend.inputs.select label="Discount Type" name="discount_type" data-search="false"
                                :isRequired="false">
                                <x-backend.inputs.select-option name="{{ translate('Amount') }}" value="amount"
                                    selected="{{ $discount_type }}" />
                                <x-backend.inputs.select-option name="{{ translate('Percentage') }}"
                                    value="percentage" selected="{{ $discount_type }}" />
                            </x-backend.inputs.select>
                        </div>

                        {{-- has variations --}}
                        <div class="hasVariation space-y-3"
                            @if ($product && !$product->has_variation) style="display:none;" @elseif(!$product) style="display:none;" @endif>
                            {{-- start:variations --}}
                            <div class="chosen_variation_options">
                                @if (!$product || !$product?->has_variation)
                                    <div class="grid md:grid-cols-2 gap-3">
                                        <div class="md:col-span-1">
                                            <div class="flex items-center">
                                                <div class="w-full">{{ translate('Attribute') }} <span
                                                        class="attribute-counter">1</span></div>
                                                <x-backend.inputs.select groupClass="w-full ms-4"
                                                    name="chosen_variations[]" onchange="getVariationValues(this)"
                                                    class="chosen_variations" :isRequired="false" data-search="false">
                                                    <option value="">{{ translate('Select Variation') }}
                                                        @foreach ($variations as $variation)
                                                            <x-backend.inputs.select-option
                                                                name="{{ $variation->collectTranslation('name') }}"
                                                                value="{{ $variation->id }}" />
                                                        @endforeach
                                                </x-backend.inputs.select>
                                            </div>
                                        </div>

                                        <div class="md:col-span-1">
                                            <div class="flex items-center">
                                                <div class="variationvalues w-full">
                                                    <x-backend.inputs.text name=""
                                                        placeholder="Select variation values" :disabled="true" />
                                                </div>
                                                <button class="button button-default" type="button"
                                                    onclick="removeVariation(this)">
                                                    <span class="footable-toggle fooicon fooicon-minus"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @foreach (generateVariationCombinations($product->variationCombinations()->get()) as $key => $combination)
                                        <div class="grid md:grid-cols-2 gap-3 mb-3">
                                            <div class="md:col-span-1">
                                                <div class="flex items-center">
                                                    <div class="w-full">{{ translate('Attribute') }} <span
                                                            class="attribute-counter">{{ $key + 1 }}</span></div>

                                                    <input type="hidden" name="chosen_variations[]"
                                                        value="{{ $combination['id'] }}">
                                                    <x-backend.inputs.text class="ms-4" name=""
                                                        value="{{ $combination['name'] }}" :disabled="true" />
                                                </div>
                                            </div>

                                            <div class="md:col-span-1">
                                                <div class="flex items-center">
                                                    <div class="variationvalues w-full">
                                                        @php
                                                            $variation_values = \App\Models\VariationValue::where(
                                                                'variation_id',
                                                                $combination['id'],
                                                            )->get();
                                                            $old_val = array_map(function ($val) {
                                                                return $val['id'];
                                                            }, $combination['values']);

                                                        @endphp

                                                        <x-backend.inputs.select
                                                            name="option_{{ $combination['id'] }}_choices[]"
                                                            onchange="generateVariationCombinations()"
                                                            data-placeholder="{{ translate('Select values here') }}"
                                                            multiple data-selection-css-class="multi-select2">
                                                            @foreach ($variation_values as $variation_value)
                                                                <x-backend.inputs.select-option
                                                                    name="{{ $variation_value->collectTranslation('name') }}"
                                                                    value="{{ $variation_value->id }}"
                                                                    selected="{{ in_array($variation_value->id, $old_val) ? $variation_value->id : '' }}" />
                                                            @endforeach
                                                        </x-backend.inputs.select>

                                                    </div>
                                                    <button class="button button-default" type="button"
                                                        onclick="removeVariation(this)">
                                                        <span class="footable-toggle fooicon fooicon-minus"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            {{-- end:variations --}}

                            <div class="mt-3">
                                <button class="button button-default" type="button" onclick="addAnotherVariation()">
                                    <span class="footable-toggle fooicon fooicon-plus me-1"></span>
                                    {{ translate('Add Attribute') }}
                                </button>
                            </div>

                            <div class="variation_combination" id="variation_combination">
                                {{-- combinations will be added here via ajax response --}}
                                @if ($product && $product->has_variation)
                                    @include('backend.admin.products.update-variation-combinations', [
                                        'combinations' => $product->variations,
                                    ])
                                @endif
                            </div>

                            {{-- Bulk Update Variations Section - Always rendered, shown when variations exist --}}
                            <div class="bulk-update-variations bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4"
                                id="bulk_update_section" style="display: none;">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-medium text-gray-700">{{ translate('Bulk Update Variations') }}
                                    </h5>
                                    <span
                                        class="text-sm text-muted">{{ translate('Apply same values to all variations') }}</span>
                                </div>
                                <div class="grid sm:grid-cols-3 gap-3">
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-600">{{ translate('Price') }}</label>
                                        <input type="number" id="bulk_price" min="0" step="0.001"
                                            class="theme-input" placeholder="0.00" />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-600">{{ translate('Discount') }}</label>
                                        <input type="number" id="bulk_discount_value" min="0" step="0.001"
                                            class="theme-input" placeholder="0.00" />
                                    </div>
                                    <div>
                                        <label
                                            class="mb-2 block text-sm font-medium text-gray-600">{{ translate('Discount Type') }}</label>
                                        <select id="bulk_discount_type" class="theme-input h-auto p-3 py-2.5">
                                            <option value="flat">{{ translate('Flat') }}</option>
                                            <option value="percentage">{{ translate('Percentage') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-3">
                                    <button type="button" class="button button-secondary"
                                        onclick="clearBulkUpdate()">
                                        {{ translate('Clear') }}
                                    </button>
                                    <button type="button" class="button button-primary" onclick="applyBulkUpdate()">
                                        {{ translate('Apply to All') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="discount-schedule-field mt-3 space-y-3">
                            @php
                                $hasDiscountSchedule =
                                    $product && $product->discount_start_date && $product->discount_end_date;
                                $start_date = '';
                                $end_date = '';

                                if ($hasDiscountSchedule) {
                                    $start_date = date('m/d/Y', $product->discount_start_date);
                                    $end_date = date('m/d/Y', $product->discount_end_date);
                                }
                            @endphp

                            <input type="hidden" name="discount_schedule_enabled" id="discount_schedule_enabled"
                                value="{{ $hasDiscountSchedule ? 1 : 0 }}">

                            <div class="flex items-center justify-between gap-4">
                                <label for="discount_schedule_toggle" class="theme-input-label pt-0 mb-0">
                                    {{ translate('Discount Schedule') }}
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <span class="text-xs text-muted">{{ translate('Off/On') }}</span>
                                    <x-backend.inputs.checkbox toggler="true" name="discount_schedule_toggle"
                                        id="discount_schedule_toggle" value="1"
                                        isChecked="{{ $hasDiscountSchedule }}" />
                                </label>
                            </div>

                            <div class="discount-schedule-range {{ $hasDiscountSchedule ? '' : 'hidden' }}">
                                <x-backend.inputs.datepicker placeholder="Start date - End date" label=""
                                    name="date_range" :isRequired="false" type="range"
                                    value="{{ $hasDiscountSchedule ? $start_date . ' - ' . $end_date : '' }}" />
                            </div>
                        </div>
                    </div>
                </div>

            @endif
            {{-- Description --}}
            <div class="card mt-3" data-product-section="description">
                <div class="card__title">
                    {{ translate('Product Description') }}
                </div>

                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.textarea rich="true" name="description"
                            placeholder="Type description of product"
                            value="{{ $product?->collectTranslation('description', $langKey) }}" :isRequired="false"
                            aiGenerate="true" />
                    </div>
                </div>
            </div>

            @if (!$product || ($product && $langKey == config('app.default_language')))
                {{-- SEO --}}
                <div class="card mt-3" data-product-section="seo">
                    <div class="card__title">
                        {{ translate('Product SEO Informations') }}
                    </div>

                    <div class="card__content">
                        <div class="space-y-3">
                            <x-backend.inputs.seo metaTitle="{{ $product?->meta_title }}"
                                metaDescription="{{ $product?->meta_description }}"
                                metaKeywords="{{ $product?->meta_keywords }}"
                                metaImage="{{ $product?->meta_image }}" />
                        </div>
                    </div>
                </div>
            @endif


            @unless ($simplifiedAdmin)
                {{-- submit button --}}
                <div class="justify-end mt-6 hidden md:flex">
                    <x-backend.inputs.button buttonText="Save Product" type="submit" />
                </div>
            @endunless
        </div>

        {{-- right bar --}}
        @if (!$product || ($product && $langKey == config('app.default_language')))
            <div class="{{ $simplifiedAdmin ? 'space-y-3' : 'md:col-span-4 space-y-3' }}">
                {{-- Status --}}
                <div class="card" data-product-section="status">
                    <div class="card__title">
                        {{ translate('Product Status') }}
                    </div>
                    <div class="card__content">
                        <div class="space-y-5">
                            @if ($product && $langKey == config('app.default_language'))
                                <div>
                                    <label class="mb-2">
                                        {{ translate('Slug') }}
                                        <span class="text-muted ml-2">{{ url('/products') }}/</span>
                                    </label>
                                    <x-backend.inputs.text name="slug" placeholder="Type custom slug"
                                        value="{{ $product?->slug }}" />
                                </div>


                                <a href="{{ url('/products') }}/{{ $product?->slug }}" target="_blank">
                                    {{ translate('Visit Product') }}
                                    <span class="ml-2 text-secondary-light">
                                        <i class="fa-solid fa-eye"></i>
                                    </span>
                                </a>
                            @endif
                            <div class="admin-status-toggle flex items-center justify-between">
                                <p>{{ translate('Published') }}</p>
                                <x-backend.inputs.checkbox toggler="true" name="is_published" value="1"
                                    :isChecked="$product?->is_published" />
                            </div>

                            <div class="admin-status-toggle flex items-center justify-between">
                                <p>{{ translate('Hide on E-commerce') }}</p>
                                <x-backend.inputs.checkbox toggler="true" name="hide_on_ecom" value="1"
                                    :isChecked="$product?->hide_on_ecom" />
                            </div>

                            @if ($product)
                                <div class="flex justify-between gap-4">
                                    <span>{{ translate('Date Created') }}</span>
                                    <span
                                        class="text-muted">{{ date('d M, Y', strtotime($product->created_at)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- QR Code & Links --}}
                @if ($product && $showQrCodeSection)
                    <div class="card" data-product-section="qr-code">
                        <div class="card__title">
                            {{ translate('Product QR Code') }}
                        </div>
                        <div class="card__content">
                            <div class="space-y-4">
                                {{-- QR Preview --}}
                                <div class="flex justify-center">
                                    <div id="qr-preview" class="border rounded-lg p-3 bg-white">
                                        <img src="{{ route('products.qr.image', ['id' => $product->id]) }}"
                                            alt="QR Code" class="w-32 h-32" id="qr-image">
                                    </div>
                                </div>

                                <div class="flex gap-2 justify-center">
                                    <button type="button" class="button button-secondary text-sm"
                                        onclick="printQrCode()">
                                        <i class="fa-solid fa-print mr-1"></i>
                                        {{ translate('Print') }}
                                    </button>
                                    <a href="{{ route('products.qr.image', ['id' => $product->id]) }}"
                                        download="product-qr-{{ $product->id }}.png"
                                        class="button button-secondary text-sm">
                                        <i class="fa-solid fa-download mr-1"></i>
                                        {{ translate('Download') }}
                                    </a>
                                </div>

                                <p class="text-xs text-muted text-center">
                                    {{ translate('Scan to view product price & details') }}
                                </p>

                                <hr class="border-gray-200 my-4">

                                {{-- QR Color Settings --}}
                                @php
                                    $qrData = $product->qr_data ?? [];
                                    $qrColor = $qrData['color'] ?? '#dc2626';
                                    $qrLinks = $qrData['links'] ?? [];
                                    $companyLogoIcon = uploadedAsset(getSetting('websiteHeaderLogo'));
                                    $qrIconOptions = [
                                        ['value' => 'fa-globe', 'icon' => 'fa-solid fa-globe', 'label' => 'Globe'],
                                        ['value' => 'fa-store', 'icon' => 'fa-solid fa-store', 'label' => 'Store'],
                                        [
                                            'value' => 'fa-cart-shopping',
                                            'icon' => 'fa-solid fa-cart-shopping',
                                            'label' => 'Cart',
                                        ],
                                        [
                                            'value' => 'fa-bag-shopping',
                                            'icon' => 'fa-solid fa-bag-shopping',
                                            'label' => 'Bag',
                                        ],
                                        [
                                            'value' => 'fa-circle-info',
                                            'icon' => 'fa-solid fa-circle-info',
                                            'label' => 'About',
                                        ],
                                        [
                                            'value' => 'fa-address-card',
                                            'icon' => 'fa-solid fa-address-card',
                                            'label' => 'Profile',
                                        ],
                                        ['value' => 'fa-link', 'icon' => 'fa-solid fa-link', 'label' => 'Link'],
                                        ['value' => 'fa-play', 'icon' => 'fa-solid fa-play', 'label' => 'Play'],
                                        [
                                            'value' => 'fa-youtube',
                                            'icon' => 'fa-brands fa-youtube',
                                            'label' => 'YouTube',
                                        ],
                                        [
                                            'value' => 'fa-facebook-f',
                                            'icon' => 'fa-brands fa-facebook-f',
                                            'label' => 'Facebook',
                                        ],
                                        [
                                            'value' => 'fa-instagram',
                                            'icon' => 'fa-brands fa-instagram',
                                            'label' => 'Instagram',
                                        ],
                                        ['value' => 'fa-x-twitter', 'icon' => 'fa-brands fa-x-twitter', 'label' => 'X'],
                                        [
                                            'value' => 'fa-pinterest',
                                            'icon' => 'fa-brands fa-pinterest',
                                            'label' => 'Pinterest',
                                        ],
                                        [
                                            'value' => 'fa-pinterest-p',
                                            'icon' => 'fa-brands fa-pinterest-p',
                                            'label' => 'Pinterest P',
                                        ],
                                        [
                                            'value' => 'fa-whatsapp',
                                            'icon' => 'fa-brands fa-whatsapp',
                                            'label' => 'WhatsApp',
                                        ],
                                        ['value' => 'fa-phone', 'icon' => 'fa-solid fa-phone', 'label' => 'Phone'],
                                        [
                                            'value' => 'fa-envelope',
                                            'icon' => 'fa-solid fa-envelope',
                                            'label' => 'Email',
                                        ],
                                        [
                                            'value' => 'fa-location-dot',
                                            'icon' => 'fa-solid fa-location-dot',
                                            'label' => 'Location',
                                        ],
                                        ['value' => 'fa-map', 'icon' => 'fa-solid fa-map', 'label' => 'Map'],
                                        [
                                            'value' => 'fa-map-marker-alt',
                                            'icon' => 'fa-solid fa-map-marker-alt',
                                            'label' => 'Marker',
                                        ],
                                        ['value' => 'fa-user', 'icon' => 'fa-solid fa-user', 'label' => 'User'],
                                        ['value' => 'fa-users', 'icon' => 'fa-solid fa-users', 'label' => 'Users'],
                                        [
                                            'value' => 'fa-id-card',
                                            'icon' => 'fa-solid fa-id-card',
                                            'label' => 'ID Card',
                                        ],
                                        [
                                            'value' => 'fa-building',
                                            'icon' => 'fa-solid fa-building',
                                            'label' => 'Business',
                                        ],
                                        [
                                            'value' => 'fa-briefcase',
                                            'icon' => 'fa-solid fa-briefcase',
                                            'label' => 'Work',
                                        ],
                                        [
                                            'value' => 'fa-calendar',
                                            'icon' => 'fa-solid fa-calendar',
                                            'label' => 'Calendar',
                                        ],
                                        ['value' => 'fa-clock', 'icon' => 'fa-solid fa-clock', 'label' => 'Time'],
                                        ['value' => 'fa-image', 'icon' => 'fa-solid fa-image', 'label' => 'Gallery'],
                                        ['value' => 'fa-camera', 'icon' => 'fa-solid fa-camera', 'label' => 'Camera'],
                                        ['value' => 'fa-music', 'icon' => 'fa-solid fa-music', 'label' => 'Music'],
                                        ['value' => 'fa-file', 'icon' => 'fa-solid fa-file', 'label' => 'File'],
                                        ['value' => 'fa-file-pdf', 'icon' => 'fa-solid fa-file-pdf', 'label' => 'PDF'],
                                        [
                                            'value' => 'fa-download',
                                            'icon' => 'fa-solid fa-download',
                                            'label' => 'Download',
                                        ],
                                        ['value' => 'fa-upload', 'icon' => 'fa-solid fa-upload', 'label' => 'Upload'],
                                        ['value' => 'fa-share', 'icon' => 'fa-solid fa-share', 'label' => 'Share'],
                                        [
                                            'value' => 'fa-share-nodes',
                                            'icon' => 'fa-solid fa-share-nodes',
                                            'label' => 'Share Network',
                                        ],
                                        ['value' => 'fa-heart', 'icon' => 'fa-solid fa-heart', 'label' => 'Favorite'],
                                        ['value' => 'fa-star', 'icon' => 'fa-solid fa-star', 'label' => 'Star'],
                                        [
                                            'value' => 'fa-thumbs-up',
                                            'icon' => 'fa-solid fa-thumbs-up',
                                            'label' => 'Like',
                                        ],
                                        [
                                            'value' => 'fa-comment',
                                            'icon' => 'fa-solid fa-comment',
                                            'label' => 'Comment',
                                        ],
                                        ['value' => 'fa-comments', 'icon' => 'fa-solid fa-comments', 'label' => 'Chat'],
                                        [
                                            'value' => 'fa-headset',
                                            'icon' => 'fa-solid fa-headset',
                                            'label' => 'Support',
                                        ],
                                        ['value' => 'fa-lock', 'icon' => 'fa-solid fa-lock', 'label' => 'Lock'],
                                        ['value' => 'fa-unlock', 'icon' => 'fa-solid fa-unlock', 'label' => 'Unlock'],
                                        ['value' => 'fa-key', 'icon' => 'fa-solid fa-key', 'label' => 'Key'],
                                        ['value' => 'fa-qrcode', 'icon' => 'fa-solid fa-qrcode', 'label' => 'QR Code'],
                                        [
                                            'value' => 'fa-barcode',
                                            'icon' => 'fa-solid fa-barcode',
                                            'label' => 'Barcode',
                                        ],
                                        ['value' => 'fa-wifi', 'icon' => 'fa-solid fa-wifi', 'label' => 'WiFi'],
                                        [
                                            'value' => 'fa-bluetooth',
                                            'icon' => 'fa-brands fa-bluetooth',
                                            'label' => 'Bluetooth',
                                        ],
                                        [
                                            'value' => 'fa-telegram',
                                            'icon' => 'fa-brands fa-telegram',
                                            'label' => 'Telegram',
                                        ],
                                        [
                                            'value' => 'fa-linkedin-in',
                                            'icon' => 'fa-brands fa-linkedin-in',
                                            'label' => 'LinkedIn',
                                        ],
                                        ['value' => 'fa-github', 'icon' => 'fa-brands fa-github', 'label' => 'GitHub'],
                                        [
                                            'value' => 'fa-discord',
                                            'icon' => 'fa-brands fa-discord',
                                            'label' => 'Discord',
                                        ],
                                        ['value' => 'fa-tiktok', 'icon' => 'fa-brands fa-tiktok', 'label' => 'TikTok'],
                                    ];

                                    if ($companyLogoIcon) {
                                        array_unshift($qrIconOptions, [
                                            'value' => $companyLogoIcon,
                                            'image_url' => $companyLogoIcon,
                                            'label' => 'Company Logo',
                                        ]);
                                    }
                                @endphp
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-medium text-sm">{{ translate('QR Code Color') }}</span>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="qr-color-picker" name="qr_color"
                                            value="{{ $qrColor }}"
                                            class="w-10 h-10 rounded cursor-pointer border-0 p-0"
                                            onchange="updateQrColor(this.value)">
                                        <span id="qr-color-value"
                                            class="text-xs text-gray-600 font-mono">{{ $qrColor }}</span>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="font-medium text-sm">{{ translate('Recipe/Additional Details') }}</span>
                                        <x-backend.inputs.checkbox toggler="true" id="show_recipe_details"
                                            name="show_recipe_details" value="1" :isChecked="$product?->show_recipe_details" />
                                    </div>

                                    <div id="recipe_details_section"
                                        style="{{ $product?->show_recipe_details ? '' : 'display:none;' }}">
                                        <div class="space-y-3">
                                            <x-backend.inputs.textarea label="Details" name="recipe_details"
                                                placeholder="Type recipe or additional details"
                                                value="{{ $product?->recipe_details }}" :isRequired="false" />

                                            <x-backend.inputs.file label="Image" name="recipe_image"
                                                value="{{ $product?->recipe_image }}" :isRequired="false"
                                                filesHint="This image will be used for recipe/additional details." />
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-gray-200 my-4">

                                {{-- QR Links Section --}}
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="font-medium text-sm">{{ translate('QR Landing Links') }}</span>
                                        <span
                                            class="text-xs text-muted">{{ translate('Shown when QR is scanned') }}</span>
                                    </div>

                                    <div id="qr-links-container">
                                        @foreach ($qrLinks as $index => $link)
                                            <div
                                                class="qr-link-item bg-gray-50 border rounded-lg mb-3 overflow-hidden">
                                                {{-- Collapsible Header --}}
                                                <div class="qr-link-header flex items-center justify-between p-3 cursor-pointer hover:bg-gray-100 transition-colors"
                                                    onclick="toggleQrLink(this)">
                                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                                        <i
                                                            class="fa-solid fa-chevron-down qr-link-chevron text-gray-400 transition-transform"></i>
                                                        <span class="qr-link-title font-medium text-sm truncate">
                                                            {{ $link['title'] ?? ($link['url'] ? parse_url($link['url'], PHP_URL_HOST) : translate('New Link')) }}
                                                        </span>
                                                    </div>
                                                    <button type="button"
                                                        class="text-red-500 hover:text-red-700 ml-2"
                                                        onclick="event.stopPropagation(); removeQrLink(this)">
                                                        <i class="fa-solid fa-trash text-xs"></i>
                                                    </button>
                                                </div>
                                                {{-- Collapsible Content --}}
                                                <div class="qr-link-content border-t border-gray-200 p-3 space-y-3">
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-600 mb-1 block">{{ translate('Link URL') }}
                                                            <span class="text-red-500">*</span></label>
                                                        <input type="url"
                                                            name="qr_links[{{ $index }}][url]"
                                                            value="{{ $link['url'] ?? '' }}"
                                                            class="theme-input text-sm qr-link-url"
                                                            placeholder="https://example.com" required
                                                            oninput="updateQrLinkTitle(this)">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="text-xs text-gray-600 mb-1 block">{{ translate('Title') }}
                                                            <span
                                                                class="text-muted">({{ translate('Optional') }})</span></label>
                                                        <input type="text"
                                                            name="qr_links[{{ $index }}][title]"
                                                            value="{{ $link['title'] ?? '' }}"
                                                            class="theme-input text-sm qr-link-title-input"
                                                            placeholder="{{ translate('e.g., Buy Now, Learn More') }}"
                                                            oninput="updateQrLinkTitle(this.closest('.qr-link-item').querySelector('.qr-link-url'))">
                                                    </div>
                                                    <div class="qr-link-icon-field border border-gray-200 rounded-lg bg-white overflow-hidden">
                                                        <button type="button"
                                                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-left hover:bg-gray-50 transition-colors"
                                                            onclick="toggleQrIconSection(this)">
                                                            <span class="min-w-0">
                                                                <span
                                                                    class="text-xs text-gray-600 block">{{ translate('Icon') }}
                                                                    <span
                                                                        class="text-muted">({{ translate('Optional') }})</span></span>
                                                                <span
                                                                    class="text-[11px] text-muted block mt-0.5">{{ translate('Expand to choose or paste a custom icon') }}</span>
                                                            </span>
                                                            <span class="flex items-center gap-2 flex-shrink-0">
                                                                <span
                                                                    class="qr-link-icon-preview inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 overflow-hidden">
                                                                    <i class="fa-solid fa-link"></i>
                                                                </span>
                                                                <i
                                                                    class="fa-solid fa-chevron-down qr-link-icon-chevron text-xs text-gray-400 transition-transform -rotate-90"></i>
                                                            </span>
                                                        </button>
                                                        <div class="qr-link-icon-content border-t border-gray-200 p-3 space-y-2"
                                                            style="display: none;">
                                                            <input type="text"
                                                                name="qr_links[{{ $index }}][icon]"
                                                                value="{{ $link['icon'] ?? '' }}"
                                                                class="theme-input text-sm qr-link-icon-input"
                                                                list="qr-link-icon-options"
                                                                placeholder="{{ translate('Select an icon or paste image URL') }}"
                                                                oninput="updateQrLinkIconPreview(this)">
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach ($qrIconOptions as $iconOption)
                                                                    <button type="button"
                                                                        class="qr-icon-option inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 hover:border-secondary hover:text-secondary transition-all"
                                                                        data-icon-value="{{ $iconOption['value'] }}"
                                                                        title="{{ $iconOption['label'] }}"
                                                                        onclick="selectQrLinkIcon(this, '{{ $iconOption['value'] }}')">
                                                                        @if (!empty($iconOption['image_url']))
                                                                            <img src="{{ $iconOption['image_url'] }}"
                                                                                alt="{{ $iconOption['label'] }}"
                                                                                class="h-5 w-5 object-contain">
                                                                        @else
                                                                            <i class="{{ $iconOption['icon'] }}"></i>
                                                                        @endif
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                            <p class="text-xs text-muted">
                                                                {{ translate('Choose a Font Awesome icon or paste a custom icon image URL') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <datalist id="qr-link-icon-options">
                                        @foreach ($qrIconOptions as $iconOption)
                                            <option value="{{ $iconOption['value'] }}">{{ $iconOption['label'] }}
                                            </option>
                                        @endforeach
                                    </datalist>

                                    <button type="button" class="button button-secondary w-full text-sm"
                                        onclick="addQrLink()">
                                        <i class="fa-solid fa-plus mr-1"></i>{{ translate('Add Link') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Categories --}}
                <div class="card" data-product-section="categories">
                    <div class="card__title">
                        {{ translate('Product Categories') }}
                    </div>
                    <div class="card__content">
                        @php
                            $productCategories = $product ? $product->categories()->pluck('category_id') : collect();
                        @endphp

                        <div class="mb-4">
                            <div class="theme-input-wrapper relative">
                                <input type="search" class="theme-input pr-10 product-category-search"
                                    data-category-search
                                    placeholder="{{ translate('Search categories or subcategories') }}"
                                    autocomplete="off">
                                <span
                                    class="absolute top-0 right-0 h-full flex items-center justify-center px-3 text-muted pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </div>
                        </div>

                        <ul class="max-h-[315px] overflow-y-auto cateogry-tree">
                            <div class="category-tree-data space-y-5">
                                @include('components.backend.inc.products.category-list', [
                                    'categories' => $categories,
                                    'productCategories' => $productCategories,
                                    'selectedCategories' => [],
                                    'langKey' => $langKey,
                                ])
                            </div>
                        </ul>
                        <p class="category-search-empty mt-3 hidden text-sm text-muted" data-category-search-empty>
                            {{ translate('No categories found') }}
                        </p>
                        @if (user()->user_type != 'seller')
                            @can('create_categories')
                                <a href="javascript:void(0);" class="text-secondary-light !mt-7 category-create-modal"
                                    data-micromodal-trigger="category-create-modal">
                                    {{ translate('Add New Category') }}
                                    <span class="ml-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>

                {{-- Brand --}}
                <div class="card" data-product-section="brand">
                    <div class="card__title">
                        {{ translate('Product Brand') }}
                    </div>
                    <div class="card__content">
                        <ul class="space-y-5 max-h-[315px] overflow-y-auto brand-tree-data">

                            @include('components.backend.inc.products.brand-list', [
                                'product' => $product,
                                'langKey' => $langKey,
                                'selectedBrand' => null,
                            ])
                        </ul>

                        @if (user()->user_type != 'seller')
                            @can('create_brands')
                                <a href="javascript:void(0);" class="text-secondary-light !mt-4 brand-create-modal"
                                    data-micromodal-trigger="brand-create-modal">
                                    {{ translate('Add New Brand') }}
                                    <span class="ml-2">
                                        <i class="fa-solid fa-plus"></i>
                                    </span>
                                </a>
                            @endcan
                        @endif
                    </div>
                </div>

                @if ($showTagsBadgesCard)
                    {{-- Tags and Badges --}}
                    <div class="card" data-product-section="tags-badges">
                        <div class="card__title">
                            {{ translate('Tags and Badges') }}
                        </div>

                        <div class="card__content">
                            <div class="space-y-8">
                                @if ($showTagsField)
                                    <div>
                                        @php
                                            $productTags = collect();
                                            if ($product) {
                                                $productTags = $product->tags()->pluck('tag_id');
                                            }
                                        @endphp
                                        <div class="mb-2 flex justify-between">
                                            <span>{{ translate('Tags') }}</span>

                                            @if (user()->user_type != 'seller')
                                                @can('create_tags')
                                                    <a href="javascript:void(0);" class="text-secondary-light tag-create-modal"
                                                        data-micromodal-trigger="tag-create-modal">

                                                        <span class="mr-1">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </span>
                                                        {{ translate('Add New Tag') }}
                                                    </a>
                                                @endcan
                                            @endif
                                        </div>

                                        <div class="tag-tree-data">
                                            @include('components.backend.inc.products.tag-list', [
                                                'tags' => $tags,
                                                'productTags' => $productTags,
                                            ])
                                        </div>
                                    </div>
                                @endif

                                @if ($showBadgesField)
                                    <div>
                                        <div class="mb-2 flex justify-between">
                                            <span>{{ translate('Badges') }}</span>

                                            @if (user()->user_type != 'seller')
                                                @can('create_badges')
                                                    <a href="javascript:void(0);" class="text-secondary-light badge-create-modal"
                                                        data-micromodal-trigger="badge-create-modal">

                                                        <span class="mr-1">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </span>
                                                        {{ translate('Add New Badge') }}
                                                    </a>
                                                @endcan
                                            @endif
                                        </div>
                                        @php
                                            $productBadges = collect();
                                            if ($product) {
                                                $productBadges = $product->badges()->pluck('badge_id');
                                            }
                                        @endphp

                                        <div class="badge-tree-data">
                                            @include('components.backend.inc.products.badge-list', [
                                                'badges' => $badges,
                                                'productBadges' => $productBadges,
                                            ])
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($showTaxesCard)
                    {{-- Taxes --}}
                    <div class="card" data-product-section="taxes">
                        <div class="card__title flex items-center justify-between">
                            <span>
                                {{ translate('Product Taxes') }}
                            </span>

                            <small class="muted">
                                ({{ translate('Default 0%') }})
                            </small>
                        </div>
                        <div class="card__content">
                            @foreach ($taxes as $tax)
                                @php
                                    $taxValue = 0;
                                    $taxType = 'amount';
                                    if ($product && $product->taxes()->count() > 0) {
                                        $productTax = $product->taxes()->firstWhere('tax_id', $tax->id);
                                        $taxValue = $productTax->tax_value;
                                        $taxType = $productTax->tax_type;
                                    }
                                @endphp
                                <input type="hidden" value="{{ $tax->id }}" name="tax_ids[]">
                                <div class="mb-4">
                                    <label for="" class="theme-input-label !pt-0">
                                        {{ $tax->name }}
                                    </label>
                                    <div class="grid md:grid-cols-2 gap-3">
                                        <div class="md:col-span-1">
                                            <x-backend.inputs.number name="tax_values[]" min="0" step="0.001"
                                                value="{{ $taxValue }}" />
                                        </div>
                                        <div class="md:col-span-1">
                                            <x-backend.inputs.select name="tax_types[]" class="">
                                                <x-backend.inputs.select-option name="{{ translate('Amount') }}"
                                                    value="amount" selected="{{ $taxType }}" />
                                                <x-backend.inputs.select-option name="{{ translate('Percentage') }}"
                                                    value="percentage" selected="{{ $taxType }}" />
                                            </x-backend.inputs.select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($showOtherInfoCard)
                    <div class="card" data-product-section="other-info">
                        <div class="card__title flex items-center justify-between">
                            <span>
                                {{ translate('Other Informations') }}
                            </span>
                        </div>
                        <div class="card__content">
                            <div class="space-y-8">

                                @if ($showEstimatedDeliveryTimeField)
                                    {{-- est_delivery_time --}}
                                    <div>
                                        <x-backend.inputs.text :labelInline="false" label="Estimated Delivery Time"
                                            name="est_delivery_time" placeholder="Type etimated delivery hours"
                                            value="{{ $product?->est_delivery_time }}" :isRequired="false" />
                                    </div>
                                @endif

                                @if ($showEmiField)
                                    {{-- emi --}}
                                    <div class="flex items-center justify-between">
                                        <p>{{ translate('EMI Available') }}</p>
                                        <x-backend.inputs.checkbox toggler="true" name="has_emi" value="1"
                                            :isChecked="$product?->has_emi" />
                                    </div>

                                    <div>
                                        <x-backend.inputs.text :labelInline="false" label="Emi Info" name="emi_info"
                                            placeholder="Type EMI info if available" value="{{ $product?->emi_info }}"
                                            :isRequired="false" />
                                    </div>
                                @endif

                                @if ($showWarrantyField)
                                    {{-- warrenty --}}
                                    <div class="flex items-center justify-between">
                                        <p>{{ translate('Warrenty Available') }}</p>
                                        <x-backend.inputs.checkbox toggler="true" name="has_warranty" value="1"
                                            :isChecked="$product?->has_warranty" />
                                    </div>

                                    <div>
                                        <label class="mb-2" for="warranty_info">{{ translate('Warranty Info') }}</label>
                                        <x-backend.inputs.text name="warranty_info" placeholder="Type Warranty info"
                                            value="{{ $product?->warranty_info }}" :isRequired="false" />
                                    </div>
                                @endif

                                @if ($showAlertQtyField)
                                    {{-- alert_qty --}}
                                    <div>
                                        <x-backend.inputs.number :labelInline="false" label="Alert Quantity" name="alert_qty"
                                            placeholder="Type alert quantity" value="{{ $product?->alert_qty ?? 0 }}"
                                            min="0" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Shipping Class --}}
                <div class="card" data-product-section="shipping">
                    <div class="card__title">
                        <span>
                            {{ translate('Shipping Class') }}
                        </span>
                    </div>
                    <div class="card__content">
                        <x-backend.inputs.select label="Assign Shipping Class" onchange="shippingCharges(this)"
                            :labelInline="false" :isRequired="false" name="shipping_class_id" class="">
                            {{-- <x-backend.inputs.select-option name="Select Shipping Class" value="" /> --}}
                            @foreach ($shippingClasses as $class)
                                <x-backend.inputs.select-option name="{{ $class->name }}"
                                    value="{{ $class->id }}"
                                    selected="{{ $product?->shipping_class_id == $class->id ? $class->id : null }}"
                                    data-shipping-charges="{{ $class->shippingCharges }}" />
                            @endforeach
                        </x-backend.inputs.select>
                        <div id="shipping-charges-display" class="mt-4 space-y-1 text-sm text-gray-600"></div>
                    </div>
                </div>

                @if ($showPreOrderField)
                    {{-- Pre-Order Settings --}}
                    @php
                        $preOrder = $product?->pre_order ?? null;
                        $isPreOrderActive = $preOrder['is_active'] ?? false;
                        $orderLimit = $preOrder['order_limit'] ?? '';
                        $totalOrderLimit = $preOrder['total_order_limit'] ?? '';
                        $startDate = $preOrder['start_date'] ?? '';
                        $endDate = $preOrder['end_date'] ?? '';
                        $expectedReleaseDate = $preOrder['expected_release_date'] ?? '';
                        $preOrderDescription = $preOrder['description'] ?? '';
                    @endphp
                    <div class="card" data-product-section="pre-order">
                        <div class="card__title flex items-center justify-between">
                            <span>{{ translate('Pre-Order Settings') }}</span>
                            <x-backend.inputs.checkbox toggler="true" name="pre_order_is_active" value="1"
                                :isChecked="$isPreOrderActive" id="pre_order_is_active" />
                        </div>
                        <div class="card__content" id="pre_order_section"
                            style="{{ !$isPreOrderActive ? 'display:none;' : '' }}">
                            <div class="space-y-4">
                                <x-backend.inputs.number label="Order Limit (per customer)" name="pre_order_order_limit"
                                    placeholder="0 for unlimited" value="{{ $orderLimit }}" min="0"
                                    :isRequired="false" />

                                <x-backend.inputs.number label="Total Order Limit" name="pre_order_total_order_limit"
                                    placeholder="0 for unlimited" value="{{ $totalOrderLimit }}" min="0"
                                    :isRequired="false" />

                                <x-backend.inputs.datepicker label="Start Date" name="pre_order_start_date"
                                    placeholder="Select start date" :isRequired="false" type="date"
                                    value="{{ $startDate }}" />

                                <x-backend.inputs.datepicker label="End Date" name="pre_order_end_date"
                                    placeholder="Select end date" :isRequired="false" type="date"
                                    value="{{ $endDate }}" />

                                <x-backend.inputs.datepicker label="Expected Release Date"
                                    name="pre_order_expected_release_date" placeholder="Select release date"
                                    :isRequired="false" type="date" value="{{ $expectedReleaseDate }}" />

                                <x-backend.inputs.textarea label="Pre-Order Description" name="pre_order_description"
                                    placeholder="Enter description shown to customers" :isRequired="false"
                                    value="{{ $preOrderDescription }}" rows="3" />
                            </div>
                        </div>
                    </div>

                    {{-- Pre-Order Variation Settings --}}
                    @if ($product && $product->has_variation)
                        @php
                            $variationPreOrders = [];
                            foreach ($product->variations as $variation) {
                                $po = $variation->pre_order;
                                if ($po === null) {
                                    $variationPreOrders[$variation->id] = 'inherit';
                                } elseif (isset($po['enabled']) && $po['enabled'] === false) {
                                    $variationPreOrders[$variation->id] = 'disable';
                                } else {
                                    $variationPreOrders[$variation->id] = 'enable';
                                }
                            }
                        @endphp
                        <div class="card" data-product-section="pre-order-variations">
                            <div class="card__title">
                                {{ translate('Pre-Order per Variation') }}
                            </div>
                            <div class="card__content">
                                <p class="text-sm text-gray-600 mb-3">
                                    {{ translate('Control which variations can be pre-ordered. If set to "Inherit", the product-level settings will be used.') }}
                                </p>
                                @foreach ($product->variations as $variation)
                                    @php
                                        $variationName = generateVariationName($variation->code);
                                        $currentSetting = $variationPreOrders[$variation->id] ?? 'inherit';
                                    @endphp
                                    <div class="mb-4 pb-4 border-b last:border-b-0">
                                        <div class="font-medium text-gray-700 mb-2">{{ $variationName }}</div>
                                        <div class="flex gap-4">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" name="pre_order_variation_{{ $variation->id }}"
                                                    value="inherit" {{ $currentSetting === 'inherit' ? 'checked' : '' }}
                                                    class="mr-2"
                                                    onchange="updateVariationPreOrder({{ $variation->id }}, 'inherit')" />
                                                <span class="text-sm">{{ translate('Inherit') }}</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" name="pre_order_variation_{{ $variation->id }}"
                                                    value="enable" {{ $currentSetting === 'enable' ? 'checked' : '' }}
                                                    class="mr-2"
                                                    onchange="updateVariationPreOrder({{ $variation->id }}, 'enable')" />
                                                <span class="text-sm">{{ translate('Enable') }}</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" name="pre_order_variation_{{ $variation->id }}"
                                                    value="disable" {{ $currentSetting === 'disable' ? 'checked' : '' }}
                                                    class="mr-2"
                                                    onchange="updateVariationPreOrder({{ $variation->id }}, 'disable')" />
                                                <span class="text-sm">{{ translate('Disable') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>
    @unless ($simplifiedAdmin)
        {{-- submit button --}}
        <div class="flex justify-end mt-6 md:hidden">
            <x-backend.inputs.button buttonText="Save Product" type="submit" />
        </div>
    @endunless

    @if ($simplifiedAdmin)
        {{-- bottom submit button --}}
        <div class="flex justify-end mt-6 mb-8">
            <x-backend.inputs.button buttonText="Save Product" type="submit" />
        </div>
    @endif
</form>

@if ($simplifiedAdmin)
    <style>
        #admin-product-essentials .admin-essential-field {
            min-width: 0;
        }

        #admin-product-essentials .theme-input-group {
            display: block;
        }

        #admin-product-essentials .theme-input-label {
            display: flex;
            padding-top: 0;
            padding-bottom: 0.5rem;
        }

        #admin-product-essentials .admin-unit-field .theme-input-label {
            justify-content: space-between;
            align-items: center;
        }

        #admin-product-essentials .admin-unit-field .theme-input-label.input-required::after {
            order: 1;
        }

        #admin-product-essentials .admin-unit-field .unit-create-modal {
            order: 2;
            margin-left: auto;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: none;
        }

        #admin-product-essentials .theme-input-wrapper {
            width: 100%;
            min-width: 0;
        }

        #admin-product-essentials .theme-input--file-upload > div:first-child {
            margin-bottom: 0.5rem;
        }

        #admin-product-essentials .file-preview {
            min-height: 0;
        }

        #admin-product-essentials .admin-direct-upload-button {
            white-space: nowrap;
        }

        #admin-product-essentials .admin-direct-upload-status {
            display: block;
            margin-top: 0.4rem;
            font-size: 0.75rem;
            color: rgb(var(--muted));
        }

        #admin-product-essentials .admin-direct-upload-status.is-error {
            color: rgb(239 68 68);
        }

        #admin-product-essentials .admin-essential-panel {
            min-width: 0;
            border: 1px solid rgb(var(--border));
            border-radius: 0.75rem;
            padding: 1rem;
            background: rgb(var(--background-primary-light));
            color: rgb(var(--foreground));
        }

        #admin-product-essentials .admin-essential-panel .card__content {
            padding: 0;
        }

        #admin-product-essentials .admin-essential-panel .cateogry-tree,
        #admin-product-essentials .admin-essential-panel .brand-tree-data {
            max-height: 9rem;
            overflow-y: auto;
        }

        #admin-product-essentials .admin-status-toggle-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        #admin-product-essentials .admin-status-toggle {
            min-height: 3rem;
            border: 1px solid rgb(var(--border));
            border-radius: 0.5rem;
            padding: 0.75rem;
            background: rgb(var(--background));
            color: rgb(var(--foreground));
        }

        #admin-product-essentials .admin-essential-panel-status label,
        #admin-product-essentials .admin-essential-panel-status p,
        #admin-product-essentials .admin-essential-panel-status > .card__content > .space-y-5 > div > span:first-child {
            color: rgb(var(--foreground));
        }

        #admin-product-essentials .admin-essential-panel-status .theme-input {
            border-color: rgb(var(--border));
            background: rgb(var(--background));
            color: rgb(var(--foreground));
        }

        #admin-product-essentials .admin-essential-panel-status a {
            color: rgb(var(--secondary-light));
        }

        .dark #admin-product-essentials .admin-essential-panel-status .text-muted {
            color: rgb(var(--muted));
        }

        .admin-product-layout .admin-product-accordion > .card__title {
            cursor: pointer;
            user-select: none;
        }

        .admin-product-layout .admin-product-accordion > .card__title::after {
            content: '';
            width: 0.55rem;
            height: 0.55rem;
            margin-left: auto;
            border-right: 2px solid rgb(var(--muted));
            border-bottom: 2px solid rgb(var(--muted));
            transform: rotate(45deg);
            transition: transform 150ms ease;
        }

        .admin-product-layout .admin-product-accordion.is-open > .card__title::after {
            transform: rotate(225deg);
        }

        .admin-product-layout .admin-product-accordion:not(.is-open) > .card__content {
            display: none;
        }

        .admin-product-layout > div,
        .admin-product-layout .card {
            width: 100%;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const essentialsGrid = document.getElementById('admin-product-essentials-grid');
            const productLayout = document.querySelector('.admin-product-layout');
            const variationToggleContainer = document.getElementById('admin-product-variation-toggle');
            const variationsContent = document.getElementById('admin-product-variations-content');
            const languageSelectorContainer = document.getElementById('admin-product-language-selector');

            if (!essentialsGrid || !productLayout || !variationToggleContainer || !variationsContent) {
                return;
            }

            const addField = (selector, extraClasses = '') => {
                const input = document.querySelector(selector);
                const field = input?.closest('.theme-input-group');

                if (!field) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = `admin-essential-field ${extraClasses}`.trim();
                wrapper.appendChild(field);
                essentialsGrid.appendChild(wrapper);
            };

            const addContainer = (selector, extraClasses = '') => {
                const container = document.querySelector(selector);

                if (!container) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = `admin-essential-field ${extraClasses}`.trim();
                wrapper.appendChild(container);
                essentialsGrid.appendChild(wrapper);
            };

            const addPanel = (sectionName, title, extraClasses = '') => {
                const section = productLayout.querySelector(`[data-product-section="${sectionName}"]`);
                const content = section?.querySelector(':scope > .card__content');

                if (!section || !content) {
                    return;
                }

                const panel = document.createElement('div');
                panel.className = `admin-essential-panel admin-essential-panel-${sectionName} ${extraClasses}`.trim();
                panel.innerHTML = `<h4 class="mb-3 text-sm font-semibold text-foreground">${title}</h4>`;
                panel.appendChild(content);
                essentialsGrid.appendChild(panel);
                section.hidden = true;
            };

            addField('#name');
            addContainer('.unit-tree-data', 'admin-unit-field');
            addField('#thumbnail_image');
            addField('#gallery_images');
            addField('#sku', 'non-variant-essential');
            addField('#price', 'non-variant-essential');
            addField('#discount_value');
            addField('#stock_qty', 'non-variant-essential');
            addField('select[name="discount_type"]');
            addContainer('.discount-schedule-field', 'md:col-span-2');

            const discountScheduleToggle = document.getElementById('discount_schedule_toggle');
            const discountScheduleEnabled = document.getElementById('discount_schedule_enabled');
            const discountScheduleRange = productLayout.querySelector('.discount-schedule-range');
            const discountScheduleInput = document.getElementById('date_range');

            const syncDiscountSchedule = () => {
                const isEnabled = discountScheduleToggle?.checked ?? false;

                if (discountScheduleEnabled) {
                    discountScheduleEnabled.value = isEnabled ? '1' : '0';
                }

                discountScheduleRange?.classList.toggle('hidden', !isEnabled);

                if (!isEnabled && discountScheduleInput) {
                    discountScheduleInput.value = '';
                }
            };

            discountScheduleToggle?.addEventListener('change', syncDiscountSchedule);
            syncDiscountSchedule();

            const unitField = essentialsGrid.querySelector('.admin-unit-field');
            const unitLabel = unitField?.querySelector('.theme-input-label');
            const addUnitAction = productLayout.querySelector('.unit-create-modal');
            const addUnitActionWrapper = addUnitAction?.closest('.text-end');

            if (unitLabel && addUnitAction) {
                unitLabel.appendChild(addUnitAction);
                addUnitActionWrapper?.remove();
            }

            const languageSelector = productLayout.querySelector('.product-language-selector');
            if (languageSelector && languageSelectorContainer) {
                languageSelectorContainer.appendChild(languageSelector);
            }

            const variationToggle = document.getElementById('product-variation-toggle');
            if (variationToggle) {
                variationToggleContainer.appendChild(variationToggle);
            }

            const variationForm = productLayout.querySelector('[data-product-section="price-stock"] .hasVariation');
            if (variationForm) {
                variationsContent.appendChild(variationForm);
            }

            addPanel('description', `{{ translate('Product Description') }}`, 'md:col-span-2');
            addPanel('status', `{{ translate('Product Status') }}`, 'md:col-span-2');
            addPanel('categories', `{{ translate('Product Categories') }}`);
            addPanel('brand', `{{ translate('Product Brand') }}`);

            const statusPanel = essentialsGrid.querySelector('.admin-essential-panel .admin-status-toggle');
            const statusContent = statusPanel?.closest('.card__content');
            const statusToggles = statusContent?.querySelectorAll('.admin-status-toggle');

            if (statusContent && statusToggles?.length) {
                const toggleRow = document.createElement('div');
                toggleRow.className = 'admin-status-toggle-row';
                statusToggles.forEach((toggle) => toggleRow.appendChild(toggle));
                statusContent.querySelector(':scope > .space-y-5')?.prepend(toggleRow);
            }

            const priceStockSection = productLayout.querySelector('[data-product-section="price-stock"]');
            if (priceStockSection) {
                priceStockSection.hidden = true;
            }

            const generalSection = productLayout.querySelector('[data-product-section="general"]');
            if (generalSection) {
                generalSection.hidden = true;
            }

            const setAccordionState = (card, isOpen) => {
                card.classList.toggle('is-open', isOpen);
                card.querySelector(':scope > .card__title')?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            productLayout.querySelectorAll('.card[data-product-section]').forEach((card) => {
                if (card.hidden) {
                    return;
                }

                const title = card.querySelector(':scope > .card__title');
                const content = card.querySelector(':scope > .card__content');

                const hasFormContent = content?.querySelector(
                    'input, select, textarea, button, a, .jodit-container',
                );

                if (!title || !content || !hasFormContent) {
                    card.hidden = true;
                    return;
                }

                card.classList.add('admin-product-accordion');
                title.setAttribute('role', 'button');
                title.setAttribute('tabindex', '0');
                setAccordionState(card, false);

                const toggleCard = (event) => {
                    if (event.target.closest('input, button, a, select, textarea, label')) {
                        return;
                    }

                    setAccordionState(card, !card.classList.contains('is-open'));
                };

                title.addEventListener('click', toggleCard);
                title.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        setAccordionState(card, !card.classList.contains('is-open'));
                    }
                });

                title.querySelectorAll('input').forEach((input) => {
                    input.addEventListener('change', function() {
                        if (this.checked) {
                            setAccordionState(card, true);
                        }
                    });
                });
            });

            const openContainingAccordion = (element) => {
                const card = element?.closest('.admin-product-accordion');
                if (card) {
                    setAccordionState(card, true);
                }
            };

            document.getElementById('product-form')?.addEventListener('invalid', function(event) {
                openContainingAccordion(event.target);
            }, true);

            const variantToggle = document.getElementById('is_variant');
            const syncVariantEssentials = () => {
                const hasVariations = variantToggle?.checked ?? false;

                essentialsGrid.querySelectorAll('.non-variant-essential').forEach((field) => {
                    field.hidden = hasVariations;
                });

                variationsContent.hidden = !hasVariations;
            };

            variantToggle?.addEventListener('change', syncVariantEssentials);
            syncVariantEssentials();

            const refreshMediaPreviews = () => {
                window.mediaManager?.getPreviouslySelectedMedia?.();
            };

            const setupDirectImageUpload = (inputId, multiple) => {
                const mediaInput = document.getElementById(inputId);
                const inputGroup = mediaInput?.closest('.theme-input--file-upload');
                const actionRow = inputGroup?.querySelector('.theme-input-wrapper > .flex');

                if (!mediaInput || !inputGroup || !actionRow) {
                    return;
                }

                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.accept = 'image/*';
                fileInput.multiple = multiple;
                fileInput.hidden = true;

                const uploadButton = document.createElement('button');
                uploadButton.type = 'button';
                uploadButton.className = 'admin-direct-upload-button button button--secondary ml-2';
                uploadButton.innerHTML = multiple
                    ? '<i class="fa-solid fa-upload mr-1"></i>{{ translate('Upload Images') }}'
                    : '<i class="fa-solid fa-upload mr-1"></i>{{ translate('Upload Image') }}';

                const status = document.createElement('span');
                status.className = 'admin-direct-upload-status';

                actionRow.appendChild(uploadButton);
                inputGroup.querySelector('.theme-input-wrapper')?.appendChild(fileInput);
                inputGroup.querySelector('.theme-input-wrapper')?.appendChild(status);

                const setStatus = (message = '', isError = false) => {
                    status.textContent = message;
                    status.classList.toggle('is-error', isError);
                };

                const renderUploadedPreviews = (uploadedFiles) => {
                    const preview = inputGroup.querySelector('.file-preview');
                    const mediaCount = inputGroup.querySelector('.media-count');

                    if (!preview || !mediaCount || !window.mediaManager?.markups?.inputMediaPreviewMarkup) {
                        refreshMediaPreviews();
                        return;
                    }

                    if (!multiple) {
                        preview.innerHTML = '';
                    }

                    uploadedFiles.forEach((file) => {
                        preview.insertAdjacentHTML(
                            'beforeend',
                            window.mediaManager.markups.inputMediaPreviewMarkup(file),
                        );
                    });

                    const selectedCount = mediaInput.value.split(',').filter(Boolean).length;
                    mediaCount.textContent = selectedCount === 1
                        ? '1 media selected'
                        : `${selectedCount} media selected`;
                };

                uploadButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    fileInput.click();
                });

                fileInput.addEventListener('change', async function() {
                    const files = Array.from(fileInput.files || []);
                    if (!files.length) {
                        return;
                    }

                    if (files.some((file) => !file.type.startsWith('image/'))) {
                        setStatus('{{ translate('Please select image files only') }}', true);
                        fileInput.value = '';
                        return;
                    }

                    uploadButton.disabled = true;
                    setStatus(`{{ translate('Uploading') }} ${files.length} ${files.length === 1 ? '{{ translate('image') }}' : '{{ translate('images') }}'}...`);

                    try {
                        const uploadedFiles = [];

                        for (const file of files) {
                            const formData = new FormData();
                            formData.append('media_file', file);

                            const response = await fetch(`{{ url('/file-manager/upload') }}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': `{{ csrf_token() }}`,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                            });

                            const uploadedFile = await response.json();
                            if (!response.ok || !uploadedFile?.id) {
                                throw new Error('{{ translate('Image upload failed') }}');
                            }

                            uploadedFiles.push(uploadedFile);
                        }

                        const existingIds = multiple
                            ? mediaInput.value.split(',').filter(Boolean)
                            : [];
                        const uploadedIds = uploadedFiles.map((file) => String(file.id));
                        mediaInput.value = [...existingIds, ...uploadedIds].join(',');
                        mediaInput.dispatchEvent(new Event('change', { bubbles: true }));
                        renderUploadedPreviews(uploadedFiles);
                        setStatus('{{ translate('Upload complete') }}');
                    } catch (error) {
                        setStatus(error?.message || '{{ translate('Image upload failed') }}', true);
                    } finally {
                        uploadButton.disabled = false;
                        fileInput.value = '';
                    }
                });
            };

            refreshMediaPreviews();
            window.setTimeout(refreshMediaPreviews, 100);
            setupDirectImageUpload('thumbnail_image', false);
            setupDirectImageUpload('gallery_images', true);
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const preOrderToggle = document.getElementById('pre_order_is_active');
        const preOrderSection = document.getElementById('pre_order_section');
        const sizeChartToggle = document.getElementById('isSizeChart');
        const sizeChartImageSection = document.getElementById('size_chart_image_section');
        const sizeChartEditor = document.getElementById('size_chart_editor');
        const sizeChartStateInput = document.getElementById('size_chart_state');
        const recipeDetailsToggle = document.getElementById('show_recipe_details');
        const recipeDetailsSection = document.getElementById('recipe_details_section');
        let initialSizeChartState = null;

        if (sizeChartStateInput?.value) {
            try {
                initialSizeChartState = JSON.parse(sizeChartStateInput.value);
            } catch (error) {
                initialSizeChartState = null;
            }
        }

        const defaultSizeChartState = {
            columns: ['', ''],
            rows: [
                ['', ''],
                ['', ''],
            ],
        };

        const escapeHtml = (value) =>
            String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const normalizeSizeChartState = (state) => {
            const source = state && typeof state === 'object' ? state : defaultSizeChartState;
            const columns = Array.isArray(source.columns) && source.columns.length ?
                source.columns.map((column) => String(column ?? '')) : [...defaultSizeChartState.columns];

            let rows = Array.isArray(source.rows) && source.rows.length ?
                source.rows.map((row) => {
                    const nextRow = Array.isArray(row) ? [...row] : [];
                    while (nextRow.length < columns.length) {
                        nextRow.push('');
                    }

                    return nextRow.slice(0, columns.length).map((cell) => String(cell ?? ''));
                }) :
                defaultSizeChartState.rows.map((row) => [...row]);

            if (!rows.length) {
                rows = [Array(columns.length).fill('')];
            }

            return {
                columns,
                rows,
            };
        };

        let sizeChartState = normalizeSizeChartState(initialSizeChartState);

        const syncSizeChartState = () => {
            if (sizeChartStateInput) {
                sizeChartStateInput.value = JSON.stringify(sizeChartState);
            }
        };

        const renderSizeChartEditor = () => {
            if (!sizeChartEditor) {
                return;
            }

            const columnControls = sizeChartState.columns
                .map((column, columnIndex) => `
                    <div class="w-64 min-w-64">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <button type="button"
                                class="w-10 h-10 rounded-md bg-secondary text-white border border-secondary shadow-sm disabled:opacity-40"
                                data-action="add-column"
                                data-column-index="${columnIndex}">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <button type="button"
                                class="w-10 h-10 rounded-md bg-white text-slate-700 border border-slate-300 shadow-sm disabled:opacity-40"
                                data-action="remove-column"
                                data-column-index="${columnIndex}"
                                ${sizeChartState.columns.length <= 1 ? 'disabled' : ''}>
                                <i class="fa-solid fa-minus"></i>
                            </button>
                        </div>
                        <input type="text"
                            class="theme-input"
                            placeholder="Column ${columnIndex + 1}"
                            data-role="column-input"
                            data-column-index="${columnIndex}"
                            value="${escapeHtml(column)}">
                    </div>
                `)
                .join('');

            const rowControls = sizeChartState.rows
                .map((row, rowIndex) => `
                    <div class="flex items-start gap-2 mb-2">
                        <div class="w-[96px] min-w-[96px] flex items-center gap-2 pt-1">
                            <button type="button"
                                class="w-10 h-10 rounded-md bg-secondary text-white border border-secondary shadow-sm disabled:opacity-40"
                                data-action="add-row"
                                data-row-index="${rowIndex}">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <button type="button"
                                class="w-10 h-10 rounded-md bg-white text-slate-700 border border-slate-300 shadow-sm disabled:opacity-40"
                                data-action="remove-row"
                                data-row-index="${rowIndex}"
                                ${sizeChartState.rows.length <= 1 ? 'disabled' : ''}>
                                <i class="fa-solid fa-minus"></i>
                            </button>
                        </div>
                        ${row.map((cell, columnIndex) => `
                            <div class="w-64 min-w-64">
                                <input type="text"
                                    class="theme-input"
                                    placeholder="Value"
                                    data-role="cell-input"
                                    data-row-index="${rowIndex}"
                                    data-column-index="${columnIndex}"
                                    value="${escapeHtml(cell)}">
                            </div>
                        `).join('')}
                    </div>
                `)
                .join('');

            sizeChartEditor.innerHTML = `
                <div class="overflow-x-auto pb-1">
                    <div class="min-w-max">
                        <div class="flex items-end gap-2 mb-2">
                            <div class="w-[96px] min-w-[96px]"></div>
                            ${columnControls}
                        </div>
                        ${rowControls}
                    </div>
                </div>
            `;

            syncSizeChartState();
        };

        function initPreOrderDatepickers() {
            $('#pre_order_section .date-picker input').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: true,
                drops: 'auto',
                autoApply: true,
                locale: {
                    format: 'DD-MM-YYYY',
                },
            });
        }

        if (preOrderToggle && preOrderSection) {
            preOrderToggle.addEventListener('change', function() {
                preOrderSection.style.display = this.checked ? 'block' : 'none';
                if (this.checked) {
                    setTimeout(initPreOrderDatepickers, 100);
                }
            });
        }

        if (preOrderToggle && preOrderToggle.checked) {
            initPreOrderDatepickers();
        }

        if (sizeChartToggle && sizeChartImageSection) {
            const toggleSizeChartImageSection = () => {
                sizeChartImageSection.style.display = sizeChartToggle.checked ? 'block' : 'none';
            };
            sizeChartToggle.addEventListener('change', toggleSizeChartImageSection);
            toggleSizeChartImageSection();
        }

        if (sizeChartEditor && sizeChartStateInput) {
            renderSizeChartEditor();

            sizeChartEditor.addEventListener('click', function(event) {
                const trigger = event.target.closest('[data-action]');
                if (!trigger) {
                    return;
                }

                const action = trigger.dataset.action;
                const columnIndex = Number(trigger.dataset.columnIndex);
                const rowIndex = Number(trigger.dataset.rowIndex);

                if (action === 'add-column') {
                    sizeChartState.columns.splice(columnIndex + 1, 0, '');
                    sizeChartState.rows = sizeChartState.rows.map((row) => {
                        const nextRow = [...row];
                        nextRow.splice(columnIndex + 1, 0, '');
                        return nextRow;
                    });
                }

                if (action === 'remove-column' && sizeChartState.columns.length > 1) {
                    sizeChartState.columns.splice(columnIndex, 1);
                    sizeChartState.rows = sizeChartState.rows.map((row) => {
                        const nextRow = [...row];
                        nextRow.splice(columnIndex, 1);
                        return nextRow;
                    });
                }

                if (action === 'add-row') {
                    sizeChartState.rows.splice(rowIndex + 1, 0, Array(sizeChartState.columns.length)
                        .fill(''));
                }

                if (action === 'remove-row' && sizeChartState.rows.length > 1) {
                    sizeChartState.rows.splice(rowIndex, 1);
                }

                renderSizeChartEditor();
            });

            sizeChartEditor.addEventListener('input', function(event) {
                const input = event.target;
                if (!(input instanceof HTMLInputElement)) {
                    return;
                }

                if (input.dataset.role === 'column-input') {
                    sizeChartState.columns[Number(input.dataset.columnIndex)] = input.value;
                    syncSizeChartState();
                }

                if (input.dataset.role === 'cell-input') {
                    const rowIndex = Number(input.dataset.rowIndex);
                    const columnIndex = Number(input.dataset.columnIndex);
                    sizeChartState.rows[rowIndex][columnIndex] = input.value;
                    syncSizeChartState();
                }
            });
        }

        if (recipeDetailsToggle && recipeDetailsSection) {
            const toggleRecipeDetailsSection = () => {
                recipeDetailsSection.style.display = recipeDetailsToggle.checked ? 'block' : 'none';
            };
            recipeDetailsToggle.addEventListener('change', toggleRecipeDetailsSection);
            toggleRecipeDetailsSection();
        }
    });

    function updateVariationPreOrder(variationId, setting) {
        // AJAX call to update variation pre-order settings
        fetch('/admin/api/pre-orders/products/{{ $product?->id }}/variations/' + variationId + '/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    enabled: setting
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Variation pre-order settings updated');
                } else {
                    toastr.error(data.message || 'Failed to update settings');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function printQrCode() {
        const productName = '{{ $product?->name ?? '' }}';
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>{{ translate('Print QR Code') }}</title>
                <style>
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                    }
                    .product-name {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 20px;
                        text-align: center;
                    }
                    .qr-image {
                        width: 200px;
                        height: 200px;
                    }
                    .qr-image img {
                        width: 100%;
                        height: 100%;
                        object-fit: contain;
                    }
                    @media print {
                        body {
                            min-height: auto;
                            padding: 0;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="product-name">${productName}</div>
                    <div class="qr-image">
                        @if ($product?->id)
                            <img src="{{ route('products.qr.image', ['id' => $product->id]) }}" alt="QR Code">
                        @endif
                    </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.print();
        };
    }

    // QR Color Management
    function updateQrColor(color) {
        document.getElementById('qr-color-value').textContent = color;
        // Note: Color will be applied to QR code after saving the product
    }

    // QR Links Management
    let qrLinkIndex = {{ count($qrLinks ?? []) }};
    const qrLinkIconOptions = @json($qrIconOptions ?? []);

    function getQrLinkIconPickerHtml() {
        return qrLinkIconOptions.map(option => `
            <button
                type="button"
                class="qr-icon-option inline-flex h-9 w-9 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 hover:border-secondary hover:text-secondary transition-all"
                data-icon-value="${option.value}"
                title="${option.label}"
                onclick="selectQrLinkIcon(this, '${option.value}')"
            >
                ${option.image_url
                    ? `<img src="${option.image_url}" alt="${option.label}" class="h-5 w-5 object-contain">`
                    : `<i class="${option.icon}"></i>`}
            </button>
        `).join('');
    }

    function isCustomQrIcon(value) {
        return /^https?:\/\//i.test(value) || value.startsWith('/');
    }

    function getQrIconClass(value) {
        const matchedOption = qrLinkIconOptions.find(option => option.value === value);

        if (matchedOption) {
            return matchedOption.icon;
        }

        if (!value) {
            return 'fa-solid fa-link';
        }

        if (value.includes(' ')) {
            return value;
        }

        const brandKeywords = ['youtube', 'facebook', 'instagram', 'twitter', 'x-twitter', 'whatsapp'];
        const prefix = brandKeywords.some(keyword => value.includes(keyword)) ? 'fa-brands' : 'fa-solid';

        return `${prefix} ${value}`;
    }

    function updateQrLinkIconButtonState(item) {
        const iconInput = item.querySelector('.qr-link-icon-input');
        const selectedValue = iconInput?.value.trim() || '';

        item.querySelectorAll('.qr-icon-option').forEach(button => {
            const isActive = button.dataset.iconValue === selectedValue;
            button.classList.toggle('border-secondary', isActive);
            button.classList.toggle('text-secondary', isActive);
            button.classList.toggle('bg-secondary/10', isActive);
        });
    }

    function updateQrLinkIconPreview(input) {
        const item = input.closest('.qr-link-item');
        const preview = item.querySelector('.qr-link-icon-preview');
        const value = input.value.trim();

        if (!value) {
            preview.innerHTML = '<i class="fa-solid fa-link"></i>';
        } else if (isCustomQrIcon(value)) {
            preview.innerHTML = `<img src="${value}" alt="" class="h-full w-full object-contain">`;
        } else {
            preview.innerHTML = `<i class="${getQrIconClass(value)}"></i>`;
        }

        updateQrLinkIconButtonState(item);
    }

    function initializeQrLinkItem(item) {
        const iconInput = item.querySelector('.qr-link-icon-input');

        if (iconInput) {
            updateQrLinkIconPreview(iconInput);
        }
    }

    function addQrLink() {
        const container = document.getElementById('qr-links-container');
        const html = `
            <div class="qr-link-item bg-gray-50 border rounded-lg mb-3 overflow-hidden">
                <div class="qr-link-header flex items-center justify-between p-3 cursor-pointer hover:bg-gray-100 transition-colors" onclick="toggleQrLink(this)">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <i class="fa-solid fa-chevron-down qr-link-chevron text-gray-400 transition-transform"></i>
                        <span class="qr-link-title font-medium text-sm truncate">{{ translate('New Link') }}</span>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700 ml-2" onclick="event.stopPropagation(); removeQrLink(this)">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
                <div class="qr-link-content border-t border-gray-200 p-3 space-y-3">
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">{{ translate('Link URL') }} <span class="text-red-500">*</span></label>
                        <input type="url" name="qr_links[${qrLinkIndex}][url]" 
                            class="theme-input text-sm qr-link-url" 
                            placeholder="https://example.com" 
                            required
                            oninput="updateQrLinkTitle(this)">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 mb-1 block">{{ translate('Title') }} <span class="text-muted">({{ translate('Optional') }})</span></label>
                        <input type="text" name="qr_links[${qrLinkIndex}][title]" 
                            class="theme-input text-sm qr-link-title-input" 
                            placeholder="{{ translate('e.g., Buy Now, Learn More') }}"
                            oninput="updateQrLinkTitle(this.closest('.qr-link-item').querySelector('.qr-link-url'))">
                    </div>
                    <div class="qr-link-icon-field border border-gray-200 rounded-lg bg-white overflow-hidden">
                        <button type="button"
                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-left hover:bg-gray-50 transition-colors"
                            onclick="toggleQrIconSection(this)">
                            <span class="min-w-0">
                                <span class="text-xs text-gray-600 block">{{ translate('Icon') }} <span class="text-muted">({{ translate('Optional') }})</span></span>
                                <span class="text-[11px] text-muted block mt-0.5">{{ translate('Expand to choose or paste a custom icon') }}</span>
                            </span>
                            <span class="flex items-center gap-2 flex-shrink-0">
                                <span class="qr-link-icon-preview inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 overflow-hidden">
                                    <i class="fa-solid fa-link"></i>
                                </span>
                                <i class="fa-solid fa-chevron-down qr-link-icon-chevron text-xs text-gray-400 transition-transform -rotate-90"></i>
                            </span>
                        </button>
                        <div class="qr-link-icon-content border-t border-gray-200 p-3 space-y-2" style="display: none;">
                            <input type="text" name="qr_links[${qrLinkIndex}][icon]" 
                                class="theme-input text-sm qr-link-icon-input" 
                                list="qr-link-icon-options"
                                placeholder="{{ translate('Select an icon or paste image URL') }}"
                                oninput="updateQrLinkIconPreview(this)">
                            <div class="flex flex-wrap gap-2">
                                ${getQrLinkIconPickerHtml()}
                            </div>
                            <p class="text-xs text-muted">{{ translate('Choose a Font Awesome icon or paste a custom icon image URL') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        initializeQrLinkItem(container.lastElementChild);
        qrLinkIndex++;
    }

    function selectQrLinkIcon(button, value) {
        const item = button.closest('.qr-link-item');
        const input = item.querySelector('.qr-link-icon-input');

        input.value = value;
        updateQrLinkIconPreview(input);
    }

    function toggleQrIconSection(button) {
        const field = button.closest('.qr-link-icon-field');
        const content = field.querySelector('.qr-link-icon-content');
        const chevron = field.querySelector('.qr-link-icon-chevron');

        if (content.style.display === 'none') {
            content.style.display = 'block';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.style.display = 'none';
            chevron.style.transform = 'rotate(-90deg)';
        }
    }

    function toggleQrLink(header) {
        const item = header.closest('.qr-link-item');
        const content = item.querySelector('.qr-link-content');
        const chevron = header.querySelector('.qr-link-chevron');

        if (content.style.display === 'none') {
            content.style.display = 'block';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.style.display = 'none';
            chevron.style.transform = 'rotate(-90deg)';
        }
    }

    function updateQrLinkTitle(urlInput) {
        const item = urlInput.closest('.qr-link-item');
        const titleDisplay = item.querySelector('.qr-link-title');
        const titleInput = item.querySelector('.qr-link-title-input');

        const url = urlInput.value;
        const customTitle = titleInput.value;

        if (customTitle) {
            titleDisplay.textContent = customTitle;
        } else if (url) {
            try {
                const urlObj = new URL(url);
                titleDisplay.textContent = urlObj.hostname.replace(/^www\./, '');
            } catch {
                titleDisplay.textContent = url;
            }
        } else {
            titleDisplay.textContent = '{{ translate('New Link') }}';
        }
    }

    function removeQrLink(button) {
        const linkItem = button.closest('.qr-link-item');
        linkItem.remove();
        reindexQrLinks();
    }

    function reindexQrLinks() {
        const container = document.getElementById('qr-links-container');
        const items = container.querySelectorAll('.qr-link-item');
        items.forEach((item, index) => {
            const inputs = item.querySelectorAll('input');
            inputs.forEach(input => {
                const fieldName = input.name.match(/\[(url|title|icon)\]$/)[1];
                input.name = `qr_links[${index}][${fieldName}]`;
            });
        });
        qrLinkIndex = items.length;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#qr-links-container .qr-link-item').forEach(initializeQrLinkItem);
    });
</script>
