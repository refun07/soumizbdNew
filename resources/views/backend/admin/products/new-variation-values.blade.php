<div class="relative">
    <x-backend.inputs.select name="option_{{ $variation_id }}_choices[]" onchange="generateVariationCombinations()"   class="initSelect2"
        data-placeholder="{{ translate('Select values here') }}" multiple data-selection-css-class="multi-select2" data-is-color="{{ $variation->is_color ?? 0 }}">
        @foreach ($variation_values as $variation_value)
            <x-backend.inputs.select-option name="{{ $variation_value->collectTranslation('name') }}"
                value="{{ $variation_value->id }}" />
        @endforeach
    </x-backend.inputs.select>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ translate('Type to search or create a custom value') }}</p>
</div>
