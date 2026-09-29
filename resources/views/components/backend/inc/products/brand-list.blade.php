<x-backend.inputs.radio>
    @foreach ($brands as $key => $brand)
        <li class="{{ $key != 0 ? 'my-4' : '' }}" data-brand-item
            data-collections="{{ $brand->collections->map(fn ($collection) => ['id' => $collection->id, 'name' => $collection->name])->values()->toJson() }}">
            <label class="inline-flex gap-3 items-center">
                <x-backend.inputs.radio-option name="brand_id" checkedValue="{{ $product?->brand_id ?? $selectedBrand }}"
                    value="{{ $brand->id }}" label="{{ $brand->collectTranslation('name', $langKey) }}" />
            </label>
        </li>
    @endforeach
</x-backend.inputs.radio>
