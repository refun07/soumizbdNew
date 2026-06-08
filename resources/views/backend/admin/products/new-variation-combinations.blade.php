@if (count($combinations[0]) > 0)
    <div class="mt-8">
        <div class="flex items-center justify-between gap-3 relative">
            <h4 class="font-medium bg-background pr-4 relative z-[1]">{{ translate('Variations') }}</h4>
            <span
                class="absolute inline-block top-1/2 left-0 right-0 -translate-y-1/2 border-b border-[#E7E7E7] w-full h-0.5"></span>
        </div>

        <div class="product-variations init-accordion accordion-container border-collapse mt-5">
            @foreach ($combinations as $key => $combination)
                @php
                    $name = '';
                    $code = '';
                    $lstKey = array_key_last($combination);

                    foreach ($combination as $option_id => $choice_id) {
                        $option_name = \App\Models\Variation::find($option_id)->collectTranslation('name');
                        $choice_name = \App\Models\VariationValue::find($choice_id)->collectTranslation('name');

                        $name .= $choice_name;
                        $code .= $option_id . ':' . $choice_id . '/';

                        if ($lstKey != $option_id) {
                            $name .= '-';
                        }
                    }
                @endphp
                <div class="ac">
                    <h2 class="ac-header">
                        <button type="button" class="ac-trigger">
                            <span class="text-muted mr-3">{{ translate('Variation') }} {{ $key + 1 }}:</span>
                            {{ $name }}
                        </button>
                    </h2>
                    <div class="ac-panel">
                        <div class="py-4 xl:py-7 px-3 md:px-6 xl:px-10 grid sm:grid-cols-2 xl:grid-cols-4 gap-7">

                            <input type="hidden" value="{{ $code }}"
                                name="variations[{{ $key }}][code]">

                            <div>
                                <label class="mb-2">{{ translate('SKU') }}</label>
                                <x-backend.inputs.text name="variations[{{ $key }}][sku]"
                                    value="{{ $name }}" placeholder="red-xl-346890" />
                            </div>
                            <div>
                                <label class="mb-2">{{ translate('Price') }}</label>
                                <x-backend.inputs.text name="variations[{{ $key }}][price]" min="0"
                                    step="0.001" value="0" placeholder="" />
                            </div>
                            <div>
                                <label class="mb-2">{{ translate('Discount') }}</label>
                                <x-backend.inputs.text type="number"
                                    name="variations[{{ $key }}][discount_value]" min="0"
                                    value="0" step="0.001" placeholder="" />
                            </div>
                            <div>
                                <label class="mb-2">{{ translate('Discount Type') }}</label>
                                <select class="theme-input h-auto p-3 py-2.5"
                                    name="variations[{{ $key }}][discount_type]">
                                    <option value="flat">{{ translate('Flat') }}</option>
                                    <option value="percentage">{{ translate('Percentage') }}</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2 {{ !useInventory() ? 'xl:col-span-3' : 'xl:col-span-4' }} ">
                                <label class="mb-2">{{ translate('Image') }}</label>
                                <x-backend.inputs.file name="variations[{{ $key }}][image]" value=""
                                    filesHint="" />
                            </div>

                            @php
                                $isColorVariation = false;
                                foreach ($combination as $option_id => $choice_id) {
                                    $variation = \App\Models\Variation::find($option_id);
                                    if ($variation && $variation->is_color == 1) {
                                        $isColorVariation = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if (\App\Models\FeatureFlag::isEnabled('color_image_enabled') && $isColorVariation)
                                <div class="sm:col-span-2 xl:col-span-4">
                                    <label class="mb-2">{{ translate('Color Swatch Image') }}</label>
                                    <div class="flex items-center gap-2">
                                        <x-backend.inputs.file name="variations[{{ $key }}][combination_image]" value=""
                                            filesHint="" />
                                        <button type="button"
                                            class="use-variation-image-btn text-nowrap px-3 py-2 text-sm bg-primary text-white rounded-md hover:bg-primary/90 opacity-50 cursor-not-allowed"
                                            disabled
                                            title="{{ translate('Upload variation image first, then use this button') }}">
                                            {{ translate('Use Variation Image') }}
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">{{ translate('Small image shown in color selector') }}</p>
                                </div>
                            @endif

                            @if (!useInventory())
                                <div>
                                    <label class="mb-2">{{ translate('Stock') }}</label>
                                    <x-backend.inputs.text type="number"
                                        name="variations[{{ $key }}][stock_qty]" min="0" value="0"
                                        placeholder="" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif


