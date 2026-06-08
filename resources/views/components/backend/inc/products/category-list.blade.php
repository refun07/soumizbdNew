@foreach ($categories as $category)
    @php
        $selected = $productCategories->contains($category->id) ? $category->id : null;
        if (isset($selectedCategories)) {
            if (in_array($category->id, $selectedCategories)) {
                $selected = $category->id;
            }
        }
    @endphp

    <li class="category {{ count($category->childrenCategories) > 0 ? 'has-subcategory' : '' }}">
        <label class="flex gap-3 items-center">
            <x-backend.inputs.checkbox class="category-selector" name="category_ids[]" value="{{ $category->id }}"
                isChecked="{{ $productCategories->contains($category->id) || $selected == $category->id }}" />
            {{ $category->collectTranslation('name', $langKey) }}
        </label>

        @if (count($category->childrenCategories) > 0)
            <button type="button" class="category-toggler">
                <i class="fa-solid fa-chevron-down"></i>
            </button>

            <ul class="pl-5 mt-4 space-y-4">
                @foreach ($category->childrenCategories as $childCategory)
                    @include('backend.admin.products.subCategory', [
                        'subCategory' => $childCategory,
                        'langKey' => $langKey,
                        'productCategories' => $productCategories,
                        'selectedCategories' => isset($selectedCategories) ? $selectedCategories : [],
                    ])
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
