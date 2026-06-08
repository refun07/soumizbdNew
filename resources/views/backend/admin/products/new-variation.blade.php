<div class="grid md:grid-cols-2 gap-3 mt-4">
    <div class="md:col-span-1">
        <div class="flex items-center">
            <div class="w-full">{{ translate('Attribute') }} <span class="attribute-counter">{{ $chosenCounter }}</span>
            </div>
            <x-backend.inputs.select groupClass="w-full ms-4" name="chosen_variations[]"
                onchange="getVariationValues(this)" class="chosen_variations" data-search="false">
                <option value="">{{ translate('Select Variation') }}
                    @foreach ($variations as $variation)
                        <x-backend.inputs.select-option name="{{ $variation->collectTranslation('name') }}"
                            value="{{ $variation->id }}" />
                    @endforeach
            </x-backend.inputs.select>
        </div>
    </div>

    <div class="md:col-span-1">
        <div class="flex items-center">
            <div class="variationvalues w-full">
                <x-backend.inputs.text name="" placeholder="Select variation values" :disabled="true" />
            </div>
            <button class="button button-default" type="button" onclick="removeVariation(this)">
                <span class="footable-toggle fooicon fooicon-minus"></span>
            </button>
        </div>
    </div>
</div>
