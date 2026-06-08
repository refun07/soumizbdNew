<form
    action="{{ $coupon ? route(routePrefix() . '.coupons.update', $coupon->id) : route(routePrefix() . '.coupons.store') }}"
    method="POST" id="product-form">
    @csrf
    @if ($coupon)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="grid md:grid-cols-12 gap-3">
        <div class="md:col-span-7 md:row-span-2">
            {{-- code --}}
            <div class="card">
                <div class="card__title flex justify-between items-center">
                    {{ translate('General Informations') }}
                </div>

                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.text label="Code" name="code" placeholder="Type coupon code"
                            value="{{ $coupon?->code }}" />

                        @php
                            $discount_value = 0;
                            $discount_type = 'amount';

                            if ($coupon) {
                                $discount_value = $coupon->discount_value;
                                $discount_type = $coupon->discount_type;
                            }
                        @endphp

                        <x-backend.inputs.number label="Amount" name="discount_value" placeholder="Type discount amount"
                            value="{{ $discount_value }}" min="0" step="0.001" />

                        <x-backend.inputs.select label="Discount Type" name="discount_type" data-search="false">
                            <x-backend.inputs.select-option name="{{ translate('Amount') }}" value="amount"
                                selected="{{ $discount_type }}" />
                            <x-backend.inputs.select-option name="{{ translate('Percentage') }}" value="percentage"
                                selected="{{ $discount_type }}" />
                        </x-backend.inputs.select>

                        @php
                            $start_date = date('m/d/Y');
                            $end_date = date('m/d/Y');

                            if ($coupon) {
                                $start_date = $coupon->start_date ? date('m/d/Y', $coupon->start_date) : date('m/d/Y');
                                $end_date = $coupon->end_date ? date('m/d/Y', $coupon->end_date) : date('m/d/Y');
                            }
                        @endphp

                        <x-backend.inputs.datepicker label="Discount Date" placeholder="Start date - End date"
                            name="date_range" type="range" value="{{ $start_date . ' - ' . $end_date }}" />


                        <x-backend.inputs.file label="Thumbnail Image" name="banner" value="{{ $coupon?->banner }}"
                            filesHint="This image will be used as thumbnail image of coupon. Recommended size: 512*512" />


                        <div class="theme-input-group">
                            <label class="theme-input-label pt-0">
                                {{ translate('Terms & Conditions') }}
                            </label>

                            <div class="theme-input-wrapper">
                                @php
                                    $conditions = collect();
                                    if ($coupon) {
                                        $conditions = $coupon->conditions;
                                    }
                                @endphp
                                @if (count($conditions) > 0)
                                    @foreach ($conditions as $key => $condition)
                                        <div class="flex items-center mb-2">
                                            <input type="text" name="infos[]" class="theme-input"
                                                placeholder="{{ translate('Type your text') }}"
                                                value="{{ $condition->text }}" />

                                            @if ($key == 0)
                                                <button class="ms-1 btn bg-secondary/10 text-green-500 rounded-md p-3"
                                                    type="button" onclick="addCondition(this)">+</button>
                                            @else
                                                <button class="ms-1 btn bg-secondary/10 text-red-500 rounded-md p-3"
                                                    type="button" onclick="removeCondition(this)">x</button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center mb-2">
                                        <input type="text" name="infos[]" class="theme-input"
                                            placeholder="{{ translate('Type your text') }}" value="" />
                                        <button class="ms-1 btn bg-secondary/10 text-green-500 rounded-md p-3"
                                            type="button" onclick="addCondition(this)">+</button>
                                    </div>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Categories & products --}}
            <div class="card mt-3">
                <div class="card__title">
                    {{ translate('Categories & Products') }}
                    <div>
                        <small class="font-normal text-orange-400">
                            {{ translate('Coupon will be applicable only for the products, categories if selected.') }}
                        </small>
                    </div>
                </div>
                <div class="card__content">
                    <div class="space-y-3">

                        @php
                            $couponProducts = $coupon ? $coupon->couponProducts()->pluck('product_id') : collect();
                        @endphp


                        <x-backend.inputs.select label="Select Products"
                            data-placeholder="{{ translate('Select products') }}" name="product_ids[]" multiple
                            data-selection-css-class="multi-select2" :isRequired="false">
                            @foreach ($products as $product)
                                @php
                                    $selected = $couponProducts->contains($product->id) ? $product->id : null;
                                @endphp

                                <x-backend.inputs.select-option
                                    name="{{ Str::of($product->collectTranslation('name'))->limit(40) }}"
                                    value="{{ $product->id }}" selected="{{ $selected }}" />
                            @endforeach
                        </x-backend.inputs.select>

                        @php
                            $couponCategories = $coupon ? $coupon->couponCategories()->pluck('category_id') : collect();
                        @endphp
                        <x-backend.inputs.select label="Select Categories"
                            data-placeholder="{{ translate('Select categories') }}" name="category_ids[]" multiple
                            data-selection-css-class="multi-select2" :isRequired="false">
                            @foreach ($categories as $category)
                                @php
                                    $selected = $couponCategories->contains($category->id) ? $category->id : null;
                                @endphp

                                <x-backend.inputs.select-option name="{{ $category->collectTranslation('name') }}"
                                    value="{{ $category->id }}" selected="{{ $selected }}" />

                                @foreach ($category->childrenCategories as $childCategory)
                                    @include('backend.admin.categories.subCategory', [
                                        'subCategory' => $childCategory,
                                        'langKey' => $langKey,
                                        'couponCategories' => $couponCategories,
                                    ])
                                @endforeach
                            @endforeach
                        </x-backend.inputs.select>
                    </div>
                </div>
            </div>

            {{-- submit button --}}
            <div class="justify-end mt-6 hidden md:flex">
                <x-backend.inputs.button buttonText="Save Coupon" type="submit" />
            </div>
        </div>

        {{-- right bar --}}
        <div class="md:col-span-5">
            {{-- Status --}}
            <div class="card">
                <div class="card__title">
                    {{ translate('Coupon Status') }}
                </div>
                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.select name="is_published" data-search="false">
                            <x-backend.inputs.select-option name="{{ translate('Published') }}" value="1"
                                selected="{{ $coupon?->is_published }}" />
                            <x-backend.inputs.select-option name="{{ translate('Unpublished') }}" value="0"
                                selected="{{ $coupon?->is_published }}" />
                        </x-backend.inputs.select>
                    </div>
                </div>
            </div>

            {{-- Shipping --}}
            <div class="card mt-3">
                <div class="card__title">
                    {{ translate('Free Shipping') }}
                </div>
                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.select name="is_free_shipping" data-search="false">
                            <x-backend.inputs.select-option name="{{ translate('Unavailable') }}" value="0"
                                selected="{{ $coupon?->is_free_shipping }}" />
                            <x-backend.inputs.select-option name="{{ translate('Has Free Shipping') }}"
                                value="1" selected="{{ $coupon?->is_free_shipping }}" />
                        </x-backend.inputs.select>
                    </div>
                </div>
            </div>

            {{-- Restrictions --}}
            <div class="card mt-3">
                <div class="card__title">
                    {{ translate('Usage Restrictions') }}
                </div>

                <div class="card__content">
                    <div class="space-y-3">
                        <x-backend.inputs.number label="Minimum Spend" name="min_spend"
                            placeholder="Type minimum spend amount" value="{{ $coupon?->discount_value }}"
                            min="0" step="0.001" />

                        <x-backend.inputs.number label="Maximum Discount" name="max_discount_value"
                            placeholder="Type maximum discount" value="{{ $coupon?->max_discount_value }}"
                            min="0" step="0.001" />

                        <x-backend.inputs.number label="Total Usage Limit" name="total_usage_limit" placeholder=""
                            value="{{ $coupon?->total_usage_limit }}" min="1" step="0.001" />

                        <x-backend.inputs.number label="Each User Limit" name="customer_usage_limit" placeholder=""
                            value="{{ $coupon?->customer_usage_limit }}" min="1" />

                    </div>
                </div>
            </div>
        </div>

        {{-- submit button --}}
        <div class="flex justify-end mt-6 md:hidden">
            <x-backend.inputs.button buttonText="Save Coupon" type="submit" />
        </div>
</form>
