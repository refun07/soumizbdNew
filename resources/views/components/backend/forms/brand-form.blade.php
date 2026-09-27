@props(['brand' => null, 'langKey' => null, 'categories' => collect()])

<form action="{{ $brand ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}" method="POST">
    @csrf

    @if ($brand)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type brand name"
            value="{{ $brand?->collectTranslation('name', $langKey) }}" />

        <x-backend.inputs.file label="Thumbnail Image" name="thumbnail_image" value="{{ $brand?->thumbnail_image }}"
            filesHint="This image will be used as thumbnail of the brand. Recommended size: 300*300" />

        <x-backend.inputs.file label="Banner Image" name="banner_image" value="{{ $brand?->banner_image }}" />

        @if ($langKey == config('app.default_language'))
            @php
                $brandCategories = $brand ? $brand->categories()->pluck('categories.id') : collect();
            @endphp

            <x-backend.inputs.select label="Brand Categories" name="category_ids[]" multiple
                class="brand-category-select" data-tags="true" data-no-add-new="1"
                data-selection-css-class="multi-select2"
                data-placeholder="{{ translate('Select categories for this brand') }}" :isRequired="false">
                @foreach ($categories ?? [] as $category)
                    @php
                        $selected = $brandCategories->contains($category->id) ? $category->id : null;
                    @endphp

                    <x-backend.inputs.select-option name="{{ $category->collectTranslation('name', $langKey) }}"
                        value="{{ $category->id }}" selected="{{ $selected }}" />

                    @foreach ($category->childrenCategories as $childCategory)
                        @include('backend.admin.categories.subCategory', [
                            'subCategory' => $childCategory,
                            'langKey' => $langKey,
                            'category' => null,
                            'selectedCategories' => $brandCategories->all(),
                        ])
                    @endforeach
                @endforeach
            </x-backend.inputs.select>
        @endif

        <x-backend.inputs.text label="Meta Title" name="meta_title" placeholder="Type meta title"
            value="{{ $brand?->collectTranslation('meta_title', $langKey) }}" :isRequired="false" aiGenerate="true" />

        <x-backend.inputs.textarea label="Meta Description" name="meta_description" placeholder="Type meta description"
            value="{{ $brand?->collectTranslation('meta_description', $langKey) }}" :isRequired="false"
            aiGenerate="true" />

        <x-backend.inputs.textarea label="Meta Keywords" name="meta_keywords"
            placeholder="Type comma separated keywords. e.g. keyword1, keyword2" value="{!! $brand?->collectTranslation('meta_keywords', $langKey) !!}"
            :isRequired="false" aiGenerate="true" />

        <x-backend.inputs.file label="Meta Image" name="meta_image" value="{{ $brand?->meta_image }}" />

        @if ($brand && $langKey == config('app.default_language'))
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $brand->slug }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Brand" type="submit" />
        </div>
    </div>
</form>
