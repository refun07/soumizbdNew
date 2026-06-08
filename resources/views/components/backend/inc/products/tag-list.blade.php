<x-backend.inputs.select data-placeholder="{{ translate('Select product tags') }}" multiple
    data-selection-css-class="multi-select2" :isRequired="false" name="tag_ids[]">
    @foreach ($tags as $tag)
        <x-backend.inputs.select-option name="{{ $tag->name }}" value="{{ $tag->id }}"
            selected="{{ $productTags->contains($tag->id) ? $tag->id : '' }}" />
    @endforeach
</x-backend.inputs.select>
