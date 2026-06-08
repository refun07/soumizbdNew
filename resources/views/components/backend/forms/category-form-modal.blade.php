<form action="" method="POST" class="category-form-modal">
    @csrf

    @php
        $langKey = config('app.default_language');
    @endphp
    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type category name" value="" />

        <x-backend.inputs.select label="Base Category" name="parent_id" class="" :isRequired="false">

            <x-backend.inputs.select-option name="N/A" value="0" />
            @foreach ($categories as $cat)
                <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name', $langKey) }}"
                    value="{{ $cat->id }}" />

                @foreach ($cat->childrenCategories as $childCategory)
                    @include('backend.admin.categories.subCategory', [
                        'subCategory' => $childCategory,
                        'langKey' => $langKey,
                        'category' => null,
                    ])
                @endforeach
            @endforeach
        </x-backend.inputs.select>

        <x-backend.inputs.number label="Sorting Priority Number" name="sorting_order_level"
            placeholder="Type sorting priority number" />

        <x-backend.inputs.select label="Filtering Variation Attributes" name="variation_ids[]" multiple
            data-selection-css-class="multi-select2"
            data-placeholder="{{ translate('Select variations as filter options') }}" class="" :isRequired="false">
            @foreach ($variations as $variation)
                <x-backend.inputs.select-option name="{{ $variation->collectTranslation('name', $langKey) }}"
                    value="{{ $variation->id }}" />
            @endforeach
        </x-backend.inputs.select>


        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Category" type="submit" />
        </div>
    </div>
</form>
