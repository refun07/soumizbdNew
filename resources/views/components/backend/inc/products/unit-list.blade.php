<x-backend.inputs.select label="Product Unit" name="unit_id" data-search="false" data-no-add-new="true">
    @foreach ($units as $unit)
    <x-backend.inputs.select-option name="{{ $unit->collectTranslation('name', $langKey) }}" value="{{ $unit->id }}" selected="{{ $product?->unit_id ?? $selectedUnit }}" />
    @endforeach
</x-backend.inputs.select>