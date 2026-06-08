<form action="{{ $category ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}"
    method="POST">
    @csrf

    @if ($category)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type category name"
            value="{{ $category?->collectTranslation('name', $langKey) }}" />

        <x-backend.inputs.select label="Base Category" name="parent_id" class="">
            <x-backend.inputs.select-option name="N/A" value="0" />
            @foreach ($categories as $cat)
                <x-backend.inputs.select-option name="{{ $cat->collectTranslation('name', $langKey) }}"
                    value="{{ $cat->id }}" selected="{{ $category?->parent_id == $cat->id ? $cat->id : 0 }}" />

                @foreach ($cat->childrenCategories as $childCategory)
                    @include('backend.admin.categories.subCategory', [
                        'subCategory' => $childCategory,
                        'langKey' => $langKey,
                        'category' => $category ? $category : null,
                    ])
                @endforeach
            @endforeach
        </x-backend.inputs.select>

        <x-backend.inputs.number label="Sorting Priority Number" name="sorting_order_level"
            placeholder="Type sorting priority number" value="{{ $category?->sorting_order_level }}" />

        <x-backend.inputs.file label="Thumbnail Image" name="thumbnail_image" value="{{ $category?->thumbnail_image }}"
            filesHint="This image will be used as thumbnail of the category. Recommended size: 64*64" />

        <x-backend.inputs.text label="Meta Title" name="meta_title" placeholder="Type meta title"
            value="{{ $category?->collectTranslation('meta_title', $langKey) }}" :isRequired="false" aiGenerate="true" />

        <x-backend.inputs.textarea label="Meta Description" name="meta_description" placeholder="Type meta description"
            value="{{ $category?->collectTranslation('meta_description', $langKey) }}" :isRequired="false"
            aiGenerate="true" />

        <x-backend.inputs.textarea label="Meta Keywords" name="meta_keywords"
            placeholder="Type comma separated keywords. e.g. keyword1, keyword2" value="{!! $category?->collectTranslation('meta_keywords', $langKey) !!}"
            :isRequired="false" aiGenerate="true" />

        <x-backend.inputs.file label="Meta Image" name="meta_image" value="{{ $category?->meta_image }}"
            filesHint="This image will be used as meta image of the category. Recommended size: 512*512" />

        @php
            $categoryVariations = $category ? $category->variations()->pluck('variation_id') : collect();
        @endphp

        <x-backend.inputs.select label="Filtering Variation Attributes" name="variation_ids[]" multiple
            data-selection-css-class="multi-select2"
            data-placeholder="{{ translate('Select variations as filter options') }}" class="" :isRequired="false">
            @foreach ($variations as $variation)
                @php
                    $selected = $categoryVariations->contains($variation->id) ? $variation->id : null;
                @endphp


                <x-backend.inputs.select-option name="{{ $variation->collectTranslation('name', $langKey) }}"
                    value="{{ $variation->id }}" selected="{{ $selected }}" />
            @endforeach
        </x-backend.inputs.select>

        @if ($category && $langKey == config('app.default_language'))
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $category->slug }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Category" type="submit" />
        </div>
    </div>
</form>
